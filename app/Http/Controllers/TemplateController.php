<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Templatemaster;
use App\Models\Templatenamemaster;
use App\Models\Templatedocs;
use App\Models\Projecttype;
use Response;
use Validator;
use DB;

class TemplateController extends Controller
{
    function store(Request $request)
    {

        $templatename = new Templatenamemaster();
        $doc_data = [];
        $datas = $request->get('datas');
        $templatename->org_id = $datas[0]['org_id'];
        $templatename->template_name = $datas[0]['template_name'];
        $templatename->created_at = date('Y-m-d H:i:s');
        $templatename->updated_at = date('Y-m-d H:i:s');
        $templatename->created_by = $datas[0]['user_id'];

        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');

        $validator = Validator::make($request->all(), [ 
            'datas.*.phase_id' => 'required', 
            'datas.*.tasks.*.person' => 'required',
            'datas.*.tasks.*.activity_desc' => 'required',
            'datas.*.tasks.*.approvers' => 'required',
            'datas.*.tasks.*.attendees' => 'required',
            'datas.*.tasks.*.fre_id' => 'required',
            'datas.*.tasks.*.start_date' => 'required',
            'datas.*.tasks.*.end_date' => 'required',
            'datas.*.tasks.*.duration' => 'required',
            'datas.*.tasks.*.task_type' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $doc_validator = Validator::make($request->all(), [ 
            'datas.*.docs.*.doc_header' => 'required',
            'datas.*.docs.*.doc_title' => 'required',
            'datas.*.docs.*.reviewers' => 'required',
            'datas.*.docs.*.approvers_level1' => 'required',
            'datas.*.docs.*.approvers_level1' => 'required',
        ]);

        if ($doc_validator->fails()) { 
            return response()->json(['error'=>$doc_validator->errors()], 401);            
        }

        if($templatename->save())
        {
            $template = $templatename->find($templatename->template_id);
        }
        else
        {
            return response()->json(['error'=>'Unable to create template'], 401); 
        }

        for($i=0;$i<count($datas);$i++) 
        {
            for($k=0;$k<count($datas[$i]['tasks']);$k++)
            {
                $data[] = [
                            'template_id' => $template->template_id,
                            'org_id' => $datas[0]['org_id'],
                            "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                            'phase_id' => $datas[$i]['phase_id'],
                            'created_by' => $datas[0]['user_id'],
                            "activity_desc" =>$datas[$i]['tasks'][$k]['activity_desc'],
                            "person" => $datas[$i]['tasks'][$k]['person'],
                            "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                            "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                            "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                            "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                            "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                            "start_date" => $datas[$i]['tasks'][$k]['start_date'],
                            "end_date" => $datas[$i]['tasks'][$k]['end_date'],
                            "file_upload_path" => $datas[$i]['tasks'][$k]['file_upload_path'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            "duration" => $datas[$i]['tasks'][$k]['duration']
                            ];
            }
            for($j=0;$j<count($datas[$i]['docs']);$j++)
            {
                $doc_data[] = [
                    'template_id' => $template->template_id,
                    'org_id' => $datas[0]['org_id'],
                    'phase_id' => $datas[$i]['docs'][$j]['phase_id'],
                    'doc_header' => $datas[$i]['docs'][$j]['doc_header'],
                    'doc_title' => $datas[$i]['docs'][$j]['doc_title'],
                    'reviewers' => $datas[$i]['docs'][$j]['reviewers'],
                    'approvers_level1' => $datas[$i]['docs'][$j]['approvers_level1'],
                    'approvers_level2' => $datas[$i]['docs'][$j]['approvers_level2'],
                   ];
            }
        }

          if(Templatemaster::insert($data) && Templatedocs::insert($doc_data))
          {
            $data = array ("message" => 'Template added successfully');
            $response = Response::json($data,200);
            echo json_encode($response);
          }
    }
    function update(Request $request,$template_id)
    {
        $datas = $request->get('datas');
        $data=[];
        $doc_data =[];
        $types = new Templatemaster();

        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');

         $update_count = 0;


         for($i=0;$i<count($datas);$i++) 
         {
            for($k=0;$k<count($datas[$i]['tasks']);$k++)
            {
                if($datas[$i]['tasks'][$k]['master_id']!=0)
                {
                    $templates = Templatemaster::where("master_id",$datas[$i]['tasks'][$k]['master_id'])->where("org_id",$datas[$i]['org_id'])->where("template_id",$datas[$i]['template_id'])->update( 
                                array( 
                                 "activity_desc" => $datas[$i]['tasks'][$k]['activity_desc'],
                                 "person" => $datas[$i]['tasks'][$k]['person'],
                                 "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                                 "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                                 "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                                 "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                                 "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                                 "start_date" => $datas[$i]['tasks'][$k]['start_date'],
                                 "end_date" => $datas[$i]['tasks'][$k]['end_date'],                 
                                 "duration" =>$datas[$i]['tasks'][$k]['duration'],
                                 "updated_at" => date('Y-m-d H:i:s'),
                                 "created_by" => $datas[$i]['user_id'],
                                 "active_status" =>$datas[$i]['tasks'][$k]['active_status'],
                                 "isDeleted" =>$datas[$i]['tasks'][$k]['isDeleted'],
                                 ));
                }
                else
                {
                    $data[] = [
                        'template_id' => $template_id,
                        'org_id' => $datas[$i]['org_id'],
                        "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                        'phase_id' => $datas[$i]['phase_id'],
                        'created_by' => $datas[$i]['user_id'],
                        "activity_desc" =>$datas[$i]['tasks'][$k]['activity_desc'],
                        "person" => $datas[$i]['tasks'][$k]['person'],
                        "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                        "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                        "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                        "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                        "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                        "start_date" => $datas[$i]['tasks'][$k]['start_date'],
                        "end_date" => $datas[$i]['tasks'][$k]['end_date'],
                        "file_upload_path" => $datas[$i]['tasks'][$k]['file_upload_path'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "duration" => $datas[$i]['tasks'][$k]['duration']
                        ];
                }
            }
            for($j=0;$j<count($datas[$i]['docs']);$j++)
            {
                if($datas[$i]['docs'][$j]['doc_id']!=0)
                {
                    //update existing doc data
                    $templates_doc = Templatedocs::where("doc_id",$datas[$i]['docs'][$j]['doc_id'])->where("org_id",$datas[$j]['org_id'])->where("template_id",$datas[$i]['template_id'])->update( 
                        array( 
                         "doc_header" => $datas[$i]['docs'][$j]['doc_header'],
                         "doc_title" => $datas[$i]['docs'][$j]['doc_title'],
                         "reviewers" => $datas[$i]['docs'][$j]['reviewers'],
                         "approvers_level1" => $datas[$i]['docs'][$j]['approvers_level1'],
                         "approvers_level2" => $datas[$i]['docs'][$j]['approvers_level2'],
                         "isDeleted" => $datas[$i]['docs'][$j]['isDeleted'],
                         ));
                }
                else
                {
                    //insert new doc data
                    $doc_data[] = [
                        'template_id' => $template_id,
                        'org_id' => $datas[$i]['org_id'],
                        'phase_id' => $datas[$i]['docs'][$j]['phase_id'],
                        'doc_header' => $datas[$i]['docs'][$j]['doc_header'],
                        'doc_title' => $datas[$i]['docs'][$j]['doc_title'],
                        'reviewers' => $datas[$i]['docs'][$j]['reviewers'],
                        'approvers_level1' => $datas[$i]['docs'][$j]['approvers_level1'],
                        'approvers_level2' => $datas[$i]['docs'][$j]['approvers_level2'],
                       ];
                }
            }
         }
         $validator = Validator::make($request->all(), [ 
            'datas.*.phase_id' => 'required',
            'datas.*.org_id' => 'required',
            'datas.*.user_id' => 'required', 
            'datas.*.tasks.*.person' => 'required',
            'datas.*.tasks.*.activity_desc' => 'required',
            'datas.*.tasks.*.approvers' => 'required',
            'datas.*.tasks.*.attendees' => 'required',
            'datas.*.tasks.*.fre_id' => 'required',
            'datas.*.tasks.*.start_date' => 'required',
            'datas.*.tasks.*.end_date' => 'required',
            'datas.*.tasks.*.duration' => 'required',
            'datas.*.tasks.*.task_type' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $doc_validator = Validator::make($request->all(), [ 
            'datas.*.docs.*.doc_id' => 'required',
            'datas.*.docs.*.doc_header' => 'required',
            'datas.*.docs.*.doc_title' => 'required',
            'datas.*.docs.*.reviewers' => 'required',
            'datas.*.docs.*.approvers_level1' => 'required',
            'datas.*.docs.*.approvers_level1' => 'required'
        ]);

        if ($doc_validator->fails()) { 
            return response()->json(['error'=>$doc_validator->errors()], 401);            
        }

        if((Templatemaster::insert($data) || $template>0) && (Templatedocs::insert($doc_data) || $templates_doc>0))
            {
            $returnData = Templatemaster::find($types->template_id);
            $data = array ("message" => 'Template Edited successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
           }
    }
    function retrievebyTemplate(Request $request,$id,$pid)
    {
        $typeCheck = ProjectType::where('type_id',$pid)->first();
        $temp='';
        if (!$typeCheck == null)
         {
            $temp = ProjectType::where('type_id',$pid)->first()->value('template_id');
         }
         else
         {
            $data = array ("message" => 'Project does not have Template');
            $response = Response::json($data,200);
            echo json_encode($response); 
         }
        $types = Templatemaster::where("org_id",$id)->where("template_id",$temp)->where("isDeleted",0);
        if ($request->input('approvers_base')) {
            $types->where('approvers',$request->input('approvers_base'));
        }

        echo json_encode($types->get()->groupBy('phase_name')); 
    }
   
    function getTemplatePhase(Request $request,$id,$tid)
    {
        $types = Templatemaster::select('phase_name')->where("org_id",$id)->where("template_id",$tid)->where("isDeleted",0);
        if ($request->input('approvers_base')) {
            $types->where('approvers',$request->input('approvers_base'));
        }

        echo json_encode($types->groupBy('phase_name')->get()); 
    }
    function getTemplateData($template_id)
    {
        $types = Templatemaster::join('tbl_designation_master as a',\DB::raw("FIND_IN_SET(a.designation_id,tbl_template_master.person)"),">",\DB::raw("'0'"))->join('tbl_designation_master as b',\DB::raw("FIND_IN_SET(b.designation_id,tbl_template_master.approvers)"),">",\DB::raw("'0'"))->join('tbl_designation_master as c',\DB::raw("FIND_IN_SET(c.designation_id,tbl_template_master.attendees)"),">",\DB::raw("'0'"))->select(DB::raw("GROUP_CONCAT(c.designation_name) as attendees_designation"),DB::raw("GROUP_CONCAT(b.designation_name) as approvers_designtion"),DB::raw("GROUP_CONCAT(a.designation_name) as person_designation"),'tbl_template_master.master_id','tbl_template_master.template_id','tbl_template_master.org_id','tbl_template_master.task_type','tbl_template_master.phase_id','tbl_template_master.activity_desc','tbl_template_master.approvers','tbl_template_master.attendees','tbl_template_master.fre_id','tbl_template_master.seq_status','tbl_template_master.seq_no','tbl_template_master.duration','tbl_template_master.start_date','tbl_template_master.end_date','tbl_template_master.file_upload_path','tbl_template_master.person')->where("tbl_template_master.template_id",$template_id)->where("tbl_template_master.isDeleted",0)->groupBy('tbl_template_master.master_id')->get();


        $docs = Templatedocs::where("template_id",$template_id)->where("isDeleted",0)->get()->groupBy('doc_title');
        
        return Response::json(['template' => $types,'docs' => $docs],'200');    
    }
    function getTemplatelist($org_id)
    {
        $lists = Templatenamemaster::select('template_id','org_id','template_name')->where('org_id',$org_id)->where('active_status',1)->get();
        return Response::json(["response"=>$lists],'200');
    }

}
