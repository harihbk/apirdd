<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspectionroot;
use Response;
use Validator;

class InspectionrootController extends Controller
{
    function index()
    {
        $types = Inspectionroot::all();
        if($types!=null) {
            $data = array ("message" => 'Inspection root data',"data" => $types );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'root_name' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = new Inspectionroot();

        $types->org_id = $request->input('org_id');
        $types->root_name = $request->input('root_name');
        $types->created_at = date('Y-m-d H:i:s');
        $types->updated_at = date('Y-m-d H:i:s');
        $types->created_by = $request->input('user_id');        
        
        if($types->save()) {
            $returnData = $types->find($types->root_id);
            $data = array ("message" => 'Inspection root added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'root_name' => 'required',  
            'user_id' => 'required',
            'active_status' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = Inspectionroot::where("root_id",$id)->update( 
            array(
             "root_name" => $request->input('root_name'), 
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status'),
             ));
        
             if($types>0)
             {
                 $returnData = Inspectionroot::find($id);
                 $data = array ("message" => 'Inspection root Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $types = Inspectionroot::where("org_id",$id)->where("isDeleted",0)->get();
        echo json_encode($types); 
    }
    function updateDeletion(Request $request,$id)
    {
        $types = Inspectionroot::where("root_id",$id)->update( 
        array("isDeleted" => $request->input('isDeleted'), "updated_at" => date('Y-m-d H:i:s')));
            if($types>0)
            {
                $returnData = Inspectionroot::find($id);
                $data = array ("message" => 'Inspection root Deleted successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
            }
    }
}
