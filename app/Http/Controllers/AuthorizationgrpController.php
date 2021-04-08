<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Authorizationgrpmaster;
use App\Models\Authorizationgrp;
use App\Models\Authorizationgrpcontent;
use App\Models\Authorizationgrpmilestone;
use App\Models\Projectworkspacemaster;
use App\Models\Projectworkspacesections;
use App\Models\Authgrpworkspacefields;
use App\Models\Authgrpworkspacesections;
use App\Models\Authgrporgaccess;
use Response;
use Validator;

class AuthorizationgrpController extends Controller
{
    function getMastercontent()
    {
        $content = Authorizationgrpmaster::where('isDeleted',0)->get();
        return Response::json($content,200);
    }

    function createAuthorizationgrp(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $datas = $request->get('datas');

        $validator = Validator::make($request->all(), [
            'datas.org_id' => 'required', 
            'datas.group_name' => 'required',
            'datas.user_id' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }

        //create authorisation group
        $group = new Authorizationgrp();
        $group->org_id = $datas['org_id'];
        $group->group_name = $datas['group_name'];
        $group->project_creation = $datas['project_creation'];
        $group->created_at = $created_at;
        $group->updated_at = $updated_at;
        $group->created_by = $datas['user_id'];

        if($group->save())
        {
            $groupDetails = $group->find($group->id);

            $data = array();

            for($i=0;$i<count($datas['content']);$i++)
            {
              $data[] = [
                'org_id' => $datas['org_id'],
                "group_id" => $groupDetails->id,
                "phase_id" => $datas['content'][$i]['phase_id'],
                "content_id" => $datas['content'][$i]['content_id'],
                "content_description" => $datas['content'][$i]['content_description'],
                "project_display" => $datas['content'][$i]['project_display'],
                "project_edit" => $datas['content'][$i]['project_edit'],
                "template_display" => $datas['content'][$i]['template_display'],
                "template_edit" => $datas['content'][$i]['template_edit'],
                "created_at" => $created_at,
                "updated_at" => $updated_at
              ];
            }
            for($k=0;$k<count($datas['milestone']);$k++)
            {
                $milestoneData[] = [
                    'org_id' => $datas['org_id'],
                    "group_id" => $groupDetails->id,
                    "config_id" => $datas['milestone'][$k]['config_id'],
                    "edit" => $datas['milestone'][$k]['edit'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }
            for($a=0;$a<count($datas['workspace_fields']);$a++)
            {
                $workspacefieldsData[] = [
                    'org_id' => $datas['org_id'],
                    "group_id" => $groupDetails->id,
                    "content_id" => $datas['workspace_fields'][$a]['content_id'],
                    "display" => $datas['workspace_fields'][$a]['display'],
                    "edit" => $datas['workspace_fields'][$a]['edit'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }

            for($b=0;$b<count($datas['workspace_sections']);$b++)
            {
                $workspacesectionsData[] = [
                    'org_id' => $datas['org_id'],
                    "group_id" => $groupDetails->id,
                    "content_id" => $datas['workspace_fields'][$b]['content_id'],
                    "display" => $datas['workspace_fields'][$b]['display'],
                    "edit" => $datas['workspace_fields'][$b]['edit'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }
            for($c=0;$c<count($datas['org_access']);$c++)
            {
                $orgData[] = [
                    'org_id' => $datas['org_id'],
                    "group_id" => $groupDetails->id,
                    "property_id" => $datas['org_access'][$c]['property_id'],
                    "access" => $datas['org_access'][$c]['access'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }

            if(Authorizationgrpcontent::insert($data) && Authorizationgrpmilestone::insert($milestoneData) && Authgrpworkspacefields::insert($workspacefieldsData) && Authgrpworkspacesections::insert($workspacesectionsData) && Authgrporgaccess::insert($orgData))
            {
                $returnData = Authorizationgrp::where('id',$groupDetails->id)->get();
                $data = array ("message" => 'Auth group Added successfully',"data" => $returnData );
                return Response::json($data,200);
            }
        }
        else
        {
            return response()->json(['response'=>"Auth group not created"], 401); 
        }
    }

    function getAuthgrplists()
    {
        $lists = Authorizationgrp::where('isDeleted',0)->get();
        return Response::json(["response"=>$lists],200);
    }

    function getWorkspacemaster()
    {
        $details = Projectworkspacemaster::where('isDeleted',0)->select('id','content')->get();
        return Response::json(["response"=>$details],200);
    }
    function getWorkspacesections()
    {
        $details = Projectworkspacesections::where('isDeleted',0)->select('id','content')->get();
        return Response::json(["response"=>$details],200);
    }
    
}
