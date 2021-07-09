<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Projectcontact;
use App\Models\FitoutCompletionCertificates;
use App\Models\FitoutDepositrefund;
use App\Models\Financeteam;
use App\Models\Projectinspections;
use App\Models\Projectinspectionitems;
use App\Models\Handovercertificate;
use Validator;
use PDF;
use File;
use Mail;


class PDFController extends Controller
{
    public function generateHOC(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required',
            'doc_path'=>'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $projectDetails = $this->getProjectDetails($request->input('project_id'));
        $inspectionData = $this->getInspectiondata($request->input('project_id'),2);
        if(count($inspectionData['inspection_items'])==0 || $inspectionData['inspection_data']=='')
        {
            return response()->json(['response'=>"No Inspections data for this Project"], 410);
        }
        $data = array();
        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
            "inspection_data" => $inspectionData['inspection_items'],
            "company_name" => $projectDetails[0]['company_name'],
            "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
            "inspection_date" => date('d-m-Y',strtotime($inspectionData['inspection_data']['requested_time']))
        ];
       

        try
        {
            $pdf = PDF::loadView('hocPDF', $data);
            $destination_path = $request->input('doc_path')."/hoc/generated/HOC_".$projectDetails[0]['project_id']."_".$projectDetails[0]['project_name'].".pdf";
            if(!File::isDirectory($request->input('doc_path')."/hoc/generated")){
                File::makeDirectory($request->input('doc_path')."/hoc/generated", 0777, true, true);
            }
            $fileMove = file_put_contents($destination_path, $pdf->output());
            if($fileMove==false)
            {
                return response()->json(['response'=>"Cannot generate HOC document"], 410);
            }
            $generated_path = $request->input('doc_path')."/hoc/generated/HOC_".$projectDetails[0]['project_id']."_".$projectDetails[0]['project_name'].".pdf";
            $hocEntrycount = Handovercertificate::where('project_id',$request->input('project_id'))->where('isDeleted',0)->count();
            if($hocEntrycount==0)
            {
                $hoc = new Handovercertificate();
                $hoc->project_id = $request->input('project_id');
                $hoc->doc_type = 'Handover Certificate';
                $hoc->created_at = $created_at;
                $hoc->updated_at = $updated_at;
                $hoc_entry = $hoc->save();
            }
            Handovercertificate::where('project_id',$request->input('project_id'))->where('isDeleted',0)->update(
                array(
                    "isGenerated"=>1,
                    "updated_at"=>$updated_at,
                    "generated_path"=>$generated_path
                )
            );
            return response()->json(['generated_path'=>$generated_path], 200);
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }
    public function generateFCC(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required',
            'doc_path'=>'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $updated_at = date('Y-m-d H:i:s');
        $projectDetails = $this->getProjectDetails($request->input('project_id'));
        $inspectionData = $this->getInspectiondata($request->input('project_id'));
        if(count($inspectionData['inspection_items'])==0 || $inspectionData['inspection_data']=='')
        {
            return response()->json(['response'=>"No Inspections data for this Project"], 410);
        }
        $data = array();
        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
            "inspection_data" => $inspectionData['inspection_items'],
            "company_name" => $projectDetails[0]['company_name'],
            "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
            "inspection_date" => date('d-m-Y',strtotime($inspectionData['inspection_data']['requested_time']))
        ];


        try
        {
            $pdf = PDF::loadView('fccPDF', $data);
            $destination_path = $request->input('doc_path')."/generated/FCC_".$projectDetails[0]['project_id']."_".$projectDetails[0]['project_name'].".pdf";
            if(!File::isDirectory($request->input('doc_path')."/generated")){
                File::makeDirectory($request->input('doc_path')."/generated", 0777, true, true);
            }
            $fileMove = file_put_contents($destination_path, $pdf->output());
            if($fileMove==false)
            {
                return response()->json(['response'=>"Cannot generate FCC document"], 410);
            }
            $generated_path = $request->input('doc_path')."/generated/FCC_".$projectDetails[0]['project_id']."_".$projectDetails[0]['project_name'].".pdf";
            FitoutCompletionCertificates::where('project_id',$request->input('project_id'))->where('isDeleted',0)->update(
                array(
                    "isGenerated"=>1,
                    "updated_at"=>$updated_at,
                    "generated_path"=>$generated_path
                )
            );
            return response()->json(['generated_path'=>$generated_path], 200); 
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }

    public function generateFDR(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $projectDetails = $this->getProjectDetails($request->input('project_id'));
        $memberDetails = $this->getProjectMembers($request->input('project_id'));
        $updated_at = date('Y-m-d H:i:s');
        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'property_name' => $projectDetails[0]['property_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "pre_date" => date('d-m-Y'),
            "rdd_manager" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
        ];
        try
        {
            $pdf = PDF::loadView('fdrPDF', $data);
            $destination_path = $request->input('doc_path')."/generated/FDR_".$projectDetails[0]['project_id']."_".$projectDetails[0]['project_name'].".pdf";
            if(!File::isDirectory($request->input('doc_path')."/generated")){
                File::makeDirectory($request->input('doc_path')."/generated", 0777, true, true);
            }
            $fileMove = file_put_contents($destination_path, $pdf->output());
            if($fileMove==false)
            {
                return response()->json(['response'=>"Cannot generate FDR document"], 410);
            }
            $generated_path = $request->input('doc_path')."/generated/FDR_".$projectDetails[0]['project_id']."_".$projectDetails[0]['project_name'].".pdf";
            FitoutDepositrefund::where('project_id',$request->input('project_id'))->where('isDeleted',0)->update(array(
                "isdrfGenerated" => 1,
                "generated_path"=>$generated_path,
                "updated_at" => $updated_at
            ));
            return response()->json(['generated_path'=>$generated_path], 200);    
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }
    public function sendFcc(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required',
            'fcc_doc_path' => 'required',
            'fcc_id' => 'required'
        ]);


        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $actual_date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');

        $memberDetails = $this->getProjectMembers($request->input('project_id'));
        $projectDetails = $this->getProjectDetails($request->input('project_id'));
        $inspectionData = $this->getInspectiondata($request->input('project_id'));
        $Mep = Projectcontact::where('project_id',$request->input('project_id'))->where('isDeleted',0)->where('member_designation',5)->get();
        $ccMembers = array();
        for($a=0;$a<count($Mep);$a++)
        {
            $ccMembers[] = $Mep[$a]['email'];
        }
        $ccMembers[] = $memberDetails[0]["rdd_manager"];
        if(count($inspectionData['inspection_items'])==0 || $inspectionData['inspection_data']=='')
        {
            return response()->json(['response'=>"No Inspections data for this Project"], 410);
        }
        FitoutCompletionCertificates::where('project_id',$request->input('project_id'))->where('id',$request->input('fcc_id'))->update(array(
            "actual_date" => $actual_date,
            "updated_at" => $updated_at
        ));
        // $data = [
        //     'unit_name' => $projectDetails[0]['unit_name'],
        //     'investor_brand' => $projectDetails[0]['investor_brand'],
        //     "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
        //     "inspection_data" => $inspectionData['inspection_items'],
        //     "company_name" => $projectDetails[0]['company_name'],
        //     "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
        //     "inspection_date" => date('d-m-Y',strtotime($inspectionData['inspection_data']['requested_time']))
        // ];
        
        $emailData = [
            "investor" => $memberDetails[0]["investor"],
            "rdd_manager" => $ccMembers,
            "project_name" => $memberDetails[0]["project_name"],
            "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
            'unit_name' => $projectDetails[0]['unit_name'],
            'property_name' => $projectDetails[0]['property_name'],
            'tenant_name'=> $projectDetails[0]['tenant_name']."-".($projectDetails[0]['tenant_last_name']!=null?$projectDetails[0]['tenant_last_name']:""),
            "inspection_date" => date('d-m-Y',strtotime($inspectionData['inspection_data']['requested_time']))
        ];
        try
        {
            // $pdf = PDF::loadView('fccPDF', $data);
            // $pre_date = date('d-m-Y H:i:s');
            // $destination_path = $request->input('fcc_doc_path')."/sent/FCC_".$pre_date.".pdf";
            // if(!File::isDirectory($request->input('fcc_doc_path')."/sent")){
            //     File::makeDirectory($request->input('fcc_doc_path')."/sent", 0777, true, true);
            // }
            // $fileMove = file_put_contents($destination_path, $pdf->output());
            // if($fileMove==false)
            // {
            //     return response()->json(['response'=>"Cannot generate FCC document"], 410);
            // }
            $fcc = FitoutCompletionCertificates::where('project_id',$request->input('project_id'))->where('isDeleted',0)->first();
            if($fcc==''|| $fcc['generated_path']==null)
            {
                return response()->json(['response'=>"FCC document not yet generated"], 410);
            }
            else
            {
                $files = [
                    $fcc['generated_path']
                ];
                Mail::send('emails.sendFcc', $emailData, function($message)use($emailData, $files) {
                    $message->to($emailData["investor"])
                    ->cc($emailData["rdd_manager"])
                     ->subject($emailData["unit_name"]."-".$emailData["property_name"]."-FCC & Final inspection report");
        
                    forEach ($files as $file){
                        $message->attach($file,['as'=>'FCC_'.$emailData['project_name'].".pdf",'mime'=> "application/pdf"]);
                    }      
                });
                if(Mail::failures())
                {
                    return response()->json(['response'=>"FCC Mail Not Sent"], 410);
                }
                else
                {
                    return response()->json(['response'=>"FCC Mail Sent"], 200);
                }
            }
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
        
    }
    public function sendHoc(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required',
            'hoc_doc_path' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $memberDetails = $this->getProjectMembers($request->input('project_id'));
        $projectDetails = $this->getProjectDetails($request->input('project_id'));
        $inspectionData = $this->getInspectiondata($request->input('project_id'),2);
        $Mep = Projectcontact::where('project_id',$request->input('project_id'))->where('isDeleted',0)->where('member_designation',5)->get();
        $ccMembers = array();
        for($a=0;$a<count($Mep);$a++)
        {
            $ccMembers[] = $Mep[$a]['email'];
        }
        $ccMembers[] = $memberDetails[0]["rdd_manager"];
        if(count($inspectionData['inspection_items'])==0 || $inspectionData['inspection_data']=='')
        {
            return response()->json(['response'=>"No Inspections data for this Project"], 410);
        }
        $hoc = Handovercertificate::where('project_id',$request->input('project_id'))->where('isDeleted',0)->first();
        if($hoc==''|| $hoc['generated_path']==null)
        {
            return response()->json(['response'=>"HOC document not yet generated"], 410);
        }
        // $data = [
        //     'unit_name' => $projectDetails[0]['unit_name'],
        //     'investor_brand' => $projectDetails[0]['investor_brand'],
        //     "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
        //     "inspection_data" => $inspectionData['inspection_items'],
        //     "company_name" => $projectDetails[0]['company_name'],
        //     "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
        //     "inspection_date" => date('d-m-Y',strtotime($inspectionData['inspection_data']['requested_time']))
        // ];
        
        $emailData = [
            "investor" => $memberDetails[0]["investor"],
            "rdd_manager" => $ccMembers,
            "project_name" => $memberDetails[0]["project_name"],
            "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
            'unit_name' => $projectDetails[0]['unit_name'],
            'property_name' => $projectDetails[0]['property_name'],
            'tenant_name'=> $projectDetails[0]['tenant_name']."-".($projectDetails[0]['tenant_last_name']!=null?$projectDetails[0]['tenant_last_name']:""),
            "inspection_date" => date('d-m-Y',strtotime($inspectionData['inspection_data']['requested_time']))
        ];
        try
        {
            // $pdf = PDF::loadView('hocPDF', $data);
            // $pre_date = date('d-m-Y H:i:s');
            // $destination_path = $request->input('hoc_doc_path')."hoc/sent/HOC_".$pre_date.".pdf";
            // if(!File::isDirectory($request->input('hoc_doc_path')."hoc/sent")){
            //     File::makeDirectory($request->input('hoc_doc_path')."hoc/sent", 0777, true, true);
            // }
            // $fileMove = file_put_contents($destination_path, $pdf->output());
            // if($fileMove==false)
            // {
            //     return response()->json(['response'=>"Cannot generate HOC document"], 410);
            // }
            $files = [
                $hoc['generated_path']            
            ];
            Mail::send('emails.sendHoc', $emailData, function($message)use($emailData, $files) {
                $message->to($emailData["investor"])
                        ->cc($emailData["rdd_manager"])
                        ->subject($emailData["unit_name"]."-".$emailData["property_name"]."-Handover Certificate");
     
                forEach ($files as $file){
                    $message->attach($file,['as'=>'HOC_'.$emailData['project_name'].".pdf",'mime'=> "application/pdf"]);
                }      
            });
            if(Mail::failures())
            {
                return response()->json(['response'=>"HOC Mail Not Sent"], 410);
            }
            else
            {
                return response()->json(['response'=>"HOC Mail Sent"], 200);
            }
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }   
    public function sendDrf(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required',
            'drf_doc_path' => 'required',
            'drf_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $memberDetails = $this->getProjectMembers($request->input('project_id'));
        $projectDetails = $this->getProjectDetails($request->input('project_id'));

        $actual_date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        FitoutDepositrefund::where('project_id',$request->input('project_id'))->where('id',$request->input('drf_id'))->update(array(
            "actual_date" => $actual_date,
            "updated_at" => $updated_at
        ));
        $finance_details = Financeteam::where('property_id',$projectDetails[0]['property_id'])->where('isDeleted',0)->get();
        $finance_email = array();
        for($i=0;$i<count($finance_details);$i++)
        {
            $finance_email[] = $finance_details[$i]['email'];
        }
        // $data = [
        //     'unit_name' => $projectDetails[0]['unit_name'],
        //     'investor_brand' => $projectDetails[0]['investor_brand'],
        //     "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
        //     "property_name"=> $projectDetails[0]['property_name']
        // ];

        $emailData = [
            "investor" => $memberDetails[0]["investor"],
            "rdd_manager" => $memberDetails[0]["rdd_manager"],
            "project_name" => $memberDetails[0]["project_name"],
            "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
            "property_name"=> $projectDetails[0]['property_name'],
            'unit_name' => $projectDetails[0]['unit_name'],
            "finance_email" => $finance_email
        ];

        $fdr = FitoutDepositrefund::where('project_id',$request->input('project_id'))->where('isDeleted',0)->first();
        if($fdr==''|| $fdr['generated_path']==null)
        {
            return response()->json(['response'=>"FDR document not yet generated"], 410);
        }
        try
        {
            // $pdf = PDF::loadView('fdrPDF', $data);
            // $pre_date = date('d-m-Y H:i:s');
            // $destination_path = $request->input('drf_doc_path')."/sent/FDR_".$pre_date.".pdf";
            // if(!File::isDirectory($request->input('drf_doc_path')."/sent")){
            //     File::makeDirectory($request->input('drf_doc_path')."/sent", 0777, true, true);
            // }
            // $fileMove = file_put_contents($destination_path, $pdf->output());
            // if($fileMove==false)
            // {
            //     return response()->json(['response'=>"Cannot generate FDR document"], 410);
            // }
            $files = [
                $fdr['generated_path']            
            ];
            if(count($finance_email)==0)
            {
                return response()->json(['response'=>"Finance Manager Not Assigned"], 410);
            }
            Mail::send('emails.sendFdr', $emailData, function($message)use($emailData, $files) {
                $message->to($emailData["finance_email"])
                        ->cc($emailData["rdd_manager"])
                        ->subject($emailData["unit_name"]."-".$emailData["property_name"]."-Fitout refund form");
     
                forEach ($files as $file){
                    $message->attach($file,['as'=>'FDR_'.$emailData['project_name'].".pdf",'mime'=> "application/pdf"]);
                }      
            });
            if(Mail::failures())
            {
                return response()->json(['response'=>"Deposit Refund Mail Not Sent"], 410);
            }
            else
            {
                return response()->json(['response'=>"Deposit Refund Sent"], 200);
            }
            
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }

    }
    public function getProjectMembers($project_id)
    {
        //retrieving project investor and assigned manager
        return Project::leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->where('tbl_projects.project_id',$project_id)->where('tbl_project_contact_details.member_designation',13)->select('users.email as rdd_manager','tbl_project_contact_details.email as investor','tbl_projects.project_name','users.mem_name','users.mem_last_name','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->get();
    }
    public function getProjectDetails($project_id)
    {
        return project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->where('tbl_projects.project_id',$project_id)->where('tbl_project_contact_details.member_designation',13)->leftjoin('tbl_company_master','tbl_company_master.company_id','=','tbl_tenant_master.company_id')->leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->select('tbl_projects.project_id','tbl_projects.project_name','tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name','tbl_properties_master.property_name','tbl_properties_master.property_id','tbl_company_master.company_name','users.mem_name','users.mem_last_name')->get();
    }
    public function getInspectiondata($project_id,$type='')
    {
        $finalInspection = Projectinspections::where('tbl_project_inspections.project_id',$project_id)->where('tbl_project_inspections.report_status',2)->where('tbl_project_inspections.isDeleted',0);
        if($type!='' && $type==2)
        {
            $finalInspection = $finalInspection->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->where('tbl_checklisttemplate_master.id',117);
        }
        $finalInspection = $finalInspection->max('inspection_id');
        $inspections = Projectinspections::join('tbl_project_inspection_items','tbl_project_inspection_items.inspection_id','=','tbl_project_inspections.inspection_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->where('tbl_project_inspections.project_id',$project_id)->where('tbl_project_inspections.report_status',2)->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$finalInspection)->select('tbl_project_inspection_items.checklist_desc','tbl_inspection_root_categories.root_name','tbl_project_inspections.inspection_id','tbl_project_inspection_items.rdd_snags','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.created_at as investor_finished_date')->get()->groupBy('root_name');
        $ins_data='';
        if($finalInspection!=null && $finalInspection!='')
        {
            $ins_data = Projectinspections::where('tbl_project_inspections.project_id',$project_id)->where('tbl_project_inspections.report_status',2)->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$finalInspection)->first();
        }

        $result_data = [
            'inspection_items'=>$inspections,
            'inspection_data'=>$ins_data
        ];

        return $result_data;
    }

    public function checking($project_id)
    {
        $memberDetails = $this->getProjectMembers($project_id);
        $projectDetails = $this->getProjectDetails($project_id);
        $inspectionData = $this->getInspectiondata($project_id,2);
        if(count($inspectionData['inspection_items'])==0)
        {
            return response()->json(['response'=>"No Inspections data for this Project"], 410);
        }
        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
            "inspection_data" => $inspectionData['inspection_items'],
            "company_name" => $projectDetails[0]['company_name'],
            "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
            "inspection_date" => date('d-m-Y',strtotime($inspectionData['inspection_data']['requested_time'])),
            "pre_date" => date('d-m-Y'),
            "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
            'property_name' => $projectDetails[0]['property_name']
        ];
        try
        {
            $pdf = PDF::loadView('fdrPDF', $data);
            return $pdf->stream();
           
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }
}
