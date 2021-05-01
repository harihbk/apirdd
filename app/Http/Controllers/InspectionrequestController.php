<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projectinspections;
use App\Models\Checklisttemplate;
use App\Models\Inspectionchecklistmaster;
use App\Models\Projectinspectionitems;
use App\Models\Projectinspectionattachments;
use App\Models\Project;
use App\Models\Inspectionroot;
use App\Models\SiteInspectionReport;
use App\Models\Siteinspectionitems;
use Response;
use Validator;
use DB;
use File;

class InspectionrequestController extends Controller
{
    /* Creating Inspection Request */
    function createInspectionRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'phase_id' => 'required', 
            'checklist_id' => 'required',
            'inspection_type' => 'required',
            'requested_time' => 'required',
            'user_id' => 'required'
        ]);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $inspectionItems = array();
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
         //check if checklist exists 
         $checklistCheck = Checklisttemplate::where('id',$request->input('checklist_id'))->where('isDeleted',0)->count();
         if($checklistCheck==0)
         {
            return response()->json(['response'=>"Selected Checklist not Exists"], 410);
         }
         else
         {
            $checklistdetails = Inspectionchecklistmaster::where("template_id",$request->input('checklist_id'))->where("isDeleted",0)->get();
            //create inspections 
            $inspection = new Projectinspections();

            $inspection->project_id = $request->input('project_id');
            $inspection->phase_id = $request->input('phase_id');
            $inspection->inspection_type = $request->input('inspection_type');
            $inspection->requested_time = $request->input('requested_time');
            $inspection->checklist_id = $request->input('checklist_id');
            $inspection->comments = $request->input('comments');
            $inspection->rdd_member_id = $request->input('user_id');
            $inspection->created_at = date('Y-m-d H:i:s');
            $inspection->updated_at = date('Y-m-d H:i:s');

            if($inspection->save())
            {
                $returnData = Projectinspections::select('inspection_id','inspection_type')->find($inspection->inspection_id);
                $inspection_id = $returnData['inspection_id']; 
                //populate inspection items
                for($i=0;$i<count($checklistdetails);$i++)
                {
                    $inspectionItems[] = [
                        "project_id" => $request->input('project_id'),
                        "inspection_id" => $inspection_id,
                        "template_id" => $request->input('checklist_id'),
                        "ch_id" => $checklistdetails[$i]['ch_id'],
                        "root_id" => $checklistdetails[$i]['root_id'],
                        "checklist_desc" => $checklistdetails[$i]['checklist_desc'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                    ];
                }
                if(Projectinspectionitems::insert($inspectionItems))
                {
                    $inspectionData = Projectinspections::select('inspection_id','inspection_type','requested_time')->find($inspection->inspection_id);
                    $data = array ("message" => 'Inspection Created successfully',"data" => $inspectionData );
                    $response = Response::json($data,200);
                    return $response;
                }
                else
                {
                    return response()->json(['response'=>"Inspection items not created"], 410);
                }
            }
            else
            {
                return response()->json(['response'=>"Inspection Request not Created"], 410);
            }
         }
    }
    /* Investor listing the inspection request for selected project */
    function investorinspectionList(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $query = Projectinspections::leftjoin('tbl_projects','tbl_project_inspections.inspection_id','=','tbl_projects.project_id')->leftjoin('users','users.mem_id','=','tbl_project_inspections.rdd_member_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_inspections.investor_id')->select('tbl_projects.project_name','tbl_project_inspections.inspection_id','tbl_project_inspections.inspection_type','tbl_project_inspections.requested_time','tbl_project_inspections.created_at','tbl_project_inspections.investor_id','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name','users.mem_name','users.mem_last_name','tbl_project_inspections.checklist_id')->where('tbl_project_inspections.project_id',$request->input('project_id'));

        if ($request->has('inspection_type') && !empty($request->input('inspection_type')))
        {
            $query->where('tbl_project_inspections.checklist_id',$request->input('inspection_type'));
        }
        $lists = $query->get();
        return response()->json(['inspections'=>$lists], 200);
    }
    /* Investor get selected inspection data */
    function investorretrieveInspectiondata(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'inspection_id' => 'required',
            'doc_path' => 'required',
            'img_path' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $query = Projectinspectionitems::leftjoin('tbl_project_inspections','tbl_project_inspections.inspection_id','=','tbl_project_inspection_items.inspection_id')->leftjoin('tbl_project_inspection_attachments','tbl_project_inspection_attachments.inspection_item_id','=','tbl_project_inspection_items.id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->select('tbl_inspection_root_categories.root_name','tbl_project_inspection_items.id','tbl_project_inspection_items.checklist_desc','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.remarks',DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.file_name) as file_name"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.file_path) as file_path"))->where('tbl_project_inspection_items.project_id',$request->input('project_id'))->where('tbl_project_inspection_items.inspection_id',$request->input('inspection_id'))->where('tbl_project_inspection_items.isDeleted',0)->where('tbl_project_inspection_items.isRescheduled',0)->groupBy('tbl_project_inspection_items.id')->get()->groupBy('root_name');
        
        $path_details = ProjectInspections::leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_inspections.phase_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->select('tbl_project_inspections.inspection_type','tbl_phase_master.phase_name','tbl_phase_master.phase_id','tbl_projects.project_name')->where('tbl_project_inspections.project_id',$request->input('project_id'))->get();

        $phase_name = $path_details[0]['phase_name'];
        $project_name = $path_details[0]['project_name'];
        $phase_id = $path_details[0]['phase_id'];
        $inspection_type = $path_details[0]['inspection_type'];
       
        $doc_path = public_path()."".$request->input('doc_path')."".$request->input('project_id')."_".$project_name."/".$phase_name;
        $img_path = public_path()."".$request->input('img_path')."".$request->input('project_id')."_".$project_name."/".$phase_name;
        if(!File::isDirectory($doc_path)){
            File::makeDirectory($doc_path, 0777, true, true);
           }
           if(!File::isDirectory($img_path)){
               File::makeDirectory($img_path, 0777, true, true);
           }
        
        return Response::json(array('doc_path' => $doc_path, 'image_path' => $img_path,'inspection_type'=>$inspection_type,'inspection_items' => $query,'phase_id'=>$phase_id));
    }
    /*Investor Update inspection details for submission */
    function updateInspectionrequestdetails(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [ 
            'datas.*.project_id' => 'required',
            'datas.*.inspection_id' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $uploaded_status=1;
        $approved_status=2;
        $rejected_status=3;
        $fileData = array();
        //check if inspection is completed
        $statusCount = Projectinspections::where('inspection_id',$datas[0]['inspection_id'])->whereIn('inspection_status',[$uploaded_status,$approved_status,$rejected_status])->count();
        if($statusCount>0)
        {
            return response()->json(['response'=>"Inspection details May be submitted,approved or rejected"], 410); 
        }
        else
        {
            for($j=0;$j<count($datas);$j++)
            {
                for($i=0;$i<count($datas[$j]['entries']);$i++)
                {
                    Projectinspectionitems::where("inspection_id",$datas[$j]['inspection_id'])->where("project_id",$datas[$j]['project_id'])->where("root_id",$datas[$j]['entries'][$i]['root_id'])->update( 
                        array( 
                        "rdd_actuals" => $datas[$j]['entries'][$i]['rdd_actuals'],
                        "remarks" => $datas[$j]['entries'][$i]['remarks'],
                        ));
                }   
            }
            Projectinspections::where('inspection_id',$datas[0]['inspection_id'])->where('project_id',$datas[0]['project_id'])->update(array(
                "inspection_status" => $uploaded_status
            ));
            return response()->json(['response'=>"Inspection details Updated"], 200); 
        } 
    }
    /*Investor Update uploaded file details for inspection */
    function updateInspectionfiledetails(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [ 
            'datas.*.project_id' => 'required',
            'datas.*.inspection_id' => 'required',
            'datas.*.id' => 'required',
            'datas.*.files.*.file_name' => 'required',
            'datas.*.files.*.file_path' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $uploaded_status=1;
        $approved_status=2;
        $rejected_status=3;
        $fileData = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $statusCount = Projectinspections::where('inspection_id',$datas[0]['inspection_id'])->whereIn('inspection_status',[$uploaded_status,$approved_status,$rejected_status])->count();
        if($statusCount>0)
        {
            return response()->json(['response'=>"Inspection details May be submitted,approved or rejected"], 410); 
        }
        else
        {
            for($j=0;$j<count($datas);$j++)
            {
                for($i=0;$i<count($datas[$j]['files']);$i++)
                {
                    $fileData[] = [
                        'project_id' => $datas[$j]['project_id'],
                        'inspection_id' => $datas[$j]['inspection_id'],
                        'inspection_item_id' => $datas[$j]['id'],
                        "investor_file_name" => $datas[$j]['files'][$i]['file_name'],
                        "investor_file_path" => $datas[$j]['files'][$i]['file_path'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "uploaded_by"=> $datas[$j]['user_id']
                    ];
                }
            }
            if(Projectinspectionattachments::insert($fileData))
            {
                return response()->json(['response'=>"File updated for inspection"], 200);
            }
        }
    }
    function rddperformApprovalinspections(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'inspection_id' => 'required',
            'approval_status' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $rescheduled_status=4;
        $approved_status=2;
        $rejected_status=3;
        $updated_at = date('Y-m-d H:i:s');
        $comments = $request->input('commemts')?$request->input('commemts'):null;
        //check for inspection status if already approvedor rejected
        $approvalCount = Projectinspections::where('inspection_id',$request->input('inspection_id'))->where('project_id',$request->input('project_id'))->whereIn('inspection_status',[$approved_status,$rejected_status,$rescheduled_status])->count();
        if($approvalCount>0)
        {
            return response()->json(['response'=>"Approval action already done"], 410);
        }
        else
        {
            $approval_action = Projectinspections::where('inspection_id',$request->input('inspection_id'))->where('project_id',$request->input('project_id'))->update(
                array(
                    "inspection_status" => $request->input('approval_status'),
                    "action_by" => $request->input('user_id'),
                    "comments" => $comments,
                    "updated_at" => $updated_at
                )
                );
            if($approval_action!=0)
            {
                $returnData = Projectinspections::where('project_id',$request->input('project_id'))->where('inspection_status',$approved_status)->orderBy('updated_at')->get();
                $inspection_details = Projectinspections::where('project_id',$request->input('project_id'))->get();

                return response()->json(['response'=>"Approval action done for inpection","requested_inspections"=>$inspection_details,"actual_inspection_report"=>$returnData], 200);
            } 
            else
            {
                return response()->json(['response'=>"Approval action Not done"], 410);
            }
        }
    }
    /* RDD Member get selected inspection data */
    function rddretrieveInspectiondata(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required',
            'inspection_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $query = Projectinspectionitems::leftjoin('tbl_project_inspections','tbl_project_inspections.inspection_id','=','tbl_project_inspection_items.inspection_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->leftjoin('tbl_project_inspection_attachments','tbl_project_inspection_attachments.inspection_item_id','=','tbl_project_inspection_items.id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->select('tbl_inspection_root_categories.root_name','tbl_project_inspection_items.id','tbl_project_inspection_items.checklist_desc','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.remarks',DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_name) as investor_file_name"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_path) as investor_file_path"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_path) as rdd_file_path"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_path) as rdd_file_name"),'tbl_project_inspection_items.investor_declarations','tbl_project_inspection_items.rdd_snags','tbl_project_inspection_items.snag_type')->where('tbl_project_inspection_items.project_id',$request->input('project_id'))->where('tbl_project_inspection_items.inspection_id',$request->input('inspection_id'))->where('tbl_project_inspection_items.isDeleted',0)->where('tbl_project_inspection_items.isRescheduled',0)->groupBy('tbl_project_inspection_items.id')->get()->groupBy('root_name');
        
        $path_details = ProjectInspections::leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_inspections.phase_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->select('tbl_project_inspections.inspection_type','tbl_phase_master.phase_name','tbl_phase_master.phase_id','tbl_projects.project_name','tbl_project_inspections.requested_time')->where('tbl_project_inspections.project_id',$request->input('project_id'))->get();

        $phase_id = $path_details[0]['phase_id'];
        $inspection_type = $path_details[0]['inspection_type'];
        $requested_time = $path_details[0]['requested_time'];

        return Response::json(array('inspection_type'=>$inspection_type,'inspection_items' => $query,'phase_id'=>$phase_id,'requested_time'=>$requested_time));
    }
    /* RDD member rescheduling Inspection Request */
    function rddperforminspectionreschedule(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'inspection_id' => 'required',
            'phase_id' => 'required', 
            'checklist_id' => 'required',
            'inspection_type' => 'required',
            'requested_time' => 'required',
            'user_id' => 'required'
        ]);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $rescheduled_status=4;
        $approved_status=2;
        $rejected_status=3;
        $pending_status=0;
        $comments = $request->input('comments')?$request->input('comments'):null;
        $inspectionItems = array();
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
         //check if checklist exists 
         $checklistCheck = Checklisttemplate::where('id',$request->input('checklist_id'))->where('isDeleted',0)->count();
         if($checklistCheck==0)
         {
            return response()->json(['response'=>"Selected Checklist not Exists"], 410);
         }
         else
         {
            //check for inspection status before rescheduling
            $inspectionCount = ProjectInspections::where('inspection_id',$request->input('inspection_id'))->where('project_id',$request->input('project_id'))->whereIn('inspection_status',[$pending_status,$approved_status,$rejected_status,$rescheduled_status])->count();
            if($inspectionCount>0)
            {
                return response()->json(['response'=>"Inspection Cannot be rescheduled at this stage"], 410);
            }
            else
            {
                $checklistdetails = Inspectionchecklistmaster::where("template_id",$request->input('checklist_id'))->where("isDeleted",0)->get();
                 //Update inspections with reschedule data
                  $rescheduling = ProjectInspections::where('inspection_id',$request->input('inspection_id'))->where('project_id',$request->input('project_id'))->update(
                      array(
                          "inspection_status"=>$rescheduled_status,
                          "comments"=>$comments
                      )
                  );
                  if($rescheduling>0)
                  {
                    $updateInspectionitems = Projectinspectionitems::where('inspection_id',$request->input('inspection_id'))->where('project_id',$request->input('project_id'))->update(
                        array(
                            "isRescheduled"=>1
                        )
                    );
                      //map checklist items to project inspections
                      for($i=0;$i<count($checklistdetails);$i++)
                        {
                            $inspectionItems[] = [
                                "project_id" => $request->input('project_id'),
                                "inspection_id" => $request->input('inspection_id'),
                                "template_id" => $request->input('checklist_id'),
                                "ch_id" => $checklistdetails[$i]['ch_id'],
                                "root_id" => $checklistdetails[$i]['root_id'],
                                "checklist_desc" => $checklistdetails[$i]['checklist_desc'],
                                "created_at" => $created_at,
                                "updated_at" => $updated_at,
                            ];
                        }
                    if(Projectinspectionitems::insert($inspectionItems))
                    {
                        $inspection_details = Projectinspections::where('project_id',$request->input('project_id'))->get();
                        return response()->json(['response'=>"Inspection rescheduled Successfully","requested_inspections"=>$inspection_details], 200);
                    }
                  }
            }
         }  
    }
    /*RDD Member - update Uploaded file details during reports */
    function rddupdateInspectionfiledetails(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [ 
            'datas.*.project_id' => 'required',
            'datas.*.inspection_id' => 'required',
            'datas.*.id' => 'required',
            'datas.*.files.*.file_name' => 'required',
            'datas.*.files.*.file_path' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $uploaded_status=1;
        $approved_status=2;
        $rejected_status=3;
        $fileData = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $statusCount = Projectinspections::where('inspection_id',$datas[0]['inspection_id'])->whereIn('inspection_status',[$rejected_status])->count();
        if($statusCount>0)
        {
            return response()->json(['response'=>"Inspection details May be submitted,approved or rejected"], 410); 
        }
        else
        {
            for($j=0;$j<count($datas);$j++)
            {
                for($i=0;$i<count($datas[$j]['files']);$i++)
                {
                    $fileData[] = [
                        'project_id' => $datas[$j]['project_id'],
                        'inspection_id' => $datas[$j]['inspection_id'],
                        'inspection_item_id' => $datas[$j]['id'],
                        "rdd_file_name" => $datas[$j]['files'][$i]['file_name'],
                        "rdd_file_path" => $datas[$j]['files'][$i]['file_path'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        "uploaded_by"=> $datas[$j]['user_id']
                    ];
                }
            }
            if(Projectinspectionattachments::insert($fileData))
            {
                return response()->json(['response'=>"File updated on inspection"], 200);
            }
        }
    }
    /*RDD member - Generate Report */
    function rddGenerateinspectionReport(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [ 
            'datas.*.project_id' => 'required',
            'datas.*.inspection_id' => 'required',
            'datas.*.entries.*.id' => 'required',
        ]);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        //check whether inspection is approved
        $inspectionCount = Projectinspections::where('project_id',$datas[0]['project_id'])->where('inspection_id',$datas[0]['inspection_id'])->where('inspection_status',2)->count();
        if($inspectionCount>0)
        {
            for($i=0;$i<count($datas);$i++)
            {
                for($j=0;$j<count($datas[$i]['entries']);$j++)
                {
                    Projectinspectionitems::where('project_id',$datas[$i]['project_id'])->where('inspection_id',$datas[$i]['inspection_id'])->where('id',$datas[$i]['entries'][$j]['id'])->update(
                        array(
                            "rdd_actuals"=>$datas[$i]['entries'][$j]['rdd_actuals']?$datas[$i]['entries'][$j]['rdd_actuals']:0,
                            "rdd_snags"=>$datas[$i]['entries'][$j]['rdd_snags']?$datas[$i]['entries'][$j]['rdd_snags']:null,
                            "snag_type"=>$datas[$i]['entries'][$j]['snag_type']?$datas[$i]['entries'][$j]['snag_type']:0
                        )
                    );
                }
            }
            return response()->json(['response'=>"Inspection Report generated"], 200);
        }
        else
        {
            return response()->json(['response'=>"Inspection not approved Yet"], 410);
        }
    }
    /* RDD Member - Create Site Inspection Report */
    function createSiteinspectionRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'project_id' => 'required', 
            'phase_id' => 'required', 
            'checklist_id' => 'required',
            'inspection_type' => 'required',
            'previous_inspection_date' => 'required',
            'inspection_date' => 'required',
            'user_id' => 'required'
        ]);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $inspectionItems = array();
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
         //check if checklist exists 
         $checklistCheck = Checklisttemplate::where('id',$request->input('checklist_id'))->where('isDeleted',0)->count();
         if($checklistCheck==0)
         {
            return response()->json(['response'=>"Selected Checklist not Exists"], 410);
         }
         else
         {
            $checklistdetails = Inspectionchecklistmaster::where("template_id",$request->input('checklist_id'))->where("isDeleted",0)->get();
            //create inspections 
            $inspection = new SiteInspectionReport();

            $inspection->project_id = $request->input('project_id');
            $inspection->phase_id = $request->input('phase_id');
            $inspection->inspection_type = $request->input('inspection_type');
            $inspection->inspection_date = $request->input('inspection_date');
            $inspection->previous_inspection_date = $request->input('previous_inspection_date');
            $inspection->checklist_id = $request->input('checklist_id');
            $inspection->comments = $request->input('comments');
            $inspection->created_by = $request->input('user_id');
            $inspection->created_at = date('Y-m-d H:i:s');
            $inspection->updated_at = date('Y-m-d H:i:s');

            if($inspection->save())
            {
                $returnData = SiteInspectionReport::select('id','inspection_type')->find($inspection->id);
                $inspection_id = $returnData['id']; 
                //populate inspection items
                for($i=0;$i<count($checklistdetails);$i++)
                {
                    $inspectionItems[] = [
                        "project_id" => $request->input('project_id'),
                        "inspection_id" => $inspection_id,
                        "template_id" => $request->input('checklist_id'),
                        "ch_id" => $checklistdetails[$i]['ch_id'],
                        "root_id" => $checklistdetails[$i]['root_id'],
                        "checklist_desc" => $checklistdetails[$i]['checklist_desc'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                    ];
                }
                if(Siteinspectionitems::insert($inspectionItems))
                {
                    $inspectionData = SiteInspectionReport::where('project_id',$request->input('project_id'))->where('isDeleted',0)->get();
                    $data = array ("message" => 'Site Inspection Created successfully',"data" => $inspectionData );
                    $response = Response::json($data,200);
                    return $response;
                }
                else
                {
                    return response()->json(['response'=>"Inspection items not created"], 410);
                }
            }
            else
            {
                return response()->json(['response'=>"Inspection Request not Created"], 410);
            }
         }
    }
}
