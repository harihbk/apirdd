<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;
use Response;

class DesignationController extends Controller
{
    function index()
    {
        $designations = Designation::all();
        if($designations!=null) {
            $data = array ("message" => 'designations data',"data" => $designations );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'designation_name' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }


        $designations = new Designation();

        $designations->org_id = $request->input('org_id');
        $designations->designation_name = $request->input('des_name');
        $designations->created_at = date('Y-m-d H:i:s');
        $designations->updated_at = date('Y-m-d H:i:s');
        $designations->created_by = $request->input('user_id');
        
        if($designations->save()) {
            $returnData = $designations->find($designations->designation_id);
            $data = array ("message" => 'Designation added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'designation_name' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $designations = Designation::where("designation_id",$id)->update( 
            array( 
             "designation_name" => $request->input('des_name'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($designations>0)
             {
                 $returnData = Designation::find($id);
                 $data = array ("message" => 'Designation Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $designation = Designation::where("org_id",$id)->get();
        echo json_encode($designation); 
    }
    function getDesignation(Request $request,$id)
    {
        $designation = Designation::where("designation_id",$id)->get();
        echo json_encode($designation); 
    }

}
