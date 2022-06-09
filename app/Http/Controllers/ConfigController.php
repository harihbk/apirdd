<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MailConfig;
use App\Models\Orgmilestoneconfig;
use App\Models\Docpathconfig;
use App\Models\Organisations;
use App\Models\MilestoneConfig;
use App\Models\Financeteam;
use App\Models\Operationsmntteam;
use Response;
use Validator;
use File;

class ConfigController extends Controller
{
    //create mail configuration for organisation
    function createMailconfig(Request $request)
    {
        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');

        $config = new MailConfig();

        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required',
            'mail_driver' => 'required',
            'domain' => 'required',
            'port' => 'required',
            'user_name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }

        $config->org_id = $request->input('org_id');
        $config->mail_driver = $request->input('mail_driver');
        $config->domain = $request->input('domain');
        $config->port = $request->input('port');
        $config->user_name = $request->input('user_name');
        $config->password = $request->input('password');
        $config->created_at = $created_at;
        $config->updated_at = $updated_at;
        
        if($config->save()) {
            $returnData = $config->find($config->config_id);
            $data = array ("message" => 'Config added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        }
    }

    function updateMailconfig(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required',
            'config_id' => 'required',
            'mail_driver' => 'required',
            'domain' => 'required',
            'port' => 'required',
            'user_name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }

        $config = MailConfig::where("config_id",$request->input('config_id'))->update(
            array(
             "mail_driver" => $request->input('mail_driver'), 
             "domain" => $request->input('domain'),
             "port" => $request->input('port'),
             "user_name" => $request->input('user_name'),
             "password" => $request->input('password'),
             "updated_at" => date('Y-m-d H:i:s'),
            )
        );

        $returnData = MailConfig::find($request->input('config_id'));
        $data = array ("message" => 'Config detail Updated successfully',"data" => $returnData );
        $response = Response::json($data,200);
        echo json_encode($response);
    }
    function getConfig($org_id)
    {
        // $config = MailConfig::find($org_id);
        $docpath = Docpathconfig::where("org_id",$org_id)->where("isDeleted",0)->first();
        return Response::json(["file_path_config"=>$docpath],200);
    }
    function createMilestoneconfig(Request $request)
    {
        $datas = $request->get('datas');
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $milestoneData = array();
        $validator = Validator::make($request->all(), [
            'datas.*.milestone.*.milestone_config_id' => 'required', 
            'data.*.milestone.*.description' => 'required',
            'data.org_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }

        for($s=0;$s<count($datas['milestone']);$s++)
        {
            $milestoneData[] = [
                'org_id' => $datas['org_id'],
                'milestone_config_id' => $datas['milestone'][$s]['milestone_config_id'],
                'description' => $datas['milestone'][$s]['description'],
                'display_status' => $datas['milestone'][$s]['display_status'],
                'created_at' => $created_at,
                'updated_at' => $updated_at
             ];
        }

        if(Orgmilestoneconfig::insert($milestoneData))
        {
            $returnData = Orgmilestoneconfig::find($datas['org_id']);
            $data = array ("message" => 'Config detail Added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);    
        }

    }
    function updateMilestoneconfig(Request $request)
    {
        $datas = $request->input('datas');
        $validator = Validator::make($request->all(), [
            'datas.*.milestone.*.milestone_config_id' => 'required', 
            'datas.*.milestone.*.description' => 'required',
            'datas.*.milestone.*.id' => 'required', 
            'datas.org_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }

        for($s=0;$s<count($datas['milestone']);$s++)
        {
            $config = Orgmilestoneconfig::where("id",$datas['milestone'][$s]['id'])->update(
                array(
                 "description" => $datas['milestone'][$s]['description'], 
                 "display_status" => $datas['milestone'][$s]['display_status'],
                 "updated_at" => date('Y-m-d H:i:s'),
                )
            );
        }

        $returnData = Orgmilestoneconfig::find($datas['org_id']);
        $data = array ("message" => 'Config detail Updated successfully',"data" => $returnData );
        $response = Response::json($data,200);
        echo json_encode($response); 

    }
    function configAction(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $path = new Docpathconfig();
        
        $validator = Validator::make($request->all(), [
            'org_id' => 'required', 
            'user_id' => 'required',
            'doc_path' => 'required',
            'image_path' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }
        $org_data = Organisations::find($request->input('org_id'));
        //check if docpath is already exists
        $pathCount = Docpathconfig::where('org_id',$request->input('org_id'))->where('isDeleted',0)->count();
        if($pathCount==0)
        {
            $path->org_id = $request->input('org_id');
            $path->doc_path = "/uploads/".$org_data['org_code'].'/'.$request->input('doc_path')."/";
            $path->image_path = "/uploads/".$org_data['org_code'].'/'.$request->input('image_path')."/";
            $path->created_at = $created_at;
            $path->updated_at = $updated_at;

            $path->save();
        }
        
        $data = array ("message" => 'Config detail Added successfully');
        $response = Response::json($data,200);
        return $response;
          
    }
    function updateDocpathconfig(Request $request)
    {
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [
            'doc_path' => 'required', 
            'image_path' => 'required',
            'org_id' => 'required', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }
        $org_data = Organisations::find($request->input('org_id'));
        $doc_path = public_path().'/uploads/'.$org_data['org_code'].'/'.$request->input('doc_path');
        $img_path = public_path().'/uploads/'.$org_data['org_code'].'/'.$request->input('image_path');
        $docs = Docpathconfig::where('org_id',$request->input('org_id'))->where('isDeleted',0)->update(
            array(
                "doc_path" => "/uploads/".$org_data['org_code'].'/'.$request->input('doc_path'),
                "image_path" => "/uploads/".$org_data['org_code'].'/'.$request->input('image_path'),
                "updated_at" => $updated_at
            )
        );

        if($docs>0)
        {
            if(!File::isDirectory($doc_path)){
                File::makeDirectory($doc_path, 0777, true, true);
            }
            if(!File::isDirectory($img_path)){
                File::makeDirectory($img_path, 0777, true, true);
            }
            $returnData = Docpathconfig::find($request->input('org_id'));
            $data = array ("message" => 'Config detail Updated successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
        }

    }
    function getMilestonemaster()
    {
        $config = MilestoneConfig::where('isDeleted',0)->get();
        return Response::json(["config"=>$config],'200');
    }
}
