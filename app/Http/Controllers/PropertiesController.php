<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Properties;
use App\Models\Floor;
use App\Models\Financeteam;
use App\Models\Operationsmntteam;
use App\Models\Maintainenceteam;
use App\Models\Marketingteam;
use App\Models\Members;
use App\Models\Centermanager;
use Response;
use Validator;
use File;


class PropertiesController extends Controller
{
    function index()
    {
        $limit = 0;
        $offset = 0;
        $properties = Properties::all();
        if($properties!=null) {
            $data = array ("message" => 'Properties data',"data" => $properties );
            $response = Response::json($data,200);
            return $response; 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'property_name' => 'required', 
            'property_logo' => 'required', 
            'no_of_floors' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $prCheck = Properties::where('property_name', $request->input('property_name'))->count();
        if($prCheck!=0)
        {
            return response()->json(['response'=>"Property name already exists"], 410); 
        }

        $properties = new Properties();

        $properties->org_id = $request->input('org_id');
        $properties->property_name = $request->input('property_name');
        $properties->property_logo = $request->input('property_logo');
        $properties->property_files = $request->input('property_files');
        $properties->property_links = $request->input('property_links');
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
                    'floor_no' => $k+intval(1),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_by' => $request->input('user_id')

                ];
            }
            if(Floor::insert($data))
            {
                $data = array ("message" => 'Property added successfully',"data" => $returnData );
                $response = Response::json($data,200);
                return $response;
            }
        } 
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required',
            'property_name' => 'required', 
            'property_logo' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $prCheck = Properties::where('property_name', $request->input('property_name'))->where('property_id','!=',$request->input('property_id'))->count();
        if($prCheck!=0)
        {
            return response()->json(['response'=>"Property name already exists"], 410); 
        }

        $properties = Properties::where("property_id",$request->input('property_id'))->update( 
                            array( 
                             "property_name" => $request->input('property_name'),
                             "property_logo" => $request->input('property_logo'),
                             "property_files" => $request->input('property_files'),
                             "property_links" => $request->input('property_links'),
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
        $validator = Validator::make($request->all(), [ 
            'doc_path' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $searchTerm = $request->input('searchkey');

        $query = Properties::where("org_id",$id)->select('property_id','property_name','property_logo','property_files','property_links','no_of_floors','created_at','updated_at','active_status');

        if (!empty($request->input('searchkey')))
        {
            $query->whereLike(['property_name'], $searchTerm);
        }
        $properties = $query->orderBy('property_name','ASC')->get();
        $doc_path = public_path()."".$request->input('doc_path')."settings/properties";
        if(!File::isDirectory($doc_path)){
               File::makeDirectory($doc_path, 0777, true, true);
        }
        $data = array ("data" => $properties,"doc_path" => $doc_path);
        $response = Response::json(['response'=>$data]);
        return $response;
    }

    function retrieveForUser(Request $request,$id)
    {
         $searchTerm = $request->input('searchkey');

         $query = Properties::where("org_id",$id)->select('property_id','property_name','property_logo','property_files','property_links','no_of_floors','created_at','updated_at','active_status');

         if (!empty($request->input('searchkey')))
         {
            $query->whereLike(['property_name'], $searchTerm);
         }
         $properties = $query->orderBy('property_name','ASC')->get();
         $data = array ("data" => $properties);
         $response = Response::json(['response'=>$data]);
         return $response;
    }
    function addMembers(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'property_id' => 'required',
            'org_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

          
          $created_at = date('Y-m-d H:i:s');
          $updated_at  = date('Y-m-d H:i:s');
          $finance_team_data = array();
          $op_team_data = array();
          $center_manager_data = array();

               $finance_team = $request->input('finance_team');
               //finance team
                for($i=0;$i<count($finance_team);$i++)
                {
                    $finance_team_data[] = [
                        "org_id" => $request->input('org_id'),
                        "property_id" => $request->input('property_id'),
                        "email" => $finance_team[$i],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "created_by" => $request->input('user_id')
                    ];
                }

                Financeteam::where('org_id',$request->input('org_id'))->where('property_id',$request->input('property_id'))->update(array("isDeleted"=>1,"updated_at" => $updated_at));
                if(count($finance_team_data)>0)
                {
                    Financeteam::insert($finance_team_data);
                }
                
                $op_team = $request->input('op_team');
                 //operational team
                for($j=0;$j<count($op_team);$j++)
                {
                    $op_team_data[] = [
                        "org_id" => $request->input('org_id'),
                        "property_id" => $request->input('property_id'),
                        "email" => $op_team[$j],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "created_by" => $request->input('user_id')
                    ];
                }

                Operationsmntteam::where('org_id',$request->input('org_id'))->where('property_id',$request->input('property_id'))->update(array("isDeleted"=>1,"updated_at" => $updated_at));
                if(count($op_team_data)>0)
                {
                    Operationsmntteam::insert($op_team_data);
                }
                
                $mt_team = $request->input('maintainence_team');
                $mt_team_data = array();
                 //maintainence team
                for($k=0;$k<count($mt_team);$k++)
                {
                    $mt_team_data[] = [
                        "org_id" => $request->input('org_id'),
                        "property_id" => $request->input('property_id'),
                        "email" => $mt_team[$k],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "created_by" => $request->input('user_id')
                    ];
                }
                Maintainenceteam::where('org_id',$request->input('org_id'))->where('property_id',$request->input('property_id'))->update(array("isDeleted"=>1,"updated_at" => $updated_at));
                if(count($mt_team_data)>0)
                {
                    Maintainenceteam::insert($mt_team_data);
                }
                $marketing_team = $request->input('marketing_team');
                $marketing_team_data = array();
                 //marketing team
                for($l=0;$l<count($marketing_team);$l++)
                {
                    $marketing_team_data[] = [
                        "org_id" => $request->input('org_id'),
                        "property_id" => $request->input('property_id'),
                        "email" => $marketing_team[$l],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "created_by" => $request->input('user_id')
                    ];
                }
                Marketingteam::where('org_id',$request->input('org_id'))->where('property_id',$request->input('property_id'))->update(array("isDeleted"=>1,"updated_at" => $updated_at));
                if(count($marketing_team_data)>0)
                {
                    Marketingteam::insert($marketing_team_data);
                }

                $center_manager = $request->input('center_manager');
                for($e=0;$e<count($center_manager);$e++)
                {
                    $center_manager_data[] = [
                        "org_id" => $request->input('org_id'),
                        "property_id" => $request->input('property_id'),
                        "email" => $center_manager[$e],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "created_by" => $request->input('user_id')
                    ];
                }
                Centermanager::where('org_id',$request->input('org_id'))->where('property_id',$request->input('property_id'))->update(array("isDeleted"=>1,"updated_at" => $updated_at));
                if(count($center_manager_data)>0)
                {
                    Centermanager::insert($center_manager_data);
                }


                $data = array ("message" => 'Members updated successfully');
                $response = Response::json($data,200);
                return $response;
    }
    function getMembers($orgid,$propid)
    {
        $finance_team = Financeteam::where('org_id',$orgid)->where('property_id',$propid)->where('isDeleted',0)->select('email','property_id')->get();
        $operations_team = Operationsmntteam::where('org_id',$orgid)->where('property_id',$propid)->where('isDeleted',0)->select('email','property_id')->get();
        $maintainence_team = Maintainenceteam::where('org_id',$orgid)->where('property_id',$propid)->where('isDeleted',0)->select('email','property_id')->get();
        $marketing_team = Marketingteam::where('org_id',$orgid)->where('property_id',$propid)->where('isDeleted',0)->select('email','property_id')->get();
        $center_manager = Centermanager::where('org_id',$orgid)->where('property_id',$propid)->where('isDeleted',0)->select('email','property_id')->get();

        return response()->json(['finance_team'=>$finance_team,'operations_team'=>$operations_team,'maintainence_team'=>$maintainence_team,'marketing_team'=>$marketing_team,'center_manager' => $center_manager], 200);
    }
    function retrievememberProperties($memid)
    {
        $memberdetails = Members::where('mem_id',$memid)->first();
        $properties=[];
        if($memberdetails['properties']!=null)
        {
            $properties = Properties::whereIn('property_id',json_decode($memberdetails['properties']))->select('property_id','property_name')->get();
        }
        return $properties;
    }
}
