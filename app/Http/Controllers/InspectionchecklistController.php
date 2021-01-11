<?php

namespace App\Http\Controllers;
use App\Models\Inspectionchecklistmaster;
use App\Models\Inspectionroot;
use Response;
use Validator;

use Illuminate\Http\Request;

class InspectionchecklistController extends Controller
{
    function retrievebyOrg(Request $request,$id)
    {
        $types = Inspectionchecklistmaster::where("org_id",$id)->where("isDeleted",0)->get();
        $root = new Inspectionroot();
        $types = Inspectionchecklistmaster::join('tbl_inspection_root_categories', 'tbl_checklist_master.root_id','=','tbl_inspection_root_categories.root_id')->get(['tbl_checklist_master.*', 'tbl_inspection_root_categories.root_name'])->groupBy('root_name');
        echo json_encode($types); 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'root_id' => 'required', 
            'checklist_desc' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = new Inspectionchecklistmaster();

        $types->org_id = $request->input('org_id');
        $types->root_id = $request->input('root_id');
        $types->checklist_desc = $request->input('checklist_desc');
        $types->created_at = date('Y-m-d H:i:s');
        $types->updated_at = date('Y-m-d H:i:s');
        $types->created_by = $request->input('user_id');        
        
        if($types->save()) {
            $returnData = $types->find($types->ch_id);
            $data = array ("message" => 'Inspection Checklist added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'root_id' => 'required', 
            'checklist_desc' => 'required', 
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = Inspectionchecklistmaster::where("ch_id",$id)->where("org_id",$request->input('org_id'))->update( 
            array(
             "root_id" => $request->input('root_id'), 
             "checklist_desc" => $request->input('checklist_desc'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($types>0)
             {
                 $returnData = Inspectionchecklistmaster::find($id);
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
