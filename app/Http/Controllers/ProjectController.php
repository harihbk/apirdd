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
use Response;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\Projectkickoffdocs;
use App\Mail\Projectmeeting;
use App\Mail\Meetingrejection;
use App\Mail\Meetingmom;
use App\Mail\Meetingreminder;
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
    function sendMail($project_id)
    {
        $contact = Projectcontact::where('project_id',$project_id)->where('isDeleted',0)->get();
        $contact_people = array();
        for($u=0;$u<count($contact);$u++)
        {
            $contact_people[] = $contact[$u]['email'];
        }
         Mail::to($contact_people)->send(new Projectkickoffdocs());
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
        $prCheck = Project::where('project_name', $projectdata[0]['project_name'] )->first();
        if(!$prCheck == null)
        {
            return response()->json(['response'=>"Project name already exists"], 410); 
        }

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
            'project.*.investor_brand' => 'required',
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
        for($n=0;$n<count($task_members);$n++) 
        {
            $membersData[] = [
                "org_id" => $projectdata[0]['org_id'],
                "project_id" => $project_id,
                "designation" => $task_members[$n]['designation'],
                "members" => $task_members[$n]['members'],
                "members_designation" => $task_members[$n]['members_designation'],
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
            $this->sendMail($project_id);
            //map the template to project for tracking tasks
            for($r=0;$r<count($templatedetails);$r++)
            {
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
                            'planned_date' => $templatedetails[$r]['end_date'],
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

        $projects = Project::join('tbl_units_master','tbl_projects.unit_id','=','tbl_units_master.unit_id')->join('tbl_company_master','tbl_projects.investor_company','=','tbl_company_master.company_id')->whereRaw("find_in_set($user,assigned_rdd_members)")->where('tbl_projects.org_id',$request->input('org_id'));

        // $projects = Project::join('tbl_project_template','tbl_project_template.project_id','=','tbl_projects.project_id')->join('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->join('tbl_units_master','tbl_projects.unit_id','=','tbl_units_master.unit_id')->join('tbl_company_master','tbl_projects.investor_company','=','tbl_company_master.company_id')->whereRaw("find_in_set($user,assigned_rdd_members)")->orWhereRaw("find_in_set($user,tbl_project_contact_details.member_id)")->orWhereRaw("find_in_set($user,tbl_project_template.approvers)")->orWhereRaw("find_in_set($user,tbl_project_template.mem_responsible)")->where('tbl_projects.org_id',$request->input('org_id'))->where('tbl_project_contact_details.isDeleted',0);

        // $projects = Project::join('tbl_project_template','tbl_project_template.project_id','=','tbl_projects.project_id')->join('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->join('tbl_units_master','tbl_projects.unit_id','=','tbl_units_master.unit_id')->join('tbl_company_master','tbl_projects.investor_company','=','tbl_company_master.company_id')->whereRaw("find_in_set($user,assigned_rdd_members)")->where('tbl_projects.org_id',$request->input('org_id'))->where('tbl_project_contact_details.isDeleted',0);

        if ($request->input('project_name')!=null)
        {
            $projects->whereLike(['tbl_projects.project_name'], $request->input('project_name'));
        }
        if ($request->input('property')!=null)
        {
            $projects->whereLike(['tbl_projects.property_id'], $request->input('property'));
        }
        if ($request->input('unit')!=null)
        {
            $projects->whereLike(['tbl_units_master.unit_name'], $request->input('property'));
        }

        $projects = $projects->select('tbl_projects.*','tbl_units_master.unit_name','tbl_company_master.company_name','tbl_company_master.brand_name')->get();
        return $projects;
    }

    function getProjectstatus(Request $request,$pid)
    {
        $projects = Projecttemplate::where("project_id",$pid)->select("phase_name",Projecttemplate::raw('count(*) as total_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status NOT IN (0,1), 1, NULL)) as inprogress_tasks'),Projecttemplate::raw('count(IF(task_status = 0, 1, NULL)) as pending_tasks'),Projecttemplate::raw('count(IF(task_status = 1, 1, NULL)) as Completed_tasks'))->groupBy("phase_name")->orderBy("id")->get();
        echo json_encode($projects);
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
        $attendees_list = explode(',',$request->input('attendees'));
        $persons_list = explode(',',$request->input('responsible_person'));
        $attendees_array = [];
        $persons_array = [];
        if ($validator->fails()) { 
            return response()->json(['errors'=>$validator->errors()], 401);            
        }
        $memid = $request->input('approver');
        //check if user belongs to approver of this task
        $userCheck = Projecttemplate::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('id',$request->input('id'))->whereRaw("find_in_set($memid,tbl_project_template.approvers)")->get();
        if(count($userCheck)==0)
        {
            return response()->json(['response'=>"Meeting Can be Approved by Approvers Persons only"], 410);
        }
        //if approved check for others approval and schedule meeting
        if($request->input('approval_status')==1)
        {
            Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("approver",$request->input('approver'))->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );

            $approvalprogress = Projecttasksapproval::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('task_id',$request->input('id'))->where('approval_status',$yet_to_approve)->where('task_status',$inprogress_task)->count();

            if($approvalprogress>0)
            {
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Approved');
                $response = Response::json($data,200);
                echo json_encode($response);
            }
            else
            {
                /* Mail to attendees about meeting notifications */
                for($j=0;$j<count($attendees_list);$j++)
                {
                    $exp  = explode("-",$attendees_list[$j]);
                    $attendees = Members::where('mem_id',explode("-",$exp[0]))->where('mem_name',explode("-",$exp[1]))->first();
                    if($attendees==null)
                    {
                        $attendees = Tenant::where('tenant_id',explode("-",$exp[0]))->where('tenant_name',explode("-",$exp[1]))->first();
                    }    
                    $attendees_array[]= $attendees->email;  
                }
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
            $reject = Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("approver",$request->input('approver'))->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );

            if($reject>0)
            {
                /* Mail to responsible person about meeting cancelled notifications */
                for($k=0;$k<count($persons_list);$k++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$k])->first();
                    $persons_array[]= $responsible_person->email;  
                }
                Mail::to($persons_array)->send(new Meetingrejection());
                Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->update(
                    array(
                        "approval_status"=>$rejection_status,
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
        $taskdata = Projecttemplate::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('id',$request->input('id'))->first();
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
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$request->input('attendee'))->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );

            $approvalprogress = Projectattendeeapproval::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('task_id',$request->input('id'))->where('approval_status',$yet_to_approve)->where('task_status',$inprogress_task)->count();

            if($approvalprogress>0)
            {
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Approved');
                $response = Response::json($data,200);
                echo json_encode($response);
            }
            else
            {
                /* Mail to responsible person about meeting Confirmed notifications */
                for($k=0;$k<count($persons_list);$k++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$k])->first();
                    $persons_array[]= $responsible_person->email;  
                }
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
            $reject = Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$request->input('attendee'))->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );
            //attendee approvals update --attendee table
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->update(
            array(
                "approval_status"=>$rejection_status,
                "task_status" => $attendee_task_rejection_status,
                "updated_at"=>$updated_at
            ));
            //attendee approvals update --approver table
            Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->update(
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

                /* Mail to responsible person about meeting Confirmed notifications */
                for($l=0;$l<count($persons_list);$l++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$l])->first();
                    $persons_array[]= $responsible_person->email;  
                }
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
        $taskdata = Projecttemplate::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('id',$request->input('id'))->first();
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
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$request->input('attendee'))->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );

            $approvalprogress = Projectattendeeapproval::where('project_id',$project_id)->where('phase_id',$request->input('phase_id'))->where('task_id',$request->input('id'))->where('approval_status',$yet_to_approve)->where('task_status',$inprogress_task)->count();

            if($approvalprogress>0)
            {
                $returnData = Projecttemplate::find($request->input('id'));
                $data = array ("message" => 'Meeting has been Approved');
                $response = Response::json($data,200);
                echo json_encode($response);
            }
            else
            {
                /* Mail to responsible person about meeting Confirmed notifications */
                for($k=0;$k<count($persons_list);$k++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$k])->first();
                    $persons_array[]= $responsible_person->email;  
                }
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
            $reject = Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("attendee",$request->input('attendee'))->update(
                array(
                    "approval_status"=>$request->input('approval_status'),
                    "updated_at"=>$updated_at
                )
              );
            //attendee approvals update --attendee table
            Projectattendeeapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->update(
            array(
                "approval_status"=>$rejection_status,
                "task_status" => $attendee_task_rejection_status,
                "updated_at"=>$updated_at
            ));
            //attendee approvals update --approver table
            Projecttasksapproval::where("project_id",$project_id)->where("phase_id",$request->input('phase_id'))->where("task_id",$request->input('id'))->where("task_status",$inprogress_task)->update(
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

                /* Mail to responsible person about meeting Confirmed notifications */
                for($l=0;$l<count($persons_list);$l++)
                {
                    $responsible_person = Members::where('mem_id',$persons_list[$l])->first();
                    $persons_array[]= $responsible_person->email;  
                }
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
    function forwardTasks(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'template_master_id' => 'required', 
            'task_id' => 'required|numeric', 
            'task_type' => 'required',
            'forwarded_from' => 'required',
            'forwarded_to' => 'required',
            'approvers' => 'required',
            'attendees' => 'required',
            'phase_name' => 'required',
            'phase_date' => 'required',
            'mem_responsible' => 'required',
            'fre_id' => 'required',
            'duration' => 'required',
            'priority' => 'required',
            'seq_status' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'user_id' => 'required',
            'isProjecttask' => 'required',
            'task_status' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $task = new Forwardtask();

        $task->project_id = $request->input('project_id');
        $task->template_master_id = $request->input('template_master_id');
        $task->task_id = $request->input('task_id');
        $task->task_type = $request->input('task_type');
        $task->forwarded_from = $request->input('forwarded_from');
        $task->forwarded_to = $request->input('forwarded_to');
        $task->approvers = $request->input('approvers');
        $task->attendees = $request->input('attendees');
        $task->phase_name = $request->input('phase_name');
        $task->phase_date = $request->input('phase_date');
        $task->mem_responsible = $request->input('mem_responsible');
        $task->fre_id = $request->input('fre_id');
        $task->duration = $request->input('duration');
        $task->priority = $request->input('priority');
        $task->seq_status = $request->input('seq_status');
        $task->seq_no = $request->input('seq_no');
        $task->seq_char = $request->input('seq_char');
        $task->start_date =  $request->input('start_date');
        $task->end_date = $request->input('end_date');
        $task->created_at = date('Y-m-d H:i:s');
        $task->updated_at = date('Y-m-d H:i:s');
        $task->created_by = $request->input('user_id');
        $task->isProjecttask = $request->input('isProjecttask');
        $task->task_status = $request->input('task_status'); 

        if($task->save())
        {
            $tasks = Projecttemplate::where("project_id",$request->input('project_id'))->where("id",$request->input('template_master_id'))->where("task_id",$request->input('task_id'))->where("task_type",$request->input('task_type'))->update(
                array(
                    "isForwarded"=>1,
                )
            );

                 $returnData = Projecttemplate::where('template_master_id',$request->input('template_master_id'))->get();
                 $data = array ("message" => 'Task forwarded successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response);
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
    function getActivetasks(Request $request,$memid,$tasktype)
    {
        $projects = Project::join('tbl_project_template', 'tbl_projects.project_id', '=', 'tbl_project_template.project_id')->whereNotIn("tbl_project_template.task_status",[0,1])->where("tbl_project_template.task_type",$tasktype)->whereRaw("find_in_set($memid,tbl_project_template.approvers)")->orWhereRaw("find_in_set($memid,tbl_project_template.attendees)")->count();
         return $projects;
    }
    function retrieveProjectworkspace($projectid)
    {
        $project_details = Project::join('fitout_deposit_master','fitout_deposit_master.status_id','=','tbl_projects.fitout_deposit_status')->where('project_id',$projectid)->join('tbl_projecttype_master','tbl_projecttype_master.type_id','=','tbl_projects.project_type')->join('tbl_company_master','tbl_company_master.company_id','=','tbl_projects.investor_company')->join('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->join('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('project_id',$projectid)->select('tbl_projects.project_id','tbl_projects.org_id','tbl_projects.project_name','tbl_projects.usage_permissions','tbl_projects.fitout_period','tbl_projects.fitout_deposit_amt','tbl_projects.fitout_deposit_filepath','tbl_projects.owner_work','tbl_projects.owner_work_amt','tbl_projects.owner_work_filepath','tbl_projects.kfd_drawing_status','tbl_projects.ivr_status','tbl_projects.ivr_amt','tbl_projects.ivr_filepath','tbl_projects.workpermit_expiry_date','tbl_projects.insurance_validity_date','tbl_projects.fif_upload_path','tbl_projects.assigned_rdd_members','tbl_projects.fitout_deposit_status','fitout_deposit_master.status_name','tbl_projects.project_type','tbl_projecttype_master.type_name','tbl_projects.investor_company','tbl_company_master.company_name','tbl_company_master.brand_name','tbl_projects.property_id','tbl_properties_master.property_name','tbl_projects.unit_id','tbl_units_master.unit_name')->get();
        $milestone_dates = Projectmilestonedates::where('project_id',$projectid)->where('active_status',1)->select('date_id','org_id','project_id','concept_submission','detailed_design_submission','unit_handover','fitout_start','fitout_completion','store_opening')->get();
        $investor_dates = Projectinvestordates::where('project_id',$projectid)->where('active_status',1)->select('date_id','org_id','project_id','concept_submission','detailed_design_submission','fitout_start','fitout_completion')->get();
        $member_contact_details = Projectcontact::join('users','users.mem_id','=','tbl_project_contact_details.member_id')->where('project_id',$projectid)->whereNotIn('tbl_project_contact_details.member_designation', [7,8])->where('isDeleted',0)->select('tbl_project_contact_details.id','tbl_project_contact_details.project_id','tbl_project_contact_details.member_designation','tbl_project_contact_details.email','tbl_project_contact_details.mobile_number','users.mem_id','users.mem_name','users.mem_last_name')->get();
        $investor_contact_details = Projectcontact::join('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->where('project_id',$projectid)->where('isDeleted',0)->select('tbl_project_contact_details.id','tbl_project_contact_details.project_id','tbl_project_contact_details.member_designation','tbl_project_contact_details.email','tbl_project_contact_details.mobile_number','tbl_tenant_master.tenant_id','tbl_tenant_master.tenant_name')->get();

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
    function assignMembers($projectid,$phase_id)
    {
        // Mail::to("csedineshbit@gmail.com")->send(new Projectkickoffdocs());
        // check if project member assigned to this phase
        $taskcheck = Projecttemplate::where('tbl_project_template.project_id',$projectid)->where('tbl_project_template.phase_id',$phase_id)->whereNull('tbl_project_template.approvers')->whereNull('tbl_project_template.mem_responsible')->get();
        $project_members = Projectmembers::where('project_id',$projectid)->get();
        if(count($taskcheck)>0)
        {
            for($i=0;$i<count($project_members);$i++)
            {
            $query1 = Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereNull('tbl_project_template.mem_responsible')->whereRaw("find_in_set(".$project_members[$i]['designation'].",mem_responsible_designation)")->get();
            if(count($query1)>0)
            {
               Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$i]['designation'].",mem_responsible_designation)")->update(array('mem_responsible'=> $project_members[$i]['members'])); 
            }
            else
            {
                Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$i]['designation'].",mem_responsible_designation)")->update(array('mem_responsible'=>DB::raw('CONCAT(mem_responsible,", '.$project_members[$i]['members'].'")')));
            }
            $query2 = Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereNull('tbl_project_template.approvers')->whereRaw("find_in_set(".$project_members[$i]['designation'].",approvers_designation)")->get();
            if(count($query2)>0)
            {
               Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$i]['designation'].",approvers_designation)")->update(array('approvers'=> $project_members[$i]['members'])); 
            }
            else
            {
                Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$i]['designation'].",approvers_designation)")->update(array('approvers'=>DB::raw('CONCAT(approvers,", '.$project_members[$i]['members'].'")')));
            }
            $query3 = Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereNull('tbl_project_template.attendees')->whereRaw("find_in_set(".$project_members[$i]['designation'].",attendees_designation)")->get();
           
            if(count($query3)>0)
            {
                Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$i]['designation'].",attendees_designation)")->update(array('attendees'=> $project_members[$i]['members_designation']));
            }
            else
            {
                Projecttemplate::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$i]['designation'].",attendees_designation)")->update(array('attendees'=>DB::raw('CONCAT(attendees,", '.$project_members[$i]['members_designation'].'")')));
            }
            }
        }
        $doccheck = Projectdocs::where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.phase_id',$phase_id)->whereNull('tbl_projecttasks_docs.reviewers')->whereNull('tbl_projecttasks_docs.approvers_level1')->whereNull('tbl_projecttasks_docs.approvers_level2')->get();
        if(count($doccheck)>0)
        {
            for($q=0;$q<count($project_members);$q++)
            {
                $query6 = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereNull('tbl_projecttasks_docs.approvers_level2')->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level2_designation)")->get();
                if(count($query6)>0)
                {
                    $a = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level2_designation)")->update(array('approvers_level2'=> $project_members[$q]['members'])); 
                }
                if(count($query6)==0)
                {
                    $b = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level2_designation)")->update(array('approvers_level2'=>DB::raw('CONCAT(approvers_level2,", '.$project_members[$q]['members'].'")')));
                }
                $query4 = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereNull('tbl_projecttasks_docs.reviewers')->whereRaw("find_in_set(".$project_members[$q]['designation'].",reviewers_designation)")->get();
                if(count($query4)>0)
                {
                    Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$q]['designation'].",reviewers_designation)")->update(array('reviewers'=> $project_members[$q]['members'])); 
                }
                if(count($query4)==0)
                {
                    Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$q]['designation'].",reviewers_designation)")->update(array('reviewers'=>DB::raw('CONCAT(reviewers,", '.$project_members[$q]['members'].'")')));
                }
                $query5 = Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereNull('tbl_projecttasks_docs.approvers_level1')->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level1_designation)")->get();
                if(count($query5)>0)
                {
                    Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level1_designation)")->update(array('approvers_level1'=> $project_members[$q]['members'])); 
                }
                if(count($query5)==0)
                {
                    Projectdocs::where('project_id',$projectid)->where('phase_id',$phase_id)->whereRaw("find_in_set(".$project_members[$q]['designation'].",approvers_level1_designation)")->update(array('approvers_level1'=>DB::raw('CONCAT(approvers_level1,", '.$project_members[$q]['members'].'")')));
                }                
            }
        }
    }
    function retrieveProjectPhase($projectid,$phase_id)
    {
        $this->assignMembers($projectid,$phase_id);
        $project_details = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->join('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_template.phase_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status',DB::raw("GROUP_CONCAT(DISTINCT a.mem_name) as member_responsible_person"),DB::raw("GROUP_CONCAT(DISTINCT b.mem_name) as approvers_person"),'tbl_phase_master.phase_name','tbl_project_template.org_id','tbl_projects.project_name')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_project_template.mem_responsible)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$projectid)->where('tbl_project_template.phase_id',$phase_id)->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
       
        $docs_details = Projectdocs::select('doc_id','project_id','phase_id','doc_header','doc_title','reviewers','approvers_level1','approvers_level2','file_path','comment','actual_date','doc_status')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_projecttasks_docs.reviewers)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_projecttasks_docs.approvers_level1)"),">",\DB::raw("'0'"))->leftjoin('users as c',\DB::raw("FIND_IN_SET(c.mem_id,tbl_projecttasks_docs.approvers_level2)"),">",\DB::raw("'0'"))->where('tbl_projecttasks_docs.isDeleted',0)->where('tbl_projecttasks_docs.project_id',$projectid)->where('tbl_projecttasks_docs.phase_id',$phase_id)->get()->groupBy('doc_header');

        $permit_details = Projectworkpermit::join('tbl_workpermit_master','tbl_workpermit_master.permit_id','=','tbl_project_workpermits.work_permit_type')->select('tbl_project_workpermits.permit_id','tbl_project_workpermits.project_id','tbl_project_workpermits.work_permit_type','tbl_project_workpermits.file_path','tbl_project_workpermits.drawing_path','tbl_project_workpermits.start_date','tbl_project_workpermits.end_date','tbl_project_workpermits.description','tbl_project_workpermits.checklist_file_path','tbl_project_workpermits.request_status','tbl_project_workpermits.investor_id','tbl_workpermit_master.permit_type')->where('project_id',$projectid)->get();

        $inspection_details = Projectinspections::select('inspection_id','project_id','inspection_type','requested_time','checklist_id','comments','inspection_status','investor_id')->where('project_id',$projectid)->get();

        $actual_inspection_reports = Inspectionreports::where('project_id',$projectid)->where('isDeleted',0)->get();

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
            $returnData = $permit->find($permit->permit_id);
            $data = array ("message" => 'Work permit has been requested',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
        }
    }
    function investorrequestInspection(Request $request,$projectid)
    {
        $validator = Validator::make($request->all(), [ 
            'inspection_type' => 'required',
            'requested_time' => 'required', 
            'checklist_id' => 'required', 
            'investor_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $inspection = new Projectinspections();

        $inspection->project_id = $projectid;
        $inspection->inspection_type = $request->input('inspection_type');
        $inspection->requested_time = $request->input('requested_time');
        $inspection->checklist_id = $request->input('checklist_id');
        $inspection->comments = $request->input('comments');
        $inspection->investor_id = $request->input('investor_id');
        $inspection->created_at = date('Y-m-d H:i:s');
        $inspection->updated_at = date('Y-m-d H:i:s');

        if($inspection->save())
        {
            $returnData = $inspection->find($inspection->inspection_id);
            $data = array ("message" => 'Inspection has been requested',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
        }
    }
    function updatePhasedetails(Request $request,$projectid,$phaseid)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $template = $request->get('data');

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
        $returnData = Projecttemplate::select('id','org_id','project_id','task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','approvers','attendees','mem_responsible','phase_id','fre_id','duration','seq_status','seq_no','planned_date','actual_date','fif_upload_path','task_status','isDeleted')->find($projectid);
        $data = array ("message" => 'Project Edited successfully',"data" => $returnData );
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
        $designations = Templatedesignations::join('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_template_designations.designation')->get(['tbl_template_designations.id','tbl_template_designations.org_id','tbl_template_designations.designation','tbl_designation_master.designation_name','tbl_designation_master.designation_user_type'])->groupBy('designation_name');
        return response()->json($designations, 200); 
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
    function sendMommail($project_id,$task_id)
    {
        $project_meeting_completed_status = 1;
        $task_data = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status',DB::raw("GROUP_CONCAT(DISTINCT a.email) as member_responsible_person"),DB::raw("GROUP_CONCAT(DISTINCT b.email) as approvers_person"),'tbl_project_template.org_id','tbl_projects.project_name')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,TRIM(tbl_project_template.mem_responsible))"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.id',$task_id)->where('tbl_project_template.task_status',$project_meeting_completed_status)->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
        if(count($task_data)>0)
        {
            $responsible_person = array();
            $attendees_person = array();
            $person_list  = explode(',',$task_data[0]['member_responsible_person']);
            $approvers_list  = explode(',',$task_data[0]['approvers_person']);
            $attendees = explode(',',$task_data[0]['attendees']);
            for($c=0;$c<count($person_list);$c++)
            {
                $responsible_person[] = $person_list[$c];
            }
            for($b=0;$b<count($approvers_list);$b++)
            {
                $responsible_person[] = $approvers_list[$b];
            }
            for($a=0;$a<count($attendees);$a++)
            {
                $res1 = explode('-',$attendees[$a]);
                $memCheck = Members::where('mem_id',$res1[0])->where('mem_name',$res1[1])->where('active_status',1)->first();
                $tenantCheck = Tenant::where('tenant_id',$res1[0])->where('tenant_name',$res1[1])->where('active_status',1)->first();
                if($memCheck!=null)
                {
                    $attendees_person[] = $memCheck['email'];
                }
                if($tenantCheck!=null)
                {
                    $attendees_person[] = $tenantCheck['email'];
                }
            }
            Mail::to($attendees_person)->cc($responsible_person)->send(new Meetingmom());
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

        $task_data = Projecttemplate::join('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->select('tbl_project_template.id','tbl_project_template.project_id','tbl_project_template.template_id','tbl_project_template.task_type','activity_desc','meeting_date','meeting_start_time','meeting_end_time','attendees','attendees_designation','approvers','approvers_designation','tbl_project_template.phase_id','mem_responsible','mem_responsible_designation','fre_id','duration','seq_status','seq_no','planned_date','actual_date','tbl_project_template.fif_upload_path','task_status',DB::raw("GROUP_CONCAT(DISTINCT a.email) as member_responsible_person"),DB::raw("GROUP_CONCAT(DISTINCT b.email) as approvers_person"),'tbl_project_template.org_id','tbl_projects.project_name')->leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,TRIM(tbl_project_template.mem_responsible))"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_project_template.approvers)"),">",\DB::raw("'0'"))->where('tbl_project_template.project_id',$project_id)->where('tbl_project_template.id',$task_id)->whereNotIn('tbl_project_template.task_status',[$project_meeting_not_scheduled_status,$project_meeting_completed_status,$project_meeting_approver_rejected_status,$project_meeting_attendee_rejected_status])->where('tbl_project_template.isDeleted',0)->groupBy('tbl_project_template.id')->get();
        if(count($task_data)>0)
        {
            $responsible_person = array();
            $attendees_person = array();
            $person_list  = explode(',',$task_data[0]['member_responsible_person']);
            $approvers_list  = explode(',',$task_data[0]['approvers_person']);
            $attendees = explode(',',$task_data[0]['attendees']);
            for($c=0;$c<count($person_list);$c++)
            {
                $responsible_person[] = $person_list[$c];
            }
            for($b=0;$b<count($approvers_list);$b++)
            {
                $responsible_person[] = $approvers_list[$b];
            }
            for($a=0;$a<count($attendees);$a++)
            {
                $res1 = explode('-',$attendees[$a]);
                $memCheck = Members::where('mem_id',$res1[0])->where('mem_name',$res1[1])->where('active_status',1)->first();
                $tenantCheck = Tenant::where('tenant_id',$res1[0])->where('tenant_name',$res1[1])->where('active_status',1)->first();
                if($memCheck!=null)
                {
                    $attendees_person[] = $memCheck['email'];
                }
                if($tenantCheck!=null)
                {
                    $attendees_person[] = $tenantCheck['email'];
                }
            }
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
    function retrieveMembertasklists($projectid,$tasktype,$memid,$memname)
    {
        $attendee = "'".$memid."-".$memname."'";
        $task_not_initiated_status = 0;
        $task_lists = ProjectTemplate::where('project_id',$projectid)->where('task_type',$tasktype)->whereNotIn('task_status', [$task_not_initiated_status])->where('isDeleted',0)->whereRaw("find_in_set($memid,mem_responsible)")->orWhereRaw("find_in_set($memid,approvers)")->orWhereRaw("find_in_set(trim($attendee),attendees)")->get();
        return response()->json(['response'=>$task_lists], 200); 
    }
    function retrievetaskApprovalstatus($projectid,$taskid)
    {
        $approval_status = Projecttasksapproval::join('users','users.mem_id','=','tbl_project_tasks_approvals.approver')->where('project_id',$projectid)->where('task_id',$taskid)->select('tbl_project_tasks_approvals.approval_id','tbl_project_tasks_approvals.approver','users.mem_name','users.mem_last_name','tbl_project_tasks_approvals.approval_status','tbl_project_tasks_approvals.task_status')->where('isDeleted',0)->get();
        return response()->json(['response'=>$approval_status], 200); 
    }
    function retrievetenantProjectlists($memid,$memname)
    {
        $attendee = "'".$memid."-".$memname."'";
        $projectData  = Project::join('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->join('tbl_project_template','tbl_project_template.project_id','=','tbl_projects.project_id')->whereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)")->orWhere('tbl_project_contact_details.member_id',$memid)->where('tbl_project_contact_details.member_designation','>',6)->select('tbl_projects.project_id','tbl_projects.project_name')->groupBy('project_id')->get();

        $propertyData  = Project::join('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->join('tbl_project_template','tbl_project_template.project_id','=','tbl_projects.project_id')->join('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->whereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)")->orWhere('tbl_project_contact_details.member_id',$memid)->where('tbl_project_contact_details.member_designation','>',6)->select('tbl_properties_master.property_id','tbl_properties_master.property_name')->groupBy('tbl_properties_master.property_id')->get();
        
        return response()->json(['project_data'=>$projectData,'property_data' => $propertyData], 200);
    }
}

