<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Units;
use Response;

class UnitsController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        $units = Units::offset($offset)->limit($limit)->get();
        if($units!=null) {
            $data = array ("message" => 'Units data',"data" => $units );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'property_id' => 'required', 
            'unit_area' => 'required', 
            'floor_id' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $units = new Units();

        $units->org_id = $request->input('org_id');
        $units->property_id = $request->input('property_id');
        $units->unit_area = $request->input('unit_area');
        $units->floor_id = $request->input('floor_id');
        $units->pod_image_path = $request->input('pod_image_path');
        $units->created_at = date('Y-m-d H:i:s');
        $units->updated_at = date('Y-m-d H:i:s');
        $units->created_by = $request->input('user_id');


        if($units->save()) {
            $returnData = $units->find($units->unit_id);
            $data = array ("message" => 'Unit added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {   
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'property_id' => 'required', 
            'unit_area' => 'required', 
            'floor_id' => 'required',
            'user_id' => 'required',
            'active_status' => "1"
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $units = Units::where("unit_id",$id)->update( 
            array( 
             "unit_area" => $request->input('unit_area'),
             "floor_id" => $request->input('floor_id'),
             "pod_image_path" => $request->input('pod_image_path'),
             "updated_at" => date('Y-m-d H:i:s'),
             "created_by" => $request->input('user_id'),
             "active_status" => $request->input('active_status')
             ));
        
             if($units>0)
             {
                $returnData = Units::find($id);
                $data = array ("message" => 'Unit Updated successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id,$propid)
    {
        $units = Units::where("org_id",$id)->where('property_id', $propid)->get();
        echo json_encode($units); 
    }
}
