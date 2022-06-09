<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Designation;
use Response;
use Validator;

class DesignationController extends Controller
{
    function index()
    {
        $designations = Designation::where('designation_user_type',1)->where('active_status',1)->get();
        if($designations!=null) {
            $data = array ("message" => 'designations data',"data" => $designations );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $designation_access_type = Designation::where('active_status',1)->max('access_type');
        
        if($designation_access_type==null)
        {
            $designation_access_type = 1;
        }

        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'designation_name' => 'required',
            'designation_user_type' => 'required',
            'level_id' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }


        $designations = new Designation();

        $designations->org_id = $request->input('org_id');
        $designations->designation_name = $request->input('designation_name');
        $designations->designation_user_type = $request->input('designation_user_type');
        $designations->level_id = $request->input('level_id');
        $designations->access_type = $designation_access_type + 1;
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
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'designation_id' => 'required',  
            'designation_name' => 'required', 
            'designation_user_type' => 'required',
            'level_id' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $designations = Designation::where("designation_id",$request->input('designation_id'))->update( 
            array( 
             "designation_name" => $request->input('designation_name'),
             "designation_user_type" => $request->input('designation_name'),
             "level_id" => $request->input('level_id'),
             "updated_at" => date('Y-m-d H:i:s'),
             "designation_user_type" => 1,
             "active_status" => $request->input('active_status')
             ));
        
             if($designations>0)
             {
                 $returnData = Designation::find($request->input('designation_id'));
                 $data = array ("message" => 'Designation Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
       // $designation = Designation::where("org_id",$id)->where('designation_user_type',1)->where('active_status',1)->get();
        $designation = Designation::where("org_id",$id)->whereIn('designation_user_type',[1,2])->where('active_status',1)->get();

        
        return Response::json(["response"=>$designation],'200');
    }
    function retrieveAttendeedesignation(Request $request,$id)
    {
        $designation = Designation::where("org_id",$id)->where('active_status',1)->get();
        return Response::json(["response"=>$designation],'200');
    }
    function getDesignation(Request $request,$id)
    {
        $designation = Designation::where("designation_id",$id)->get();
        echo json_encode($designation); 
    }
    function retrieveByUsertype($org_id,$user_type)
    {
        $designation = Designation::where("designation_user_type",$user_type)->where("active_status",1)->where("org_id",$org_id)->get();
        return Response::json(["response"=>$designation],'200');
    }

}
