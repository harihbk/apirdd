<?php

namespace App\Http\Controllers;
use App\Models\Inspectionchecklistmaster;
use App\Models\Inspectionroot;
use App\Models\Checklisttemplate;
use Response;
use Validator;

use Illuminate\Http\Request;

class InspectionchecklistController extends Controller
{
    function retrievebyOrg(Request $request,$id,$tempid)
    {
        $types = Inspectionchecklistmaster::join('tbl_inspection_root_categories', 'tbl_checklist_master.root_id','=','tbl_inspection_root_categories.root_id')->leftjoin('users',\DB::raw("FIND_IN_SET(users.mem_id,tbl_inspection_root_categories.members)"),">",\DB::raw("'0'"))->get(['tbl_checklist_master.*', 'tbl_inspection_root_categories.root_name','tbl_inspection_root_categories.mem_designation','users.mem_name'])->where("org_id",$id)->where("template_id",$tempid)->where("isDeleted",0)->groupBy('root_name');
        echo json_encode($types); 
    }
    function store(Request $request)
    {
        $datas = $request->get('template');

        $types = new Checklisttemplate();

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $types->org_id = $datas['org_id'];
        $types->template_name = $datas['template_name'];
        $types->created_at = $created_at;
        $types->updated_at = $updated_at;
        $types->created_by = $datas['user_id'];      

        if($types->save()) {
        $template_id = $types->find($types->id);
        for($k=0;$k<count($datas['entries']);$k++) 
        {
            //insert template activities
            $data[] = [
                'org_id' => $datas['org_id'],
                'template_id' => $template_id->id,
                'root_id' => $datas['entries'][$k]['root_id'],
                'checklist_desc' => $datas['entries'][$k]['checklist_desc'],
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                'created_by' => $datas['user_id']
                ];
        }

        $validator = Validator::make($request->all(), [ 
            'template.org_id' => 'required', 
            'template.entries.*.root_id' => 'required',
            'template.entries.*.checklist_desc' => 'required',
            'template.user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if(Inspectionchecklistmaster::insert($data))
        {
            $data = array ("message" => 'Template added successfully');
            $response = Response::json($data,200);
            echo json_encode($response);
        }
        }
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required',
            'template_id' => 'required',
            'root_id' => 'required', 
            'checklist_desc' => 'required', 
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = Inspectionchecklistmaster::where("ch_id",$request->input('org_id'))->where("org_id",$request->input('org_id'))->where("template_id",$request->input('template_id'))->update( 
            array(
             "root_id" => $request->input('root_id'), 
             "checklist_desc" => $request->input('checklist_desc'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($types>0)
             {
                 $returnData = Inspectionchecklistmaster::find($request->input('org_id'));
                 $data = array ("message" => 'Inspection Checklist Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function updateDeletion(Request $request,$id)
    {
        $types = Inspectionchecklistmaster::where("ch_id",$id)->update( 
        array("isDeleted" => $request->input('isDeleted'), "updated_at" => date('Y-m-d H:i:s')));
            if($types>0)
            {
                $returnData = Inspectionchecklistmaster::find($id);
                $data = array ("message" => 'Inspection ChecklistDoc  Deleted successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
            }
    }
}
