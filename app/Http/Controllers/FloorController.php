<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\Properties;
use Response;
use Validator;

class FloorController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        //$floors = Floor::offset($offset)->limit($limit)->get();
        $floor = Floor::all();
        if($floor!=null) {
            $data = array ("message" => 'Floors data',"data" => $floor );
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
            //update properties count
            $floor_count = Floor::where('org_id',$request->input('org_id'))->where('property_id',$request->input('property_id'))->where('active_status',1)->count();
            $properties = Properties::where("property_id",$request->input('property_id'))->update( 
                array( 
                 "no_of_floors" => $floor_count,
                 "updated_at" => date('Y-m-d h:i:s'),
                 "created_by" => $request->input('user_id')
                 ));
            $returnData = $floors->find($floors->floor_id);
            $data = array ("message" => 'Floor added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'floor_id' => 'required',
            'property_id' => 'required', 
            'floor_no' => 'required', 
            'floor_code' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $floors = Floor::where("floor_id",$request->input('floor_id'))->update( 
            array(
             "property_id" => $request->input('property_id'), 
             "floor_no" => $request->input('floor_no'),
             "floor_code" => $request->input('floor_code'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($floors>0)
             {
                 if($request->input('active_status')==0)
                 {
                    $floor_count = Floor::where('property_id',$request->input('property_id'))->where('active_status',1)->count();
                    $properties = Properties::where("property_id",$request->input('property_id'))->update( 
                        array( 
                         "no_of_floors" => $floor_count,
                         "updated_at" => date('Y-m-d h:i:s'),
                         "created_by" => $request->input('user_id')
                         ));
                 }
                 $returnData = Floor::find($request->input('floor_id'));
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
