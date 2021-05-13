<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Floor;
use App\Models\Units;
use App\Models\Properties;
use Response;
use Validator;
use File;

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
        $floors->floor_details = $request->input('floor_details');
        $floors->pod_image_path = $request->input('pod_image_path');
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
             "floor_details" => $request->input('floor_details'),
             "pod_image_path" => $request->input('pod_image_path'),
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
        $validator = Validator::make($request->all(), [ 
            'doc_path' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $floors = Floor::where("property_id",$id)->get();
        $doc_path = public_path()."".$request->input('doc_path')."settings/floors";
        if(!File::isDirectory($doc_path)){
               File::makeDirectory($doc_path, 0777, true, true);
           }
           return Response::json(array('doc_path' => $doc_path,'floors' => $floors));
    }
    /* Remove floor */
    function removeFloor($propertyid,$floorid)
    {
        //check whether floor is assigned with unit
        $unitCount = Units::where('property_id',$propertyid)->where('floor_id',$floorid)->count();
        if($unitCount==0)
        {
            //check whether floor is last of the property
            $floorDetails = Floor::select('floor_id')->where('property_id',$propertyid)->orderBy('floor_id','DESC')->limit(1)->get();
            if(intval($floorDetails[0]['floor_id'])==intval($floorid))
            {
                //can delete floor
                $deleteQuery = Floor::where('property_id',$propertyid)->where('floor_id',$floorid)->delete();
                if($deleteQuery==1)
                {
                    return response()->json(['response'=>"Floor Removed Successfully"], 200);
                }
                else
                {
                    return response()->json(['response'=>"Floor Not Removed"], 200);
                }
            }   
            else
            {
                return response()->json(['response'=>"Last floor Can Only be removed,cannot remove this floor"], 410);
            }
        }
        else
        {
            return response()->json(['response'=>"Units already assigned,cannot remove this floor"], 410);
        }
    }
}
