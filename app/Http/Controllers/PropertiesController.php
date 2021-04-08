<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\Floor;
use Response;
use Validator;

class PropertiesController extends Controller
{
    function index()
    {
        $limit = 0;
        $offset = 0;
        //$properties = Properties::offset($offset)->limit($limit)->get();
        $properties = Properties::all();
        if($properties!=null) {
            $data = array ("message" => 'Properties data',"data" => $properties );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'property_name' => 'required', 
            'no_of_floors' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $properties = new Properties();

        $properties->org_id = $request->input('org_id');
        $properties->property_name = $request->input('property_name');
        $properties->no_of_floors = $request->input('no_of_floors');
        $properties->created_at = date('Y-m-d h:i:s');
        $properties->updated_at = date('Y-m-d h:i:s');
        $properties->created_by = $request->input('user_id');

        if($properties->save()) {
            $returnData = $properties->find($properties->property_id);
            $floors = new Floor();
            for($k=0;$k<$request->input('no_of_floors');$k++)
            {
                $data[] = [
                    'org_id' => $request->input('org_id'),
                    'property_id' => $returnData['property_id'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => $request->input('user_id')

                ];
            }
            if(Floor::insert($data))
            {
                $data = array ("message" => 'Property added successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response);
            }
        } 
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required',
            'property_name' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $properties = Properties::where("property_id",$request->input('property_id'))->update( 
                            array( 
                             "property_name" => $request->input('property_name'),
                             "updated_at" => date('Y-m-d h:i:s'),
                             "created_by" => $request->input('user_id'),
                             "active_status" => $request->input('active_status')
                             ));
        if($properties>0)
        {
            $returnData = Properties::find($request->input('property_id'));
            $data = array ("message" => 'Property Updated successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        }
    }
    function retrieve(Request $request,$id)
    {
        $limit = 10;
        $offset = 0;

        $searchTerm = $request->input('searchkey');

        $query = Properties::where("org_id",$id)->select('property_id','property_name','no_of_floors','created_at','updated_at','active_status');

        if (!empty($request->input('searchkey')))
        {
            $query->whereLike(['property_name'], $searchTerm);
        }
        $properties = $query->offset($offset)->limit($limit)->get();
        echo json_encode($properties); 
    }
}
