<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use Response;
use Validator;

class FloorController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        $floors = Floor::offset($offset)->limit($limit)->get();
        if($floors!=null) {
            $data = array ("message" => 'Floors data',"data" => $floors );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'property_id' => 'required', 
            'floor_no' => 'required', 
            'floor_code' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $floors = new Floor();

        $floors->org_id = $request->input('org_id');
        $floors->property_id = $request->input('property_id');
        $floors->floor_no = $request->input('floor_no');
        $floors->floor_code = $request->input('floor_code');
        $floors->created_at = date('Y-m-d H:i:s');
        $floors->updated_at = date('Y-m-d H:i:s');
        $floors->created_by = $request->input('user_id');        
        
        if($floors->save()) {
            $returnData = $floors->find($floors->floor_id);
            $data = array ("message" => 'Floor added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required', 
            'floor_no' => 'required', 
            'floor_code' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $floors = Floor::where("floor_id",$id)->update( 
            array(
             "property_id" => $request->input('property_id'), 
             "floor_no" => $request->input('floor_no'),
             "floor_code" => $request->input('floor_code'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($floors>0)
             {
                 $returnData = Floor::find($id);
                 $data = array ("message" => 'Floor detail Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $limit = 1;
        $offset = 1;
        $floors = Floor::where("org_id",$id)->offset($offset)->limit($limit)->get();
        echo json_encode($floors); 
    }
    function getFloor(Request $request,$id)
    {
        $floors = Floor::where("floor_id",$id)->get();
        echo json_encode($floors); 
    }
    function retrieveByProperty(Request $request,$id)
    {
        $floors = Floor::where("property_id",$id)->get();
        echo json_encode($floors); 
    }
    
}
