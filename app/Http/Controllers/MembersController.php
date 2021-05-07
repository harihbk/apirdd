<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Members;
use App\Models\Tenant;
use App\Models\Superuser;
use App\Models\Designation;
use Response;
use Validator;

class MembersController extends Controller
{
    function index()
    {
        $limit = 100;
        $offset = 0;
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
            'mem_level' => 'required'
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
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'mem_id' => 'required',
            'mem_org_id' => 'required', 
            'mem_name' => 'required', 
            'email' => 'required', 
            'mobile_no' => 'required',
            'gender' => 'required',
            'mem_designation' => 'required',
            'mem_signature_path' => 'required',
            'mem_designation' => 'required',
            'mem_level' => 'required', 
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $members = Members::where("mem_id",$request->input('mem_id'))->update( 
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
            $returnData = Members::find($request->input('mem_id'));
            $data = array ("message" => 'Member Updated successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $searchTerm = $request->input('searchkey');

        $query = Members::join('tbl_designation_master','tbl_designation_master.designation_id','=','users.mem_designation')->where('users.mem_org_id',$id)->select('users.mem_id','users.mem_org_id','users.mem_name','users.mem_signature_path','users.created_at','users.mem_last_name','users.email','users.mobile_no','users.mem_designation','tbl_designation_master.designation_name','users.gender','users.access_type','users.active_status');

        if (!empty($request->input('searchkey')))
        {
            $query->whereLike(['mem_name','mem_last_name'], $searchTerm);
        }
        if (!empty($request->input('member_role')))
        {
            $query->where('mem_designation',$request->input('member_role'));
        }

        $members = $query->get();
        // echo json_encode($members); 
        return $members;
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
    function getMemberByDesignation(Request $request,$org_id,$designation_id)
    {
        $members='';
        $tenant_type=2;
        $des_details  = Designation::where('designation_id',$designation_id)->get();
        $user_type = $des_details[0]['designation_user_type'];
        if($user_type==1)
        {
            $members = Members::join('tbl_designation_master','tbl_designation_master.designation_id','=','users.mem_designation')->where('users.active_status',1)->where('users.mem_designation',$designation_id)->where('users.mem_org_id',$org_id)->select('users.mem_id','users.mem_name','users.mem_last_name','users.mem_designation','tbl_designation_master.designation_name','users.email','users.mobile_no')->get();
        }
        else
        {
            if($designation_id==7)
            {
                $tenant_type = 1;
            }
            if($designation_id==8)
            {
                $tenant_type = 2;
            }
            $members = Tenant::join('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_tenant_master.tenant_designation')->where('tbl_tenant_master.active_status',1)->where('tenant_type',$tenant_type)->select('tbl_tenant_master.tenant_id','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name','tbl_tenant_master.email','tbl_designation_master.designation_name','tbl_tenant_master.email','tbl_tenant_master.tenant_mobile')->get();
        }
        // return response()->json(['response'=>$members], 200);
        echo json_encode($members);
    }
    function createSuperuser(Request $request)
    {
        $members = new Superuser();

      
        $members->mem_name = $request->input('mem_name');
        $members->email = $request->input('email');
        $members->password = Hash::make($request->input('password'));
        $members->created_at = date('Y-m-d H:i:s');
        $members->updated_at = date('Y-m-d H:i:s');
        
        if($members->save()) {
            $returnData = $members->find($members->id);
            echo json_encode($returnData); 
        } 
    }
    function retreiveMembersforProject(Request $request)
    {
        $designations = explode(',',$request->input('designations'));
        for($i=0;$i<count($designations);$i++)
        {
            $des_details  = Designation::where('designation_id',$designations[$i])->get();
            if($des_details[0]['designation_user_type']==1)
            {
                $members = Members::join('tbl_designation_master','tbl_designation_master.designation_id','=','users.mem_designation')->where('users.active_status',1)->where('users.mem_designation',$des_details[0]['designation_id'])->where('users.mem_org_id',$request->input('org_id'))->select('users.mem_id','users.mem_name','users.mem_last_name','users.mem_designation','tbl_designation_master.designation_name','users.email','users.mobile_no')->get();
            }
            else
            {

                $members = Tenant::join('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_tenant_master.tenant_designation')->where('tbl_tenant_master.active_status',1)->select('tbl_tenant_master.tenant_id','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name','tbl_tenant_master.email','tbl_designation_master.designation_name','tbl_tenant_master.email','tbl_tenant_master.tenant_mobile')->get();
            }   
            $datas[] = $members;
        }
        echo json_encode($datas);
         
    }
    
}
