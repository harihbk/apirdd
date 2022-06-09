<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Templatemaster;
use App\Models\Templatenamemaster;
use App\Models\Templatedocs;
use App\Models\Projecttype;
use App\Models\Templatedesignations;
use App\Models\Filesupload;
use Response;
use Validator;
use DB;
use File;

class TemplateController extends Controller
{
    function store(Request $request)
    {
        $templatename = new Templatenamemaster();
        $doc_data = [];
        $validator = Validator::make($request->all(), [ 
            'datas.*.phase_id' => 'required', 
            'datas.*.tasks.*.person' => 'required',
            'datas.*.tasks.*.activity_desc' => 'required',
            'datas.*.tasks.*.approvers' => 'required',
            'datas.*.tasks.*.duration' => 'required',
            'datas.*.tasks.*.task_type' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $doc_validator = Validator::make($request->all(), [ 
            'datas.*.docs.*.doc_header' => 'required',
            'datas.*.docs.*.doc_title' => 'required',
            'datas.*.docs.*.reviewers' => 'required',
            'datas.*.docs.*.approvers_level1' => 'required'
        ]);

        if ($doc_validator->fails()) { 
            return response()->json(['error'=>$doc_validator->errors()], 401);            
        }
        $datas = $request->get('datas');
        $templatename->org_id = $datas[0]['org_id'];
        $templatename->template_name = $datas[0]['template_name'];
        $templatename->created_at = date('Y-m-d H:i:s');
        $templatename->updated_at = date('Y-m-d H:i:s');
        $templatename->created_by = $datas[0]['user_id'];

        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');

        if($templatename->save())
        {
            $template = $templatename->find($templatename->template_id);
        }
        else
        {
            return response()->json(['error'=>'Unable to create template'], 401); 
        }

        for($i=0;$i<count($datas);$i++) 
        {
            for($k=0;$k<count($datas[$i]['tasks']);$k++)
            {
                $data[] = [
                            'template_id' => $template->template_id,
                            'org_id' => $datas[0]['org_id'],
                            "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                            'phase_id' => $datas[$i]['phase_id'],
                            'created_by' => $datas[0]['user_id'],
                            "activity_desc" =>$datas[$i]['tasks'][$k]['activity_desc'],
                            "person" => $datas[$i]['tasks'][$k]['person'],
                            "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                            "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                            "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                            "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                            "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                            "file_upload_path" => $datas[$i]['tasks'][$k]['file_upload_path'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            "duration" => $datas[$i]['tasks'][$k]['duration']
                            ];
            }
            for($j=0;$j<count($datas[$i]['docs']);$j++)
            {
                $doc_data[] = [
                    'template_id' => $template->template_id,
                    'org_id' => $datas[0]['org_id'],
                    'phase_id' => $datas[$i]['phase_id'],
                    'doc_header' => $datas[$i]['docs'][$j]['doc_header'],
                    'doc_title' => $datas[$i]['docs'][$j]['doc_title'],
                    'reviewers' => $datas[$i]['docs'][$j]['reviewers'],
                    'approvers_level1' => $datas[$i]['docs'][$j]['approvers_level1'],
                    'approvers_level2' => $datas[$i]['docs'][$j]['approvers_level2'],
                   ];
            }
        }
        
          if(Templatemaster::insert($data) && Templatedocs::insert($doc_data))
          {
            //get template tasks designations
            $this->storeDesignations($template->template_id,$datas[0]['org_id'],$datas[0]['user_id']);
            $data = array ("message" => 'Template added successfully');
            $response = Response::json($data,200);
            echo json_encode($response);
          }
    }


    function deleteemplate(Request $request){
        $temp_id = $request->get('template_id');
        $UpdateDetails = Templatenamemaster::where('template_id', '=',  $temp_id)->first();
        $UpdateDetails->active_status = 0;
        $UpdateDetails->save();

        Projecttype::where('template_id', '=',  $temp_id)->delete();


        $response = Response::json("deleted",200);
        echo json_encode($response);

    }


   function copystore(Request $request){
       // get previous template
        $temp_id = $request->get('template_id');
        $temp_name = $request->get('templatename');
        $user_id  = $request->get('user_id');
        $org_id = $request->get('org_id');
        $prevtemplatename =  Templatenamemaster::find($temp_id)->first();

        $templatename = new Templatenamemaster();
        $templatename->org_id = $org_id;
        $templatename->template_name = $temp_name;
        $templatename->created_at = date('Y-m-d H:i:s');
        $templatename->updated_at = date('Y-m-d H:i:s');
        $templatename->created_by = $user_id;
        if($templatename->save())
        {

            $pt = new Projecttype();
            $pt->org_id = $org_id;
            $pt->template_id = $templatename->template_id;
            $pt->type_name = $temp_name;
            $pt->created_at = date('Y-m-d H:i:s');
            $pt->updated_at = date('Y-m-d H:i:s');
            $pt->created_by = $user_id;
            $pt->active_status = 1;
            $pt->save();


            $created_at =  date('Y-m-d H:i:s');
            $updated_at =  date('Y-m-d H:i:s');
            $template = $templatename->find($templatename->template_id);
            $tempmaster = Templatemaster::where('template_id',$temp_id)->get()->toArray();
            for($k=0;$k<count($tempmaster);$k++)
            {
                $data[] = [
                            'template_id' => $template->template_id,
                            'org_id' => $org_id,
                            "task_type" => $tempmaster[$k]['task_type'],
                            'phase_id' => $tempmaster[$k]['phase_id'],
                            'created_by' => $user_id,
                            "activity_desc" =>$tempmaster[$k]['activity_desc'],
                            "person" => $tempmaster[$k]['person'],
                            "approvers" => $tempmaster[$k]['approvers'],
                            "attendees" => $tempmaster[$k]['attendees'],
                            "fre_id" => $tempmaster[$k]['fre_id'],
                            "seq_status" => $tempmaster[$k]['seq_status'],
                            "seq_no" => $tempmaster[$k]['seq_no'],
                            "file_upload_path" => $tempmaster[$k]['file_upload_path'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            "duration" => $tempmaster[$k]['duration']
                            ];
            }

            $tempdocs = Templatedocs::where('template_id',$temp_id)->get()->toArray();
            for($j=0;$j<count($tempdocs);$j++)
            {
                $doc_data[] = [
                    'template_id' => $template->template_id,
                    'org_id' => $org_id,
                    'phase_id' => $tempdocs[$j]['phase_id'],
                    'doc_header' => $tempdocs[$j]['doc_header'],
                    'doc_title' => $tempdocs[$j]['doc_title'],
                    'reviewers' => $tempdocs[$j]['reviewers'],
                    'approvers_level1' => $tempdocs[$j]['approvers_level1'],
                    'approvers_level2' => $tempdocs[$j]['approvers_level2'],
                   ];
            }


            if(Templatemaster::insert($data) && Templatedocs::insert($doc_data))
            {
              //get template tasks designations
              $this->storeDesignations($template->template_id,$org_id,$user_id);
              $data = array ("message" => 'Template added successfully');
              $response = Response::json($data,200);
              echo json_encode($response);
            }
        }
   }

   function update(Request $request,$template_id)
   {
       $datas = $request->get('datas');
       $data=[];
       $doc_data =[];
       $types = new Templatemaster();

       $created_at =  date('Y-m-d H:i:s');
       $updated_at =  date('Y-m-d H:i:s');

        $update_count = 0;


        for($i=0;$i<count($datas);$i++)
        {
           for($k=0;$k<count($datas[$i]['tasks']);$k++)
           {
               if($datas[$i]['tasks'][$k]['master_id']!=0)
               {
                   $templates = Templatemaster::where("master_id",$datas[$i]['tasks'][$k]['master_id'])->where("org_id",$datas[$i]['org_id'])->where("template_id",$datas[$i]['template_id'])->update(
                               array(
                                "activity_desc" => $datas[$i]['tasks'][$k]['activity_desc'],
                                "person" => $datas[$i]['tasks'][$k]['person'],
                                "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                                "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                                "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                                "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                                "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                                "file_upload_path" => $datas[$i]['tasks'][$k]['file_upload_path'],
                                "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                                "duration" =>$datas[$i]['tasks'][$k]['duration'],
                                "updated_at" => date('Y-m-d H:i:s'),
                                "created_by" => $datas[$i]['user_id'],
                                "active_status" =>$datas[$i]['tasks'][$k]['active_status'],
                                "isDeleted" =>$datas[$i]['tasks'][$k]['isDeleted'],
                                ));
               }
               else
               {
                   $data[] = [
                       'template_id' => $template_id,
                       'org_id' => $datas[$i]['org_id'],
                       "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                       'phase_id' => $datas[$i]['phase_id'],
                       'created_by' => $datas[$i]['user_id'],
                       "activity_desc" =>$datas[$i]['tasks'][$k]['activity_desc'],
                       "person" => $datas[$i]['tasks'][$k]['person'],
                       "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                       "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                       "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                       "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                       "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                       "file_upload_path" => $datas[$i]['tasks'][$k]['file_upload_path'],
                       "created_at" => $created_at,
                       "updated_at" => $updated_at,
                       "duration" => $datas[$i]['tasks'][$k]['duration']
                       ];
               }
           }
           for($j=0;$j<count($datas[$i]['docs']);$j++)
           {
               if($datas[$i]['docs'][$j]['doc_id']!=0)
               {
                   //update existing doc data
                   $templates_doc = Templatedocs::where("doc_id",$datas[$i]['docs'][$j]['doc_id'])->where("org_id",$datas[$i]['org_id'])->where("template_id",$datas[$i]['template_id'])->update(
                       array(
                        "doc_header" => $datas[$i]['docs'][$j]['doc_header'],
                        "doc_title" => $datas[$i]['docs'][$j]['doc_title'],
                        "reviewers" => $datas[$i]['docs'][$j]['reviewers'],
                        "approvers_level1" => $datas[$i]['docs'][$j]['approvers_level1'],
                        "approvers_level2" => $datas[$i]['docs'][$j]['approvers_level2'],
                        "isDeleted" => $datas[$i]['docs'][$j]['isDeleted'],
                        ));
               }
               else
               {
                   //insert new doc data
                   $doc_data[] = [
                       'template_id' => $template_id,
                       'org_id' => $datas[$i]['org_id'],
                       'phase_id' => $datas[$i]['phase_id'],
                       'doc_header' => $datas[$i]['docs'][$j]['doc_header'],
                       'doc_title' => $datas[$i]['docs'][$j]['doc_title'],
                       'reviewers' => $datas[$i]['docs'][$j]['reviewers'],
                       'approvers_level1' => $datas[$i]['docs'][$j]['approvers_level1'],
                       'approvers_level2' => $datas[$i]['docs'][$j]['approvers_level2'],
                      ];
               }
           }
        }
        $validator = Validator::make($request->all(), [
           'datas.*.phase_id' => 'required',
           'datas.*.org_id' => 'required',
           'datas.*.user_id' => 'required',
           'datas.*.tasks.*.person' => 'required',
           'datas.*.tasks.*.activity_desc' => 'required',
           'datas.*.tasks.*.approvers' => 'required',
           'datas.*.tasks.*.attendees' => 'required',
           'datas.*.tasks.*.duration' => 'required',
           'datas.*.tasks.*.task_type' => 'required',
       ]);

       // if ($validator->fails()) {
       //     return response()->json(['error'=>$validator->errors()], 401);
       // }

       $doc_validator = Validator::make($request->all(), [
           'datas.*.docs.*.doc_id' => 'required',
           'datas.*.docs.*.doc_header' => 'required',
           'datas.*.docs.*.doc_title' => 'required',
           'datas.*.docs.*.reviewers' => 'required',
           'datas.*.docs.*.approvers_level1' => 'required'
       ]);

       // if ($doc_validator->fails()) {
       //     return response()->json(['error'=>$doc_validator->errors()], 401);
       // }

       if((Templatemaster::insert($data) || $template>0) && (Templatedocs::insert($doc_data) || $templates_doc>0))
           {
           //update template tasks designations
           $a = $this->updateDesignations($template_id,$datas[0]['org_id'],$datas[0]['user_id']);
           $returnData = Templatemaster::find($types->template_id);
           $data = array ("message" => 'Template Edited successfully',"data" => $returnData );
           $response = Response::json($data,200);
           echo json_encode($response);
          }
   }

   
    function update123(Request $request,$template_id)
    {
        $datas = $request->get('datas');
        $data=[];
        $doc_data =[];
        $types = new Templatemaster();

        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');

         $update_count = 0;


         for($i=0;$i<count($datas);$i++) 
         {
            for($k=0;$k<count($datas[$i]['tasks']);$k++)
            {
                if($datas[$i]['tasks'][$k]['master_id']!=0)
                {
                    $templates = Templatemaster::where("master_id",$datas[$i]['tasks'][$k]['master_id'])->where("org_id",$datas[$i]['org_id'])->where("template_id",$datas[$i]['template_id'])->update( 
                                array( 
                                 "activity_desc" => $datas[$i]['tasks'][$k]['activity_desc'],
                                 "person" => $datas[$i]['tasks'][$k]['person'],
                                 "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                                 "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                                 "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                                 "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                                 "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                                 "file_upload_path" => $datas[$i]['tasks'][$k]['file_upload_path'],
                                 "task_type" => $datas[$i]['tasks'][$k]['task_type'],                
                                 "duration" =>$datas[$i]['tasks'][$k]['duration'],
                                 "updated_at" => date('Y-m-d H:i:s'),
                                 "created_by" => $datas[$i]['user_id'],
                                 "active_status" =>$datas[$i]['tasks'][$k]['active_status'],
                                 "isDeleted" =>$datas[$i]['tasks'][$k]['isDeleted'],
                                 ));
                }
                else
                {
                    $data[] = [
                        'template_id' => $template_id,
                        'org_id' => $datas[$i]['org_id'],
                        "task_type" => $datas[$i]['tasks'][$k]['task_type'],
                        'phase_id' => $datas[$i]['phase_id'],
                        'created_by' => $datas[$i]['user_id'],
                        "activity_desc" =>$datas[$i]['tasks'][$k]['activity_desc'],
                        "person" => $datas[$i]['tasks'][$k]['person'],
                        "approvers" => $datas[$i]['tasks'][$k]['approvers'],
                        "attendees" => $datas[$i]['tasks'][$k]['attendees'],
                        "fre_id" => $datas[$i]['tasks'][$k]['fre_id'],
                        "seq_status" => $datas[$i]['tasks'][$k]['seq_status'],
                        "seq_no" => $datas[$i]['tasks'][$k]['seq_no'],
                        "file_upload_path" => $datas[$i]['tasks'][$k]['file_upload_path'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "duration" => $datas[$i]['tasks'][$k]['duration']
                        ];
                }
            }
            for($j=0;$j<count($datas[$i]['docs']);$j++)
            {
                if($datas[$i]['docs'][$j]['doc_id']!=0)
                {
                    //update existing doc data
                    $templates_doc = Templatedocs::where("doc_id",$datas[$i]['docs'][$j]['doc_id'])->where("org_id",$datas[$i]['org_id'])->where("template_id",$datas[$i]['template_id'])->update( 
                        array( 
                         "doc_header" => $datas[$i]['docs'][$j]['doc_header'],
                         "doc_title" => $datas[$i]['docs'][$j]['doc_title'],
                         "reviewers" => $datas[$i]['docs'][$j]['reviewers'],
                         "approvers_level1" => $datas[$i]['docs'][$j]['approvers_level1'],
                         "approvers_level2" => $datas[$i]['docs'][$j]['approvers_level2'],
                         "isDeleted" => $datas[$i]['docs'][$j]['isDeleted'],
                         ));
                }
                else
                {
                    //insert new doc data
                    $doc_data[] = [
                        'template_id' => $template_id,
                        'org_id' => $datas[$i]['org_id'],
                        'phase_id' => $datas[$i]['phase_id'],
                        'doc_header' => $datas[$i]['docs'][$j]['doc_header'],
                        'doc_title' => $datas[$i]['docs'][$j]['doc_title'],
                        'reviewers' => $datas[$i]['docs'][$j]['reviewers'],
                        'approvers_level1' => $datas[$i]['docs'][$j]['approvers_level1'],
                        'approvers_level2' => $datas[$i]['docs'][$j]['approvers_level2'],
                       ];
                }
            }
         }
         $validator = Validator::make($request->all(), [ 
            'datas.*.phase_id' => 'required',
            'datas.*.org_id' => 'required',
            'datas.*.user_id' => 'required', 
            'datas.*.tasks.*.person' => 'required',
            'datas.*.tasks.*.activity_desc' => 'required',
            'datas.*.tasks.*.approvers' => 'required',
            'datas.*.tasks.*.attendees' => 'required',
            'datas.*.tasks.*.duration' => 'required',
            'datas.*.tasks.*.task_type' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $doc_validator = Validator::make($request->all(), [ 
            'datas.*.docs.*.doc_id' => 'required',
            'datas.*.docs.*.doc_header' => 'required',
            'datas.*.docs.*.doc_title' => 'required',
            'datas.*.docs.*.reviewers' => 'required',
            'datas.*.docs.*.approvers_level1' => 'required'
        ]);

        if ($doc_validator->fails()) { 
            return response()->json(['error'=>$doc_validator->errors()], 401);            
        }

        if((Templatemaster::insert($data) || $template>0) && (Templatedocs::insert($doc_data) || $templates_doc>0))
            {
            //update template tasks designations
            $a = $this->updateDesignations($template_id,$datas[0]['org_id'],$datas[0]['user_id']);
            $returnData = Templatemaster::find($types->template_id);
            $data = array ("message" => 'Template Edited successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
           }
    }
    function retrievebyTemplate(Request $request,$id,$pid)
    {
        $typeCheck = ProjectType::where('type_id',$pid)->first();
        $temp='';
        if (!$typeCheck == null)
         {
            $temp = ProjectType::where('type_id',$pid)->first()->value('template_id');
         }
         else
         {
            $data = array ("message" => 'Project does not have Template');
            $response = Response::json($data,200);
            echo json_encode($response); 
         }
        $types = Templatemaster::where("org_id",$id)->where("template_id",$temp)->where("isDeleted",0);
        if ($request->input('approvers_base')) {
            $types->where('approvers',$request->input('approvers_base'));
        }

        echo json_encode($types->get()->groupBy('phase_name')); 
    }
   
    function getTemplatePhase(Request $request,$id,$tid)
    {
        $types = Templatemaster::select('phase_name')->where("org_id",$id)->where("template_id",$tid)->where("isDeleted",0);
        if ($request->input('approvers_base')) {
            $types->where('approvers',$request->input('approvers_base'));
        }

        echo json_encode($types->groupBy('phase_name')->get()); 
    }
    function getTemplateData($template_id,$phaseid)
    {
        if($phaseid=='' || $phaseid==null)
        {
            return response()->json(['response'=>"Phase Id Missing"], 411);
        }
        $types = Templatemaster::join('tbl_designation_master as a',\DB::raw("FIND_IN_SET(a.designation_id,tbl_template_master.person)"),">",\DB::raw("'0'"))->join('tbl_designation_master as b',\DB::raw("FIND_IN_SET(b.designation_id,tbl_template_master.approvers)"),">",\DB::raw("'0'"))->join('tbl_designation_master as c',\DB::raw("FIND_IN_SET(c.designation_id,tbl_template_master.attendees)"),">",\DB::raw("'0'"))->select(DB::raw("GROUP_CONCAT(DISTINCT c.designation_name) as attendees_designation"),DB::raw("GROUP_CONCAT(DISTINCT  b.designation_name) as approvers_designtion"),DB::raw("GROUP_CONCAT(DISTINCT a.designation_name) as person_designation"),'tbl_template_master.master_id','tbl_template_master.template_id','tbl_template_master.org_id','tbl_template_master.task_type','tbl_template_master.phase_id','tbl_template_master.activity_desc','tbl_template_master.approvers','tbl_template_master.attendees','tbl_template_master.fre_id','tbl_template_master.seq_status','tbl_template_master.seq_no','tbl_template_master.duration','tbl_template_master.start_date','tbl_template_master.end_date','tbl_template_master.file_upload_path','tbl_template_master.person')->where("tbl_template_master.template_id",$template_id)->where("tbl_template_master.phase_id",$phaseid)->where("tbl_template_master.isDeleted",0)->groupBy('tbl_template_master.master_id')->get();

        $docs = Templatedocs::select('tbl_template_docs_master.*',DB::raw("GROUP_CONCAT(DISTINCT d.designation_name) as reviewers_designation"),DB::raw("GROUP_CONCAT(DISTINCT e.designation_name) as approvers_level1_designation"),DB::raw("GROUP_CONCAT(DISTINCT e.designation_name) as approvers_level1_designation"),DB::raw("GROUP_CONCAT(DISTINCT f.designation_name) as approvers_level2_designation"))->leftjoin('tbl_designation_master as d',\DB::raw("FIND_IN_SET(d.designation_id,tbl_template_docs_master.reviewers)"),">",\DB::raw("'0'"))->leftjoin('tbl_designation_master as e',\DB::raw("FIND_IN_SET(e.designation_id,tbl_template_docs_master.approvers_level1)"),">",\DB::raw("'0'"))->leftjoin('tbl_designation_master as f',\DB::raw("FIND_IN_SET(f.designation_id,tbl_template_docs_master.approvers_level2)"),">",\DB::raw("'0'"))->where("template_id",$template_id)->where("phase_id",$phaseid)->where("isDeleted",0)->groupBy('doc_id')->get()->groupBy('doc_header');
        
        return Response::json(['tasks' => $types,'docs' => $docs],'200');    
    }
    function getTemplatelist(Request $request,$org_id)
    {
        $doc_validator = Validator::make($request->all(), [ 
            'doc_path' => 'required'
        ]);

        if ($doc_validator->fails()) { 
            return response()->json(['error'=>$doc_validator->errors()], 401);            
        }
        $lists = Templatenamemaster::select('template_id','org_id','template_name')->where('org_id',$org_id)->where('active_status',1)->get();
        $doc_path = public_path()."".$request->input('doc_path')."settings/templates";
        if(!File::isDirectory($doc_path)){
               File::makeDirectory($doc_path, 0777, true, true);
           }
        return Response::json(["response"=>$lists,"doc_path"=>$doc_path],'200');
    }
    //for storing designations on template creation
    function storeDesignations($template_id,$org_id,$user_id)
    {
        $datas = [];
        $designations = [];
        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');
        $tasks = Templatemaster::where('template_id',$template_id)->where('isDeleted',0)->select('person','approvers','attendees')->get();
        $docs = Templatedocs::where('template_id',$template_id)->where('isDeleted',0)->select('reviewers','approvers_level1','approvers_level2')->get();
        for($i=0;$i<count($tasks);$i++)
        {
            $a = explode(',',$tasks[$i]['person']);
            $b = explode(',',$tasks[$i]['approvers']);
            $c = explode(',',$tasks[$i]['attendees']);
            
            for($j=0;$j<count($a);$j++)
            {
                if(!in_array($a[$j],$datas))
                {
                    array_push($datas,$a[$j]);
                }
            }
            for($k=0;$k<count($b);$k++)
            {
                if(!in_array($b[$k],$datas))
                {
                    array_push($datas,$b[$k]);
                }
            }
            for($l=0;$l<count($c);$l++)
            {
                if(!in_array($c[$l],$datas))
                {
                    array_push($datas,$c[$l]);
                }
            }
        }
        for($m=0;$m<count($docs);$m++)
        {
            $p = explode(',',$docs[$m]['reviewers']);
            $q = explode(',',$docs[$m]['approvers_level1']);
            $r = explode(',',$docs[$m]['approvers_level2']);
            for($x=0;$x<count($p);$x++)
            {
                if(!in_array($p[$x],$datas))
                {
                    array_push($datas,$p[$x]);
                }
            }
            for($y=0;$y<count($q);$y++)
            {
                if(!in_array($q[$y],$datas))
                {
                    array_push($datas,$q[$y]);
                }
            }
            for($z=0;$z<count($r);$z++)
            {
                if(!in_array($r[$z],$datas))
                {
                    array_push($datas,$r[$z]);
                }
            }
        }
        //store designations
        for($r=0;$r<count($datas);$r++)
        {
            $designations[] = [
                'org_id' => $org_id,
                'template_id' => $template_id,
                'designation_id' => $datas[$r],
                'created_by' => $user_id,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            ];
        }
        if(Templatedesignations::insert($designations))
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }
    //for editing designations on template edit
    function updateDesignations($template_id,$org_id,$user_id)
    {
        $datas = [];
        $designations = [];
        $created_at =  date('Y-m-d H:i:s');
        $updated_at =  date('Y-m-d H:i:s');
        //remove the old entries and enter new entries
        Templatedesignations::where('template_id',$template_id)->delete();
        $tasks = Templatemaster::where('template_id',$template_id)->where('isDeleted',0)->select('person','approvers','attendees')->get();
        $docs = Templatedocs::where('template_id',$template_id)->where('isDeleted',0)->select('reviewers','approvers_level1','approvers_level2')->get();
        for($i=0;$i<count($tasks);$i++)
        {
            $a = explode(',',$tasks[$i]['person']);
            $b = explode(',',$tasks[$i]['approvers']);
            $c = explode(',',$tasks[$i]['attendees']);
            
            for($j=0;$j<count($a);$j++)
            {
                if(!in_array($a[$j],$datas))
                {
                    array_push($datas,$a[$j]);
                }
            }
            for($k=0;$k<count($b);$k++)
            {
                if(!in_array($b[$k],$datas))
                {
                    array_push($datas,$b[$k]);
                }
            }
            for($l=0;$l<count($c);$l++)
            {
                if(!in_array($c[$l],$datas))
                {
                    array_push($datas,$c[$l]);
                }
            }
        }
        for($m=0;$m<count($docs);$m++)
        {
            $p = explode(',',$docs[$m]['reviewers']);
            $q = explode(',',$docs[$m]['approvers_level1']);
            $r = explode(',',$docs[$m]['approvers_level2']);
            for($x=0;$x<count($p);$x++)
            {
                if(!in_array($p[$x],$datas))
                {
                    array_push($datas,$p[$x]);
                }
            }
            for($y=0;$y<count($q);$y++)
            {
                if(!in_array($q[$y],$datas))
                {
                    array_push($datas,$q[$y]);
                }
            }
            for($z=0;$z<count($r);$z++)
            {
                if(!in_array($r[$z],$datas))
                {
                    array_push($datas,$r[$z]);
                }
            }
        }
        //store designations
        for($r=0;$r<count($datas);$r++)
        {
            $designations[] = [
                'org_id' => $org_id,
                'template_id' => $template_id,
                'designation_id' => $datas[$r],
                'created_by' => $user_id,
                "created_at" => $created_at,
                "updated_at" => $updated_at
            ];
        }
             if(Templatedesignations::insert($designations))
                {
                    return 1;
                }
                else
                {
                    return 0;
                }
    }

}
