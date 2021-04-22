<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checklisttemplate;
use Response;
use Validator;

class ChecklisttemplateController extends Controller
{
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'template_name' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = new Checklisttemplate();

        $types->org_id = $request->input('org_id');
        $types->template_name = $request->input('template_name');
        $types->created_at = date('Y-m-d H:i:s');
        $types->updated_at = date('Y-m-d H:i:s');
        $types->created_by = $request->input('user_id');        
        
        if($types->save()) {
            $returnData = $types->find($types->id);
            $data = array ("message" => 'Template added successfully',"data" => $returnData);
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }

    function retrievebyOrg(Request $request,$orgid)
    {
        $templates = Checklisttemplate::where("org_id",$orgid)->where("isDeleted",0)->get();
        return $templates; 
    }
}
