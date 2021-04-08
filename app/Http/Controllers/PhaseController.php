<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Phase;
use Response;
use Validator;

class PhaseController extends Controller
{
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'phase_name' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $phase = new Phase();

        $phase->org_id = $request->input('org_id');
        $phase->phase_name = $request->input('phase_name');
        $phase->created_at = date('Y-m-d H:i:s');
        $phase->updated_at = date('Y-m-d H:i:s');
        $phase->created_by = $request->input('user_id');
        
        if($phase->save()) {
            $returnData = $phase->find($phase->phase_id);
            $data = array ("message" => 'Phase added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function deletePhase(Request $request,$phase_id)
    {
        $phase = Phase::where("phase_id",$phase_id)->update( 
            array( 
             "isDeleted" => 1,
             "updated_at" => date('Y-m-d H:i:s'),
             ));
        if($phase>0)
        {
            $returnData = Phase::find($phase_id);
            $data = array ("message" => 'Phase removed successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
        }
    }
    function retrieveByorg($org_id)
    {
        $phase = Phase::select("phase_id","phase_name","org_id")->where("org_id",$org_id)->where("isDeleted",0)->get();
        echo json_encode($phase);
    }
}
