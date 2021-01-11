<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Members;
use Response;
use Validator;

class MembersController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        $members = Members::offset($offset)->limit($limit)->get();
        if($members!=null) {
            $data = array ("message" => 'Members data',"data" => $members );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'mem_org_id' => 'required', 
            'mem_name' => 'required', 
            'email' => 'required', 
            'password' => 'required', 
            'mobile_no' => 'required',
            'gender' => 'required',
            'mem_designation' => 'required',
            'mem_signature_path' => 'required',
            'mem_designation' => 'required',
            'mem_level' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $members = new Members();

        $members->mem_org_id = $request->input('mem_org_id');
        $members->mem_name = $request->input('mem_name');
        $members->mem_last_name = $request->input('mem_last_name');
        $members->email = $request->input('email');
        $members->password = Hash::make($request->input('password'));
        $members->mobile_no = $request->input('mobile_no');
        $members->gender = $request->input('gender');
        $members->mem_designation = $request->input('mem_designation');
        $members->mem_signature_path = $request->input('mem_signature_path');
        $members->mem_level = $request->input('mem_level');
        $members->created_at = date('Y-m-d H:i:s');
        $members->updated_at = date('Y-m-d H:i:s');
        $members->access_type = $request->input('access_type');
        
        if($members->save()) {
            $returnData = $members->find($members->mem_id);
            $data = array ("message" => 'Member added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'mem_name' => 'required', 
            'email' => 'required', 
            'password' => 'required', 
            'mobile_no' => 'required',
            'gender' => 'required',
            'mem_designation' => 'required',
            'mem_signature_path' => 'required',
            'mem_designation' => 'required',
            'mem_level' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $members = Members::where("mem_id",$id)->update( 
                            array( 
                             "mem_name" => $request->input('mem_name'),
                             "mem_last_name" => $request->input('mem_last_name'),
                             "email" => $request->input('email'),
                             "mobile_no" => $request->input('mobile_no'),
                             "gender" => $request->input('gender'),
                             "mem_designation" => $request->input('mem_designation'),
                             "mem_signature_path" => $request->input('mem_signature_path'),
                             "mem_level" => $request->input('mem_level'),
                             "updated_at" => date('Y-m-d H:i:s'),
                             "access_type" => $request->input('access_type'),
                             "active_status" => $request->input('active_status')
                             ));
        if($members>0)
        {
            $returnData = Members::find($id);
            $data = array ("message" => 'Member Updated successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $members = Members::where("mem_org_id",$id)->get();
        echo json_encode($members); 
    }
    function getMember(Request $request,$id)
    {
        $members = Members::select('mem_org_id','mem_name','mem_last_name','email','mobile_no','mem_designation','mem_signature_path')->where("mem_id",$id)->get();
        echo json_encode($members); 
    }
    //Get member by access type for Project creation screen - leasing team
    function getMemberByType(Request $request,$id,$tid)
    {
        $members = Members::select('mem_org_id','mem_name','mem_last_name','email','mobile_no','mem_designation','mem_signature_path')->where("mem_org_id",$id)->where("access_type",$tid)->get();
        echo json_encode($members); 
    }
    
}
