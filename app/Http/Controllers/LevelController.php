<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Level;
use Response;
use Validator;

class LevelController extends Controller
{
    function index()
    {
        $levels = Level::all();
        if($levels!=null) {
            $data = array ("message" => 'Levels data',"data" => $levels );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'level_name' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $levels = new Level();

        $levels->org_id = $request->input('org_id');
        $levels->level_name = $request->input('level_name');
        $levels->created_at = date('Y-m-d H:i:s');
        $levels->updated_at = date('Y-m-d H:i:s');
        $levels->created_by = $request->input('user_id');
        
        if($levels->save()) {
            $returnData = $levels->find($levels->level_id);
            $data = array ("message" => 'Level added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'level_id' => 'required',
            'level_name' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $levels = Level::where("level_id",$request->input('level_id'))->update( 
            array( 
             "level_name" => $request->input('level_name'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($levels>0)
             {
                 $returnData = Level::find($request->input('level_id'));
                 $data = array ("message" => 'Level Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $levels = Level::where("org_id",$id)->get();
        echo json_encode($levels); 
    }
}
