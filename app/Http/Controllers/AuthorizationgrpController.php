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
            'datas.group_name' => 'required',
            'datas.user_id' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }

        //create authorisation group
        $group = new Authorizationgrp();
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
                    "group_id" => $groupDetails->id,
                    "content_id" => $datas['workspace_sections'][$b]['content_id'],
                    "display" => $datas['workspace_sections'][$b]['display'],
                    "change" => $datas['workspace_sections'][$b]['change'],
                    "edit" => $datas['workspace_sections'][$b]['edit'],
                    "created_at" => $created_at,
                    "updated_at" => $updated_at
                ];
            }
            for($c=0;$c<count($datas['org_access']);$c++)
            {
                for($d=0;$d<count($datas['org_access'][$c]['property']);$d++)
                {
                    $orgData[] = [
                        'org_id' => $datas['org_access'][$c]['org_id'],
                        "org_access" => $datas['org_access'][$c]['org_access'],
                        "group_id" => $groupDetails->id,
                        "property_id" => $datas['org_access'][$c]['property'][$d]['property_id'],
                        "property_access" => $datas['org_access'][$c]['property'][$d]['property_access'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at
                    ];
                }
                
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
    function getAuthgrpData($id)
    {
        $auth_grp_content = Authorizationgrp::leftjoin('tbl_authorization_group_content','tbl_authorization_group_content.group_id','=','tbl_authorization_groups.id')->leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_authorization_group_content.phase_id')->where('tbl_authorization_groups.id',$id)->where('tbl_authorization_groups.isDeleted',0)->get()->groupBy('phase_name');

        $auth_milestone = Authorizationgrp::leftjoin('tbl_authorization_group_milestones','tbl_authorization_group_milestones.group_id','=','tbl_authorization_groups.id')->leftjoin('tbl_milestone_config_master','tbl_milestone_config_master.config_id','=','tbl_authorization_group_milestones.config_id')->select('tbl_milestone_config_master.type_name','tbl_milestone_config_master.date_type','tbl_authorization_group_milestones.*')->where('tbl_authorization_groups.id',$id)->where('tbl_authorization_groups.isDeleted',0)->get()->groupBy('date_type');

        $workspace_fields = Authorizationgrp::leftjoin('tbl_authgrp_workspace_fields','tbl_authgrp_workspace_fields.group_id','=','tbl_authorization_groups.id')->leftjoin('tbl_project_workspace_master','tbl_project_workspace_master.id','=','tbl_authgrp_workspace_fields.content_id')->where('tbl_authorization_groups.id',$id)->where('tbl_authorization_groups.isDeleted',0)->get()->groupBy('content');

        $workspace_sections = Authorizationgrp::leftjoin('tbl_authgrp_workspace_sections','tbl_authgrp_workspace_sections.group_id','=','tbl_authorization_groups.id')->leftjoin('tbl_project_workspace_sections','tbl_project_workspace_sections.id','=','tbl_authgrp_workspace_sections.content_id')->where('tbl_authorization_groups.id',$id)->where('tbl_authorization_groups.isDeleted',0)->get()->groupBy('content');

        return Response::json(array('auth_grp_content' => $auth_grp_content,'auth_milestone' => $auth_milestone,"workspace_fields"=>$workspace_fields,"workspace_sections"=>$workspace_sections));
    }
    function editAuthorizationgrp(Request $request)
    {
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $datas = $request->get('datas');

        $validator = Validator::make($request->all(), [
            'datas.group_name' => 'required',
            'datas.user_id' => 'required',
            'datas.id' => 'required',
            'datas.project_creation' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['response'=>$validator->errors()], 401);            
        }


        for($i=0;$i<count($datas['content']);$i++)
        {
            Authorizationgrpcontent::where('group_id',$datas['id'])->where('id',$datas['content'][$i]['id'])->update(
                array(
                    "content_description"=> $datas['content'][$i]['content_description'],
                    "project_display"=> $datas['content'][$i]['project_display'],
                    "project_edit"=> $datas['content'][$i]['project_edit'],
                    "template_display"=> $datas['content'][$i]['template_display'],
                    "template_edit"=> $datas['content'][$i]['template_edit'],
                    "updated_at" => $updated_at
                )
            );
        }

        for($j=0;$j<count($datas['auth_milestone']);$j++)
        {
            Authorizationgrpmilestone::where('group_id',$datas['id'])->where('id',$datas['auth_milestone'][$j]['id'])->update(
                array(
                    "edit"=> $datas['auth_milestone'][$j]['edit'],
                    "updated_at"=> $updated_at
                )
            );
        }
        for($k=0;$k<count($datas['workspace_fields']);$k++)
        {
            Authgrpworkspacefields::where('group_id',$datas['id'])->where('id',$datas['workspace_fields'][$k]['id'])->update(
                array(
                    "display"=> $datas['workspace_fields'][$k]['display'],
                    "edit"=> $datas['workspace_fields'][$k]['edit'],
                    "updated_at"=> $updated_at
                )
            );
        }
        for($l=0;$l<count($datas['workspace_sections']);$l++)
        {
            Authgrpworkspacesections::where('group_id',$datas['id'])->where('id',$datas['workspace_sections'][$l]['id'])->update(
                array(
                    "display"=> $datas['workspace_sections'][$l]['display'],
                    "edit"=> $datas['workspace_sections'][$l]['edit'],
                    "change"=> $datas['workspace_sections'][$l]['change'],
                    "updated_at"=> $updated_at
                )
            );
        }
    $returnData = Authorizationgrp::where('id',$datas['id'])->get();
    $data = array ("message" => 'Auth group Edited successfully',"data" => $returnData );
    return Response::json($data,200);
}


}