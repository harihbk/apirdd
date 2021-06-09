<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Projecttemplate;
use App\Models\Projectcontact;
use App\Models\Projectdocs;
use App\Models\Projectworkpermit;
use App\Models\Projectinspections;
use Response;
use Validator;
use DB;
use File;

class DashboardController extends Controller
{
    //retrieve rdd member task lists 
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
            $task_lists = ProjectTemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->leftjoin('tbl_project_tasks_approvals','tbl_project_tasks_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_attendees_approvals','tbl_attendees_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->leftjoin('tbl_company_master','tbl_company_master.company_id','=','tbl_projects.investor_company')->leftjoin('tbl_task_forwards','tbl_task_forwards.task_id','=','tbl_project_template.id')->where('tbl_project_template.task_type',$request->input('task_type'));
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
            $task_lists = $task_lists->whereNotIn('tbl_project_template.task_status', [0,1])->where('tbl_project_template.isDeleted',0)->select('tbl_project_template.*','tbl_properties_master.property_name','tbl_projects.project_name','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_company_master.company_name','tbl_task_forwards.task_id as forwarded_task','tbl_task_forwards.forwarded_from','tbl_task_forwards.forwarded_to')->where(function($query) use ($memid,$attendee){
                $query->orwhereRaw("find_in_set($memid,tbl_project_template.mem_responsible)")
                ->orWhereRaw("find_in_set($memid,tbl_project_template.approvers)")
                ->orWhereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)");
            });
            $task_lists = $task_lists->where('tbl_projects.property_id',$request->input('property_id'))->when(!is_null('tbl_task_forwards') , function ($query) use($memid){
                $query->orWhere('tbl_task_forwards.forwarded_to',$memid);
             });
            
            
             $task_lists = $task_lists->groupBy('tbl_project_template.id')->get();

            return $task_lists;
        }
        if($request->input('task_type')==2)
        {
            $check = null;
            $task_lists = Projectdocs::leftjoin('users as a',\DB::raw("FIND_IN_SET(a.mem_id,tbl_projecttasks_docs.reviewers)"),">",\DB::raw("'0'"))->leftjoin('users as b',\DB::raw("FIND_IN_SET(b.mem_id,tbl_projecttasks_docs.approvers_level1)"),">",\DB::raw("'0'"))->leftjoin('users as c',\DB::raw("FIND_IN_SET(c.mem_id,tbl_projecttasks_docs.approvers_level2)"),">",\DB::raw("'0'"))->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_projecttasks_docs.project_id')->leftjoin('tbl_project_docs_approvals','tbl_project_docs_approvals.doc_id','=','tbl_projecttasks_docs.doc_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->leftjoin('tbl_task_forwards','tbl_task_forwards.task_id','=','tbl_projecttasks_docs.doc_id');
            if ($request->input('project_id')!= null && $request->input('project_id')!='')
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
                    $task_lists->whereIn('tbl_project_docs_approvals.approval_status', [0])->where('tbl_project_docs_approvals.approver_id',$memid);
                }
            }

            $task_lists = $task_lists->whereNotIn("tbl_projecttasks_docs.doc_status",[0,8])->where(function($query) use ($memid,$attendee){
                $query->orwhereRaw("find_in_set($memid,tbl_projecttasks_docs.reviewers)")
                ->orWhereRaw("find_in_set($memid,tbl_projecttasks_docs.approvers_level1)")
                ->orWhereRaw("find_in_set($memid,tbl_projecttasks_docs.approvers_level2)");
            })->where('tbl_projects.property_id',$request->input('property_id'))->select('tbl_projecttasks_docs.doc_id','tbl_projecttasks_docs.project_id','tbl_projecttasks_docs.phase_id','tbl_projecttasks_docs.doc_header','tbl_projecttasks_docs.doc_title','tbl_projecttasks_docs.reviewers','tbl_projecttasks_docs.approvers_level1','tbl_projecttasks_docs.approvers_level2','tbl_projecttasks_docs.file_path','tbl_projecttasks_docs.comment','tbl_projecttasks_docs.actual_date','tbl_projecttasks_docs.due_date','tbl_projecttasks_docs.doc_status','tbl_properties_master.property_name','tbl_units_master.unit_name','tbl_projects.project_name','tbl_task_forwards.task_id as forwarded_task','tbl_task_forwards.forwarded_from','tbl_task_forwards.forwarded_to')->where('tbl_projects.property_id',$request->input('property_id'))->when(!is_null('tbl_task_forwards') , function ($query) use($memid){
                $query->orWhere('tbl_task_forwards.forwarded_to',$memid);
             });
            $task_lists = $task_lists->groupBy('doc_id')->get()->groupBy('doc_header');
            return $task_lists;
        }
        if($request->input('task_type')==3)
        {
            $work_permits = Projectworkpermit::leftjoin('tbl_workpermit_master','tbl_workpermit_master.permit_id','=','tbl_project_workpermits.work_permit_type')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_workpermits.project_id')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->select('tbl_project_workpermits.*','tbl_workpermit_master.permit_type','tbl_projects.project_name','tbl_properties_master.property_name','tbl_units_master.unit_name')->where('tbl_projects.property_id',$request->input('property_id'))->whereNotIn('tbl_project_workpermits.request_status',[1,2])->where(function($query) use ($memid){
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

            //todo tasks
            $task_lists = ProjectTemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->leftjoin('tbl_project_tasks_approvals','tbl_project_tasks_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_attendees_approvals','tbl_attendees_approvals.task_id','=','tbl_project_template.id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->leftjoin('tbl_company_master','tbl_company_master.company_id','=','tbl_projects.investor_company');
            if ($request->has('project_id') && !empty($request->input('project_id')))
            {
                $task_lists->where('tbl_project_template.project_id', $request->input('project_id'));
            }
            $task_lists = $task_lists->whereNotIn('tbl_project_template.task_status', [0,1])->where('tbl_project_template.isDeleted',0)->select('tbl_project_template.*','tbl_properties_master.property_name','tbl_projects.project_name','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_company_master.company_name')->where('tbl_projects.property_id',$request->input('property_id'))->where('tbl_project_template.task_type',2)->groupBy('tbl_project_template.id')->get();

            return Response::json(array("work_permits"=>$work_permits,"inspections"=>$inspections,"task_lists"=>$task_lists));
        }
        
    }
    //retrieve investor task lists
    function retrieveinvestortasklists($projectid,$tasktype,$memid,$memname)
    {
        $attendee = "'".$memid."-".$memname."'";
        $task_not_initiated_status = 0;
        $completed_status = 1;
        $approvers_rejected_status = 5;
        $attendee_rejected_status = 6;
        $meeting_scheduled_status = 2;
        $task_lists = ProjectTemplate::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_template.project_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_project_template.task_type',$tasktype)->where('tbl_projects.project_id',$projectid)->whereNotIn('tbl_project_template.task_status', [$task_not_initiated_status,$completed_status,$meeting_scheduled_status,$attendee_rejected_status,$approvers_rejected_status])->where('tbl_project_template.isDeleted',0)->whereRaw("find_in_set(trim($attendee),tbl_project_template.attendees)")->select('tbl_project_template.*','tbl_properties_master.property_name','tbl_projects.project_name','tbl_units_master.unit_name')->groupBy('tbl_project_template.id')->get();

        return $task_lists;
    }


}
