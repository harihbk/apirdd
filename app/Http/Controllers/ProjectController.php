<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Projecttemplate;
use Response;
use Validator;

class ProjectController extends Controller
{
    function retrieveByorg($id)
    {
        $limit = 1;
        $offset = 1;
        $projects = Project::where("org_id",$id)->get();
        echo json_encode($projects); 
    }
    function store(Request $request)
    {
        $project = new Project();
        $template = new Projecttemplate();

        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');

        $projectdata = $request->get('project');
        
        $prCheck = Project::where('project_name', $projectdata[0]['project_name'] )->first();
        if(!$prCheck == null)
        {
            return response()->json(['project'=>"Project name already exists"], 401); 
        }

        $project->org_id = $projectdata[0]['org_id'];
        $project->project_name = $projectdata[0]['project_name'];
        $project->project_type = $projectdata[0]['project_type'];
        $project->property_id = $projectdata[0]['property_id'];
        $project->unit_id = $projectdata[0]['unit_id'];
        $project->usage_permissions = $projectdata[0]['usage_permissions'];
        $project->leasing_representative = $projectdata[0]['leasing_representative'];
        $project->leasing_comments = $projectdata[0]['leasing_comments'];
        $project->fitout_period = $projectdata[0]['fitout_period'];
        $project->fitout_deposit_status = $projectdata[0]['fitout_deposit_status'];
        $project->fitout_deposit_amt = $projectdata[0]['fitout_deposit_amt'];
        $project->fitout_currency_type = $projectdata[0]['fitout_currency_type'];
        $project->insurance_validity_date = $projectdata[0]['insurance_validity_date'];
        $project->assigned_rdd_members = $projectdata[0]['assigned_rdd_members'];
        $project->investor_company = $projectdata[0]['investor_company'];
        $project->investor_brand = $projectdata[0]['investor_brand'];
        $project->created_at = date('Y-m-d H:i:s');
        $project->updated_at = date('Y-m-d H:i:s');
        $project->created_by = $projectdata[0]['user_id'];

        //Template Mapping on project Creation
        $datas = $request->get('template');

        for($i=0;$i<count($datas);$i++) 
        {
            for($k=0;$k<count($datas[$i]['tasks']);$k++)
            {
                $data[] = [
                    'project_id' => $datas[$i]['project_id'],
                    'template_id' => $datas[$i]['template_id'],
                    'template_master_id' => $datas[$i]['tasks'][$k]['template_master_id'],
                    'task_id' => $datas[$i]['tasks'][$k]['task_id'],
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
        $validator1 = Validator::make($request->all(), [ 
            'template.*.project_id' => 'required', 
            'template.*.template_id' => 'required', 
            'template.*.phase_name' => 'required',
            'template.*.phase_date' => 'required',
            'template.*.user_id' => 'required',
            'template.*.tasks.*.template_master_id' => 'required',
            'template.*.tasks.*.task_id' => 'required',
            'template.*.tasks.*.approvers' => 'required',
            'template.*.tasks.*.mem_responsible' => 'required',
            'template.*.tasks.*.duration' => 'required',
            'template.*.tasks.*.fre_id' => 'required',
            'template.*.tasks.*.start_date' => 'required',
            'template.*.tasks.*.end_date' => 'required',
            'template.*.tasks.*.duration' => 'required',
        ]);

        $validator2 = Validator::make($request->all(), [ 
            'project.*.org_id' => 'required', 
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

        if($project->save() && Projecttemplate::insert($data))
        {
            $returnData = Project::select('project_id','project_name','created_at')->find($project->project_id);
            $data = array ("message" => 'Project Created successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
        }
    }
    function update(Request $request,$id)
    {
        $project = new Project();
        $template = new Projecttemplate();

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $projectdata = $request->get('project');
        
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

            if(Projecttemplate::insert($data) || $template>0)
            {
            $returnData = Project::select('project_id','project_name','created_at')->find($project->project_id);
            $data = array ("message" => 'Project data Edited successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
           }

    }
    function getMemberProjects(Request $request,$mid)
    {
        $projects = Project::whereRaw("find_in_set($mid,assigned_rdd_members)")->get();
        echo json_encode($projects); 
    }

}
