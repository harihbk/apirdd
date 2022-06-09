<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workpermit;
use Response;
use Validator;

class WorkpermitController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        //$types = Workpermit::offset($offset)->limit($limit)->get();
        $types = Workpermit::where('isDeleted',0)->get();
        if($types!=null) {
            $data = array ("message" => 'Work permit data',"data" => $types );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'permit_type' => 'required',
            'department' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        
        $prCheck = Workpermit::where('permit_type', $request->input('permit_type'))->where('isDeleted',0)->count();
        if($prCheck!=0)
        {
            return response()->json(['response'=>"Permit name already exists"], 410); 
        }

        $types = new Workpermit();

        $types->org_id = $request->input('org_id');
        $types->permit_type = $request->input('permit_type');
        $types->department = $request->input('department');
        if($request->has('permit_desc')) {
            $types->permit_desc = $request->input('permit_desc');
        }
        $types->created_at = date('Y-m-d H:i:s');
        $types->updated_at = date('Y-m-d H:i:s');
        $types->created_by = $request->input('user_id');        
        
        if($types->save()) {
            $returnData = $types->find($types->permit_id);
            $data = array ("message" => 'Work Permit added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'permit_id' => 'required', 
            'permit_type' => 'required',
            'department' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $prCheck = Workpermit::where('permit_type', $request->input('permit_type'))->where('isDeleted',0)->where('permit_id','!=',$request->input('permit_id'))->count();
        if($prCheck!=0)
        {
            return response()->json(['response'=>"Permit type name already exists"], 410); 
        }

        $types = Workpermit::where("permit_id",$request->input('permit_id'))->update( 
            array(
             "permit_type" => $request->input('permit_type'),
             "department" => $request->input('department'), 
             "permit_desc" => $request->input('permit_desc'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status'),
             ));
        
             if($types>0)
             {
                 $returnData = Workpermit::find($request->input('type_id'));
                 $data = array ("message" => 'Work Permit Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $limit = 1;
        $offset = 1;
        //$types = Workpermit::where("org_id",$id)->offset($offset)->limit($limit)->get();
        $types = Workpermit::where("org_id",$id)->where("isDeleted",0)->get();
        echo json_encode($types); 
    }
    function updateDeletion(Request $request,$id)
    {
        $types = Workpermit::where("permit_id",$id)->update( 
        array("isDeleted" => $request->input('isDeleted'), "updated_at" => date('Y-m-d H:i:s')));
            if($types>0)
            {
                $returnData = Workpermit::find($id);
                $data = array ("message" => 'Work Permit Deleted successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
            }
    }
}
