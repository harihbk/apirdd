<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Projecttemplate;
use App\Models\Projectcontact;
use App\Models\Projectdocs;
use App\Models\Forwardtask;
use App\Models\Templatemaster;
use App\Models\Projectmilestonedates;
use App\Models\Projectinvestordates;
use App\Models\Properties;
use App\Models\Templatedocs;
use App\Models\Projectworkpermit;
use App\Models\Projectinspections;
use App\Models\Projecttasksapproval;
use App\Models\Projectattendeeapproval;
use App\Models\Templatedesignations;
use App\Models\Projectmembers;
use App\Models\Designation;
use App\Models\SiteInspectionReport;
use App\Models\FitoutCompletionCertificates;
use App\Models\Preopeningdocs;
use App\Models\FitoutDepositrefund;
use App\Models\Inspectionreports;
use App\Models\Members;
use App\Models\Docpathconfig;
use App\Models\Tenant;
use App\Models\Organisations;
use App\Models\Projectdocshistory;
use App\Models\ProjectdocsApproval;
use App\Models\Checklisttemplate;
use App\Models\Inspectionchecklistmaster;
use App\Models\Projectinspectionitems;
use App\Models\Notifications;
use App\Models\Emailtemplate;
use Response;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\Projectkickoffdocs;
use App\Mail\Projectmeeting;
use App\Mail\Meetingrejection;
use App\Mail\Meetingmom;
use App\Mail\Meetingreminder;
use App\Mail\Projectnotification;
use DB;
use File;

class ProjectController extends Controller
{
    function retrieveByorg($id)
    {
        $limit = 1;
        $offset = 1;
        $projects = Project::where("org_id",$id)->get();
        echo json_encode($projects); 
    }
    function sendMail($project_id,$type)
    {
        $contact = Projectcontact::where('project_id',$project_id)->where('isDeleted',0)->get();
        $contact_people = array();
        for($u=0;$u<count($contact);$u++)
        {
            $contact_people[] = $contact[$u]['email'];
        }
        if($type==1)
        {
            $project_details = Project::leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->leftjoin('tbl_project_milestone_dates','tbl_project_milestone_dates.project_id','=','tbl_projects.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projects.investor_brand','tbl_properties_master.property_name','tbl_project_milestone_dates.concept_submission','tbl_project_milestone_dates.detailed_design_submission','tbl_project_milestone_dates.unit_handover','tbl_project_milestone_dates.fitout_completion','tbl_project_milestone_dates.store_opening','users.mem_name','users.mem_last_name','users.email','tbl_tenant_master.tenant_id','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->where('tbl_projects.project_id',$project_id)->where('tbl_project_contact_details.member_designation',13)->groupBy('tbl_projects.project_id')->get();

            $data = array();
            $data = [
                "tenant_name" => $project_details[0]['tenant_name'],
                "tenant_last_name" => $project_details[0]['tenant_last_name'],
                "investor_brand" => $project_details[0]['investor_brand'],
                "property_name" => $project_details[0]['property_name'],
                "concept_submission" => date("d-m-Y", strtotime($project_details[0]['concept_submission'])),
                "detailed_design_submission" => date("d-m-Y", strtotime($project_details[0]['detailed_design_submission'])),
                "unit_handover" => date("d-m-Y", strtotime($project_details[0]['unit_handover'])),
                "fitout_completion" => date("d-m-Y", strtotime($project_details[0]['fitout_completion'])),
                "store_opening" => date("d-m-Y", strtotime($project_details[0]['store_opening'])),
                "mem_name" => $project_details[0]['mem_name'],
                "mem_last_name" => $project_details[0]['mem_last_name'],
                "email" => $project_details[0]['email']
            ];
    
            Mail::send('emails.projectcreation', $data, function($message)use($data,$contact_people) {
            $message->to($contact_people)
                    ->subject('RDD - Welcoming email');
            });  
        }
        else
        {
            Mail::to($contact_people)->send(new Projectnotification());
            if(Mail::failures())
            {
                return response()->json(['response'=>"Project notify Mail Not Sent"], 411);
            }
            else
            {
                return response()->json(['response'=>"Project notify Mail Sent"], 200);
            }

        }
    }
    function rddrequestPayment($project_id)
    {
        
        $project_details = Project::leftjoin('tbl_finance_team','tbl_finance_team.org_id','=','tbl_projects.org_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->where('tbl_projects.project_id',$project_id)->select('tbl_projects.investor_brand','tbl_properties_master.property_name','tbl_projects.ivr_status','tbl_projects.ivr_amt','tbl_projects.owner_work','tbl_projects.owner_work_amt','tbl_projects.fitout_deposit_status','tbl_projects.fitout_deposit_amt','users.mem_name','users.mem_last_name','users.email as mem_email','tbl_finance_team.mem_name as finance_name','tbl_finance_team.email as finance_email')->get();

        if(count($project_details)>0)
        {
            $data = array();
            $data = [
                "brand_name" => $project_details[0]['investor_brand'],
                "property" => $project_details[0]['property_name'],
                "ivr_amt" => $project_details[0]['ivr_amt'],
                "owner_work_amt" => $project_details[0]['owner_work_amt'],
                "fitout_deposit_amt" => $project_details[0]['fitout_deposit_amt'],
                "mem_name" => $project_details[0]['mem_name'],
                "mem_last_name" => $project_details[0]['mem_last_name'],
                "mem_email" => $project_details[0]['mem_email'],
                "finance_name" => $project_details[0]['finance_name'],
                "finance_email" => $project_details[0]['finance_email']
            ];

            if($project_details[0]['owner_work']!=1)
            {
                return response()->json(['response'=>"Owner Work amount not Paid"], 410);
            }
            if($project_details[0]['ivr_status']!=1)
            {
                return response()->json(['response'=>"IVR amount not Paid"], 410);
            }
            if($project_details[0]['fitout_deposit_status']!=16)
            {
                return response()->json(['response'=>"Fitout Deposit amount not Paid"], 410);
            }
            else
            {
                Mail::send('emails.projectpaymentrequest', $data, function($message)use($data) {
                    $message->to($data['finance_email'])
                            ->cc($data['mem_email'])
                            ->subject('RDD - Payment Request');
                    }); 
                    if(Mail::failures())
                    {
                        return response()->json(['response'=>"Request Payment Not Sent"], 410);
                    }
                    else
                    {
                        return response()->json(['response'=>"Request payment Mail Sent"], 200);
                    }
            }
        }  
    }
    function store(Request $request)
    {
        $project = new Project();
        $template = new Projecttemplate();

        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');

        $projectdata = $request->get('project');
        //check whether the template is available
        $templatedetails = Templatemaster::where('template_id',$projectdata[0]['template_id'])->where('isDeleted',0)->get();

        if(count($templatedetails)==0)
        {
            return response()->json(['response'=>'Template Not Exists'], 420);  
        }
        // $prCheck = Project::where('project_name', $projectdata[0]['project_name'])->first();
        // if($prCheck!=null)
        // {
        //     return response()->json(['response'=>"Project name already exists"], 410); 
        // }
        // return response()->json(['response'=>"checking"], 410);
        // exit;
        $project->org_id = $projectdata[0]['org_id'];
        $project->project_name = $projectdata[0]['project_name'];
        $project->project_type = $projectdata[0]['project_type'];
        $project->property_id = $projectdata[0]['property_id'];
        $project->unit_id = $projectdata[0]['unit_id'];
        $project->usage_permissions = $projectdata[0]['usage_permissions'];
        $project->fitout_period = $projectdata[0]['fitout_period'];
        $project->fitout_deposit_status = $projectdata[0]['fitout_deposit_status'];
        $project->fitout_deposit_amt = $projectdata[0]['fitout_deposit_amt'];
        // $project->fitout_currency_type = $projectdata[0]['fitout_currency_type'];
        // $project->insurance_validity_date = $projectdata[0]['insurance_validity_date'];
        $project->fif_upload_path = $projectdata[0]['fif_upload_path'];
        $project->assigned_rdd_members = $projectdata[0]['assigned_rdd_members'];
        $project->investor_company = $projectdata[0]['investor_company'];
        $project->investor_brand = $projectdata[0]['investor_brand'];
        $project->created_at = date('Y-m-d H:i:s');
        $project->updated_at = date('Y-m-d H:i:s');
        $project->created_by = $projectdata[0]['user_id'];


        $validator2 = Validator::make($request->all(), [ 
            'project.*.org_id' => 'required', 
            'project.*.project_name' => 'required',
            'project.*.doc_path' => 'required',
            'project.*.image_path' => 'required', 
            'project.*.project_type' => 'required',
            'project.*.template_id' => 'required',
            'project.*.property_id' => 'required',
            'project.*.unit_id' => 'required',
            'project.*.fitout_period' => 'required',
            'project.*.fitout_deposit_status' => 'required',
            'project.*.fitout_deposit_amt' => 'required',
            'project.*.fif_upload_path' => 'required',
            'project.*.assigned_rdd_members' => 'required',
            'project.*.investor_company' => 'required',
            'project.*.user_id' => 'required',
        ]);

        if ($validator2->fails()) { 
            return response()->json(['project'=>$validator2->errors()], 401);            
        }

        if($project->save())
        {
            $returnData = Project::select('project_id','project_name','created_at')->find($project->project_id);
            $project_id = $returnData['project_id']; 
        }

        //project milestone dates
        $milestone_dates = $request->get('milestone_dates');

        for($i=0;$i<count($milestone_dates);$i++) 
        {
            $milestoneData[] = [
                'org_id' => $projectdata[0]['org_id'],
                'project_id' => $project_id,
                'concept_submission' => $milestone_dates[$i]['concept_submission'],
                'detailed_design_submission' => $milestone_dates[$i]['detailed_design_submission'],
                'unit_handover' => $milestone_dates[$i]['unit_handover'],
                'fitout_start' => $milestone_dates[$i]['fitout_start'],
                'fitout_completion' => $milestone_dates[$i]['fitout_completion'],
                'store_opening' => $milestone_dates[$i]['store_opening'],
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                'created_by' => $projectdata[0]['user_id']
             ];
        }

        $validator1 = Validator::make($request->all(), [  
            'milestone_dates.*.concept_submission' => 'required',
            'milestone_dates.*.detailed_design_submission' => 'required',
            'milestone_dates.*.unit_handover' => 'required',
            'milestone_dates.*.fitout_start' => 'required',
            'milestone_dates.*.fitout_completion' => 'required',
            'milestone_dates.*.store_opening' => 'required',
        ]);


        if ($validator1->fails()) { 
            return response()->json(['milestone_dates'=>$validator1->errors()], 401);            
        }


        //project investor planned dates
        $investor_dates = $request->get('investor_dates');

        for($j=0;$j<count($investor_dates);$j++) 
        {
            $investorData[] = [
                'project_id' => $project_id,
                'org_id' => $projectdata[0]['org_id'],
                'concept_submission' => $investor_dates[$j]['concept_submission'],
                'detailed_design_submission' => $investor_dates[$j]['detailed_design_submission'],
                'fitout_start' => $investor_dates[$j]['fitout_start'],
                'fitout_completion' => $investor_dates[$j]['fitout_completion'],
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                'created_by' => $projectdata[0]['user_id']
             ];
        }


        $validator3 = Validator::make($request->all(), [ 
            'investor_dates.*.concept_submission' => 'required',
            'investor_dates.*.detailed_design_submission' => 'required',
            'investor_dates.*.fitout_start' => 'required',
            'investor_dates.*.fitout_completion' => 'required'
        ]);


        if ($validator3->fails()) { 
            return response()->json(['milestone_dates'=>$validator3->errors()], 401);            
        }

        //project contact details
        $contact = $request->get('contact_details');
        $mobile_num = null;
        $contact_people = array();
        $contactNotifications = array();

        for($i=0;$i<count($contact);$i++) 
        {
            $contactData[]=[
                'project_id' => $project_id,
                'member_id' => $contact[$i]['member_id'],
                'member_designation' => $contact[$i]['member_designation'],
                'email' => $contact[$i]['email'],
                'mobile_number' => $contact[$i]['mobile_number'],
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                'created_by' => $projectdata[0]['user_id'],
            ];
            $contact_people[] = $contact[$i]['email'];

            $contactNotifications[]=[
                "project_id" => $project_id,
                "content" => "Project ".$projectdata[0]['project_name']." has been created",
                "user" => $contact[$i]['member_id'],
                "user_type" => ($contact[$i]['member_designation']==13?2:$contact[$i]['member_designation']==14)?2:1,
                "notification_type"=>env('NOTIFY_PROJECT_CREATION'),
                "created_at" => $created_at,
                "updated_at" => $updated_at
            ];
        }

        $validator3 = Validator::make($request->all(), [ 
            'contact_details.*.member_id' => 'required',
            'contact_details.*.member_designation' => 'required',
            'contact_details.*.email' => 'required'
        ]);

        if ($validator3->fails()) { 
            return response()->json(['Contact'=>$validator3->errors()], 401);            
        }
        
        //project document mapping from template
        $templatedocsdetails = Templatedocs::where('template_id',$projectdata[0]['template_id'])->where('isDeleted',0)->get();

        for($s=0;$s<count($templatedocsdetails);$s++)
        {
            $templatedocData[] = [
                'project_id' => $project_id,
                'phase_id' => $templatedocsdetails[$s]['phase_id'],
                'doc_header' => $templatedocsdetails[$s]['doc_header'],
                'doc_title' => $templatedocsdetails[$s]['doc_title'],
                'reviewers_designation' => $templatedocsdetails[$s]['reviewers'],
                'approvers_level1_designation'=>$templatedocsdetails[$s]['approvers_level1'],
                'approvers_level2_designation'=>$templatedocsdetails[$s]['approvers_level2'],
                'created_at' => $created_at,
                'updated_at' => $updated_at,
                'created_by'=>$projectdata[0]['user_id']
             ];
        }

        //project members by designation mapping
        $membersData = [];
        $task_members = $request->get('task_members');
        for($n=0;$n<count($contact);$n++) 
        {
            $membersData[] = [
                "org_id" => $projectdata[0]['org_id'],
                "project_id" => $project_id,
                "designation" => $contact[$n]['member_designation'],
                "members" => $contact[$n]['member_id'],
                "members_designation" => $contact[$n]['designation_user'],
                "created_by" => $projectdata[0]['user_id'],
                "created_at" => $created_at,
                "updated_at" => $updated_at                
            ];
        }

        $validator4 = Validator::make($request->all(), [ 
            'task_members.*.members' => 'required',
            'task_members.*.designation' => 'required',
        ]);

        if ($validator4->fails()) { 
            return response()->json(['Members'=>$validator4->errors()], 401);            
        }

        if(Projectcontact::insert($contactData) && Projectmilestonedates::insert($milestoneData)&& Projectinvestordates::insert($investorData) && Projectdocs::insert($templatedocData) && Projectmembers::insert($membersData))
        {
            $this->sendMail($project_id,1);
            //update all doc tasks due date with detailed submission
            Projectdocs::where('project_id',$project_id)->update(
                array(
                    'due_date' => $milestone_dates[0]['detailed_design_submission']
                )
            );
            //make fitout completion certificate entry
            $fcc = new FitoutCompletionCertificates();
            $fcc->project_id = $project_id;
            $fcc->doc_type = "Fitout Completion Certificate";
            $fcc->planned_date = $milestone_dates[0]['fitout_completion'];
            $fcc->created_at = $created_at;
            $fcc->updated_at = $updated_at;

            $certificate_generation = $fcc->save();

            //make fitout deposit refund
            $store_open_date = $milestone_dates[0]['store_opening'];
            $fdeposit_refund = new FitoutDepositrefund();
            $fdeposit_refund->project_id = $project_id;
            $fdeposit_refund->doc_type = 'Deposit Refund Form';
            $fdeposit_refund->planned_date = date('Y-m-d', strtotime($store_open_date. ' + 30 days'));
            $fdeposit_refund->created_at = $created_at;
            $fdeposit_refund->updated_at = $updated_at;

            $deposit_refund = $fdeposit_refund->save();

            //map the template to project for tracking tasks
            for($r=0;$r<count($templatedetails);$r++)
            {
                $planned_date = date('Y-m-d h:i:s', strtotime('+'.$templatedetails[$r]['duration'].' days'));
                $templateData[] = [
                            'project_id' => $project_id,
                            'org_id' => $projectdata[0]['org_id'],
                            'template_id' => $projectdata[0]['template_id'],
                            'template_master_id' => $templatedetails[$r]['master_id'],
                            'task_type' => $templatedetails[$r]['task_type'],
                            'activity_desc' => $templatedetails[$r]['activity_desc'],
                            'approvers_designation' => $templatedetails[$r]['approvers'],
                            'attendees_designation' => $templatedetails[$r]['attendees'],
                            'phase_id' => $templatedetails[$r]['phase_id'],
                            'mem_responsible_designation' => $templatedetails[$r]['person'],
                            'fre_id' => $templatedetails[$r]['fre_id'],
                            'seq_status' => $templatedetails[$r]['seq_status'],
                            'seq_no' => $templatedetails[$r]['seq_no'],
                            'duration' => $templatedetails[$r]['duration'],
                            'planned_date' => $planned_date,
                            'fif_upload_path' => $templatedetails[$r]['fif_upload_path'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            'created_by' => $projectdata[0]['user_id']
                         ];
            }

         if(Projecttemplate::insert($templateData))
         {
             //mail functionality,project directory logic here
            /* Directory Creation */
            $doc_path = public_path()."".$projectdata[0]['doc_path']."/".$project_id."_".$projectdata[0]['project_name'];;
             $img_path = public_path()."".$projectdata[0]['image_path']."/".$project_id."_".$projectdata[0]['project_name'];;
              if(!File::isDirectory($doc_path)){
                File::makeDirectory($doc_path, 0777, true, true);
                }
                if(!File::isDirectory($img_path)){
                    File::makeDirectory($img_path, 0777, true, true);
                }
            Notifications::insert($contactNotifications);
            $returnData = Project::select('project_id','project_name','created_at')->find($project->project_id);
            $data = array ("message" => 'Project Created successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);   
         }
        }
    }
    function update(Request $request)
    {
        $project = new Project();
        $template = new Projecttemplate();

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $projectdata = $request->get('project');
        $data=[];
        
        // $prCheck = Project::where('project_name', $projectdata[0]['project_name'] )->first();
        // if(!$prCheck == null)
        // {
        //     return response()->json(['project'=>"Project name already exists"], 401); 
        // }
        $projects = Project::where("project_id",$projectdata[0]['project_id'])->update( 
            array( 
             "usage_permissions" => $projectdata[0]['usage_permissions'],
             "leasing_representative" => $projectdata[0]['leasing_representative'],
             "leasing_comments" => $projectdata[0]['leasing_comments'],
             "fitout_period" => $projectdata[0]['fitout_period'],
             "fitout_deposit_status" => $projectdata[0]['fitout_deposit_status'],
             "fitout_currency_type" => $projectdata[0]['fitout_currency_type'],
             "insurance_validity_date" => $projectdata[0]['insurance_validity_date'],
             "assigned_rdd_members" => $projectdata[0]['assigned_rdd_members'],
             "investor_company" => $projectdata[0]['investor_company'],
             "investor_brand" => $projectdata[0]['investor_brand'],
             "updated_at" => $updated_at
             ));

             $datas = $request->get('template');

             for($i=0;$i<count($datas);$i++) 
             {
                for($k=0;$k<count($datas[$i]['tasks']);$k++)
                {
                    if($datas[$i]['tasks'][$k]['template_master_id']!=0)
                    {
                        //Update Logic
                        $template = Projecttemplate::where("id", $datas[$i]['tasks'][$k]['id'])->update( 
                            array( 
                             "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                             "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                             "phase_name" => $datas[$i]['phase_name'],
                             "phase_date" => $datas[$i]['phase_date'],
                             "mem_responsible" => $datas[$i]['tasks'][$k]['mem_responsible'],
                             "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                             "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                             "duration" => $datas[$i]['tasks'][$k]['duration'],
                             "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                             "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                             "seq_char" => $datas[$i]['tasks'][$k]['seq_char'],
                             "start_date" => $datas[$i]['tasks'][$k]['start_date'],
                             "end_date" => $datas[$i]['tasks'][$k]['end_date'],
                             "created_by" => $datas[$i]['user_id'],
                             "updated_at" => $updated_at,
                             ));
                    }
                    else
                    {
                        //insert template activities
                        $data[] = [
                            'project_id' => $datas[$i]['project_id'],
                            'template_id' => $datas[$i]['template_id'],
                            'template_master_id' => $datas[$i]['tasks'][$k]['template_master_id'],
                            'task_id' => $datas[$i]['tasks'][$k]['task_id'],
                            'task_type' => $datas[$i]['tasks'][$k]['task_type'],
                            "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                            "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                            'phase_name' => $datas[$i]['phase_name'],
                            'phase_date' => $datas[$i]['phase_date'],
                            'mem_responsible' => $datas[$i]['tasks'][$k]['mem_responsible'],
                            "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                            "duration" => $datas[$i]['tasks'][$k]['duration'],
                            "priority" => $datas[$i]['tasks'][$k]['priority'],
                            "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                            "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                            "seq_char" => $datas[$i]['tasks'][$k]['seq_char'],
                            "start_date" => $datas[$i]['tasks'][$k]['start_date'],
                            "end_date" => $datas[$i]['tasks'][$k]['end_date'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            'created_by' => $datas[$i]['user_id'],
                            'isProjecttask' => $datas[$i]['tasks'][$k]['isprojectTask'],
                         ];
                    }
                }
             }

             $validator1 = Validator::make($request->all(), [ 
                'template.*.project_id' => 'required', 
                'template.*.template_id' => 'required', 
                'template.*.phase_name' => 'required',
                'template.*.phase_date' => 'required',
                'template.*.user_id' => 'required',
                'template.*.tasks.*.template_master_id' => 'required',
                'template.*.tasks.*.task_id' => 'required',
                'template.*.tasks.*.task_type' => 'required',
                'template.*.tasks.*.approvers' => 'required',
                'template.*.tasks.*.mem_responsible' => 'required',
                'template.*.tasks.*.duration' => 'required',
                'template.*.tasks.*.fre_id' => 'required',
                'template.*.tasks.*.start_date' => 'required',
                'template.*.tasks.*.end_date' => 'required',
                'template.*.tasks.*.duration' => 'required',
            ]);


             $validator2 = Validator::make($request->all(), [ 
                'project.*.project_id' => 'required', 
                'project.*.project_name' => 'required', 
                'project.*.project_type' => 'required',
                'project.*.property_id' => 'required',
                'project.*.unit_id' => 'required',
                'project.*.leasing_representative' => 'required',
                'project.*.fitout_period' => 'required',
                'project.*.fitout_deposit_status' => 'required',
                'project.*.fitout_deposit_amt' => 'required',
                'project.*.fitout_currency_type' => 'required',
                'project.*.insurance_validity_date' => 'required',
                'project.*.assigned_rdd_members' => 'required',
                'project.*.investor_company' => 'required',
                'project.*.investor_brand' => 'required',
                'project.*.user_id' => 'required',
            ]);
    
    
            if ($validator1->fails() || $validator2->fails()) { 
                return response()->json(['project'=>$validator2->errors(),'Template'=>$validator1->errors()], 401);            
            }

            //edit project contact details
            $contact = $request->get('contact_details');
           

            for($i=0;$i<count($contact);$i++) 
            {
                $template = Projectcontact::where("id", $contact[$i]['id'])->update( 
                    array( 
                    'member_id' => $contact[$i]['member_id'],
                    'member_designation' => $contact[$i]['member_designation'],
                    'email' => $contact[$i]['email'],
                    'mobile_number' => $contact[$i]['mobile_number'],
                    "updated_at" => $updated_at,
                    ));

            }
    
            $validator3 = Validator::make($request->all(), [ 
                'contact_details.*.member_id' => 'required',
                'contact_details.*.member_designation' => 'required',
                'contact_details.*.email' => 'required',
                'contact_details.*.mobile_number' => 'required',
                'contact_details.*.user_id' => 'required'
            ]);
    
            if ($validator3->fails()) { 
                return response()->json(['Contact'=>$validator3->errors()], 401);            
            }


            if(Projecttemplate::insert($data) || $template>0)
            {
            $returnData = Project::select('project_id','project_name','created_at')->find($project->project_id);
            $data = array ("message" => 'Project data Edited successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
           }

    }
    function getMemberProjects(Request $request)
    {
        $limit = 10;
        $offset = 0;

        $user = $request->input('user_id');

        $projects = Project::join('tbl_units_master','tbl_projects.unit_id','=','tbl_units_master.unit_id')->join('tbl_company_master','tbl_projects.investor_company','=','tbl_company_master.company_id')->join('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->join('tbl_project_template','tbl_project_template.project_id','=','tbl_projects.project_id')->select('tbl_projects.*','tbl_units_master.unit_name','tbl_company_master.company_name','tbl_company_master.brand_name');

        if ($request->input('project_name')!=null)
        {
            $projects->whereLike(['tbl_projects.project_name'], $request->input('project_name'));
        }
        if ($request->input('unit')!=null)
        {
            $projects->whereLike(['tbl_units_master.unit_name'], $request->input('unit'));
        }


        $projects = $projects->where('tbl_projects.org_id',$request->input('org_id'))->where('tbl_projects.property_id',$request->input('property'))->where(function($query) use ($user){
            $query->orwhereRaw("find_in_set($user,assigned_rdd_members)")
                  ->orWhereRaw("find_in_set($user,tbl_project_contact_details.member_id)")
                //   ->orWhereRaw("find_in_set($user,tbl_project_template.approvers)")
                //   ->orWhereRaw("find_in_set($user,tbl_project_template.mem_responsible)")
                  ->orWhereRaw("find_in_set($user,tbl_projects.created_by)");
           })->groupBy('tbl_projects.project_id')->get();
        return $projects;
    }

    function getProjectstatus(Request $request,$pid)
    {

        $startup = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',1)->get();

        $design = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',2)->get();

        /* Design Phase */
        $design_docs = Projectdocs::where("project_id",$pid)->where("phase_id",2)->select(Projectdocs::raw('count(*) as total_tasks'),Projectdocs::raw('count(IF(doc_status = 0, 1, NULL)) as pending_tasks'),Projectdocs::raw('count(IF(doc_status NOT IN (0,8), 1, NULL)) as inprogress_tasks'),Projectdocs::raw('count(IF(doc_status = 8, 1, NULL)) as Completed_tasks'))->get();

        $design[0]['total_tasks'] = $design[0]['total_tasks']+$design_docs[0]['total_tasks'];
        $design[0]['pending_tasks'] = $design[0]['pending_tasks']+$design_docs[0]['pending_tasks'];
        $design[0]['inprogress_tasks'] = $design[0]['inprogress_tasks']+$design_docs[0]['inprogress_tasks'];
        $design[0]['Completed_tasks'] = $design[0]['Completed_tasks']+$design_docs[0]['Completed_tasks'];

         /* Fitout Phase */
        $fitout = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',3)->get();

        $work_permits = Projectworkpermit::where("project_id",$pid)->select(Projectworkpermit::raw('count(*) as total_tasks'),Projectworkpermit::raw('count(IF(request_status = 0, 1, NULL)) as pending_tasks'),Projectworkpermit::raw('count(IF(request_status IN (2), 1, NULL)) as inprogress_tasks'),Projectworkpermit::raw('count(IF(request_status = 1, 1, NULL)) as Completed_tasks'))->get();
        
        $project_inspections = Projectinspections::where("project_id",$pid)->select(Projectinspections::raw('count(*) as total_tasks'),Projectinspections::raw('count(IF(report_status = 0, 1, NULL)) as pending_tasks'),Projectinspections::raw('count(IF(report_status IN (1,3), 1, NULL)) as inprogress_tasks'),Projectworkpermit::raw('count(IF(report_status = 2, 1, NULL)) as Completed_tasks'))->get();
        
        $fitout[0]['total_tasks'] = $fitout[0]['total_tasks']+intval(($work_permits[0]['total_tasks']==0)?1:$work_permits[0]['total_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?1:$project_inspections[0]['total_tasks']);
        $fitout[0]['pending_tasks'] = $fitout[0]['pending_tasks']+intval(($work_permits[0]['total_tasks']==0)?1:$work_permits[0]['pending_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?1:$project_inspections[0]['pending_tasks']);
        $fitout[0]['inprogress_tasks'] = $fitout[0]['inprogress_tasks']+intval(($work_permits[0]['total_tasks']==0)?0:$work_permits[0]['inprogress_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?0:$project_inspections[0]['inprogress_tasks']);
        $fitout[0]['Completed_tasks'] = $fitout[0]['Completed_tasks']+intval(($work_permits[0]['total_tasks']==0)?0:$work_permits[0]['Completed_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?0:$project_inspections[0]['Completed_tasks']);
        
        /*Completion Phase */
        $completion = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',4)->get();

        $fitout_completion = FitoutCompletionCertificates::where('project_id',$pid)->select(FitoutCompletionCertificates::raw('count(*) as total_tasks'),FitoutCompletionCertificates::raw('count(IF(isGenerated = 0, 1, NULL)) as pending_tasks'),FitoutCompletionCertificates::raw('count(IF(isGenerated = 1, 1, NULL)) as Completed_tasks'))->get();

        $pre_opening_completion = Preopeningdocs::where('project_id',$pid)->select(Preopeningdocs::raw('count(*) as total_tasks'),Preopeningdocs::raw('count(IF(doc_status = 0, 1, NULL)) as pending_tasks'),Preopeningdocs::raw('count(IF(doc_status IN (1,3), 1, NULL)) as inprogress_tasks'),Preopeningdocs::raw('count(IF(doc_status = 2, 1, NULL)) as Completed_tasks'))->get();

        $fitout_deposit_refund = FitoutDepositrefund::where('project_id',$pid)->select(FitoutDepositrefund::raw('count(*) as total_tasks'),FitoutDepositrefund::raw('count(IF(isdrfGenerated = 0, 1, NULL)) as pending_tasks'),Preopeningdocs::raw('count(IF(isdrfGenerated = 1, 1, NULL)) as Completed_tasks'))->get();

        $completion[0]['total_tasks'] = $completion[0]['total_tasks']+$fitout_completion[0]['total_tasks']+$pre_opening_completion[0]['total_tasks']+$fitout_deposit_refund[0]['total_tasks'];
        $completion[0]['pending_tasks'] = $completion[0]['pending_tasks']+$fitout_completion[0]['pending_tasks']+$pre_opening_completion[0]['pending_tasks']+$fitout_deposit_refund[0]['pending_tasks'];
        $completion[0]['inprogress_tasks'] = $completion[0]['inprogress_tasks']+$pre_opening_completion[0]['inprogress_tasks'];
        $completion[0]['Completed_tasks'] = $completion[0]['Completed_tasks']+$fitout_completion[0]['Completed_tasks']+$pre_opening_completion[0]['Completed_tasks']+$fitout_deposit_refund[0]['Completed_tasks'];
        
        return response()->json(array("response"=> ['startup_phase'=>$startup,'design_phase'=>$design,'fitout_phase' => $fitout,'completion_phase'=>$completion]), 200);
    }
    function rddscheduleMeeting(Request $request,$project_id)
    {
        $validator = Validator::make($request->all(), [ 
            'id' => 'required',
            'phase_id' => 'required',
            'task_type' => 'required',
            'approvers' => 'required',
            'attendees' => 'required',
            'meeting_topic' => 'required',
            'meeting_date' => 'required',
            'meeting_start_time' => 'required',
            'meeting_end_time' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }
        /* map the approvers approval status  */
        $task_status = 0;
        $project_meeting_schedule_status=2;
        $project_meeting_completed_status=1;
        $project_meeting_approvers_approved_status=3;
        $project_meeting_attendees_approved_status=4;
        $updated_at = date('Y-m-d H:i:s');
        $created_at = date('Y-m-d H:i:s');
        $memid = $request->input('user_id');

        //check if this user is responsible person for this task
        $userCheck = Projecttemplate::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('id',$request->input('id'))->whereRaw("find_in_set($memid,tbl_project_template.mem_responsible)")->get();
        if(count($userCheck)==0)
        {
            return response()->json(['response'=>"Meeting Can be scheduled by responsible Persons only"], 410);
        }
        else
        {
            $taskCheck = Projecttemplate::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('id',$request->input('id'))->whereRaw("find_in_set($memid,tbl_project_template.mem_responsible)")->whereNotIn('task_status', [$project_meeting_schedule_status,$project_meeting_completed_status,$project_meeting_approvers_approved_status,$project_meeting_attendees_approved_status])->get();
            if(count($taskCheck)==0)
            {
                return response()->json(['response'=>"Meeting may be scheduled or in approval stage,cannot be scheduled"], 411);
            }
        }
        $approvers_list = explode(',',$request->input('approvers'));
        $attendees_list = explode(',',$request->input('attendees'));
        $approvers_array = [];
        $approverNotifications = array();
        $investorattendees = array();

        $taskDetails = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.phase_id',$request->input('phase_id'))->where('tbl_project_template.id',$request->input('id'))->select('tbl_project_template.*','tbl_projects.project_name')->get();


       for($k=0;$k<count($approvers_list);$k++)
       {
           $approversData[] = [
               'project_id' => $project_id,
               'phase_id' => $request->input('phase_id'),
               'task_id' => $request->input('id'),
               'task_type' => $request->input('task_type'),
               'approver' => $approvers_list[$k]
           ];
           $approvers = Members::where('mem_id',$approvers_list[$k])->first();
           $approvers_array[]= $approvers->email;

           $approverNotifications[]=[
            "project_id" => $project_id,
            "content" =>  $request->input('meeting_topic')." for Project ".$taskDetails[0]['project_name']." has been created.Kindly make Approval action",
            "user" => $approvers_list[$k],
            "user_type" => 1,
            "notification_type"=>env('NOTIFY_MEETING'),
            "created_at" => $created_at,
            "updated_at" => $updated_at
        ];

       }
       for($j=0;$j<count($attendees_list);$j++)
       {
            $attendeesData[] = [
                'project_id' => $project_id,
                'phase_id' => $request->input('phase_id'),
                'task_id' => $request->input('id'),
                'task_type' => $request->input('task_type'),
                'attendee' => $attendees_list[$j]
            ]; 
       }
       //update old approver entries for this task
       Projecttasksapproval::where('project_id',$project_id)->where('task_id',$request->input('id'))->where('phase_id',$request->input('phase_id'))->update(array("isDeleted"=>1));
       Projectattendeeapproval::where('project_id',$project_id)->where('task_id',$request->input('id'))->where('phase_id',$request->input('phase_id'))->update(array("isDeleted"=>1));
        if(Projecttasksapproval::insert($approversData) && Projectattendeeapproval::insert($attendeesData))
        {
           //Mail to approvers about meeting notification
           $tasks = Projecttemplate::where("project_id",$project_id)->where("id",$request->input('id'))->where("task_type",$request->input('task_type'))->update(
            array(
                "task_status"=>$task_status,
                "meeting_topic"=>$request->input('meeting_topic'),
                "meeting_date"=>$request->input('meeting_date'),
                "meeting_start_time"=>$request->input('meeting_start_time'),
                "meeting_end_time"=>$request->input('meeting_end_time'),
                "task_status" => $project_meeting_schedule_status,
                "updated_at" => $updated_at
                )
            );
            Mail::to($approvers_array)->send(new projectmeeting());
            if($tasks>0)
            {
                Notifications::insert($approverNotifications);
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Schdeuled successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response);
            }
            else
            {
                return response()->json(['response'=>"Meeting cannot be scheduled"], 421);
            }
        }
        
    }
    function rddmeetingApprovalaction(Request $request,$project_id)
    {
        $validator = Validator::make($request->all(), [ 
            'id' => 'required',
            'phase_id' => 'required',
            'approver'=> 'required',
            'attendees'=> 'required',
            'responsible_person'=> 'required',
            'approval_status' => 'required'
        ]);
        $yet_to_approve=0;
        $inprogress_task=0;
        $rejection_status=2;
        $project_task_rejection_status=5;
        $project_task_approval_status=3;
        $task_rescheduled_status=2;
        $updated_at = date('Y-m-d H:i:s');
        $created_at = date('Y-m-d H:i:s');
        $attendees_list = explode(',',$request->input('attendees'));
        $persons_list = explode(',',$request->input('responsible_person'));
        $attendees_array = [];
        $persons_array = [];
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }
        $memid = $request->input('approver');
        $taskDetails = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.phase_id',$request->input('phase_id'))->where('tbl_project_template.id',$request->input('id'))->select('tbl_project_template.*','tbl_projects.project_name')->get();

        //check if user belongs to approver of this task
        $userCheck = Projecttemplate::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('id',$request->input('id'))->whereRaw("find_in_set($memid,tbl_project_template.approvers)")->get();
        if(count($userCheck)==0)
        {
            return response()->json(['response'=>"Meeting Can be Approved by Approvers Persons only"], 410);
        }
        //if approved check for others approval and schedule meeting
        if($request->input('approval_status')==1)
        {
            Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("approver",$request->input('approver'))->where("isDeleted",0)->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );

            $approvalprogress = Projecttasksapproval::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('task_id',$request->input('id'))->where('approval_status',$yet_to_approve)->where('task_status',$inprogress_task)->where("isDeleted",0)->count();

            if($approvalprogress>0)
            {
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Approved');
                $response = Response::json($data,200);
                echo json_encode($response);
            }
            else
            {
                $attendeeNotifications = array();
                /* Mail to attendees about meeting notifications */
                for($j=0;$j<count($attendees_list);$j++)
                {
                    $user_type=1;
                    $mem_id=0;
                    $exp  = explode("-",$attendees_list[$j]);
                    $attendees = Members::where('mem_id',explode("-",$exp[0]))->where('mem_name',explode("-",$exp[1]))->first();
                    $mem_id = $attendees!=null?$attendees->mem_id:0;
                    if($attendees==null)
                    {
                        $attendees = Tenant::where('tenant_id',explode("-",$exp[0]))->where('tenant_name',explode("-",$exp[1]))->first();
                        $mem_id = $attendees->tenant_id;
                        $user_type=2;
                    }    
                    $attendees_array[]= $attendees->email;  
                    $attendeeNotifications[]=[
                        "project_id" => $project_id,
                        "content" =>  $taskDetails[0]['meeting_topic']." for Project ".$taskDetails[0]['project_name']." has been created.Kindly make Approval action",
                        "user" => $mem_id,
                        "user_type" => $user_type,
                        "notification_type"=>env('NOTIFY_MEETING'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                Notifications::insert($attendeeNotifications);
                Mail::to($attendees_array)->send(new projectmeeting());
                Projecttemplate::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("id",$request->input('id'))->update(
                 array(
                        "task_status"=>$project_task_approval_status,
                        "updated_at"=>$updated_at
                 ));
                    
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Approved');
                $response = Response::json($data,200);
                echo json_encode($response);
            }
        }
        else
        {
            $reject = Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("approver",$request->input('approver'))->where("isDeleted",0)->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );

            if($reject>0)
            {
                $responsibleNotifications = array();
                /* Mail to responsible person about meeting cancelled notifications */
                for($k=0;$k<count($persons_list);$k++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$k])->first();
                    $persons_array[]= $responsible_person->email;  
                    $responsibleNotifications[]=[
                        "project_id" => $project_id,
                        "content" =>  $taskDetails[0]['meeting_topic']." for Project ".$taskDetails[0]['project_name']." has been rejected by Approvers.",
                        "user" => $responsible_person->mem_id,
                        "user_type" => 1,
                        "notification_type"=>env('NOTIFY_MEETING'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                Notifications::insert($responsibleNotifications);
                Mail::to($persons_array)->send(new Meetingrejection());
                Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->update(
                    array(
                        "task_status" => $task_rescheduled_status,
                        "updated_at"=>$updated_at
                    )
                  );

                  Projecttemplate::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("id",$request->input('id'))->update(
                      array(
                          "task_status"=>$project_task_rejection_status,
                          "updated_at"=>$updated_at
                      ));
                      $returnData = Projecttemplate::find($request->input('id'));
                      $data = array ("message" => 'Meeting has been rejected');
                      $response = Response::json($data,200);
                      echo json_encode($response);
            }
        }
    }
    function investormeetingAction(Request $request,$project_id)
    {
        $validator = Validator::make($request->all(), [ 
            'id' => 'required',
            'phase_id' => 'required',
            'attendee'=> 'required',
            'attendee_name'=> 'required',
            'responsible_person' => 'required',
            'approval_status' => 'required'
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }
        $updated_at = date('Y-m-d H:i:s');
        $created_at = date('Y-m-d H:i:s');
        $yet_to_approve=0;
        $inprogress_task=0;
        $project_task_approval_status=4;
        $rejection_status=2;
        $attendee_task_rejection_status=2;
        $approvers_task_rejection_status=4;
        $project_task_rejection_status=6;
        $memid = $request->input('attendee');
        $persons_array = [];
        $persons_list = explode(',',$request->input('responsible_person'));
        $taskdata = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.phase_id',$request->input('phase_id'))->where('tbl_project_template.id',$request->input('id'))->select('tbl_project_template.*','tbl_projects.project_name')->first();
        $a = explode(',',$taskdata['attendees']);
        for($z=0;$z<count($a);$z++)
        {
            $id = explode('-',$a[$z]);
            $ids[] = trim($id[0]);
        }
        if(!in_array($memid,$ids))
        {
            return response()->json(['response'=>"Meeting Can be confirmed by Attendees only"], 410);
        }
        $attendee = $request->input('attendee')."-".$request->input('attendee_name');
        //if approved check for others approval and schedule meeting
        if($request->input('approval_status')==1)
        {
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$attendee)->where("isDeleted",0)->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );
            $approvalprogress = Projectattendeeapproval::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('task_id',$request->input('id'))->where('approval_status',$yet_to_approve)->where('task_status',$inprogress_task)->where("isDeleted",0)->count();

            if($approvalprogress>0)
            {
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Approved');
                $response = Response::json($data,200);
                echo json_encode($response);
            }
            else
            {
                $approverNotifications = array();
                /* Mail to responsible person about meeting Confirmed notifications */
                for($k=0;$k<count($persons_list);$k++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$k])->first();
                    $persons_array[]= $responsible_person->email; 
                    
                    $approverNotifications[]=[
                        "project_id" => $project_id,
                        "content" =>  $taskdata['meeting_topic']." for Project ".$taskdata['project_name']." has been Approved",
                        "user" => $persons_list[$k],
                        "user_type" => 1,
                        "notification_type"=>env('NOTIFY_MEETING'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                Notifications::insert($approverNotifications);
                Mail::to($persons_array)->send(new Projectmeeting());
                Projecttemplate::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("id",$request->input('id'))->update(
                    array(
                           "task_status"=>$project_task_approval_status,
                           "updated_at"=>$updated_at
                    ));
                       
                   $returnData = Projecttemplate::find($request->input('id'));
                   $data = array ("message" => 'Meeting has been Approved');
                   $response = Response::json($data,200);
                   echo json_encode($response);
            }
        }
        else
        {
            $reject = Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$attendee)->where("isDeleted",0)->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );
            //attendee approvals update --attendee table
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->where("isDeleted",0)->update(
            array(
                "task_status" => $attendee_task_rejection_status,
                "updated_at"=>$updated_at
            ));
            //attendee approvals update --approver table
            Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->where("isDeleted",0)->update(
                array(
                    "task_status" => $approvers_task_rejection_status,
                    "updated_at"=>$updated_at
                ));
            //attendee approvals update --project template table
            Projecttemplate::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("id",$request->input('id'))->update(
                array(
                    "task_status"=>$project_task_rejection_status,
                    "updated_at"=>$updated_at
                ));

                $approverNotifications = array();
                /* Mail to responsible person about meeting Confirmed notifications */
                for($l=0;$l<count($persons_list);$l++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$l])->first();
                    $persons_array[]= $responsible_person->email;  
                    $approverNotifications[]=[
                        "project_id" => $project_id,
                        "content" =>  $taskdata['meeting_topic']." for Project ".$taskdata['project_name']." has been Approved",
                        "user" => $persons_list[$l],
                        "user_type" => 1,
                        "notification_type"=>env('NOTIFY_MEETING'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                Notifications::insert($approverNotifications);
                Mail::to($persons_array)->send(new Meetingrejection());

                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been rejected');
                $response = Response::json($data,200);
                echo json_encode($response);
        }
    }
    function rddattendeeMeetingsaction(Request $request,$project_id)
    {
        $validator = Validator::make($request->all(), [ 
            'id' => 'required',
            'phase_id' => 'required',
            'attendee'=> 'required',
            'attendee_name'=> 'required',
            'responsible_person' => 'required',
            'approval_status' => 'required'
        ]);
        
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }
        $updated_at = date('Y-m-d H:i:s');
        $created_at = date('Y-m-d H:i:s');
        $yet_to_approve=0;
        $inprogress_task=0;
        $project_task_approval_status=4;
        $rejection_status=2;
        $attendee_task_rejection_status=2;
        $approvers_task_rejection_status=4;
        $project_task_rejection_status=6;
        $memid = $request->input('attendee');
        $attendee = $request->input('attendee')."-".$request->input('attendee_name');
        $persons_array = [];
        $persons_list = explode(',',$request->input('responsible_person'));
        $taskdata = Projecttemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.phase_id',$request->input('phase_id'))->where('tbl_project_template.id',$request->input('id'))->select('tbl_project_template.*','tbl_projects.project_name')->first();
        $a = explode(',',$taskdata['attendees']);
        for($z=0;$z<count($a);$z++)
        {
            $id = explode('-',$a[$z]);
            $ids[] = trim($id[0]);
        }
        if(!in_array($memid,$ids))
        {
            return response()->json(['response'=>"Meeting Can be confirmed by Attendees only"], 410);
        }
        //if approved check for others approval and schedule meeting
        if($request->input('approval_status')==1)
        {
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$attendee)->where("isDeleted",0)->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );

            $approvalprogress = Projectattendeeapproval::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('task_id',$request->input('id'))->where('approval_status',$yet_to_approve)->where('task_status',$inprogress_task)->where("isDeleted",0)->count();

            if($approvalprogress>0)
            {
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Approved');
                $response = Response::json($data,200);
                echo json_encode($response);
            }
            else
            {
                $responsibleNotifications = array();
                /* Mail to responsible person about meeting Confirmed notifications */
                for($k=0;$k<count($persons_list);$k++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$k])->first();
                    $persons_array[]= $responsible_person->email;  

                    $responsibleNotifications[]=[
                        "project_id" => $project_id,
                        "content" =>  $taskdata['meeting_topic']." for Project ".$taskdata['project_name']." has been Approved",
                        "user" => $persons_list[$k],
                        "user_type" => 1,
                        "notification_type"=>env('NOTIFY_MEETING'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];

                }
                Notifications::insert($responsibleNotifications);
                Mail::to($persons_array)->send(new Projectmeeting());
                Projecttemplate::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("id",$request->input('id'))->update(
                    array(
                           "task_status"=>$project_task_approval_status,
                           "updated_at"=>$updated_at
                    ));
                       
                   $returnData = Projecttemplate::find($request->input('id'));
                   $data = array ("message" => 'Meeting has been Approved');
                   $response = Response::json($data,200);
                   echo json_encode($response);
            }
        }
        else
        {
            $reject = Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$attendee)->where("isDeleted",0)->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );
            //attendee approvals update --attendee table
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->where("isDeleted",0)->update(
            array(
                "approval_status"=>$rejection_status,
                "task_status" => $attendee_task_rejection_status,
                "updated_at"=>$updated_at
            ));
            //attendee approvals update --approver table
            Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->where("isDeleted",0)->update(
                array(
                    "approval_status"=>$rejection_status,
                    "task_status" => $approvers_task_rejection_status,
                    "updated_at"=>$updated_at
                ));
            //attendee approvals update --project template table
            Projecttemplate::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("id",$request->input('id'))->update(
                array(
                    "task_status"=>$project_task_rejection_status,
                    "updated_at"=>$updated_at
                ));
                $responsibleNotifications = array();
                /* Mail to responsible person about meeting Confirmed notifications */
                for($l=0;$l<count($persons_list);$l++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$l])->first();
                    $persons_array[]= $responsible_person->email;  

                    $responsibleNotifications[]=[
                        "project_id" => $project_id,
                        "content" =>  $taskdata['meeting_topic']." for Project ".$taskdata['project_name']." has been Rejected",
                        "user" => $persons_list[$l],
                        "user_type" => 1,
                        "notification_type"=>env('NOTIFY_MEETING'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                Notifications::insert($responsibleNotifications);
                Mail::to($persons_array)->send(new Meetingrejection());

                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been rejected');
                $response = Response::json($data,200);
                echo json_encode($response);
        }
    }
    function performDocaction(Request $request)
    {
        $tasks = Projectdocs::where("project_id",$request->input('project_id'))->where("doc_id",$request->input('doc_id'))->where("master_task_id",$request->input('master_task_id'))->where("task_type",$request->input('task_type'))->update(
            array(
                "doc_status"=>$request->input('doc_status'),
                "updated_at"=>date('Y-m-d H:i:s')
            )
        );

        $data = array ("message" => 'Action on document has been done successfully');
        $response = Response::json($data,200);
        echo json_encode($response);
    }
    //meeting forward
    function forwardMeeting(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'phase_id' => 'required', 
            'task_id' => 'required|numeric', 
            'task_type' => 'required',
            'approver_type' => 'required',
            'forwarded_from' => 'required',
            'forwarded_to' => 'required',
            'user_type' => 'required',
            'mem_name' => 'required',
            'forwarded_mem_name' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        //approver type [1]
        if($request->input('approver_type')==1)
        {
            //check if task is completed or already approved by this user
            $approver_approvalStatus = Projecttasksapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('approver',$request->input('forwarded_from'))->where('isDeleted',0)->whereIn('approval_status',[1,2])->count();
            if($approver_approvalStatus>0)
            {
                return response()->json(['response'=>"Already Approval action done for this task"], 410); 
            }
            else
            {
                //update existing approver entry
                $updateApprover = Projecttasksapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('approver',$request->input('forwarded_from'))->update(array("isDeleted"=>1));
                if($updateApprover!=0)
                {
                    //make forwarded approver entry
                    $forwarded_approver = new Projecttasksapproval();
                    $forwarded_approver->project_id = $request->input('project_id');
                    $forwarded_approver->phase_id = $request->input('phase_id');
                    $forwarded_approver->task_type = $request->input('task_type');
                    $forwarded_approver->task_id = $request->input('task_id');
                    $forwarded_approver->approver = $request->input('forwarded_to');
                    $forwarded_approver->created_at = $created_at;
                    $forwarded_approver->updated_at = $updated_at;
                    if($forwarded_approver->save())
                    {
                        $forwardentry = new Forwardtask();
                        $forwardentry->project_id = $request->input('project_id');
                        $forwardentry->phase_id = $request->input('phase_id');
                        $forwardentry->task_id = $request->input('task_id');
                        $forwardentry->task_type = $request->input('task_type');
                        $forwardentry->forwarded_from = $request->input('forwarded_from');
                        $forwardentry->forwarded_to = $request->input('forwarded_to');
                        $forwardentry->user_type = $request->input('user_type');
                        $forwardentry->created_at = $created_at;
                        $forwardentry->updated_at = $updated_at;
                        if($forwardentry->save())
                        {
                            return response()->json(['response'=>"Task forwarded Successfully"], 200);
                        }
                        else
                        {
                            Projecttasksapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('approver',$request->input('forwarded_to'))->update(array("isDeleted"=>0));
                            return response()->json(['response'=>"Task not forwarded"], 410);
                        }
                    }
                    else
                    {
                        Projecttasksapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('approver',$request->input('forwarded_from'))->update(array("isDeleted"=>0));
                        return response()->json(['response'=>"Task not forwarded"], 410);
                    }
                }
                else
                {
                    return response()->json(['response'=>"Task not forwarded"], 410);
                }
            }
        }
        //attendee type [2]
        if($request->input('approver_type')==2)
        {
            $attendee = $request->input('forwarded_from')."-".$request->input('mem_name');
            $approver_approvalStatus = Projectattendeeapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('attendee',$attendee)->where('isDeleted',0)->whereIn('approval_status',[1,2])->count();
            if($approver_approvalStatus>0)
            {
                return response()->json(['response'=>"Already Approval action done for this task"], 410); 
            }
            else
            {
                //update existing approver entry
                $updateApprover = Projectattendeeapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('attendee',$attendee)->update(array("isDeleted"=>1));
                if($updateApprover!=0)
                {
                    //make forwarded approver entry
                    $forwarded_approver = new Projectattendeeapproval();
                    $forwarded_approver->project_id = $request->input('project_id');
                    $forwarded_approver->phase_id = $request->input('phase_id');
                    $forwarded_approver->task_type = $request->input('task_type');
                    $forwarded_approver->task_id = $request->input('task_id');
                    $forwarded_approver->attendee = $request->input('forwarded_to')."-".$request->input('forwarded_mem_name');
                    $forwarded_approver->created_at = $created_at;
                    $forwarded_approver->updated_at = $updated_at;
                    if($forwarded_approver->save())
                    {
                        $forwardentry = new Forwardtask();
                        $forwardentry->project_id = $request->input('project_id');
                        $forwardentry->phase_id = $request->input('phase_id');
                        $forwardentry->task_id = $request->input('task_id');
                        $forwardentry->task_type = $request->input('task_type');
                        $forwardentry->forwarded_from = $request->input('forwarded_from');
                        $forwardentry->forwarded_to = $request->input('forwarded_to');
                        $forwardentry->user_type = $request->input('user_type');
                        $forwardentry->created_at = $created_at;
                        $forwardentry->updated_at = $updated_at;
                        if($forwardentry->save())
                        {
                            return response()->json(['response'=>"Task forwarded Successfully"], 200);
                        }
                        else
                        {
                            Projectattendeeapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('attendee',$attendee)->update(array("isDeleted"=>0));
                            return response()->json(['response'=>"Task not forwarded"], 410);
                        }
                    }
                    else
                    {
                        Projectattendeeapproval::where('project_id',$request->input('project_id'))->where('task_type',$request->input('task_type'))->where('task_id',$request->input('task_id'))->where('attendee',$attendee)->update(array("isDeleted"=>0));
                        return response()->json(['response'=>"Task not forwarded"], 410);
                    }
                }
                else
                {
                    return response()->json(['response'=>"Task not forwarded"], 410);
                }

            }
        }
    }   
    function completetask(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'template_master_id' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $details = Projecttemplate::where('id',$request->input('template_master_id'))->where('project_id',$request->input('project_id'))->get();
        if($details[0]['isForwarded'])
        {
            Forwardtask::where('template_master_id',$request->input('template_master_id'))->where('project_id',$request->input('project_id'))->update(
                array(
                    "task_status"=>1,
                )
                );
                Projecttemplate::where('id',$request->input('template_master_id'))->update(
                    array(
                        "task_status"=>1,
                    )
                );
        }
        else
        {
            Projecttemplate::where('id',$request->input('template_master_id'))->update(
                array(
                    "task_status"=>1,
                )
            );
        }
        $returnData = Projecttemplate::where('template_master_id',$request->input('template_master_id'))->get();
        $data = array ("message" => 'Task Completed successfully',"data" => $returnData );
        $response = Response::json($data,200);
        echo json_encode($response);

    }
    function getActivetasks(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required', 
            'user_id' => 'required',
            'memname' => 'required', 
            'task_type' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $memid = $request->input('user_id');
        $memname = $request->input('memname');

		$attendee = "'".$memid."-".$memname."'";
        $task_count = 0;
        if($request->input('task_type') ==1)
        {
            $assigned_count = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->whereNotIn("tbl_project_template.task_status",[0,1])->where("tbl_project_template.task_type",1)->where(function($query) use ($memid,$attendee){
                $query->orwhereRaw("find_in_set($memid,tbl_project_template.mem_responsible)")
                ->orWhereRaw("find_in_set($memid,tbl_project_template.approvers)")
                ->orWhereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)");
            })->where('tbl_projects.property_id',$request->input('property_id'))->count();

            $forward_count = ProjectTemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->join('tbl_task_forwards','tbl_task_forwards.task_id','=','tbl_project_template.id')->whereNotIn("tbl_project_template.task_status",[0,1])->where("tbl_project_template.task_type",1)->where('tbl_task_forwards.forwarded_to',$memid)->where('tbl_projects.property_id',$request->input('property_id'))->count();
            $task_count = intval($assigned_count) + intval($forward_count);
        }
        if($request->input('task_type')==2)
        {
            $task_count = Projectdocs::join('tbl_projects','tbl_projects.project_id','=','tbl_projecttasks_docs.project_id')->whereNotIn("tbl_projecttasks_docs.doc_status",[0,8])->where(function($query) use ($memid,$attendee){
                $query->orwhereRaw("find_in_set($memid,tbl_projecttasks_docs.reviewers)")
                ->orWhereRaw("find_in_set($memid,tbl_projecttasks_docs.approvers_level1)")
                ->orWhereRaw("find_in_set($memid,tbl_projecttasks_docs.approvers_level2)");
            })->where('tbl_projects.property_id',$request->input('property_id'))->count();
        }
        if($request->input('task_type')==3)
        {
            $work_permits = Projectworkpermit::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_workpermits.project_id')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->where('tbl_projects.property_id',$request->input('property_id'))->whereNotIn('tbl_project_workpermits.request_status',[1,2])->where(function($query) use ($memid){
                $query->orwhereRaw("find_in_set($memid,tbl_projects.assigned_rdd_members)")
                      ->orWhereRaw("find_in_set($memid,tbl_project_contact_details.member_id)");
               })->count(Projectworkpermit::raw('DISTINCT tbl_project_workpermits.permit_id'));
              
            $inspections = Projectinspections::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->where('tbl_projects.property_id',$request->input('property_id'))->whereNotIn('tbl_project_inspections.inspection_status',[2,3,4])->where(function($query) use ($memid){
            $query->orwhereRaw("find_in_set($memid,tbl_projects.assigned_rdd_members)")
                    ->orWhereRaw("find_in_set($memid,tbl_project_contact_details.member_id)");
            })->count(Projectinspections::raw('DISTINCT tbl_project_inspections.inspection_id'));   
            
            $task_count = intval($work_permits)+intval($inspections);
        }
        return $task_count;
    }
    function retrieveProjectworkspace($projectid)
    {
        $project_details = Project::join('fitout_deposit_master','fitout_deposit_master.status_id','=','tbl_projects.fitout_deposit_status')->where('project_id',$projectid)->join('tbl_projecttype_master','tbl_projecttype_master.type_id','=','tbl_projects.project_type')->join('tbl_company_master','tbl_company_master.company_id','=','tbl_projects.investor_company')->join('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->join('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('project_id',$projectid)->select('tbl_projects.project_id','tbl_projects.org_id','tbl_projects.project_name','tbl_projects.usage_permissions','tbl_projects.fitout_period','tbl_projects.fitout_deposit_amt','tbl_projects.fitout_deposit_filepath','tbl_projects.owner_work','tbl_projects.owner_work_amt','tbl_projects.owner_work_filepath','tbl_projects.kfd_drawing_status','tbl_projects.ivr_status','tbl_projects.ivr_amt','tbl_projects.ivr_filepath','tbl_projects.workpermit_expiry_date','tbl_projects.insurance_validity_date','tbl_projects.fif_upload_path','tbl_projects.assigned_rdd_members','tbl_projects.fitout_deposit_status','fitout_deposit_master.status_name','tbl_projects.project_type','tbl_projecttype_master.type_name','tbl_projects.investor_company','tbl_company_master.company_name','tbl_projects.investor_brand','tbl_projects.property_id','tbl_properties_master.property_name','tbl_projects.unit_id','tbl_units_master.unit_name','tbl_units_master.unit_area')->get();
        $milestone_dates = Projectmilestonedates::where('project_id',$projectid)->where('active_status',1)->select('date_id','org_id','project_id','concept_submission','detailed_design_submission','unit_handover','fitout_start','fitout_completion','store_opening')->get();
        $investor_dates = Projectinvestordates::where('project_id',$projectid)->where('active_status',1)->select('date_id','org_id','project_id','concept_submission','detailed_design_submission','fitout_start','fitout_completion')->get();
        $member_contact_details = Projectcontact::join('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_project_contact_details.member_designation')->join('users','users.mem_id','=','tbl_project_contact_details.member_id')->where('project_id',$projectid)->whereNotIn('tbl_project_contact_details.member_designation', [13,14])->where('isDeleted',0)->where('project_id',$projectid)->select('tbl_project_contact_details.id','tbl_project_contact_details.project_id','tbl_project_contact_details.member_designation','tbl_project_contact_details.email','tbl_project_contact_details.mobile_number','users.mem_id','users.mem_name','users.mem_last_name','tbl_designation_master.designation_name')->get();
        $investor_contact_details = Projectcontact::leftjoin('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_project_contact_details.member_designation')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->where('tbl_project_contact_details.project_id',$projectid)->where('tbl_project_contact_details.isDeleted',0)->whereIn('tbl_project_contact_details.member_designation',[13,14])->select('tbl_project_contact_details.id','tbl_project_contact_details.project_id','tbl_project_contact_details.member_designation','tbl_project_contact_details.email','tbl_project_contact_details.mobile_number','tbl_tenant_master.tenant_id','tbl_tenant_master.tenant_name','tbl_designation_master.designation_name')->get();

        // old one ---$member_contact_details = Projectcontact::join('users','users.mem_id','=','tbl_project_contact_details.member_id')->where('project_id',$projectid)->whereNotIn('tbl_project_contact_details.member_designation', [7,8])->where('isDeleted',0)->select('tbl_project_contact_details.id','tbl_project_contact_details.project_id','tbl_project_contact_details.member_designation','tbl_project_contact_details.email','tbl_project_contact_details.mobile_number','users.mem_id','users.mem_name','users.mem_last_name')->get();
        // $investor_contact_details = Projectcontact::join('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->where('project_id',$projectid)->where('isDeleted',0)->select('tbl_project_contact_details.id','tbl_project_contact_details.project_id','tbl_project_contact_details.member_designation','tbl_project_contact_details.email','tbl_project_contact_details.mobile_number','tbl_tenant_master.tenant_id','tbl_tenant_master.tenant_name')->get();





        $paths = Docpathconfig::where('org_id',$project_details[0]['org_id'])->where('isDeleted',0)->get();
        $doc_path = public_path()."".$paths[0]['doc_path']."".$project_details[0]['project_id']."_".$project_details[0]['project_name']."/workspace_docs";
        $img_path = public_path()."".$paths[0]['image_path']."".$project_details[0]['project_id']."_".$project_details[0]['project_name']."/workspace_docs";
        if(!File::isDirectory($doc_path)){
            File::makeDirectory($doc_path, 0777, true, true);
           }
           if(!File::isDirectory($img_path)){
               File::makeDirectory($img_path, 0777, true, true);
           }

        return Response::json(array('project' => $project_details,'milestone_dates' => $milestone_dates,'investor_dates' => $investor_dates,'member_contact_details' => $member_contact_details,'investor_contact_details' => $investor_contact_details,'doc_path' => $doc_path, 'image_path' => $img_path ));
    }
    function assignApprovers($projectid,$docid)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $approverData = array();
        //check if approvers already assigned
        $approverassignedCount = Projectdocs::where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.doc_id',$docid)->where('isApproversassigned',0)->count();
        if($approverassignedCount>0)
        {
            $details = Projectdocs::where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.doc_id',$docid)->get();
            for($r=0;$r<count($details);$r++)
            {
                $reviewers = explode(',',$details[$r]['reviewers']);
                $approvers_level1 = explode(',',$details[$r]['approvers_level1']);
                $approvers_level2 = explode(',',$details[$r]['approvers_level2']);
                for($x=0;$x<count($reviewers);$x++)
                {
                    $approverData[] = [
                        'project_id' => $details[$r]['project_id'],
                        'doc_id' => $details[$r]['doc_id'],
                        'approver_type' => 1,
                        'approver_id'=>$reviewers[$x],
                        'created_at' => $created_at,
                        'updated_at' => $updated_at
                    ];
                }
                for($y=0;$y<count($approvers_level1);$y++)
                {
                    $approverData[] = [
                        'project_id' => $details[$y]['project_id'],
                        'doc_id' => $details[$y]['doc_id'],
                        'approver_type' => 2,
                        'approver_id'=>$approvers_level1[$y],
                        'created_at' => $created_at,
                        'updated_at' => $updated_at
                    ];
                }
                for($z=0;$z<count($approvers_level2);$z++)
                {
                    $approverData[] = [
                        'project_id' => $details[$z]['project_id'],
                        'doc_id' => $details[$z]['doc_id'],
                        'approver_type' => 3,
                        'approver_id'=>$approvers_level2[$z],
                        'created_at' => $created_at,
                        'updated_at' => $updated_at
                    ];
                }
            }
            if(ProjectdocsApproval::insert($approverData))
            {
                $updateStatus = Projectdocs::where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.doc_id',$docid)->update(
                    array(
                        "isApproversassigned"=>1
                    )
                    );
                if($updateStatus!=0)
                {
                    return 1;
                }
                else
                {
                    return 2;
                }
            }
        }
        else
        {
            return 1;
        }
    }
    function assignMembers($projectid,$phase_id)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $taskcheck = Projecttemplate::where('tbl_project_template.project_id',$projectid)->where('tbl_project_template.phase_id',$phase_id)->whereNull('tbl_project_template.approvers')->whereNull('tbl_project_template.mem_responsible')->get();
        $project_members = Projectmembers::where('project_id',$projectid)->get();
        if(count($taskcheck)>0)
        {
            for($i=0;$i<count($project_members);$i++)
            {
                for($k=0;$k<count($taskcheck);$k++)
                {
                    $query1 = Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereNull('tbl_project_template.mem_responsible')->whereRaw("find_in_set(".$project_members[$i]['designation'].",mem_responsible_designation)")->get();
                    if(count($query1)>0)
                    {
                    Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereRaw("find_in_set(".$project_members[$i]['designation'].",mem_responsible_designation)")->update(array('mem_responsible'=> $project_members[$i]['members'])); 
                    }
                    else
                    {
                        Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereRaw("find_in_set(".$project_members[$i]['designation'].",mem_responsible_designation)")->update(array('mem_responsible'=>DB::raw('CONCAT(mem_responsible,",'.$project_members[$i]['members'].'")')));
                    }
                    $query2 = Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereNull('tbl_project_template.approvers')->whereRaw("find_in_set(".$project_members[$i]['designation'].",approvers_designation)")->get();
                    if(count($query2)>0)
                    {
                    Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereRaw("find_in_set(".$project_members[$i]['designation'].",approvers_designation)")->update(array('approvers'=> $project_members[$i]['members'])); 
                    }
                    else
                    {
                        Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereRaw("find_in_set(".$project_members[$i]['designation'].",approvers_designation)")->update(array('approvers'=>DB::raw('CONCAT(approvers,",'.$project_members[$i]['members'].'")')));
                    }
                    $query3 = Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereNull('tbl_project_template.attendees')->whereRaw("find_in_set(".$project_members[$i]['designation'].",attendees_designation)")->get();
                
                    if(count($query3)>0)
                    {
                        Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereRaw("find_in_set(".$project_members[$i]['designation'].",attendees_designation)")->update(array('attendees'=> $project_members[$i]['members_designation']));
                    }
                    else
                    {
                        Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->where('id',$taskcheck[$k]['id'])->whereRaw("find_in_set(".$project_members[$i]['designation'].",attendees_designation)")->update(array('attendees'=>DB::raw('CONCAT(attendees,",'.$project_members[$i]['members_designation'].'")')));
                    }
                }
            }
        }
        $doccheck = Projectdocs::where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.phase_id',$phase_id)->whereNull('tbl_projecttasks_docs.reviewers')->whereNull('tbl_projecttasks_docs.approvers_level1')->whereNull('tbl_projecttasks_docs.approvers_level2')->get();
        if(count($doccheck)>0)
        {
            for($q=0;$q<count($project_members);$q++)
            {
                for($l=0;$l<count($doccheck);$l++)
                {
                    $query6 = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereNull('tbl_projecttasks_docs.approvers_level2')->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level2_designation)")->get();
                    if(count($query6)>0)
                    {
                        $a = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level2_designation)")->update(array('approvers_level2'=> $project_members[$q]['members'])); 
                    }
                    if(count($query6)==0)
                    {
                        $b = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level2_designation)")->update(array('approvers_level2'=>DB::raw('CONCAT(approvers_level2,",'.$project_members[$q]['members'].'")')));
                    }
                    $query4 = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereNull('tbl_projecttasks_docs.reviewers')->whereRaw("find_in_set(".$project_members[$q]['designation'].",reviewers_designation)")->get();
                    if(count($query4)>0)
                    {
                        $c = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereRaw("find_in_set(".$project_members[$q]['designation'].",reviewers_designation)")->update(array('reviewers'=> $project_members[$q]['members'])); 
                    }
                    if(count($query4)==0)
                    {
                        $d = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereRaw("find_in_set(".$project_members[$q]['designation'].",reviewers_designation)")->update(array('reviewers'=>DB::raw('CONCAT(reviewers,",'.$project_members[$q]['members'].'")')));
                    }
                    $query5 = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereNull('tbl_projecttasks_docs.approvers_level1')->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level1_designation)")->get();
                    if(count($query5)>0)
                    {
                        $e = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level1_designation)")->update(array('approvers_level1'=> $project_members[$q]['members'])); 
                    }
                    if(count($query5)==0)
                    {
                        $f = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->where('doc_id',$doccheck[$l]['doc_id'])->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level1_designation)")->update(array('approvers_level1'=>DB::raw('CONCAT(approvers_level1,",'.$project_members[$q]['members'].'")')));
                    }
                }                
            }
        }
    }
    function retrieveProjectPhase($projectid,$phase_id)
    {
        $this->assignMembers($projectid,$phase_id);
        $project_details = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->join('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_template.phase_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status',DB::raw("GROUP_CONCAT(DISTINCT a.mem_name) as member_responsible_person"),DB::raw("GROUP_CONCAT(DISTINCT b.mem_name) as approvers_person"),'tbl_phase_master.phase_name','tbl_project_template.org_id','tbl_projects.project_name')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_project_template.mem_responsible)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$projectid)->where('tbl_project_template.phase_id',$phase_id)->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
       
        $docs_details = Projectdocs::select('doc_id','project_id','phase_id','doc_header','doc_title','reviewers','approvers_level1','approvers_level2','file_path','comment','actual_date','due_date','doc_status')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_projecttasks_docs.reviewers)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_projecttasks_docs.approvers_level1)"),">",\DB::raw("'0'"))->leftjoin('users as c',\DB::raw("FIND_IN_SET(c.mem_id,tbl_projecttasks_docs.approvers_level2)"),">",\DB::raw("'0'"))->where('tbl_projecttasks_docs.isDeleted',0)->where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.phase_id',$phase_id)->groupBy('doc_id')->get()->groupBy('doc_header');

        $permit_details = Projectworkpermit::join('tbl_workpermit_master','tbl_workpermit_master.permit_id','=','tbl_project_workpermits.work_permit_type')->select('tbl_project_workpermits.permit_id','tbl_project_workpermits.project_id','tbl_project_workpermits.work_permit_type','tbl_project_workpermits.file_path','tbl_project_workpermits.drawing_path','tbl_project_workpermits.start_date','tbl_project_workpermits.end_date','tbl_project_workpermits.description','tbl_project_workpermits.checklist_file_path','tbl_project_workpermits.request_status','tbl_project_workpermits.investor_id','tbl_workpermit_master.permit_type')->where('project_id',$projectid)->get();

        $inspection_details = Projectinspections::select('inspection_id','project_id','inspection_type','requested_time','checklist_id','comments','inspection_status','report_status','investor_id')->where('project_id',$projectid)->get();

        $actual_inspection_reports = Projectinspections::select('inspection_id','project_id','inspection_type','requested_time','checklist_id','comments','inspection_status','report_status','investor_id')->where('project_id',$projectid)->where('inspection_status',2)->get();

        $milestone_dates = Projectmilestonedates::where('project_id',$projectid)->where('active_status',1)->select('date_id','org_id','project_id','concept_submission','detailed_design_submission','unit_handover','fitout_start','fitout_completion','store_opening')->get();

        $investor_dates = Projectinvestordates::where('project_id',$projectid)->where('active_status',1)->select('date_id','org_id','project_id','concept_submission','detailed_design_submission','fitout_start','fitout_completion')->get();
        $siteinspection = SiteInspectionReport::where('project_id',$projectid)->where('isDeleted',0)->get();
        $fitout_certificates = FitoutCompletionCertificates::where('project_id',$projectid)->where('isDeleted',0)->get();
        $preopening_docs = Preopeningdocs::where('project_id',$projectid)->where('isDeleted',0)->get();
        $fitout_refund = FitoutDepositrefund::where('project_id',$projectid)->where('isDeleted',0)->get();

        $paths = Docpathconfig::where('org_id',$project_details[0]['org_id'])->where('isDeleted',0)->get();
        $doc_path = public_path()."".$paths[0]['doc_path']."".$project_details[0]['project_id']."_".$project_details[0]['project_name']."/".$project_details[0]['phase_name'];
        $img_path = public_path()."".$paths[0]['image_path']."".$project_details[0]['project_id']."_".$project_details[0]['project_name']."/".$project_details[0]['phase_name'];
        if(!File::isDirectory($doc_path)){
            File::makeDirectory($doc_path, 0777, true, true);
           }
           if(!File::isDirectory($img_path)){
               File::makeDirectory($img_path, 0777, true, true);
           }

        return Response::json(array('project' => $project_details,'doc_details' => $docs_details,'requested_permits'=>$permit_details,'requested_inspections' => $inspection_details,'milestone_dates' => $milestone_dates,'investor_dates'=>$investor_dates,"actual_inspection_reports"=> $actual_inspection_reports,"site_inspection_reports"=> $siteinspection,"fitout_completion_certificates"=>$fitout_certificates,"pre_opening_docs"=>$preopening_docs,"fitout_deposit_refund"=> $fitout_refund,"doc_path"=>$doc_path,"image_path"=>$img_path));
    }
    function updateFitoutdetails(Request $request,$project_id)
    {
        $projectdata = $request->get('project');
        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');
        $project = new Project();
        $milestoneData = [];
        $investorData = [];
        $contactData = [];

        for($i=0;$i<count($projectdata);$i++) 
        {
            
            $project = Project::where("project_id",$project_id)->where("org_id",$projectdata[$i]['org_id'])->update(
                array(
                    "fitout_deposit_status" => $projectdata[$i]['fitout_deposit_status'],
                    "fitout_deposit_amt" => $projectdata[$i]['fitout_deposit_amt'],
                    "fitout_deposit_filepath" => $projectdata[$i]['fitout_deposit_filepath'],
                    "insurance_validity_date" => $projectdata[$i]['insurance_validity_date'],
                    "owner_work" => $projectdata[$i]['owner_work'],
                    "owner_work_amt" => $projectdata[$i]['owner_work_amt'],
                    "owner_work_filepath" => $projectdata[$i]['owner_work_filepath'],
                    "kfd_drawing_status" => $projectdata[$i]['kfd_drawing_status'],
                    "ivr_status" => $projectdata[$i]['ivr_status'],
                    "ivr_amt" => $projectdata[$i]['ivr_amt'],
                    "ivr_filepath" => $projectdata[$i]['ivr_filepath'],
                    "workpermit_expiry_date" => $projectdata[$i]['workpermit_expiry_date'],
                    "updated_at" => $updated_at
                )
            );
        }

        //project milestone dates
        $milestone_dates = $request->get('milestone_dates');

        for($j=0;$j<count($milestone_dates);$j++) 
        {
            if($milestone_dates[$j]['date_id']!=0)
            { 
                //update  existing milestone dates
                Projectmilestonedates::where('date_id',$milestone_dates[$j]['date_id'])->where('project_id',$project_id)->update(array(
                    'concept_submission' => $milestone_dates[$j]['concept_submission'],
                    'detailed_design_submission' => $milestone_dates[$j]['detailed_design_submission'],
                    'unit_handover' => $milestone_dates[$j]['unit_handover'],
                    'fitout_start' => $milestone_dates[$j]['fitout_start'],
                    'fitout_completion' => $milestone_dates[$j]['fitout_completion'],
                    'store_opening' => $milestone_dates[$j]['store_opening'],
                    "updated_at" => $updated_at,
                    "active_status" => $milestone_dates[$j]['active_status']
                ));
            }
            else
            {
                $milestoneData[] = [
                    'org_id' => $projectdata[0]['org_id'],
                    'project_id' => $project_id,
                    'concept_submission' => $milestone_dates[$j]['concept_submission'],
                    'detailed_design_submission' => $milestone_dates[$j]['detailed_design_submission'],
                    'unit_handover' => $milestone_dates[$j]['unit_handover'],
                    'fitout_start' => $milestone_dates[$j]['fitout_start'],
                    'fitout_completion' => $milestone_dates[$j]['fitout_completion'],
                    'store_opening' => $milestone_dates[$j]['store_opening'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at,
                    'created_by' => $projectdata[0]['user_id']
                 ];
            }
        }

        //project investor planned dates
        $investor_dates = $request->get('investor_dates');

        for($k=0;$k<count($investor_dates);$k++) 
        {
            if($investor_dates[$k]['date_id']!=0)
            {
                //update existing milestone dates
                Projectinvestordates::where('date_id',$investor_dates[$k]['date_id'])->where('project_id',$project_id)->update(array(
                    'concept_submission' => $investor_dates[$k]['concept_submission'],
                    'detailed_design_submission' => $investor_dates[$k]['detailed_design_submission'],
                    'fitout_start' => $investor_dates[$k]['fitout_start'],
                    "updated_at" => $updated_at,
                    'active_status' => $investor_dates[$k]['active_status'],
                ));
            }
            else
            {
                $investorData[] = [
                    'project_id' => $project_id,
                    'org_id' => $projectdata[0]['org_id'],
                    'concept_submission' => $investor_dates[$k]['concept_submission'],
                    'detailed_design_submission' => $investor_dates[$k]['detailed_design_submission'],
                    'fitout_start' => $investor_dates[$k]['fitout_start'],
                    'fitout_completion' => $investor_dates[$k]['fitout_completion'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at,
                    'created_by' => $projectdata[0]['user_id']
                 ];
            }
        }

        //project contact details
        $contact = $request->get('contact_details');

        for($r=0;$r<count($contact);$r++) 
        {
            if($contact[$r]['id']!=0)
            {
                Projectcontact::where('project_id',$project_id)->where('id',$contact[$r]['id'])->update(
                    array(
                        'member_id' => $contact[$r]['member_id'],
                        'member_designation' => $contact[$r]['member_designation'],
                        'email' => $contact[$r]['email'],
                        'mobile_number' => $contact[$r]['mobile_number'],
                        "updated_at" => $updated_at,
                        "isDeleted" => $contact[$r]['isDeleted']
                    )
                    );
            }
            else
            {
                $contactData[]=[
                    'project_id' => $project_id,
                    'member_id' => $contact[$r]['member_id'],
                    'member_designation' => $contact[$r]['member_designation'],
                    'email' => $contact[$r]['email'],
                    'mobile_number' => $contact[$r]['mobile_number'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at,
                    'created_by' => $projectdata[0]['user_id'],
                ];
            }
        }

        if($project>0 || Projectmilestonedates::insert($milestoneData) || Projectinvestordates::insert($investorData) || Projectcontact::insert($contactData))
        {
            //mail to be send on account of fitout details update
            $returnData = Project::select('project_id','project_name','created_at')->find($project_id);
            $data = array ("message" => 'Project Edited successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);  
        }
    }
    function investorrequestWorkpermit(Request $request,$projectid)
    {
        if($projectid=="" || $projectid==null)
        {
            return response()->json(['response'=>"No Project chosen for creating work permit"], 411);
        }
        $validator = Validator::make($request->all(), [ 
            'work_permit_type' => 'required',
            'start_date' => 'required', 
            'end_date' => 'required', 
            'investor_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $permit = new Projectworkpermit();

        $permit->project_id = $projectid;
        $permit->work_permit_type = $request->input('work_permit_type');
        $permit->file_path = $request->input('file_path');
        $permit->drawing_path = $request->input('drawing_path');
        $permit->start_date = $request->input('start_date');
        $permit->end_date = $request->input('end_date');
        $permit->description = $request->input('comments');
        $permit->company_name = $request->input('company_name');
        $permit->contact_name = $request->input('contact_name');
        $permit->contact_no = $request->input('contact_no');
        $permit->investor_id = $request->input('investor_id');
        $permit->created_at = date('Y-m-d H:i:s');
        $permit->updated_at = date('Y-m-d H:i:s');
        
        if($permit->save())
        {
            $project_details = Project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')
            ->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->select('users.mem_name','users.mem_last_name','users.email as mem_email','tbl_tenant_master.email as tenant_email','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->where('tbl_projects.project_id',$projectid)->where('tbl_project_contact_details.member_designation',13)->groupBy('tbl_projects.project_id')->get();

            if(count($project_details)!=0)
            {
                $data = array();
                $data = [
                    "tenant_name" => $project_details[0]['tenant_name'],
                    "tenant_last_name" => $project_details[0]['tenant_last_name'],
                    "mem_email" => $project_details[0]['mem_email'],
                    "tenant_email" => $project_details[0]['tenant_email']

                ];

                Mail::send('emails.projectworkpermits', $data, function($message)use($data) {
                $message->to($data['mem_email'])
                        ->cc($data['tenant_email'])
                        ->subject('RDD - Work Permit Request');
                });  
            }              
            $returnData = $permit->find($permit->permit_id);
            $data = array ("message" => 'Work permit has been requested',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
        }
    }
    function updatePhasedetails(Request $request,$projectid,$phaseid)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $template = $request->get('data');
        $docs = $request->get('docs');
        $investor_dates = $request->get('investor_dates');

        for($i=0;$i<count($template);$i++) 
        {
            
            $project = Projecttemplate::where("project_id",$projectid)->where("phase_id",$phaseid)->where("org_id",$template[$i]['org_id'])->where("id",$template[$i]['id'])->update(
                array(
                    "activity_desc" => $template[$i]['activity_desc'],
                    "meeting_date" => $template[$i]['meeting_date'],
                    "meeting_start_time" => $template[$i]['meeting_start_time'],
                    "meeting_end_time" => $template[$i]['meeting_end_time'],
                    "approvers" => $template[$i]['approvers'],
                    "attendees" => $template[$i]['attendees'],
                    "mem_responsible" => $template[$i]['mem_responsible'],
                    "fre_id" => $template[$i]['fre_id'],
                    "duration" => $template[$i]['duration'],
                    "seq_status" => $template[$i]['seq_status'],
                    "seq_no" => $template[$i]['seq_no'],
                    "actual_date" => $template[$i]['actual_date'],
                    "fif_upload_path" => $template[$i]['fif_upload_path'],
                    "updated_at" =>$updated_at,
                    "task_status" => $template[$i]['task_status']
                )
            );
        }
        for($k=0;$k<count($investor_dates);$k++) 
        {
            //update existing investor dates
            Projectinvestordates::where('date_id',$investor_dates[$k]['date_id'])->where('project_id',$projectid)->update(array(
                'concept_submission' => $investor_dates[$k]['concept_submission'],
                'detailed_design_submission' => $investor_dates[$k]['detailed_design_submission'],
                'fitout_start' => $investor_dates[$k]['fitout_start'],
                "updated_at" => $updated_at,
            ));
        }
        //for docs details update
        for($n=0;$n<count($docs);$n++)
        {
            $project = Projectdocs::where("project_id",$projectid)->where("phase_id",$phaseid)->where("doc_id",$docs[$n]['doc_id'])->update(
                array(
                    "actual_date"=>$docs[$n]['actual_date'],
                    "updated_at" =>$updated_at
                )
            );
        }

        //for work permit updates
        $permit = $request->get('permits');
        for($q=0;$q<count($permit);$q++)
        {
            Projectworkpermit::where("project_id",$projectid)->where("permit_id",$permit[$q]['permit_id'])->update(
                array(
                    "request_status" => $permit[$q]['request_status']
                )
            );
        }

        //for requested inspections
        $inspection = $request->get('inspections');        
        for($s=0;$s<count($inspection);$s++)
        {
            Projectinspections::where("project_id",$projectid)->where("inspection_id",$inspection[$s]['inspection_id'])->update(
                array(
                    "inspection_status" => $inspection[$s]['inspection_status']
                )
            );
        }

        //for requested inspections
        $actual_inspection = $request->get('actual_inspections');        
        for($r=0;$r<count($actual_inspection);$r++)
        {
            Projectinspections::where("project_id",$projectid)->where("inspection_id",$actual_inspection[$r]['inspection_id'])->update(
                array(
                    "report_status" => $actual_inspection[$r]['report_status']
                )
            );
        }

        //for fitout completion certificates
        if($request->has('fitout_certificate'))
        {
            $fccdata = $request->get('fitout_certificate'); 
            for($x=0;$x<count($fccdata);$x++)
            {
                FitoutCompletionCertificates::where("project_id",$projectid)->where("id",$fccdata[$x]['id'])->where("isDeleted",0)->update(
                    array(
                        "actual_date" => $fccdata[$x]['actual_date'],
                        "comments" => $fccdata[$x]['comments']
                    )
                );
            }
        }

        //for pre opening docs
        if($request->has('pre_opening_docs'))
        {
            $predocs = $request->get('pre_opening_docs'); 
            for($y=0;$y<count($predocs);$y++)
            {
                Preopeningdocs::where("project_id",$projectid)->where("id",$predocs[$y]['id'])->where("isDeleted",0)->update(
                    array(
                        "actual_date" => $predocs[$y]['actual_date'],
                    )
                );
            }
        }

        //for fitout deposit refund
        if($request->has('fitout_deposit_refund'))
        {
            $fitout_deposit_refund = $request->get('fitout_deposit_refund'); 
            for($z=0;$z<count($fitout_deposit_refund);$z++)
            {
                FitoutDepositrefund::where("project_id",$projectid)->where("id",$fitout_deposit_refund[$z]['id'])->where("isDeleted",0)->update(
                    array(
                        "actual_date" => $fitout_deposit_refund[$z]['actual_date'],
                        "comments" => $fitout_deposit_refund[$z]['comments']
                    )
                );
            }
        }


        $returnData = Projecttemplate::select('id','org_id','project_id','task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','approvers','attendees','mem_responsible','phase_id','fre_id','duration','seq_status','seq_no','planned_date','actual_date','fif_upload_path','task_status','isDeleted')->find($projectid);
        $data = array ("message" => 'Project Edited successfully');
        $response = Response::json($data,200);
        echo json_encode($response);
    }
    function updatedocDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'doc_id' => 'required',
            'user_id' => 'required',
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $userid = $request->input('user_id');
        $docid = $request->input('doc_id');
        $data = Projectdocs::where("doc_id",$docid)->whereRaw("find_in_set(".$userid.",approvers_level1)")->orWhereRaw("find_in_set(".$userid.",approvers_level2)")->orWhereRaw("find_in_set(".$userid.",reviewers)")->get();
         if(count($data)>0)
         {
                $project = Projectdocs::where("project_id",$request->input('project_id'))->where("doc_id",$request->input('doc_id'))->update(
                    array(
                            "comment" => $request->input('comment'),
                            "actual_date" => $request->input('actual_date')
                         )
                    );
                $data = Projectdocs::where("doc_id",$docid)->whereRaw("find_in_set(".$userid.",approvers_level1)")->orWhereRaw("find_in_set(".$userid.",approvers_level2)")->orWhereRaw("find_in_set(".$userid.",reviewers)")->get();
                $data = array ("message" => 'Data Edited successfully',"data" => $data );
                $response = Response::json($data,200);
                echo json_encode($response);
         }
         else
         {
            return response()->json(['response'=>"User not have access to comment"], 410); 
         }
    }
    function retrieveTemplateDesignations($templateid)
    {
        
        $contract_designation = Designation::where('designation_user_type',2)->get()->groupBy('designation_name');
        $designations = Templatedesignations::join('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_template_designations.designation_id')->where('template_id',$templateid)->get(['tbl_template_designations.id','tbl_template_designations.org_id','tbl_template_designations.designation_id','tbl_designation_master.designation_name','tbl_designation_master.designation_user_type'])->groupBy('designation_name')->union($contract_designation);
        return $designations;
    }
    function investorWorkpermitlist(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $query = Projectworkpermit::leftjoin('tbl_workpermit_master','tbl_workpermit_master.permit_id','=','tbl_project_workpermits.work_permit_type')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_workpermits.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_projects.unit_id','=','tbl_units_master.unit_id')->select('tbl_project_workpermits.*','tbl_projects.project_name','tbl_workpermit_master.permit_type','tbl_properties_master.property_name','tbl_units_master.*')->where('tbl_project_workpermits.project_id',$request->input('project_id'));
        if ($request->has('work_permit_type') && !empty($request->input('work_permit_type')))
        {
            $query->where('work_permit_type',$request->input('work_permit_type'));
        }
        $lists = $query->get();
        return response()->json($lists, 200);
    }
    function sendMommail(Request $request,$project_id,$task_id)
    {
        $validator = Validator::make($request->all(), [ 
            'subject' => 'required',
            'content' => 'required'

        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $data = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $attachment_files = array();
        $data['subject'] = $request->input('subject');
        $data['content'] = $request->input('content');
        $project_meeting_completed_status = 1;
        $task_data = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status',DB::raw("GROUP_CONCAT(DISTINCT a.email) as member_responsible_person"),DB::raw("GROUP_CONCAT(DISTINCT b.email) as approvers_person"),'tbl_project_template.org_id','tbl_projects.project_name','tbl_project_template.meeting_topic')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,TRIM(tbl_project_template.mem_responsible))"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.id',$task_id)->where('tbl_project_template.task_status',$project_meeting_completed_status)->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
        if(count($task_data)>0)
        {
            $responsible_person = array();
            $attendees_person = array();
            $momNotifications = array();
            $person_list  = explode(',',$task_data[0]['member_responsible_person']);
            $approvers_list  = explode(',',$task_data[0]['approvers_person']);
            $attendees = explode(',',$task_data[0]['attendees']);
            for($c=0;$c<count($person_list);$c++)
            {
                $responsible_person[] = $person_list[$c];
                $memCheck = Members::where('email',$person_list[$c])->first();
                $momNotifications[] = [
                    "project_id" => $project_id,
                    "content" =>  "You have received MOM details of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                    "user" => $memCheck['mem_id'],
                    "user_type" => 1,
                    "notification_type"=>env('NOTIFY_MOM'),
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }
            for($b=0;$b<count($approvers_list);$b++)
            {
                $responsible_person[] = $approvers_list[$b];
                $memCheck = Members::where('email',$approvers_list[$b])->first();
                $momNotifications[] = [
                    "project_id" => $project_id,
                    "content" =>  "You have received MOM details of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                    "user" => $memCheck['mem_id'],
                    "user_type" => 1,
                    "notification_type"=>env('NOTIFY_MOM'),
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }
            for($a=0;$a<count($attendees);$a++)
            {
                $res1 = explode('-',$attendees[$a]);
                $memCheck = Members::where('mem_id',$res1[0])->where('mem_name',$res1[1])->first();
                $tenantCheck = Tenant::where('tenant_id',$res1[0])->where('tenant_name',$res1[1])->first();
                if($memCheck!=null)
                {
                    $attendees_person[] = $memCheck['email'];
                    $momNotifications[] = [
                        "project_id" => $project_id,
                        "content" =>  "You have received MOM details of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                        "user" => $res1[0],
                        "user_type" => 1,
                        "notification_type"=>env('NOTIFY_MOM'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                if($tenantCheck!=null)
                {
                    $attendees_person[] = $tenantCheck['email'];
                    $momNotifications[] = [
                        "project_id" => $project_id,
                        "content" =>  "You have received MOM details of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                        "user" => $res1[0],
                        "user_type" => 2,
                        "notification_type"=>env('NOTIFY_MOM'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
            }

            $data['attendees'] = $attendees_person;
            $data['responsible_person'] = $responsible_person;
            Notifications::insert($momNotifications);
            Mail::send('emails.projectmom', $data, function($message)use($data) {
                $message->to($data['attendees'])
                         ->cc($data['responsible_person'])
                        ->subject($data["subject"])
                        ->setBody($data["content"], 'text/html');
                        
                // if(count($attachment_files)>0)
                // {
                //         foreach ($attachment_files as $file){
                //          $message->attach($file);
                //         }
                //  }
            });
            if(Mail::failures())
            {
                return response()->json(['response'=>"Mom Mail Not Sent"], 411);
            }
            else
            {
                return response()->json(['response'=>"Mom Mail Sent"], 200);
            }
        }
        else
        {
            return response()->json(['response'=>"Meeting not Completed"], 410);
        }
    }
    function sendRemindermail($project_id,$task_id)
    {
        $project_meeting_not_scheduled_status = 0;
        $project_meeting_completed_status = 1;
        $project_meeting_approver_rejected_status = 5;
        $project_meeting_attendee_rejected_status = 6;

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $task_data = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status',DB::raw("GROUP_CONCAT(DISTINCT a.email) as member_responsible_person"),DB::raw("GROUP_CONCAT(DISTINCT b.email) as approvers_person"),'tbl_project_template.org_id','tbl_projects.project_name','tbl_project_template.meeting_topic')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,TRIM(tbl_project_template.mem_responsible))"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.id',$task_id)->whereNotIn('tbl_project_template.task_status',[$project_meeting_not_scheduled_status,$project_meeting_completed_status,$project_meeting_approver_rejected_status,$project_meeting_attendee_rejected_status])->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
        if(count($task_data)>0)
        {
            $responsible_person = array();
            $attendees_person = array();
            $momNotifications = array();
            $person_list  = explode(',',$task_data[0]['member_responsible_person']);
            $approvers_list  = explode(',',$task_data[0]['approvers_person']);
            $attendees = explode(',',$task_data[0]['attendees']);
            for($c=0;$c<count($person_list);$c++)
            {
                $responsible_person[] = $person_list[$c];
                $memCheck = Members::where('email',$person_list[$c])->first();
                $momNotifications[] = [
                    "project_id" => $project_id,
                    "content" =>  "You have received Reminder of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                    "user" => $memCheck['mem_id'],
                    "user_type" => 1,
                    "notification_type"=>env('NOTIFY_MEETING_REMIINDER'),
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }
            for($b=0;$b<count($approvers_list);$b++)
            {
                $responsible_person[] = $approvers_list[$b];
                $memCheck = Members::where('email',$approvers_list[$b])->first();
                $momNotifications[] = [
                    "project_id" => $project_id,
                    "content" => "You have received Reminder of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                    "user" => $memCheck['mem_id'],
                    "user_type" => 1,
                    "notification_type"=>env('NOTIFY_MEETING_REMIINDER'),
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }
            for($a=0;$a<count($attendees);$a++)
            {
                $res1 = explode('-',$attendees[$a]);
                $memCheck = Members::where('mem_id',$res1[0])->where('mem_name',$res1[1])->first();
                $tenantCheck = Tenant::where('tenant_id',$res1[0])->where('tenant_name',$res1[1])->first();
                if($memCheck!=null)
                {
                    $attendees_person[] = $memCheck['email'];
                    $momNotifications[] = [
                        "project_id" => $project_id,
                        "content" =>  "You have received Reminder of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                        "user" => $res1[0],
                        "user_type" => 1,
                        "notification_type"=>env('NOTIFY_MEETING_REMIINDER'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                if($tenantCheck!=null)
                {
                    $attendees_person[] = $tenantCheck['email'];
                    $momNotifications[] = [
                        "project_id" => $project_id,
                        "content" =>  "You have received Reminder of ".$task_data[0]['meeting_topic']." for Project ".$task_data[0]['project_name'],
                        "user" => $res1[0],
                        "user_type" => 2,
                        "notification_type"=>env('NOTIFY_MEETING_REMIINDER'),
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
            }
            Notifications::insert($momNotifications);
            Mail::to($attendees_person)->cc($responsible_person)->send(new Meetingreminder());
            if(Mail::failures())
            {
                return response()->json(['response'=>"Meeting reminder Mail Not Sent"], 411);
            }
            else
            {
                return response()->json(['response'=>"Meeting Reminder Mail Sent"], 200);
            }
        }
        else
        {
            return response()->json(['response'=>"Meeting Reminder Cannot be sent"], 410);
        }
    }
    function rddcreateWorkpermit(Request $request,$projectid)
    {
        if($projectid=="" || $projectid==null)
        {
            return response()->json(['response'=>"No Project chosen for creating work permit"], 411);
        }
        $validator = Validator::make($request->all(), [ 
            'work_permit_type' => 'required',
            'start_date' => 'required', 
            'end_date' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $permit = new Projectworkpermit();

        $permit->project_id = $projectid;
        $permit->work_permit_type = $request->input('work_permit_type');
        $permit->file_path = $request->input('file_path');
        $permit->drawing_path = $request->input('drawing_path');
        $permit->start_date = $request->input('start_date');
        $permit->end_date = $request->input('end_date');
        $permit->description = $request->input('comments');
        $permit->company_name = $request->input('company_name');
        $permit->contact_name = $request->input('contact_name');
        $permit->contact_no = $request->input('contact_no');
        $permit->rdd_member_id = $request->input('user_id');
        $permit->created_at = date('Y-m-d H:i:s');
        $permit->updated_at = date('Y-m-d H:i:s');
        
        if($permit->save())
        {
            $returnData = $permit->find($permit->permit_id);
            $data = array ("message" => 'Work permit has been Created',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
        }
    }
    function retrieveMembertasklists(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required',
            'task_type' => 'required', 
            'user_id' => 'required', 
            'memname' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $memid = $request->input('user_id');
        $memname = $request->input('memname');

        $attendee = "'".$memid."-".$memname."'";
        $task_not_initiated_status = 0;
        $task_lists = "";

        if($request->input('task_type')==1)
        {
            $forwarded_tasks = ProjectTemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->leftjoin('tbl_project_tasks_approvals','tbl_project_tasks_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_attendees_approvals','tbl_attendees_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_task_forwards','tbl_task_forwards.task_id','=','tbl_project_template.id')->where('tbl_project_template.task_type',$request->input('task_type'))->whereNotIn('tbl_project_template.task_status', [0,1])->where('tbl_project_template.isDeleted',0)->select('tbl_project_template.*','tbl_properties_master.property_name','tbl_projects.project_name','tbl_units_master.unit_name')->where('tbl_task_forwards.forwarded_to',$memid);
            if ($request->has('project_id') && !empty($request->input('project_id')))
            {
                $forwarded_tasks->where('tbl_project_template.project_id', $request->input('project_id'));
            }
            if ($request->has('status') && !empty($request->input('status')))
            {
                if($request->input('status')==1)
                {
                    $forwarded_tasks->whereIn('tbl_project_tasks_approvals.approval_status', [1,2])->where('tbl_project_tasks_approvals.approver',$memid);
                }
                if($request->input('status')==0)
                {
                    $forwarded_tasks->whereIn('tbl_project_tasks_approvals.approval_status', [0])->where('tbl_project_tasks_approvals.approver',$memid);
                }
            }
            $forwarded_tasks = $forwarded_tasks->where('tbl_projects.property_id',$request->input('property_id'))->groupBy('tbl_project_template.id')->get();

            for($p=0;$p<count($forwarded_tasks);$p++)
            {
                $forwarded_tasks[$p]['forwarded_task']=1;
            }

            $task_lists = ProjectTemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->leftjoin('tbl_project_tasks_approvals','tbl_project_tasks_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_attendees_approvals','tbl_attendees_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.property_id','=','tbl_projects.property_id')->where('tbl_project_template.task_type',$request->input('task_type'))->whereNotIn('tbl_project_template.task_status', [0,1])->where('tbl_project_template.isDeleted',0)->select('tbl_project_template.*','tbl_properties_master.property_name','tbl_projects.project_name','tbl_units_master.unit_name')->where(function($query) use ($memid,$attendee){
                $query->orwhereRaw("find_in_set($memid,tbl_project_template.mem_responsible)")
                ->orWhereRaw("find_in_set($memid,tbl_project_template.approvers)")
                ->orWhereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)");
            });
            if ($request->has('project_id') && !empty($request->input('project_id')))
            {
                $task_lists->where('tbl_project_template.project_id', $request->input('project_id'));
            }
            if ($request->has('status') && !empty($request->input('status')))
            {
                if($request->input('status')==1)
                {
                    $task_lists->whereIn('tbl_project_tasks_approvals.approval_status', [1,2])->where('tbl_project_tasks_approvals.approver',$memid);
                }
                if($request->input('status')==0)
                {
                    $task_lists->whereIn('tbl_project_tasks_approvals.approval_status', [0])->where('tbl_project_tasks_approvals.approver',$memid);
                }
            }
            $task_lists = $task_lists->where('tbl_projects.property_id',$request->input('property_id'))->groupBy('tbl_project_template.id')->get()->union($forwarded_tasks);

            return $task_lists;
        }
        if($request->input('task_type')==2)
        {
            $task_lists = Projectdocs::leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_projecttasks_docs.reviewers)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_projecttasks_docs.approvers_level1)"),">",\DB::raw("'0'"))->leftjoin('users as c',\DB::raw("FIND_IN_SET(c.mem_id,tbl_projecttasks_docs.approvers_level2)"),">",\DB::raw("'0'"))->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_projecttasks_docs.project_id')->leftjoin('tbl_project_docs_approvals','tbl_project_docs_approvals.doc_id','=','tbl_projecttasks_docs.doc_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.property_id','=','tbl_projects.property_id')->whereNotIn("tbl_projecttasks_docs.doc_status",[0,8])->where(function($query) use ($memid,$attendee){
                $query->orwhereRaw("find_in_set($memid,tbl_projecttasks_docs.reviewers)")
                ->orWhereRaw("find_in_set($memid,tbl_projecttasks_docs.approvers_level1)")
                ->orWhereRaw("find_in_set($memid,tbl_projecttasks_docs.approvers_level2)");
            })->where('tbl_projects.property_id',$request->input('property_id'))->select('tbl_projecttasks_docs.doc_id','tbl_projecttasks_docs.project_id','tbl_projecttasks_docs.phase_id','tbl_projecttasks_docs.doc_header','tbl_projecttasks_docs.doc_title','tbl_projecttasks_docs.reviewers','tbl_projecttasks_docs.approvers_level1','tbl_projecttasks_docs.approvers_level2','tbl_projecttasks_docs.file_path','tbl_projecttasks_docs.comment','tbl_projecttasks_docs.actual_date','tbl_projecttasks_docs.due_date','tbl_projecttasks_docs.doc_status','tbl_properties_master.property_name','tbl_units_master.unit_name','tbl_projects.project_name')->where('tbl_projects.property_id',$request->input('property_id'));
            if ($request->has('project_id') && !empty($request->input('project_id')))
            {
                $task_lists->where('tbl_projecttasks_docs.project_id', $request->input('project_id'));
            }
            if ($request->has('status') && !empty($request->input('status')))
            {
                if($request->input('status')==1)
                {
                    $task_lists->whereIn('tbl_project_docs_approvals.approval_status', [1,2])->where('tbl_project_docs_approvals.approver_id',$memid);
                }
                if($request->input('status')==0)
                {
                    $task_lists->whereIn('tbl_project_tasks_approvals.approval_status', [0])->where('tbl_project_docs_approvals.approver_id',$memid);
                }
            }
            $task_lists = $task_lists->groupBy('doc_id')->get()->groupBy('doc_header');
            return $task_lists;
        }
        if($request->input('task_type')==3)
        {
            $work_permits = Projectworkpermit::leftjoin('tbl_workpermit_master','tbl_workpermit_master.permit_id','=','tbl_project_workpermits.work_permit_type')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_workpermits.project_id')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.property_id','=','tbl_projects.property_id')->select('tbl_project_workpermits.*','tbl_workpermit_master.permit_type','tbl_projects.project_name','tbl_properties_master.property_name','tbl_units_master.unit_name')->where('tbl_projects.property_id',$request->input('property_id'))->whereNotIn('tbl_project_workpermits.request_status',[1,2])->where(function($query) use ($memid){
                $query->orwhereRaw("find_in_set($memid,tbl_projects.assigned_rdd_members)")
                      ->orWhereRaw("find_in_set($memid,tbl_project_contact_details.member_id)");
               });
            if ($request->has('project_id') && !empty($request->input('project_id')))
            {
                $work_permits->where('tbl_project_workpermits.project_id', $request->input('project_id'));
            }
            $work_permits = $work_permits->groupBy('permit_id')->get()->groupBy('project_name');
              
            $inspections = Projectinspections::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.property_id','=','tbl_projects.property_id')->select('tbl_project_inspections.*','tbl_projects.project_name','tbl_properties_master.property_name','tbl_units_master.unit_name')->where('tbl_projects.property_id',$request->input('property_id'))->whereNotIn('tbl_project_inspections.inspection_status',[2,3,4])->where(function($query) use ($memid){
            $query->orwhereRaw("find_in_set($memid,tbl_projects.assigned_rdd_members)")
                    ->orWhereRaw("find_in_set($memid,tbl_project_contact_details.member_id)");
            });
            if ($request->has('project_id') && !empty($request->input('project_id')))
            {
                $inspections->where('tbl_project_inspections.project_id', $request->input('project_id'));
            }
            $inspections= $inspections->groupBy('inspection_id')->get()->groupBy('project_name');
            return Response::json(array("work_permits"=>$work_permits,"inspections"=>$inspections));
        }
        
    }
    function retrieveinvestortasklists($projectid,$tasktype,$memid,$memname)
    {
        $attendee = "'".$memid."-".$memname."'";
        $task_not_initiated_status = 0;
        $completed_status = 1;
        $approvers_rejected_status = 5;
        $attendee_rejected_status = 6;
        $meeting_scheduled_status = 2;
        $task_lists = ProjectTemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.property_id','=','tbl_projects.property_id')->where('tbl_project_template.task_type',$tasktype)->where('tbl_projects.project_id',$projectid)->whereNotIn('tbl_project_template.task_status', [$task_not_initiated_status,$completed_status,$meeting_scheduled_status,$attendee_rejected_status,$approvers_rejected_status])->where('tbl_project_template.isDeleted',0)->whereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)")->select('tbl_project_template.*','tbl_properties_master.property_name','tbl_projects.project_name','tbl_units_master.unit_name')->groupBy('tbl_project_template.id')->get();

        return $task_lists;
    }
    function retrievetaskApprovalstatus($projectid,$taskid)
    {
        $approval_status = Projecttasksapproval::join('users','users.mem_id','=','tbl_project_tasks_approvals.approver')->where('project_id',$projectid)->where('task_id',$taskid)->select('tbl_project_tasks_approvals.approval_id','tbl_project_tasks_approvals.approver','users.mem_name','users.mem_last_name','tbl_project_tasks_approvals.approval_status','tbl_project_tasks_approvals.task_status')->where('isDeleted',0)->orderBy('tbl_project_tasks_approvals.updated_at','DESC')->get();

        $attendee_approval_status = Projectattendeeapproval::where('project_id',$projectid)->where('task_id',$taskid)->select('tbl_attendees_approvals.approval_id','tbl_attendees_approvals.attendee','tbl_attendees_approvals.approval_status','tbl_attendees_approvals.task_status')->where('isDeleted',0)->orderBy('tbl_attendees_approvals.updated_at','DESC')->get();
        return response()->json(['approvers'=>$approval_status,'attendees' =>$attendee_approval_status], 200); 
    }
    /*Investor dashboard - Retrieve properties,assigned to investor [dropdown] */
    function retrievetenantPropertylists($memid,$memname)
    {
        $attendee = "'".$memid."-".$memname."'";
        $propertyData  = Project::join('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->join('tbl_project_template','tbl_project_template.project_id','=','tbl_projects.project_id')->join('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->whereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)")->orWhere('tbl_project_contact_details.member_id',$memid)->where('tbl_project_contact_details.member_designation','>',6)->select('tbl_properties_master.property_id','tbl_properties_master.property_name')->groupBy('tbl_properties_master.property_id')->get();

        return response()->json(['property_data'=>$propertyData], 200);
    }
    /*Investor dashboard - Retrieve projects and its properties assigned to investor [dropdown] */
    function retrievetenantProjectlists($memid,$memname,$propertyid)
    {
        $attendee = "'".$memid."-".$memname."'";
        $projectData  = Project::join('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->join('tbl_project_template','tbl_project_template.project_id','=','tbl_projects.project_id')->whereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)")->orWhere('tbl_project_contact_details.member_id',$memid)->where('tbl_project_contact_details.member_designation','>',6)->where('tbl_projects.property_id',$propertyid)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projects.property_id')->groupBy('project_id')->get();
        
        return response()->json(['project_data'=>$projectData ], 200);
    }
    /*Investor dashboard - Retrieve workspace details for chosen project */
    function retrieveInvestorProjectworkspace($projectid,$propertyid)
    {
        $project_details = Project::join('fitout_deposit_master','fitout_deposit_master.status_id','=','tbl_projects.fitout_deposit_status')->where('project_id',$projectid)->where('tbl_projects.property_id',$propertyid)->join('tbl_projecttype_master','tbl_projecttype_master.type_id','=','tbl_projects.project_type')->join('tbl_company_master','tbl_company_master.company_id','=','tbl_projects.investor_company')->join('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->join('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_projects.project_id',$projectid)->select('tbl_projects.project_id','tbl_projects.org_id','tbl_projects.project_name','tbl_projects.usage_permissions','tbl_projects.fitout_period','tbl_projects.fitout_deposit_amt','tbl_projects.fitout_deposit_filepath','tbl_projects.owner_work','tbl_projects.owner_work_amt','tbl_projects.owner_work_filepath','tbl_projects.kfd_drawing_status','tbl_projects.ivr_status','tbl_projects.ivr_amt','tbl_projects.ivr_filepath','tbl_projects.workpermit_expiry_date','tbl_projects.insurance_validity_date','tbl_projects.fif_upload_path','tbl_projects.assigned_rdd_members','tbl_projects.fitout_deposit_status','fitout_deposit_master.status_name','tbl_projects.project_type','tbl_projecttype_master.type_name','tbl_projects.investor_company','tbl_company_master.company_name','tbl_company_master.brand_name','tbl_projects.property_id','tbl_properties_master.property_name','tbl_projects.unit_id','tbl_units_master.unit_name')->get();


        $milestone_dates = Projectmilestonedates::join('tbl_projects','tbl_projects.project_id','=','tbl_project_milestone_dates.project_id')->where('tbl_projects.property_id',$propertyid)->where('tbl_project_milestone_dates.project_id',$projectid)->where('tbl_project_milestone_dates.active_status',1)->select('tbl_project_milestone_dates.date_id','tbl_project_milestone_dates.org_id','tbl_project_milestone_dates.project_id','tbl_project_milestone_dates.concept_submission','tbl_project_milestone_dates.detailed_design_submission','tbl_project_milestone_dates.unit_handover','tbl_project_milestone_dates.fitout_start','tbl_project_milestone_dates.fitout_completion','tbl_project_milestone_dates.store_opening')->get();
        $investor_dates = Projectinvestordates::join('tbl_projects','tbl_projects.project_id','=','tbl_project_investor_planned_dates.project_id')->where('tbl_projects.property_id',$propertyid)->where('tbl_project_investor_planned_dates.project_id',$projectid)->where('tbl_project_investor_planned_dates.active_status',1)->select('tbl_project_investor_planned_dates.date_id','tbl_project_investor_planned_dates.org_id','tbl_project_investor_planned_dates.project_id','tbl_project_investor_planned_dates.concept_submission','tbl_project_investor_planned_dates.detailed_design_submission','tbl_project_investor_planned_dates.fitout_start','tbl_project_investor_planned_dates.fitout_completion')->get();

        return Response::json(array('project' => $project_details,'milestone_dates' => $milestone_dates,'investor_dates' => $investor_dates));
    }
    /* Investor get document tasks assigned for uploading */
    function retrieveInvestorDoctasks(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'property_id' => 'required', 
            'org_id' => 'required', 
            'org_code' => 'required',
            'project_name' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $docs = Projectdocs::join('tbl_projects','tbl_projects.project_id', '=','tbl_projecttasks_docs.project_id')->join('tbl_properties_master','tbl_projects.property_id', '=','tbl_properties_master.property_id')->join('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_projecttasks_docs.phase_id')->select('tbl_projecttasks_docs.*','tbl_projects.project_name','tbl_properties_master.property_name','tbl_projects.org_id','tbl_phase_master.phase_name')->where('tbl_projecttasks_docs.project_id',$request->input('project_id'))->where('tbl_projects.property_id',$request->input('property_id'))->get()->groupBy('doc_header');
        foreach($docs as $x => $val)
        { 
            for($i=0;$i<count($docs[$x]);$i++)
            {
                $doc_path = public_path().'/uploads/'.$request->input('org_code').'/documents/'.$request->input('project_id').'_'.$request->input('project_name')."/".$docs[$x][$i]['phase_name']."/".$docs[$x][$i]['doc_header'];
                $img_path = public_path().'/uploads/'.$request->input('org_code').'/images/'.$request->input('project_id').'_'.$request->input('project_name')."/".$docs[$x][$i]['phase_name']."/".$docs[$x][$i]['doc_header'];
                if(!File::isDirectory($doc_path)){
                    File::makeDirectory($doc_path, 0777, true, true);
                    }
                    if(!File::isDirectory($img_path)){
                        File::makeDirectory($img_path, 0777, true, true);
                    }
                $docs[$x][$i]['doc_path'] = $doc_path;
                $docs[$x][$i]['img_path'] = $img_path;

            }
        }
        return response()->json(['docs'=>$docs], 200);
    }
    /* Investor perform document tasks action and updating */
    function investordocActions(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $datas = $request->get('datas');
        $data = array();
        $validator = Validator::make($request->all(), [ 
            'datas.*.project_id' => 'required', 
            'datas.*.phase_id' => 'required',
            'datas.*.user_id' => 'required',
            'datas.*.docs.*.doc_id' => 'required',
            // 'datas.*.docs.*.file_name' => 'required',
            'datas.*.docs.*.file_path' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $doc_uploaded_status=1;
        
        for($i=0;$i<count($datas);$i++) 
        {
            for($k=0;$k<count($datas[$i]['docs']);$k++)
            {
                $templates = Projectdocs::where("project_id",$datas[$i]['project_id'])->where("phase_id",$datas[$i]['phase_id'])->where("doc_id",$datas[$i]['docs'][$k]['doc_id'])->update( 
                    array( 
                     "doc_status" => $doc_uploaded_status,
                     "uploaded_by"  => $datas[$i]['user_id'],
                     ));
                     $file_name = $datas[$i]['docs'][$k]['file_name']?$datas[$i]['docs'][$k]['file_name']:null;
                     $docsHistory[] = [
                        'project_id' => $datas[$i]['project_id'],
                        'doc_id' => $datas[$i]['docs'][$k]['doc_id'],
                        'file_name' => $file_name,
                        "file_path"  => $datas[$i]['docs'][$k]['file_path'],
                        "uploaded_by"  => $datas[$i]['user_id'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];    
            }
        }
        $approverAssigningStatus = $this->assignApprovers($datas[0]['project_id'],$datas[0]['docs'][0]['doc_id']);
        $taskdata = Projectdocs::join('tbl_projects','tbl_projects.project_id','=','tbl_projecttasks_docs.project_id')->where('tbl_projecttasks_docs.project_id',$datas[0]['project_id'])->where('tbl_projecttasks_docs.phase_id',$datas[0]['phase_id'])->where('tbl_projecttasks_docs.doc_id',$datas[0]['docs'][0]['doc_id'])->select('tbl_projecttasks_docs.*','tbl_projects.project_name','tbl_projects.investor_brand')->first();
        $reviewers = explode(',',$taskdata['reviewers']);
        $reviewerNotifications = array();
        for($f=0;$f<count($reviewers);$f++)
        {
            $reviewerNotifications[] = [
                "project_id" => $datas[0]['project_id'],
                "content" =>  $taskdata['doc_title']." file for Project ".$taskdata['project_name']." has been Uploaded",
                "user" => $reviewers[$f],
                "user_type" => 1,
                "notification_type"=>env('NOTIFY_DOCUMENT'),
                "created_at" => $created_at,
                "updated_at" => $updated_at
            ];
        }
        $tenant_details = Tenant::where('tenant_id',$datas[0]['user_id'])->select('tenant_id','tenant_name','tenant_last_name','email as tenant_email')->get();

        if(count($tenant_details)>0)
        {
            $emaildata = array();
            $emaildata = [
                "tenant_name" => $tenant_details[0]['tenant_name'],
                "tenant_last_name" => $tenant_details[0]['tenant_last_name'],
                "investor_brand" => $taskdata['investor_brand'],
                "tenant_email" => $tenant_details[0]['tenant_email'],
                "doc_header" => $taskdata['doc_header']

            ];
            Mail::send('emails.drawingsubmission', $emaildata, function($message)use($emaildata) {
                $message->to($emaildata['tenant_email'])
                        ->subject('RDD - Drawings Submission');
                });
        }   
        Notifications::insert($reviewerNotifications);
        Projectdocshistory::insert($docsHistory);
        return response()->json(['response'=>"Document Upload action has been registered"], 200);
    }
    function getDochistory($docid)
    {
        $history = Projectdocshistory::where('doc_id',$docid)->where('isDeleted',0)->get();
        $member_details = Projectdocs::where('doc_id',$docid)->select('reviewers','approvers_level1','approvers_level2')->get();
        return response()->json(['approvers'=>$member_details,'document_history'=>$history], 200);
    }
    function rddperformApprovaldocaction(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'doc_id' => 'required', 
            'approver_id' => 'required',
            'approver_type' => 'required',
            'approval_status' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $memid = $request->input('approver_id');
        $approval_status=$request->input('approval_status');
        $rejection_status=2;
        $reuploaded_status=3;
        $doc_uploaded_status=1;
        $yet_to_start = 0;
        $reviewers_approved_status = 2;
        $reviewers_resubmit = 3;
        $approved_status=1;
        $all_approved_status=8;
        $app1_approved_status=4;
        $app1_resubmit_status=5;
        $app2_resubmit_status=7;
        $updated_at = date('Y-m-d H:i:s');
        $created_at = date('Y-m-d H:i:s');

        $taskdata = Projectdocs::join('tbl_projects','tbl_projects.project_id','=','tbl_projecttasks_docs.project_id')->where('tbl_projecttasks_docs.project_id',$request->input('project_id'))->where('tbl_projecttasks_docs.doc_id',$request->input('doc_id'))->select('tbl_projecttasks_docs.*','tbl_projects.project_name')->first();
        //check if user is authorised for approval action
        if($request->input('approver_type')==1)
        {
            $rev_count = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,reviewers)")->count();
            if($rev_count==0)
            {
                return response()->json(['response'=>"Not a valid Reviewer for Approval Action"], 410);
            }
            else
            {
                Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,reviewers)")->update(
                    array(
                        "comment" => $request->input('comment'),
                        "updated_at" => $updated_at
                    )
                );
                $docuploadcountQuery = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,reviewers)")->where('doc_status',$doc_uploaded_status)->count();
                if($docuploadcountQuery>0)
                {
                    //check if this user already made approval action
                    $userapprovecheckQuery = Projectdocs::join('tbl_project_docs_approvals','tbl_project_docs_approvals.doc_id','=','tbl_projecttasks_docs.doc_id')->where('tbl_projecttasks_docs.project_id',$request->input('project_id'))->where('tbl_projecttasks_docs.doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,reviewers)")->where('tbl_project_docs_approvals.approver_id',$memid)->where('tbl_project_docs_approvals.approval_status',$yet_to_start)->count();
                    if($userapprovecheckQuery==0)
                    {
                        return response()->json(['response'=>"Already made Approval Action"], 410);
                    }
                    //if approves
                    $approveQuery = ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approver_type',$request->input('approver_type'))->where('approver_id',$memid)->update(
                        array(
                            "approval_status"=>$approval_status
                        )
                    );
                    if($approveQuery!=0)
                    {
                        //if  approved
                        if($approval_status==1)
                        {
                            //get count of reviewers if any need to approve
                            $rev_count = ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approver_type',$request->input('approver_type'))->where('approval_status',$yet_to_start)->count();
                            if($rev_count==0)
                            {
                                //notification part
                                $approvers1Notifications = array();
                                $approvers1 = explode(',',$taskdata['approvers_level1']);
                                for($f=0;$f<count($approvers1);$f++)
                                {
                                    $approverNotifications[]=[
                                        "project_id" => $request->input('project_id'),
                                        "content" =>  $taskdata['doc_title']." file for Project ".$taskdata['project_name']." has been Uploaded.Kindly make approval action",
                                        "user" => $approvers1[$f],
                                        "user_type" => 1,
                                        "notification_type"=>env('NOTIFY_DOCUMENT'),
                                        "created_at" => $created_at,
                                        "updated_at" => $updated_at
                                    ];
                                }
                                Notifications::insert($approvers1Notifications);
                                //update doc task status
                                Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                    array('doc_status'=>$reviewers_approved_status)
                                );
                                return response()->json(['response'=>"Document Approved Successfully"], 200);
                            }
                            else
                            {
                                return response()->json(['response'=>"Document Approved Successfully"], 200);
                            }
                        }
                        if($approval_status==2)
                        {
                            //update all other approvers to initial stage
                            ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "approval_status"=>$yet_to_start
                                )
                            );
                            //update doc history to rejected state
                            Projectdocshistory::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approval_status',$yet_to_start)->update(
                                array(
                                    "approval_status"=>$rejection_status,
                                    "updated_at" => $updated_at
                                )
                            );
                            //update doc tasks status
                            Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "doc_status"=>$reviewers_resubmit
                                )
                            );
                            //rejected redirect flow to investor for re upload
                            return response()->json(['response'=>"Document has been declared to resubmit"], 200);
                        }
                        if($approval_status==3)
                        {
                            //for uploading from reviewer,make entry in docs history
                            $history = new Projectdocshistory();
                            Projectdocshistory::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approval_status',$yet_to_start)->update(
                                array(
                                    "approval_status"=>$rejection_status
                                )
                            );
                            for($l=0;$l<count($request->input('file_path'));$l++)
                            {
                                $history->project_id = $request->input('project_id');
                                $history->doc_id = $request->input('doc_id');
                                // $history->file_name = $request->input('file_name');
                                $history->file_path = $request->input('file_path')[$l];
                                $history->uploaded_by = $request->input('approver_id');
                                $history->approval_status = $approved_status;
                                $history->save();
                            }
                            //update doc tasks status
                            Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('doc_status',$yet_to_start)->update(
                                array(
                                    "doc_status"=>$all_approved_status
                                )
                            );
                            //update other approvers status
                            ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "approval_status"=>$approved_status
                                )
                            );
                            return response()->json(['response'=>"Document Approved Successfully"], 200);
                        }
                    }
                }
                else
                {
                    $approvecheckQuery = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,reviewers)")->whereIn('doc_status',[$reviewers_approved_status,$all_approved_status,$app1_approved_status])->count();
                    if($approvecheckQuery>0)
                    {
                        return response()->json(['response'=>"Document Already Approved or rejected"], 410);
                    }
                    else
                    {
                        //doc not uploaded
                        return response()->json(['response'=>"Document not uploaded"], 410);
                    }
                }
                
            }

        }
        if($request->input('approver_type')==2)
        {
            $rev_count = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level1)")->count();
            if($rev_count==0)
            {
                return response()->json(['response'=>"Not a valid level1 approver for Approval Action"], 410);
            }
            else
            {
                Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level1)")->update(
                    array(
                        "comment" => $request->input('comment')
                    )
                );
                $docrevapprovecountQuery = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level1)")->where('doc_status',$reviewers_approved_status)->count();
                if($docrevapprovecountQuery>0)
                {
                    //check if this user already made approval action
                    $userapprovecheckQuery = Projectdocs::join('tbl_project_docs_approvals','tbl_project_docs_approvals.doc_id','=','tbl_projecttasks_docs.doc_id')->where('tbl_projecttasks_docs.project_id',$request->input('project_id'))->where('tbl_projecttasks_docs.doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level1)")->where('tbl_project_docs_approvals.approver_id',$memid)->where('tbl_project_docs_approvals.approval_status',$yet_to_start)->count();
                    if($userapprovecheckQuery==0)
                    {
                        return response()->json(['response'=>"Already made Approval Action"], 410);
                    }
                    //if approves
                    $approveQuery = ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approver_type',$request->input('approver_type'))->where('approver_id',$memid)->update(
                        array(
                            "approval_status"=>$approval_status
                        )
                    );
                    if($approveQuery!=0)
                    {
                        //if  approved
                        if($approval_status==1)
                        {
                            //get count of approvers_level1 if any need to approve
                            $app1_count = ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approver_type',$request->input('approver_type'))->where('approval_status',$yet_to_start)->count();
                            if($app1_count==0)
                            {
                                //update doc task status
                                Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                    array('doc_status'=>$app1_approved_status)
                                );
                                return response()->json(['response'=>"Document Approved Successfully"], 200);
                            }
                            else
                            {
                                return response()->json(['response'=>"Document Approved Successfully"], 200);
                            }
                        }
                        if($approval_status==2)
                        {
                            //update all other approvers to initial stage
                            ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "approval_status"=>$yet_to_start
                                )
                            );
                            //update doc history to rejected state
                            Projectdocshistory::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approval_status',$yet_to_start)->update(
                                array(
                                    "approval_status"=>$rejection_status
                                )
                            );
                            //update doc tasks status
                            Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "doc_status"=>$app1_resubmit_status
                                )
                            );
                            //rejected redirect flow to investor for re upload
                            return response()->json(['response'=>"Document has been declared to resubmit"], 200);
                        }
                        if($approval_status==3)
                        {
                            //for uploading from approver_level1,make entry in docs history
                            $history = new Projectdocshistory();
                            Projectdocshistory::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approval_status',$yet_to_start)->update(
                                array(
                                    "approval_status"=>$rejection_status
                                )
                            );
                            $history->project_id = $request->input('project_id');
                            $history->doc_id = $request->input('doc_id');
                            $history->file_name = $request->input('file_name');
                            $history->file_path = $request->input('file_path');
                            $history->uploaded_by = $request->input('approver_id');
                            $history->approval_status = $approved_status;
                            $history->save();
                            //update doc tasks status
                            Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('doc_status',$yet_to_start)->update(
                                array(
                                    "doc_status"=>$all_approved_status
                                )
                            );
                            //update other approvers status
                            ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "approval_status"=>$approved_status
                                )
                            );
                            return response()->json(['response'=>"Document Approved Successfully"], 200);
                        }
                    }
                }
                else
                {
                    $approvecheckQuery = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level1)")->whereIn('doc_status',[$all_approved_status,$app1_approved_status])->count();
                    if($approvecheckQuery>0)
                    {
                        return response()->json(['response'=>"Document Already Approved or rejected"], 410);
                    }
                    else
                    {
                       return response()->json(['response'=>"Reviewers for this document not yet approved"], 410);
                    }
                }
            }
        }
        if($request->input('approver_type')==3)
        {
            $rev_count = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level2)")->count();
            if($rev_count==0)
            {
                return response()->json(['response'=>"Not a valid level2 approver for Approval Action"], 410);
            }
            else
            {
                Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level2)")->update(
                    array(
                        "comment" => $request->input('comment')
                    )
                );
                $docapp1approvecountQuery = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level2)")->where('doc_status',$app1_approved_status)->count();
                if($docapp1approvecountQuery>0)
                {
                    $userapprovecheckQuery = Projectdocs::join('tbl_project_docs_approvals','tbl_project_docs_approvals.doc_id','=','tbl_projecttasks_docs.doc_id')->where('tbl_projecttasks_docs.project_id',$request->input('project_id'))->where('tbl_projecttasks_docs.doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level2)")->where('tbl_project_docs_approvals.approver_id',$memid)->where('tbl_project_docs_approvals.approval_status',$yet_to_start)->count();
                    if($userapprovecheckQuery==0)
                    {
                        return response()->json(['response'=>"Already made Approval Action"], 410);
                    }
                    $approveQuery = ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approver_type',$request->input('approver_type'))->where('approver_id',$memid)->update(
                        array(
                            "approval_status"=>$approval_status
                        )
                    );
                    if($approveQuery!=0)
                    {
                        if($approval_status==1)
                        {
                            //get count of approvers_level2 if any need to approve
                            $rev_count = ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approver_type',$request->input('approver_type'))->where('approval_status',$yet_to_start)->count();
                            if($rev_count==0)
                            {
                                //update doc task status
                                Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                    array('doc_status'=>$all_approved_status)
                                );
                                return response()->json(['response'=>"Document Approved Successfully"], 200);
                            }
                            else
                            {
                                return response()->json(['response'=>"Document Approved Successfully"], 200);
                            }
                        }
                        if($approval_status==2)
                        {
                            //update all other approvers to initial stage
                            ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "approval_status"=>$yet_to_start
                                )
                            );
                            //update doc history to rejected state
                            Projectdocshistory::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approval_status',$yet_to_start)->update(
                                array(
                                    "approval_status"=>$rejection_status
                                )
                            );
                            //update doc tasks status
                            Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "doc_status"=>$app2_resubmit_status
                                )
                            );
                            //rejected redirect flow to investor for re upload
                            return response()->json(['response'=>"Document has been declared to resubmit"], 200);
                        }
                        if($approval_status==3)
                        {
                            //for uploading from approver_level1,make entry in docs history
                            $history = new Projectdocshistory();
                            Projectdocshistory::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('approval_status',$yet_to_start)->update(
                                array(
                                    "approval_status"=>$rejection_status
                                )
                            );
                            $history->project_id = $request->input('project_id');
                            $history->doc_id = $request->input('doc_id');
                            $history->file_name = $request->input('file_name');
                            $history->file_path = $request->input('file_path');
                            $history->uploaded_by = $request->input('approver_id');
                            $history->approval_status = $approved_status;
                            $history->save();
                            //update doc tasks status
                            Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('doc_status',$app1_approved_status)->update(
                                array(
                                    "doc_status"=>$all_approved_status
                                )
                            );
                            //update other approvers status
                            ProjectdocsApproval::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->update(
                                array(
                                    "approval_status"=>$approved_status
                                )
                            );
                            return response()->json(['response'=>"Document Approved Successfully"], 200); 
                        }
                    }
                }
                else
                {
                    $approvecheckQuery = Projectdocs::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->WhereRaw("find_in_set($memid,approvers_level2)")->whereIn('doc_status',[$all_approved_status])->count();
                    if($approvecheckQuery>0)
                    {
                        return response()->json(['response'=>"Document Already Approved or rejected"], 410);
                    }
                    else
                    {
                        return response()->json(['response'=>"Approvers level 1 for this document not yet approved"], 410);
                    }
                }
            }
        }
    }
    /* Investor Phase wise details retrieval */
    function investorretrieveProjectPhase($projectid,$phase_id)
    {
        $project_details = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->join('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_template.phase_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status',DB::raw("GROUP_CONCAT(DISTINCT a.mem_name) as member_responsible_person"),DB::raw("GROUP_CONCAT(DISTINCT b.mem_name) as approvers_person"),'tbl_phase_master.phase_name','tbl_project_template.org_id','tbl_projects.project_name')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_project_template.mem_responsible)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$projectid)->where('tbl_project_template.phase_id',$phase_id)->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
       
        $docs_details = Projectdocs::select('doc_id','project_id','phase_id','doc_header','doc_title','reviewers','approvers_level1','approvers_level2','file_path','comment','actual_date','due_date','doc_status')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_projecttasks_docs.reviewers)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_projecttasks_docs.approvers_level1)"),">",\DB::raw("'0'"))->leftjoin('users as c',\DB::raw("FIND_IN_SET(c.mem_id,tbl_projecttasks_docs.approvers_level2)"),">",\DB::raw("'0'"))->where('tbl_projecttasks_docs.isDeleted',0)->where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.phase_id',$phase_id)->get()->groupBy('doc_header');

        $permit_details = Projectworkpermit::join('tbl_workpermit_master','tbl_workpermit_master.permit_id','=','tbl_project_workpermits.work_permit_type')->select('tbl_project_workpermits.permit_id','tbl_project_workpermits.project_id','tbl_project_workpermits.work_permit_type','tbl_project_workpermits.file_path','tbl_project_workpermits.drawing_path','tbl_project_workpermits.start_date','tbl_project_workpermits.end_date','tbl_project_workpermits.description','tbl_project_workpermits.checklist_file_path','tbl_project_workpermits.request_status','tbl_project_workpermits.investor_id','tbl_workpermit_master.permit_type')->where('project_id',$projectid)->get();

        $inspection_details = Projectinspections::select('inspection_id','project_id','inspection_type','requested_time','checklist_id','comments','inspection_status','investor_id')->where('project_id',$projectid)->get();

        $siteinspection = SiteInspectionReport::where('project_id',$projectid)->where('isDeleted',0)->get();
        $fitout_certificates = FitoutCompletionCertificates::where('project_id',$projectid)->where('isDeleted',0)->get();
        $preopening_docs = Preopeningdocs::where('project_id',$projectid)->where('isDeleted',0)->get();
        $fitout_refund = FitoutDepositrefund::where('project_id',$projectid)->where('isDeleted',0)->get();

        $paths = Docpathconfig::where('org_id',$project_details[0]['org_id'])->where('isDeleted',0)->get();
        $doc_path = public_path()."".$paths[0]['doc_path']."".$project_details[0]['project_id']."_".$project_details[0]['project_name']."/".$project_details[0]['phase_name'];
        $img_path = public_path()."".$paths[0]['image_path']."".$project_details[0]['project_id']."_".$project_details[0]['project_name']."/".$project_details[0]['phase_name'];
        if(!File::isDirectory($doc_path)){
            File::makeDirectory($doc_path, 0777, true, true);
           }
           if(!File::isDirectory($img_path)){
               File::makeDirectory($img_path, 0777, true, true);
           }

        return Response::json(array('project' => $project_details,'doc_details' => $docs_details,'requested_permits'=>$permit_details,'requested_inspections' => $inspection_details,"fitout_completion_certificates"=>$fitout_certificates,"pre_opening_docs"=>$preopening_docs,"fitout_deposit_refund"=> $fitout_refund,"doc_path"=>$doc_path,"image_path"=>$img_path));
    }
    /*RDD member get checklist[dep status,insurance validity,meeting status] for workpermit */
    function rddRetrieveworkpermitstatus($projectid)
    {
        $pre_date = date('Y-m-d');
        $insurance_validity=0;
        $fitout_deposit_status=0;
        $meeting_status=0;
        $paid_status=16;
        $status = Project::where('tbl_projects.project_id',$projectid)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projects.insurance_validity_date','tbl_projects.fitout_deposit_status')->get();
        $meetingStatus = Projecttemplate::where('project_id',$projectid)->where('phase_id',1)->where('task_type',1)->where('task_status',1)->count();
        if($pre_date<=$status[0]['insurance_validity_date'])
        {
            $insurance_validity=1;
        }
        if($status[0]['fitout_deposit_status']==$paid_status)
        {
            $fitout_deposit_status=1;
        }
        if($meetingStatus>0)
        {
            $meeting_status=1;
        }
        return Response::json(['insurance_expiry_status' => $insurance_validity,'fitout_deposit_status' => $fitout_deposit_status,'induction_meeting_status'=>$meeting_status]);
    }
    /*RDD member get checklist[dep status,insurance validity,meeting status] for FCC */
    function rddRetrievefcccheckliststatus($projectid)
    {
        $pre_date = date('Y-m-d');
        $fitout_deposit_status=0;
        $kfd_drawing_status=0;
        $owner_work_status=0;
        $snag_status=0;
        $paid_status=16;
        $status = Project::leftjoin('tbl_fitout_completion_certificates','tbl_fitout_completion_certificates.project_id','=','tbl_projects.project_id')->where('tbl_projects.project_id',$projectid)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projects.insurance_validity_date','tbl_projects.fitout_deposit_status','tbl_projects.kfd_drawing_status','tbl_projects.owner_work')->get();
        $snagCount = Projectinspectionitems::where('project_id',$projectid)->where('isRescheduled',0)->where('snag_type',1)->count();
        if($snagCount==0)
        {
            $snag_status=1;
        }
        if($status[0]['fitout_deposit_status']==$paid_status)
        {
            $fitout_deposit_status=1;
        }
        if($status[0]['kfd_drawing_status']==2)
        {
            $kfd_drawing_status=1;
        }
        if($status[0]['owner_work']==1)
        {
            $owner_work_status=1;
        }
        return Response::json(array("response"=>['kfd_drawing_status' => $kfd_drawing_status,'fitout_deposit_status' => $fitout_deposit_status,'snag_status' => $snag_status,'owner_work'=> $owner_work_status]));
    }
    /*RDD member get checklist for fitout deposit refund */
    function rddRetrievefitoutdepositcheckliststatus($projectid)
    {
        $pre_date = date('Y-m-d');
        $fitout_deposit_status=0;
        $fcc_generated_status=0;
        $kfd_drawing_status=0;
        $owner_work_status=0;
        $snag_status=0;
        $paid_status=16;
        $status = Project::leftjoin('tbl_fitout_completion_certificates','tbl_fitout_completion_certificates.project_id','=','tbl_projects.project_id')->where('tbl_projects.project_id',$projectid)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projects.insurance_validity_date','tbl_projects.fitout_deposit_status','tbl_projects.kfd_drawing_status','tbl_projects.owner_work','tbl_fitout_completion_certificates.isGenerated')->get();
        $snagCount = Projectinspectionitems::where('project_id',$projectid)->where('isRescheduled',0)->where('snag_type',1)->count();
        if($snagCount==0)
        {
            $snag_status=1;
        }
        if($status[0]['fitout_deposit_status']==$paid_status)
        {
            $fitout_deposit_status=1;
        }
        if($status[0]['kfd_drawing_status']==2)
        {
            $kfd_drawing_status=1;
        }
        if($status[0]['isGenerated']==1)
        {
            $fcc_generated_status=1;
        }
        if($status[0]['owner_work']==1)
        {
            $owner_work_status=1;
        }
        return Response::json(array("response"=>['kfd_drawing_status' => $kfd_drawing_status,'fitout_deposit_status' => $fitout_deposit_status,'fcc_generated_status'=>$fcc_generated_status,'snag_status' => $snag_status,'owner_work'=> $owner_work_status]));
    }
    /*Investor Getting active task count - for dashboard */
    function investorgetActivetasks(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required', 
            'user_id' => 'required',
            'memname' => 'required', 
            "project_id" => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $memid = $request->input('user_id');
        $memname = $request->input('memname');

		$attendee = "'".$memid."-".$memname."'";
        $meeting_task_count = 0;
        $document_task_count = 0;
        $work_permit_count = 0;
        $inspection_count = 0;
        $meeting_task_count = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->whereNotIn("tbl_project_template.task_status",[0,1,2,5,6])->where("tbl_project_template.task_type",1)->where(function($query) use ($attendee){
                $query->orWhereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)");
            })->where('tbl_projects.property_id',$request->input('property_id'))->where('tbl_projects.project_id',$request->input('project_id'))->count();
         $document_task_count = Projectdocs::join('tbl_projects','tbl_projects.project_id','=','tbl_projecttasks_docs.project_id')->whereNotIn("tbl_projecttasks_docs.doc_status",[0])->where('tbl_projects.property_id',$request->input('property_id'))->where('tbl_projects.project_id',$request->input('project_id'))->count();
         $work_permit_count = Projectworkpermit::where('tbl_project_workpermits.project_id',$request->input('project_id'))->where('tbl_project_workpermits.isDeleted',0)->where('tbl_project_workpermits.rdd_member_id','!=',0)->count();
         $inspection_count = Projectinspections::where('tbl_project_inspections.project_id',$request->input('project_id'))->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.rdd_member_id','!=',0)->count();

         return Response::json(array("response"=> ['meeting_task_count' => $meeting_task_count,'document_task_count' => $document_task_count,'work_permit_count'=>$work_permit_count,'inspection_count' => $inspection_count]));
    }
    /* Investor retrieving doc path for pre opening docs */
    function investorgetPredocspath(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'doc_path' => 'required', 
            'project_id' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $phase_name = "Completion phase";
        //get project details
        $projectDetails = Project::where('project_id',$request->input('project_id'))->first();

        $doc_path = public_path()."".$request->input('doc_path')."".$projectDetails['project_id']."_".$projectDetails['project_name']."/".$phase_name."/Preopening docs";
           if(!File::isDirectory($doc_path)){
               File::makeDirectory($doc_path, 0777, true, true);
           }
           
        return Response::json(['doc_path' => $doc_path]);
    }
    /* Investor - Create Preopening docs */
    function investorcreatePredoc(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [ 
            'submitted_file_path' => 'required', 
            'project_id' => 'required',
            'doc_title' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $projectDetails = Project::leftjoin('tbl_project_milestone_dates','tbl_project_milestone_dates.project_id','=','tbl_projects.project_id')->where('tbl_projects.project_id',$request->input('project_id'))->get();

        $predoc = new Preopeningdocs();

        $predoc->project_id = $request->input('project_id');
        $predoc->doc_title = $request->input('doc_title');
        $predoc->submitted_file_path = $request->input('submitted_file_path');
        $predoc->due_date = $projectDetails[0]['store_opening'];
        $predoc->created_at = $created_at;
        $predoc->updated_at = $updated_at;

        if($predoc->save())
        {
            $returnData = $predoc->find($predoc->id);
            $data = array ("message" => 'Pre Opening doc added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            return $response;
        }
    }
    function investorgetPredocslist($projectid)
    {
        $predocs = Preopeningdocs::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_preopening_docs.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->where('tbl_preopening_docs.project_id',$projectid)->select('tbl_preopening_docs.*','tbl_projects.project_name','tbl_properties_master.property_name')->groupBy('tbl_preopening_docs.id')->get();
        return response()->json(['pre_opening_docs'=>$predocs], 200); 
    }
    /*RDD Member - Work Permit Approval action */
    function rddworkPermitApproval(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [ 
            'permit_id' => 'required', 
            'project_id' => 'required',
            'request_status' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        //check if already approved or rejected
        $checkCount = Projectworkpermit::where("project_id",$request->input('project_id'))->where("permit_id",$request->input('permit_id'))->whereIn("request_status",[1,2])->count();
        if($checkCount>0)
        {
            return response()->json(['response'=>"Already approval action done on this work permit"], 410);
        }
        else
        {
            $updateQuery = Projectworkpermit::where("project_id",$request->input('project_id'))->where("permit_id",$request->input('permit_id'))->update(
                array(
                    "request_status" => $request->input('request_status'),
                    "updated_at" => $updated_at
                )
            );
            $returnData = Projectworkpermit::find($request->input('permit_id'));
            return response()->json(['response'=>"Work Permit Status Updated Succesfully",'work_permit'=>$returnData], 200);
        }
    }
    /* RDD Member - Inspection Approval action */
    function rddinspectionsApproval(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [ 
            'inspection_id' => 'required', 
            'project_id' => 'required',
            'inspection_status' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        //check if already approved or rejected
        $checkCount = Projectinspections::where("project_id",$request->input('project_id'))->where("inspection_id",$request->input('inspection_id'))->whereIn("inspection_status",[2,3,4])->count();
        if($checkCount>0)
        {
            return response()->json(['response'=>"Already approval action done on this inspection"], 410);
        }
        else
        {
            Projectinspections::where("project_id",$request->input('project_id'))->where("inspection_id",$request->input('inspection_id'))->update(
                array(
                    "inspection_status" => $request->input('inspection_status'),
                    "updated_at" => $updated_at
                )
            );
            $returnData = Projectinspections::find($request->input('inspection_id'));
            return response()->json(['response'=>"Inspection Status Updated Succesfully",'inpsection'=>$returnData], 200);
        }
    }
    function investorretrievetaskApprovalstatus($projectid,$taskid)
    {
        // $approval_status = Projecttasksapproval::join('users','users.mem_id','=','tbl_project_tasks_approvals.approver')->where('project_id',$projectid)->where('task_id',$taskid)->select('tbl_project_tasks_approvals.approval_id','tbl_project_tasks_approvals.approver','users.mem_name','users.mem_last_name','tbl_project_tasks_approvals.approval_status','tbl_project_tasks_approvals.task_status')->where('isDeleted',0)->orderBy('tbl_project_tasks_approvals.updated_at','DESC')->get();

        $attendee_approval_status = Projectattendeeapproval::where('project_id',$projectid)->where('task_id',$taskid)->select('tbl_attendees_approvals.approval_id','tbl_attendees_approvals.attendee','tbl_attendees_approvals.approval_status','tbl_attendees_approvals.task_status')->where('isDeleted',0)->orderBy('tbl_attendees_approvals.updated_at','DESC')->get();
        return response()->json(['attendees' =>$attendee_approval_status], 200); 
    }
    /* RDD Member - Retrieve calendar meetings - dashboard */
    function rddretrieveMeetings(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required', 
            'user_id' => 'required',
            'meetings_date' => 'required',
            'mem_name' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $startDate = date('Y-m-01', strtotime($request->input('meetings_date')));
        $endDate = date('Y-m-t', strtotime($request->input('meetings_date')));
        $memid = $request->input('user_id');
        $attendee = '"'.$request->input('user_id')."-".$request->input('mem_name').'"';
        $meetings = Projecttemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->select('tbl_project_template.*','tbl_projects.project_name')->where('tbl_projects.property_id',$request->input('property_id'))->where(function($query) use ($memid,$attendee){
            $query->orwhereRaw("find_in_set($memid,tbl_project_template.mem_responsible)")
            ->orWhereRaw("find_in_set($memid,tbl_project_template.approvers)")
            ->orWhereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)");
        })->whereIn('tbl_project_template.task_status',[1,4])->whereBetween('tbl_project_template.meeting_date', [$startDate.' 00:00:00',$endDate.' 23:59:59'])->get();

        return response()->json(['response'=>$meetings], 200); 
    }
    /* RDD mark Project as complete */
    function rddprojectComplete($pid)
    {
        $startup = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',1)->get();

        $design = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',2)->get();

        /* Design Phase */
        $design_docs = Projectdocs::where("project_id",$pid)->where("phase_id",2)->select(Projectdocs::raw('count(*) as total_tasks'),Projectdocs::raw('count(IF(doc_status = 0, 1, NULL)) as pending_tasks'),Projectdocs::raw('count(IF(doc_status NOT IN (0,8), 1, NULL)) as inprogress_tasks'),Projectdocs::raw('count(IF(doc_status = 8, 1, NULL)) as Completed_tasks'))->get();

        $design[0]['total_tasks'] = $design[0]['total_tasks']+$design_docs[0]['total_tasks'];
        $design[0]['pending_tasks'] = $design[0]['pending_tasks']+$design_docs[0]['pending_tasks'];
        $design[0]['inprogress_tasks'] = $design[0]['inprogress_tasks']+$design_docs[0]['inprogress_tasks'];
        $design[0]['Completed_tasks'] = $design[0]['Completed_tasks']+$design_docs[0]['Completed_tasks'];

         /* Fitout Phase */
        $fitout = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',3)->get();

        $work_permits = Projectworkpermit::where("project_id",$pid)->select(Projectworkpermit::raw('count(*) as total_tasks'),Projectworkpermit::raw('count(IF(request_status = 0, 1, NULL)) as pending_tasks'),Projectworkpermit::raw('count(IF(request_status IN (2), 1, NULL)) as inprogress_tasks'),Projectworkpermit::raw('count(IF(request_status = 1, 1, NULL)) as Completed_tasks'))->get();
        
        $project_inspections = Projectinspections::where("project_id",$pid)->select(Projectinspections::raw('count(*) as total_tasks'),Projectinspections::raw('count(IF(report_status = 0, 1, NULL)) as pending_tasks'),Projectinspections::raw('count(IF(report_status IN (1,3), 1, NULL)) as inprogress_tasks'),Projectworkpermit::raw('count(IF(report_status = 2, 1, NULL)) as Completed_tasks'))->get();
        
        $fitout[0]['total_tasks'] = $fitout[0]['total_tasks']+intval(($work_permits[0]['total_tasks']==0)?1:$work_permits[0]['total_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?1:$project_inspections[0]['total_tasks']);
        $fitout[0]['pending_tasks'] = $fitout[0]['pending_tasks']+intval(($work_permits[0]['total_tasks']==0)?1:$work_permits[0]['pending_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?1:$project_inspections[0]['pending_tasks']);
        $fitout[0]['inprogress_tasks'] = $fitout[0]['inprogress_tasks']+intval(($work_permits[0]['total_tasks']==0)?0:$work_permits[0]['inprogress_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?0:$project_inspections[0]['inprogress_tasks']);
        $fitout[0]['Completed_tasks'] = $fitout[0]['Completed_tasks']+intval(($work_permits[0]['total_tasks']==0)?0:$work_permits[0]['Completed_tasks'])+intval(($project_inspections[0]['total_tasks']==0)?0:$project_inspections[0]['Completed_tasks']);
        
        /*Completion Phase */
        $completion = Projecttemplate::where("project_id",$pid)->select(Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->where('phase_id',4)->get();

        $fitout_completion = FitoutCompletionCertificates::where('project_id',$pid)->select(FitoutCompletionCertificates::raw('count(*) as total_tasks'),FitoutCompletionCertificates::raw('count(IF(isGenerated = 0, 1, NULL)) as pending_tasks'),FitoutCompletionCertificates::raw('count(IF(isGenerated = 1, 1, NULL)) as Completed_tasks'))->get();

        $pre_opening_completion = Preopeningdocs::where('project_id',$pid)->select(Preopeningdocs::raw('count(*) as total_tasks'),Preopeningdocs::raw('count(IF(doc_status = 0, 1, NULL)) as pending_tasks'),Preopeningdocs::raw('count(IF(doc_status IN (1,3), 1, NULL)) as inprogress_tasks'),Preopeningdocs::raw('count(IF(doc_status = 2, 1, NULL)) as Completed_tasks'))->get();

        $fitout_deposit_refund = FitoutDepositrefund::where('project_id',$pid)->select(FitoutDepositrefund::raw('count(*) as total_tasks'),FitoutDepositrefund::raw('count(IF(isdrfGenerated = 0, 1, NULL)) as pending_tasks'),Preopeningdocs::raw('count(IF(isdrfGenerated = 1, 1, NULL)) as Completed_tasks'))->get();

        $completion[0]['total_tasks'] = $completion[0]['total_tasks']+$fitout_completion[0]['total_tasks']+$pre_opening_completion[0]['total_tasks']+$fitout_deposit_refund[0]['total_tasks'];
        $completion[0]['pending_tasks'] = $completion[0]['pending_tasks']+$fitout_completion[0]['pending_tasks']+$pre_opening_completion[0]['pending_tasks']+$fitout_deposit_refund[0]['pending_tasks'];
        $completion[0]['inprogress_tasks'] = $completion[0]['inprogress_tasks']+$pre_opening_completion[0]['inprogress_tasks'];
        $completion[0]['Completed_tasks'] = $completion[0]['Completed_tasks']+$fitout_completion[0]['Completed_tasks']+$pre_opening_completion[0]['Completed_tasks']+$fitout_deposit_refund[0]['Completed_tasks'];
        
        if((intval($startup[0]['total_tasks']) == intval($startup[0]['Completed_tasks'])) && (intval($design[0]['total_tasks']) == intval($design[0]['Completed_tasks'])) && (intval($fitout[0]['total_tasks']) == intval($fitout[0]['Completed_tasks'])) && (intval($completion[0]['total_tasks']) == intval($completion[0]['Completed_tasks'])))
        {
            $projectUpdate = Project::where('project_id',$pid)->update(
                array(
                    "project_status" =>1
                )
            );
            if($projectUpdate!=0)
            {
                return response()->json(['response'=>"Project Completion status Updated"], 200);
            }
            else
            {
                return response()->json(['response'=>"Project Completion status not Updated"], 410);
            }
        }
        else
        {
            return response()->json(['response'=>"Project tasks not yet Completed"], 410); 
        }
    } 
    /* Send Notify about doc to investor */
    function rddSenddocmailtoinvestor(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'doc_header' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $doc_header = $request->input('doc_header');
        $tenant_details = Project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->where('tbl_projects.project_id',$request->input('project_id'))->where('tbl_project_contact_details.member_designation',13)->select('tbl_tenant_master.*','tbl_projects.investor_brand')->get();

        $emaildata = [
            "tenant_name" => $tenant_details[0]['tenant_name'],
            "tenant_last_name" => $tenant_details[0]['tenant_last_name'],
            "investor_brand" => $tenant_details[0]['investor_brand'],
            "tenant_email" => $tenant_details[0]['email'],
            "doc_header" => $doc_header

        ];
        Mail::send('emails.drawingsubmission', $emaildata, function($message)use($emaildata,$doc_header) {
            $message->to($emaildata['tenant_email'])
                    ->subject('RDD - '.$doc_header.' Drawings Submission');
            });
        if(Mail::failures())
        {
            return response()->json(['response'=>"Document notify Mail Not Sent"], 410);
        }
        else
        {
            return response()->json(['response'=>"Document notify Mail Sent to Investor"], 200);
        }
    }
    /* Send Notify about doc to manager */
    function rddSenddocmailtomanager(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'doc_header' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $doc_header = $request->input('doc_header');
        $tenant_details = Project::leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->where('tbl_projects.project_id',$request->input('project_id'))->select('users.mem_name','users.mem_last_name','users.email','tbl_projects.*')->get();

        $emaildata = [
            "tenant_name" => $tenant_details[0]['mem_name'],
            "tenant_last_name" => $tenant_details[0]['mem_last_name'],
            "investor_brand" => $tenant_details[0]['investor_brand'],
            "tenant_email" => $tenant_details[0]['email'],
            "doc_header" => $doc_header
        ];

        Mail::send('emails.drawingsubmission', $emaildata, function($message)use($emaildata,$doc_header) {
            $message->to($emaildata['tenant_email'])
                    ->subject('RDD - '.$doc_header.' Drawings Submission');
            });
        if(Mail::failures())
        {
            return response()->json(['response'=>"Document notify Mail Not Sent"], 410);
        }
        else
        {
            return response()->json(['response'=>"Document notify Mail Sent to Rdd manager"], 200);
        }

    }

    function getMomtemplate(Request $request,$project_id,$task_id)
    {
        $project_meeting_completed_status = 1;
        $task_data = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status','tbl_project_template.org_id','tbl_projects.project_name','tbl_project_template.meeting_topic')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,TRIM(tbl_project_template.mem_responsible))"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.id',$task_id)->where('tbl_project_template.task_status',$project_meeting_completed_status)->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
        if(count($task_data)>0)
        {
            $attendees = explode(',',$task_data[0]['attendees']);
        $attendees_person  = array();
        for($a=0;$a<count($attendees);$a++)
            {
                $res1 = explode('-',$attendees[$a]);
                $tenantCheck = Tenant::where('tenant_id',$res1[0])->where('tenant_name',$res1[1])->first();
                if($tenantCheck!=null)
                {
                    $attendees_person[] = $tenantCheck['tenant_name']." ".$tenantCheck['tenant_last_name'];
                }
            }

            $content_id = env('MOM_DRAFT');
            $attendees = implode(" ",$attendees_person);
            $template = Emailtemplate::where('id',$content_id)->where('isDeleted',0)->first();
            $content = $template['content'];
            $find = array("Investor_Name","Meeting_Date");
            $replace = array("INVESTOR_NAME_HERE","MEETING_DATE_HERE");
            if($task_data[0]['meeting_date']!=null && $attendees!=null)
            {
                $replace = array($attendees,date('d-m-Y', strtotime($task_data[0]['meeting_date'])));
            }
            $finalContent = str_replace($find,$replace,$content);
            return response()->json(['subject'=>$template['subject'],'content'=>$finalContent], 200);
        }
        else
        {
            return response()->json(['response'=>"Meeting Not Completed"], 411);
        }
    }
    /* Check for mulitple doc upload in doc history */
    function docsChecking(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'doc_id' => 'required',
            'file_names'=>'required',
            'doc_path' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        for($i=0;$i<count($request->input('file_names'));$i++)
        {
            $file_path = $request->input('doc_path')."/".$request->input('file_names')[$i];
            $checkQuery = Projectdocshistory::where('project_id',$request->input('project_id'))->where('doc_id',$request->input('doc_id'))->where('file_path',$file_path)->get();
            if(count($checkQuery)==0)
            {
                $history = new Projectdocshistory();
                $history->project_id = $request->input('project_id');
                $history->doc_id = $request->input('doc_id');
                $history->file_name = $request->input('file_names')[$i];
                $history->file_path = $file_path;
                $history->uploaded_by = $request->input('user_id');
                $history->approval_status = 1;
                $history->created_at = $created_at;
                $history->updated_at = $updated_at;
                $history->save();

            }
            else
            {
                continue;
            }
        }
        return response()->json(['response'=>"Document entry made"], 200);

    }
} 
