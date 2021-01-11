<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisations;
use Response;
use Validator;

class OrganisationsController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 2;
        $organisations = Organisations::offset($offset)->limit($limit)->get();
        if($organisations!=null) {
            $data = array ("message" => 'Organisation data',"data" => $organisations );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_name' => 'required', 
            'org_code' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $organisations = new Organisations();

        $organisations->org_name = $request->input('org_name');
        $organisations->org_code = $request->input('org_code');
        $organisations->created_at = date('Y-m-d H:i:s');
        $organisations->updated_at = date('Y-m-d H:i:s');
        $organisations->created_by = $request->input('user_id');


        if($organisations->save()) {
            $returnData = $organisations->find($organisations->org_id);
            $data = array ("message" => 'Organisation added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'org_name' => 'required', 
            'org_code' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $organisations = Organisations::where("org_id",$id)->update( 
                            array( 
                             "org_name" => $request->input('org_name'),
                             "org_code" => $request->input('org_code'),
                             "updated_at" => date('Y-m-d H:i:s'),
                             "created_by" => $request->input('user_id'),
                             "active_status" => $request->input('active_status')
                             ));
        if($organisations>0)
        {
            $returnData = Organisations::find($id);
            $data = array ("message" => 'Organisation Updated successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        }
    }
}
