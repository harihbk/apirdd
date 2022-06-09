<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Projectmilestonedates;
use App\Models\Projectinvestordates;
use App\Models\Projectcontact;
use App\Models\Projecttemplate;
use App\Models\Projectdocs;
use App\Models\Projectinspections;
use App\Models\FitoutCompletionCertificates;
use App\Models\FitoutDepositrefund;
use PDF;
use Response;
use Validator;

class ReportController extends Controller
{
    function generateReport(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $result = array();
        $projectData = Project::
        leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')
        ->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')
        ->leftjoin('tbl_company_master','tbl_company_master.company_id','=','tbl_projects.investor_company')
        ->leftjoin('tbl_projecttype_master','tbl_projecttype_master.type_id','=','tbl_projects.project_type')
        ->where('tbl_projects.property_id',$request->input('property_id'));
        if($request->input('unit_id')!='')
        {
            $projectData = $projectData->where('tbl_projects.unit_id',$request->input('unit_id'));
        }
         $projectData = $projectData->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projects.investor_brand','tbl_projects.fitout_period','tbl_projects.fitout_deposit_status','tbl_projects.ivr_status','tbl_projects.insurance_validity_date','tbl_projects.project_status','tbl_company_master.company_name','tbl_properties_master.property_id','tbl_properties_master.property_name','tbl_properties_master.property_logo','tbl_units_master.unit_Id','tbl_units_master.unit_name','tbl_projecttype_master.type_name','tbl_units_master.unit_area')
        ->groupBy('tbl_projects.project_id')->take(3)
        ->get();
        $data = array();
        if(count($projectData)==0)
        {
            return response()->json(['response'=>"No data for this Property"], 410);
        }
        for($i=0;$i<count($projectData);$i++)
        {
            /* Milestone section */
            $milestoneData = Projectmilestonedates::where('project_id',$projectData[$i]['project_id'])->select('project_id','concept_submission','detailed_design_submission','version','unit_handover','fitout_start','fitout_completion','store_opening')->get();
            $projectData[$i]->milestone_data = $milestoneData;

             /* Investor dates section */
            $investorDates = Projectinvestordates::where('project_id',$projectData[$i]['project_id'])->select('project_id','concept_submission','detailed_design_submission','version','fitout_start','fitout_completion')->get();
            $projectData[$i]->investor_dates = $investorDates;

            /* Contact details section */
            $rddContactsData = Projectcontact::
                            leftjoin('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_project_contact_details.member_designation')
                            ->leftjoin('users','users.mem_id','=','tbl_project_contact_details.member_id')
                            ->whereNotIn('tbl_project_contact_details.member_designation',[13,14])
                           ->where('tbl_project_contact_details.project_id',$projectData[$i]['project_id'])
                           ->select('tbl_designation_master.designation_id','tbl_designation_master.designation_name','users.mem_name as first_name','users.mem_last_name as last_name')
                           ->get();

            $investorContactsData = Projectcontact::
                            leftjoin('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_project_contact_details.member_designation')
                            ->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')
                            ->whereIn('tbl_project_contact_details.member_designation',[13,14])
                           ->where('tbl_project_contact_details.project_id',$projectData[$i]['project_id'])
                           ->select('tbl_designation_master.designation_id','tbl_designation_master.designation_name','tbl_tenant_master.tenant_name as first_name','tbl_tenant_master.tenant_last_name as last_name')
                           ->get();
            $projectData[$i]->rdd_contact_data = $rddContactsData;
            $projectData[$i]->investor_contact_data = $investorContactsData;

            /* Phase wise Data Section */
            //startup Phase
            $startupPhaseData = Projecttemplate::
                         leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_template.phase_id')
                         ->where('tbl_project_template.project_id',$projectData[$i]['project_id'])
                         ->where('tbl_project_template.phase_id',1)
                         ->select('tbl_project_template.phase_id','tbl_project_template.activity_desc','tbl_project_template.actual_date','tbl_project_template.planned_date','tbl_project_template.task_status','tbl_phase_master.phase_name')
                         ->groupBy('tbl_phase_master.phase_name')->get();
            $projectData[$i]->startup_phase = $startupPhaseData;

            //Design Phase
            $designPhaseData = Projectdocs::
                               leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_projecttasks_docs.phase_id')
                               ->where('tbl_projecttasks_docs.project_id',$projectData[$i]['project_id'])
                               ->where('tbl_projecttasks_docs.phase_id',2)
                               ->select('tbl_projecttasks_docs.doc_header','tbl_projecttasks_docs.doc_title','tbl_projecttasks_docs.due_date','tbl_projecttasks_docs.doc_status','tbl_projecttasks_docs.updated_at')
                               ->get()->groupBy('doc_header');
            $projectData[$i]->design_phase = $designPhaseData;
                        
            //Fitout Phase
             $fitoutPhaseData = ProjectTemplate::
                         leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_template.phase_id')
                         ->where('tbl_project_template.project_id',$projectData[$i]['project_id'])
                         ->where('tbl_project_template.phase_id',3)
                         ->select('tbl_project_template.phase_id','tbl_project_template.activity_desc','tbl_project_template.actual_date','tbl_project_template.planned_date','tbl_project_template.task_status','tbl_phase_master.phase_name')
                         ->groupBy('tbl_phase_master.phase_name')->get();
            
            $projectData[$i]->fitout_phase = $fitoutPhaseData;

            $inspectionData = Projectinspections::
                              leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')
                              ->select('tbl_checklisttemplate_master.template_name','tbl_project_inspections.requested_time','tbl_project_inspections.updated_at','tbl_project_inspections.inspection_status','tbl_project_inspections.report_status')
                              ->where('tbl_project_inspections.project_id',$projectData[$i]['project_id'])
                              ->where('tbl_project_inspections.inspection_status',2)  
                              ->get();
            $projectData[$i]->inspection_data = $inspectionData;

            // //Completion Phase
            $fccData = FitoutCompletionCertificates::
                       where('tbl_fitout_completion_certificates.project_id',$projectData[$i]['project_id']) 
                       ->select('tbl_fitout_completion_certificates.planned_date','tbl_fitout_completion_certificates.actual_date','tbl_fitout_completion_certificates.isGenerated')
                       ->get();
            $projectData[$i]->fcc_data = $fccData;

            $fdrData = FitoutDepositrefund::
                       where('tbl_fitout_deposit_refund.project_id',$projectData[$i]['project_id']) 
                       ->select('tbl_fitout_deposit_refund.planned_date','tbl_fitout_deposit_refund.actual_date','tbl_fitout_deposit_refund.isdrfGenerated')
                       ->get(); 
            $projectData[$i]->fdr_data = $fdrData;
        }
        try
        {
            $prop = explode("/public",$projectData[0]->property_logo);
            $data = array('result'=>$projectData,'property_name'=>$projectData[0]->property_name,'property_logo'=>$prop[1]);
            $pdf = PDF::loadView('reportPDF', $data);
            return $pdf->stream();
        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }
}
