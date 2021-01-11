<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Templatemaster;
use App\Models\Projecttype;
use Response;
use Validator;

class TemplateController extends Controller
{
    function store(Request $request)
    {
        $template_id = Templatemaster::max('template_id');

        if($template_id==null)
        {
            $template_id = 0;
        }

        $datas = $request->get('datas');
        $types = new Templatemaster();

         $created_at =  date('Y-m-d H:i:s');
         $updated_at =  date('Y-m-d H:i:s');
         for($i=0;$i<count($datas);$i++) 
         {
           for($k=0;$k<count($datas[$i]['tasks']);$k++)
           {
             $data[] = [
                'template_id' => $template_id+1,
                'org_id' => $datas[$i]['org_id'],
                "task_id" => $datas[$i]['tasks'][$k]['task_id'],
                'phase_name' => $datas[$i]['phase_name'],
                'created_by' => $datas[$i]['user_id'],
                "activity_desc" =>$datas[$i]['tasks'][$k]['activity_desc'],
                "person" => $datas[$i]['tasks'][$k]['person'],
                "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                "seq_char" => $datas[$i]['tasks'][$k]['seq_char'],
                "start_date" => $datas[$i]['tasks'][$k]['start_date'],
                "end_date" => $datas[$i]['tasks'][$k]['end_date'],
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                "duration" => $datas[$i]['tasks'][$k]['duration']
             ];
           }
         }

         $validator = Validator::make($request->all(), [ 
            'datas.*.org_id' => 'required', 
            'datas.*.phase_name' => 'required', 
            'datas.*.user_id' => 'required',
            'datas.*.tasks.*.person' => 'required',
            'datas.*.tasks.*.activity_desc' => 'required',
            'datas.*.tasks.*.approvers' => 'required',
            'datas.*.tasks.*.fre_id' => 'required',
            'datas.*.tasks.*.start_date' => 'required',
            'datas.*.tasks.*.end_date' => 'required',
            'datas.*.tasks.*.duration' => 'required',
            'datas.*.tasks.*.task_id' => 'required',

        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

         if(Templatemaster::insert($data))
         {
            $data = array ("message" => 'Template added successfully');
                $response = Response::json($data,200);
                echo json_encode($response);
         }
        
    }
    function update(Request $request,$id)
    {
        $datas = $request->get('datas');
        $types = new Templatemaster();

         $updated_at =  date('Y-m-d H:i:s');

         $update_count = 0;

         for($i=0;$i<count($datas);$i++) 
         {              
             $templates = Templatemaster::where("master_id",$datas[$i]['master_id'])->where("org_id",$datas[$i]['org_id'])->where("template_id",$datas[$i]['template_id'])->update( 
                array( 
                 "activity_desc" => $datas[$i]['activity_desc'],
                 "person" => $datas[$i]['person'],
                 "approvers" => $datas[$i]['approvers'],
                 "fre_id" => $datas[$i]['fre_id'],
                 "seq_status" => $datas[$i]['seq_status'],
                 "seq_no" => $datas[$i]['seq_no'],
                 "seq_char" => $datas[$i]['seq_char'],
                 "task_id" => $datas[$i]['task_id'],
                 "start_date" => $datas[$i]['start_date'],
                 "end_date" => $datas[$i]['end_date'],                 
                 "duration" =>$datas[$i]['duration'],
                 "updated_at" => date('Y-m-d H:i:s'),
                 "created_by" => $datas[$i]['user_id'],
                 "active_status" =>$datas[$i]['active_status']
                 ));

                 if($templates>0)
                 {
                    $update_count++;
                 }
         }
         if($update_count==count($datas))
         {
            $data = array ("message" => 'Template Updated successfully');
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
    function updateDeletion(Request $request,$id)
    {
        $types = Templatemaster::where("ch_id",$id)->update( 
        array("isDeleted" => $request->input('isDeleted'), "updated_at" => date('Y-m-d H:i:s')));
            if($types>0)
            {
                $returnData = Templatemaster::find($id);
                $data = array ("message" => 'Template Checklist  Deleted successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
            }
    }
    function getTemplatePhase(Request $request,$id,$tid)
    {
        $types = Templatemaster::select('phase_name')->where("org_id",$id)->where("template_id",$tid)->where("isDeleted",0);
        if ($request->input('approvers_base')) {
            $types->where('approvers',$request->input('approvers_base'));
        }

        echo json_encode($types->groupBy('phase_name')->get()); 
    }

}
