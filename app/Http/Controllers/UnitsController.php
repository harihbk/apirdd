<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Units;
use Response;
use Validator;
use File;
class UnitsController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        //$units = Units::offset($offset)->limit($limit)->get();
        $units = Units::all();
        if($units!=null) {
            $data = array ("message" => 'Units data',"data" => $units );
            $response = Response::json($data,200);
            return $response;
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'property_id' => 'required', 
            'unit_name' => 'required',
            'unit_area' => 'required', 
            'floor_id' => 'required',
            'pod_image_path' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $prCheck = Units::where('unit_name', $request->input('unit_name'))->where('property_id',$request->input('property_id'))->count();
        if($prCheck!=0)
        {
            return response()->json(['response'=>"Unit name already exists"], 410); 
        }

        $units = new Units();

        $units->org_id = $request->input('org_id');
        $units->property_id = $request->input('property_id');
        $units->unit_name = $request->input('unit_name');
        $units->zone = $request->input('zone');
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
    function update(Request $request)
    {   
        $validator = Validator::make($request->all(), [ 
            'unit_id' => 'required',
            'org_id' => 'required', 
            'property_id' => 'required',
            'unit_name' => 'required', 
            'unit_area' => 'required', 
            'floor_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $prCheck = Units::where('unit_name', $request->input('unit_name'))->where('property_id',$request->input('property_id'))->where('unit_id','!=',$request->input('unit_id'))->count();
        if($prCheck!=0)
        {
            return response()->json(['response'=>"Unit name already exists"], 410); 
        }

        $units = Units::where("unit_id",$request->input('unit_id'))->update( 
            array( 
             "unit_name"=> $request->input('unit_name'),
             "zone"=> $request->input('zone'),
             "unit_area" => $request->input('unit_area'),
             "floor_id" => $request->input('floor_id'),
             "pod_image_path" => $request->input('pod_image_path'),
             "updated_at" => date('Y-m-d H:i:s'),
             "created_by" => $request->input('user_id'),
             "active_status" => $request->input('active_status')
             ));
        
             if($units>0)
             {
                $returnData = Units::find($request->input('unit_id'));
                $data = array ("message" => 'Unit Updated successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id,$propid)
    {
        $limit = 10;
        $offset = 0;

        $searchTerm = $request->input('searchkey');

        $query = Units::where("org_id",$id)->select('unit_id','unit_name','zone','property_id','unit_area','floor_id','pod_image_path','created_at','updated_at','active_status');

        if (!empty($request->input('searchkey')))
        {
            $query->whereLike(['unit_name'], $searchTerm);
        }

        $units = $query->where('property_id', $propid)->offset($offset)->limit($limit)->get();
        echo json_encode($units); 
    }
    function retrieveByFloor(Request $request,$propid,$floorid)
    {
        $validator = Validator::make($request->all(), [ 
            'image_path' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $searchTerm = $request->input('searchkey');

        $query = Units::where("floor_id",$floorid)->where("property_id",$propid)->select('unit_id','unit_name','zone','property_id','unit_area','floor_id','pod_image_path','created_at','updated_at','active_status');

        if (!empty($request->input('searchkey')))
        {
            $query->whereLike(['unit_name'], $searchTerm);
        }

        $img_path = public_path()."".$request->input('image_path')."settings/units";
        if(!File::isDirectory($img_path)){
               File::makeDirectory($img_path, 0777, true, true);
           }

        $units = $query->orderBy('unit_name','ASC')->get();
        return Response::json(array('img_path' => $img_path,'units' => $units));
    }
    function retrieveUnitsforprojectcreation(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'property_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        // $unit_details = Units::leftjoin('tbl_projects','tbl_projects.unit_id','=','tbl_units_master.unit_id')->select('tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_units_master.zone','tbl_units_master.property_id','tbl_units_master.unit_area','tbl_units_master.floor_id','tbl_units_master.pod_image_path')->where("tbl_units_master.org_id",$request->input('org_id'))->where("tbl_units_master.property_id",$request->input('property_id'))->whereNull('tbl_projects.project_id')->orWhere('tbl_projects.project_status',1)->orderBy('tbl_units_master.unit_name', 'ASC')->get();


        $unit_details = Units::leftjoin('tbl_projects','tbl_projects.unit_id','=','tbl_units_master.unit_id')->select('tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_units_master.zone','tbl_units_master.property_id','tbl_units_master.unit_area','tbl_units_master.floor_id','tbl_units_master.pod_image_path')->where("tbl_units_master.org_id",$request->input('org_id'))->where("tbl_units_master.property_id",$request->input('property_id'))->where(function($query){
            $query->orWhereNull('tbl_projects.project_id')->orWhere('tbl_projects.project_status',1);
           })->orderBy('tbl_units_master.unit_name', 'ASC')->get();


        
        echo json_encode($unit_details);
    }
}
