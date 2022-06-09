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
use App\Models\Siteinspectionattachments;
use App\Models\Notifications;
use App\Models\Projectcontact;
use App\Models\Maintainenceteam;
use Response;
use Validator;
use DB;
use File;
use PDF;
use Illuminate\Support\Facades\Mail;
use App\Mail\Projectinspection;

class InspectionrequestController extends Controller
{
    /* Creating Inspection Request */
    function createInspectionRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'phase_id' => 'required',
            'checklist_id' => 'required',
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
            $inspection->requested_time = $request->input('requested_time');
            $inspection->checklist_id = $request->input('checklist_id');
            $inspection->comments = $request->input('comments');
            $inspection->inspection_status = 1;
            $inspection->rdd_member_id = $request->input('user_id');
            $inspection->created_at = date('Y-m-d H:i:s');
            $inspection->updated_at = date('Y-m-d H:i:s');

            if($inspection->save())
            {
                $returnData = Projectinspections::select('inspection_id')->find($inspection->inspection_id);

                
                
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
                    $projectDetails = $this->getProjectDetails($request->input('project_id'));

                    $org_id = $projectDetails[0]['org_id'];
                    $property_id = $projectDetails[0]['property_id'];
                     

                     $investor =  Projectinspections::where("project_id",$request->input('project_id'))->where("inspection_id",$inspection->inspection_id)->first();
                     $inspectiontype = Checklisttemplate::where('id',$investor->checklist_id)->pluck('template_name')->first();
                    
                     $to = Projectcontact::where('project_id',$request->input('project_id'))->whereIn('member_designation',[13,14])->pluck('email')->toArray();

                     $cc = Projectcontact::where('project_id',$request->input('project_id'))->whereIn('member_designation',[2,27,5])->pluck('email')->toArray();
                     $to1 = Maintainenceteam::where('org_id',$org_id)->where('property_id',$property_id)->pluck('email')->toArray();
                     $cccc = array_merge($cc,$to1);
                     $subject = $projectDetails[0]["unit_name"]."-".$projectDetails[0]["property_name"]."-".$projectDetails[0]["investor_brand"]."- ".$inspectiontype." Invitation";

                     $data = [
                         'mem_name' => $projectDetails[0]['mem_name'],
                         'mem_last_name' => $projectDetails[0]['mem_last_name'],
                         'inspectiontype' => $inspectiontype,
                         'date'         => date('Y-m-d H:i:s')
                     ];

                    Mail::send('emails.requestinspectionrdd', $data, function($message)use($to,$cccc,$subject) {
                        $message->to($to)->cc($cccc)
                                ->subject($subject);
   
   
                    });



                    $inspectionData = Projectinspections::select('inspection_id','requested_time')->find($inspection->inspection_id);
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

        $query = Projectinspections::leftjoin('tbl_projects','tbl_project_inspections.project_id','=','tbl_projects.project_id')->leftjoin('users','users.mem_id','=','tbl_project_inspections.rdd_member_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_inspections.investor_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->select('tbl_project_inspections.*','tbl_projects.project_name','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name','users.mem_name','users.mem_last_name','tbl_properties_master.property_name','tbl_checklisttemplate_master.template_name as inspection_type')->where('tbl_project_inspections.project_id',$request->input('project_id'));

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
  
          $query = Projectinspectionitems::leftjoin('tbl_project_inspections','tbl_project_inspections.inspection_id','=','tbl_project_inspection_items.inspection_id')->leftjoin('tbl_project_inspection_attachments','tbl_project_inspection_attachments.inspection_item_id','=','tbl_project_inspection_items.id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->select('tbl_inspection_root_categories.root_name','tbl_project_inspection_items.id','tbl_project_inspection_items.checklist_desc','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.remarks',DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.rdd_file_name) as rdd_file_name"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.rdd_file_path) as rdd_file_path"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_name) as investor_file_name"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_path) as investor_file_path"),'tbl_project_inspection_items.ch_id','tbl_project_inspection_items.root_id','tbl_project_inspection_items.investor_declarations','tbl_project_inspection_items.snag_type','tbl_project_inspection_items.rdd_snags')->where('tbl_project_inspection_items.project_id',$request->input('project_id'))->where('tbl_project_inspection_items.inspection_id',$request->input('inspection_id'))->where('tbl_project_inspection_items.isDeleted',0)->where('tbl_project_inspection_items.isRescheduled',0)->groupBy('tbl_project_inspection_items.id')->get()->groupBy('root_name');
  
          foreach($query as $k=>$v){
              foreach($v as $kk=>$vv){
                $ins_item_id = $query[$k][$kk]['id'];
                $ff = Projectinspectionattachments::where('inspection_item_id',$ins_item_id)->where('project_id',$request->input('project_id'))->orderBy('id', 'desc')->first();
    //hari
                $query[$k][$kk]['rdd_file_path'] = $ff->rdd_file_path ?? '';
                $query[$k][$kk]['rdd_file_name'] = $ff->rdd_file_name ?? '';
                if($query[$k][$kk]['rdd_file_path']){
                    $ext = pathinfo($ff->rdd_file_name, PATHINFO_EXTENSION);
                    $query[$k][$kk]['extension'] = $ext;
                }
              }
            }
  
  
          $path_details = ProjectInspections::leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_inspections.phase_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->select('tbl_checklisttemplate_master.template_name as inspection_type','tbl_project_inspections.requested_time','tbl_phase_master.phase_name','tbl_phase_master.phase_id','tbl_projects.project_name','tbl_project_inspections.comments')->where('tbl_project_inspections.project_id',$request->input('project_id'))->where('tbl_project_inspections.inspection_id',$request->input('inspection_id'))->get();
  
          $phase_name = $path_details[0]['phase_name'];
          $project_name = $path_details[0]['project_name'];
          $phase_id = $path_details[0]['phase_id'];
          $inspection_type = $path_details[0]['inspection_type'];
          $requested_time = $path_details[0]['requested_time'];
          $comments = $path_details[0]['comments'];
  
          $doc_path = public_path()."".$request->input('doc_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
          $img_path = public_path()."".$request->input('img_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
          if(!File::isDirectory($doc_path)){
              File::makeDirectory($doc_path, 0777, true, true);
             }
             if(!File::isDirectory($img_path)){
                 File::makeDirectory($img_path, 0777, true, true);
             }
  
          return Response::json(array('comments'=>$comments,'doc_path' => $doc_path, 'image_path' => $img_path,'inspection_type'=>$inspection_type,'inspection_items' => $query,'phase_id'=>$phase_id,'requested_time' => $requested_time));
      }
      
    /* Investor get selected inspection data */
    function investorretrieveInspectiondatadd(Request $request)
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

        $query = Projectinspectionitems::leftjoin('tbl_project_inspections','tbl_project_inspections.inspection_id','=','tbl_project_inspection_items.inspection_id')->leftjoin('tbl_project_inspection_attachments','tbl_project_inspection_attachments.inspection_item_id','=','tbl_project_inspection_items.id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->select('tbl_inspection_root_categories.root_name','tbl_project_inspection_items.id','tbl_project_inspection_items.checklist_desc','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.remarks',DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.rdd_file_name) as rdd_file_name"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.rdd_file_path) as rdd_file_path"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_name) as investor_file_name"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_path) as investor_file_path"),'tbl_project_inspection_items.ch_id','tbl_project_inspection_items.root_id','tbl_project_inspection_items.investor_declarations','tbl_project_inspection_items.snag_type','tbl_project_inspection_items.rdd_snags')->where('tbl_project_inspection_items.project_id',$request->input('project_id'))->where('tbl_project_inspection_items.inspection_id',$request->input('inspection_id'))->where('tbl_project_inspection_items.isDeleted',0)->where('tbl_project_inspection_items.isRescheduled',0)->groupBy('tbl_project_inspection_items.id')->get()->groupBy('root_name');

        foreach($query as $k=>$v){
            foreach($v as $kk=>$vv){
              $ins_item_id = $query[$k][$kk]['id'];
              $ff = Projectinspectionattachments::where('inspection_item_id',$ins_item_id)->where('project_id',$request->input('project_id'))->orderBy('id', 'desc')->first();
  //hari
              $query[$k][$kk]['rdd_file_path'] = $ff->rdd_file_path ?? '';
              $query[$k][$kk]['rdd_file_name'] = $ff->rdd_file_name ?? '';
              if($query[$k][$kk]['rdd_file_path']){
                  $ext = pathinfo($ff->rdd_file_name, PATHINFO_EXTENSION);
                  $query[$k][$kk]['extension'] = $ext;
              }
            }
          }

        $path_details = ProjectInspections::leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_inspections.phase_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->select('tbl_checklisttemplate_master.template_name as inspection_type','tbl_project_inspections.requested_time','tbl_phase_master.phase_name','tbl_phase_master.phase_id','tbl_projects.project_name','tbl_project_inspections.comments')->where('tbl_project_inspections.project_id',$request->input('project_id'))->where('tbl_project_inspections.inspection_id',$request->input('inspection_id'))->get();

        $phase_name = $path_details[0]['phase_name'];
        $project_name = $path_details[0]['project_name'];
        $phase_id = $path_details[0]['phase_id'];
        $inspection_type = $path_details[0]['inspection_type'];
        $requested_time = $path_details[0]['requested_time'];
        $comments = $path_details[0]['comments'];

        $doc_path = public_path()."".$request->input('doc_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
        $img_path = public_path()."".$request->input('img_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
        if(!File::isDirectory($doc_path)){
            File::makeDirectory($doc_path, 0777, true, true);
           }
           if(!File::isDirectory($img_path)){
               File::makeDirectory($img_path, 0777, true, true);
           }

        return Response::json(array('comments'=>$comments,'doc_path' => $doc_path, 'image_path' => $img_path,'inspection_type'=>$inspection_type,'inspection_items' => $query,'phase_id'=>$phase_id,'requested_time' => $requested_time));
    }
    /*Investor Update uploaded file details for inspection */
    function updateInspectionfiledetails(Request $request)
    {
        $datas = $request->get('datas');
        $attachmentData = array();
        $validator = Validator::make($request->all(), [
            'datas.*.project_id' => 'required',
            'datas.*.inspection_id' => 'required',
            'datas.*.entries.*.id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $submitted_status=1;
        $uploaded_status=1;
        $approved_status=2;
        $rejected_status=3;
        $fileData = array();
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $statusCount = Projectinspections::where('inspection_id',$datas[0]['inspection_id'])->whereIn('inspection_status',[$uploaded_status,$approved_status,$rejected_status])->count();
        if(1!=1)
     //   if($statusCount>0)
        {
           // return response()->json(['response'=>"Inspection details May be submitted,approved or rejected"], 410);
        }
        else
        {
            for($j=0;$j<count($datas);$j++)
            {
                for($i=0;$i<count($datas[$j]['entries']);$i++)
                {
                    Projectinspectionitems::where('project_id',$datas[$j]['project_id'])->where('inspection_id',$datas[$j]['inspection_id'])->where('id',$datas[$j]['entries'][$i]['id'])->update(
                        array(
                            "snag_type" => $datas[$j]['entries'][$i]['snag_type'],
                            "investor_declarations" => $datas[$j]['entries'][$i]['investor_declarations'],
                            "remarks" => $datas[$j]['entries'][$i]['remarks'],
                            "updated_at" => $updated_at
                        )
                    );

                    $project_id = 0;
                    // //upload inspection attachments
                    $aa = is_array($datas[$j]['entries'][$i]['attachments']) ? count($datas[$j]['entries'][$i]['attachments']) : 0 ;

                    for($k=0;$k<$aa;$k++)
                    {
                        $attachmentData[] = [
                            'project_id' => $datas[$j]['project_id'],
                            'inspection_id' => $datas[$j]['inspection_id'],
                            'inspection_item_id' => $datas[$j]['entries'][$i]['id'],
                            "investor_file_name" => $datas[$j]['entries'][$i]['attachments'][$k]['investor_file_name'],
                            "investor_file_path" => $datas[$j]['entries'][$i]['attachments'][$k]['investor_file_path'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            "uploaded_by"=> $datas[$j]['user_id']
                        ];
                        $project_id = $datas[$j]['project_id'];
                    }
                }
            }
            Projectinspectionattachments::insert($attachmentData);
            if(1==1)
            {
                $contactDetails = Projectcontact::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_contact_details.project_id')->leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_project_contact_details.project_id',$datas[0]['project_id'])->where('tbl_project_contact_details.isDeleted',0)->whereNotIn('tbl_project_contact_details.member_designation',[13,14])->select('tbl_project_contact_details.*','tbl_projects.project_name','tbl_projects.investor_brand','users.email as rdd_manager_email','users.mem_name','users.mem_last_name','tbl_properties_master.property_name','tbl_units_master.unit_name')->get();
                $inspectionDetails = Projectinspections::where('inspection_id',$datas[0]['inspection_id'])->first();
                $checklistDetails = Checklisttemplate::where('id',$inspectionDetails['checklist_id'])->first();

                $data = array();
                $data = [
                    "rdd_manager_email" => $contactDetails[0]['rdd_manager_email'],
                    "rdd_manager_name" => $contactDetails[0]['mem_name']." ".($contactDetails[0]['mem_last_name']!=null?$contactDetails[0]['mem_last_name']:""),
                    "unit_name" => $contactDetails[0]['unit_name'],
                    "property_name" => $contactDetails[0]['property_name'],
                    "investor_brand" => $contactDetails[0]['investor_brand'],
                    "inspection_type" =>$checklistDetails['template_name']
                ];
                $cc = Projectcontact::where('project_id',$project_id)->whereIn('member_designation',[27,13,14])->pluck('email')->toArray();

                Mail::send('emails.projectinspections', $data, function($message)use($data,$cc) {
                        $message->to($data['rdd_manager_email'])->cc($cc)

                                ->subject($data['unit_name']."-".$data['property_name']."-".$data['investor_brand']."- ".$data['inspection_type']." Request");
                        });
               //update inspection status to submitted
               Projectinspections::where('project_id',$datas[0]['project_id'])->where('inspection_id',$datas[0]['inspection_id'])->update(
                   array(
                       "inspection_status"=>$submitted_status,
                       "comments"=>$datas[0]['comments']?$datas[0]['comments']:null,
                       "updated_at" => $updated_at
                   )
               );
                return response()->json(['response'=>"File updated for inspection"], 200);
            }
        }
    }
    /* RDD Member get selected inspection data */
    function rddretrieveInspectiondata(Request $request)
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

        $doc_path="";
        $img_path="";
        $phase_id = "";
        $project_name = "";
        $phase_name = "";
        $inspection_type = "";
        $requested_time = "";
        $comments="";
        $query = Projectinspectionitems::leftjoin('tbl_project_inspections','tbl_project_inspections.inspection_id','=','tbl_project_inspection_items.inspection_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->leftjoin('tbl_project_inspection_attachments','tbl_project_inspection_attachments.inspection_item_id','=','tbl_project_inspection_items.id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->select('tbl_inspection_root_categories.root_name','tbl_project_inspection_items.id','tbl_project_inspection_items.checklist_desc','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.remarks',DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_name) as investor_file_name"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.investor_file_path) as investor_file_path"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.rdd_file_path) as rdd_file_path"),DB::raw("GROUP_CONCAT(tbl_project_inspection_attachments.rdd_file_name) as rdd_file_name"),'tbl_project_inspection_items.investor_declarations','tbl_project_inspection_items.rdd_snags','tbl_project_inspection_items.snag_type','tbl_project_inspection_items.remarks')->where('tbl_project_inspection_items.project_id',$request->input('project_id'))->where('tbl_project_inspection_items.inspection_id',$request->input('inspection_id'))->where('tbl_project_inspection_items.isDeleted',0)->where('tbl_project_inspection_items.isRescheduled',0)->groupBy('tbl_project_inspection_items.id')->get()->groupBy('root_name');

        foreach($query as $k=>$v){
            foreach($v as $kk=>$vv){
              $ins_item_id = $query[$k][$kk]['id'];
              $ff = Projectinspectionattachments::where('inspection_item_id',$ins_item_id)->where('project_id',$request->input('project_id'))->orderBy('id', 'desc')->first();
  //hari
              $query[$k][$kk]['rdd_file_path'] = $ff->rdd_file_path ?? '';
              $query[$k][$kk]['rdd_file_name'] = $ff->rdd_file_name ?? '';
              if($query[$k][$kk]['rdd_file_path']){
                  $ext = pathinfo($ff->rdd_file_name, PATHINFO_EXTENSION);
                  $query[$k][$kk]['extension'] = $ext;
              }
            }
          }

        $path_details = ProjectInspections::leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_inspections.phase_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->select('tbl_checklisttemplate_master.template_name as inspection_type','tbl_phase_master.phase_name','tbl_phase_master.phase_id','tbl_projects.project_name','tbl_project_inspections.requested_time','tbl_project_inspections.file_paths','tbl_project_inspections.comments')->where('tbl_project_inspections.project_id',$request->input('project_id'))->where('tbl_project_inspections.inspection_id',$request->input('inspection_id'))->get();

        if(count($path_details)>0)
        {
            $phase_id = $path_details[0]['phase_id'];
            $project_name = $path_details[0]['project_name'];
            $phase_name = $path_details[0]['phase_name'];
            $inspection_type = $path_details[0]['inspection_type'];
            $requested_time = $path_details[0]['requested_time'];
            $comments = $path_details[0]['comments'];
            $file_paths = $path_details[0]['file_paths'];

            $doc_path = public_path()."".$request->input('doc_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
            $img_path = public_path()."".$request->input('img_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
            if(!File::isDirectory($doc_path)){
                File::makeDirectory($doc_path, 0777, true, true);
            }
            if(!File::isDirectory($img_path)){
                File::makeDirectory($img_path, 0777, true, true);
            }
        }


        return Response::json(array('comments'=>$comments,'inspection_type'=>$inspection_type,'inspection_items' => $query,'phase_id'=>$phase_id,'requested_time'=>$requested_time,'doc_path'=>$doc_path,'image_path' =>$img_path,"file_paths"=>$file_paths));
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
                          "requested_time"=>$request->input('requested_time'),
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
    /*RDD member - Save and send inspection to investor */
    function rddSendinspectiondata(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [
            'datas.*.project_id' => 'required',
            // 'datas.*.doc_path' => 'required',
            'datas.*.inspection_id' => 'required',
            'datas.*.user_id' => 'required',
            'datas.*.entries.*.id' => 'required',
        ]);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $attachmentData = array();
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        $ins_data = Projectinspections::where('tbl_project_inspections.project_id',$datas[0]['project_id'])->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$datas[0]['inspection_id'])->first();

        // ->where('tbl_project_inspections.report_status',2)
        //commented on 07-12-2021
        // if($ins_data==null || $ins_data=='')
        // {
        //     return response()->json(['response'=>"Kindly Approve before Sending report"], 410);
        // }


        $file_paths = count($datas[0]['file_paths'])>0?$datas[0]['file_paths']:null;
        //check whether inspection is approved
        $inspectionCount = Projectinspections::where('project_id',$datas[0]['project_id'])->where('inspection_id',$datas[0]['inspection_id'])->where('inspection_status',2)->count();
        if($inspectionCount>0)
        {
            Projectinspections::where('project_id',$datas[0]['project_id'])->where('inspection_id',$datas[0]['inspection_id'])->update(
                array(
                    "comments" => $datas[0]['comments'],
                    "file_paths" => $file_paths
                )
            );
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

                    //upload inspection attachments
                    for($k=0;$k<count($datas[$i]['entries'][$j]['attachments']);$k++)
                    {
                        $attachmentData[] = [
                            'project_id' => $datas[$i]['project_id'],
                            'inspection_id' => $datas[$i]['inspection_id'],
                            'inspection_item_id' => $datas[$i]['entries'][$j]['id'],
                            "rdd_file_name" => $datas[$i]['entries'][$j]['attachments'][$k]['rdd_file_name'],
                            "rdd_file_path" => $datas[$i]['entries'][$j]['attachments'][$k]['rdd_file_path'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            "uploaded_by"=> $datas[$i]['user_id']
                        ];
                    }
                }
            }
            Projectinspectionattachments::insert($attachmentData);


            //send mail to rdd manager
            $memberDetails = Project::join('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->select('users.email')->where('project_id',$datas[0]['project_id'])->get();
            $ccMembers = array();
            $inv_members = array();
            $ccMembers[] =  $memberDetails[0]['email'];
            $Mep = Projectcontact::where('project_id',$datas[0]['project_id'])->where('isDeleted',0)->where('member_designation',5)->get();
            for($a=0;$a<count($Mep);$a++)
            {
                $ccMembers[] = $Mep[$a]['email'];
            }
            $investors = Projectcontact::where('project_id',$datas[0]['project_id'])->where('isDeleted',0)->where('member_designation',13)->get();
            for($b=0;$b<count($investors);$b++)
            {
                $inv_members[] = $investors[$b]['email'];
            }
            $projectDetails = $this->getProjectDetails($datas[0]['project_id']);
            //get inspection data
            $inspectionData = Projectinspections::join('tbl_project_inspection_items','tbl_project_inspection_items.inspection_id','=','tbl_project_inspections.inspection_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->where('tbl_project_inspections.project_id',$datas[0]['project_id'])->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->where('tbl_project_inspections.report_status',2)->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$datas[0]['inspection_id'])->select('tbl_project_inspection_items.checklist_desc','tbl_inspection_root_categories.root_name','tbl_project_inspections.inspection_id','tbl_project_inspection_items.rdd_snags','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.created_at as investor_finished_date','tbl_checklisttemplate_master.template_name')->get()->groupBy('root_name');

            $checklistdata = Checklisttemplate::leftjoin('tbl_project_inspections','tbl_project_inspections.checklist_id','=','tbl_checklisttemplate_master.id')->where('tbl_project_inspections.inspection_id',$datas[0]['inspection_id'])->first();


            $data = array();
            $data = [
                'unit_name' => $projectDetails[0]['unit_name'],
                'investor_brand' => $projectDetails[0]['investor_brand'],
                "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
                "inspection_data" => $inspectionData,
                "company_name" => $projectDetails[0]['company_name'],
                "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
                "inspection_date" => date('d-m-Y',strtotime($ins_data['requested_time'])),
                "pre_date" => date('d-m-Y'),
                "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
                'property_name' => $projectDetails[0]['property_name']
            ];

            $emailData = array();
            $emailData = [
                "cc_members" => $ccMembers,
                "investors" => $inv_members,
                "unit_name" => $projectDetails[0]['unit_name'],
                "property_name" => $projectDetails[0]['property_name'],
                "template_name" => $checklistdata['template_name'],
                "inspection_date" => date('d-m-Y',strtotime($ins_data['requested_time'])),
                'investor_brand' => $projectDetails[0]['investor_brand'],
                "investor_name" => $projectDetails[0]['tenant_name']." ".($projectDetails[0]['tenant_last_name']!=null?$projectDetails[0]['tenant_last_name']:""),
                "inspection_date" => date('d-m-Y',strtotime($ins_data['requested_time']))
            ];

            // try
            // {
            //     $pdf = PDF::loadView('inspection', $data);
            //     $pre_date = date('d-m-Y H:i:s');

            //    // $pathss = "/Applications/XAMPP/xamppfiles/htdocs/rdd";
            //     $pathss = "/var/www/html/rdd_portal/api";
            //     $destination_path = "$pathss/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/".$datas[0]['inspection_id']."_".$checklistdata['template_name'].".pdf";
            //     $path = "$pathss/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/";

            //     // $destination_path = "/var/www/html/rdd_portal/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/".$datas[0]['inspection_id']."_".$checklistdata['template_name'].".pdf";
            //     // $path = "/var/www/html/rdd_portal/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/";


            //     if(!File::isDirectory($path)){
            //         File::makeDirectory($path, 0777, true, true);
            //     }
            //     $fileMove = file_put_contents($destination_path, $pdf->output());
            //     if($fileMove==false)
            //     {
            //         return response()->json(['response'=>"Cannot Send Inspection Report"], 410);
            //     }
            //     // $files = [
            //     //     "/var/www/html/rdd_portal/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/".$datas[0]['inspection_id']."_".$checklistdata['template_name'].".pdf"
            //     // ];

            //     $files = [
            //         "$pathss/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/".$datas[0]['inspection_id']."_".$checklistdata['template_name'].".pdf"
            //     ];

            //     $cc = Projectcontact::where('project_id',$datas[0]['project_id'])->whereIn('member_designation',[2,27,5])->pluck('email')->toArray();  


            //     Mail::send('emails.inspection', $emailData, function($message)use($emailData, $files,$cc) {
            //         $message->to($emailData["investors"])
            //                // ->cc($emailData["cc_members"])
            //                 ->cc($cc)
                            
            //                 ->subject($emailData["unit_name"]."-".$emailData["property_name"]."-".$emailData["investor_brand"]."- Inspection Report");

            //         forEach ($files as $file){
            //             $message->attach($file,['as'=>$emailData["unit_name"]."-".$emailData["property_name"].". Inspection Report pdf",'mime'=> "application/pdf"]);
            //         }
            //     });
            //     }
            // catch (\Exception $e) {
            //     return $e->getMessage();
            // }

            return response()->json(['response'=>"Inspection Report data Saved and sent"], 200);
        }
        else
        {
            return response()->json(['response'=>"Inspection not approved Yet"], 410);
        }
    }
    /*RDD Member - generating report for Inspection */
    function rddGenerateinspectionReport(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'inspection_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        //send mail to rdd manager
        $memberDetails = Project::join('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->select('users.email')->where('project_id',$request->input('project_id'))->get();
        $rddManager = $memberDetails[0]['email'];
        $ins_data = Projectinspections::where('tbl_project_inspections.project_id',$request->input('project_id'))->where('tbl_project_inspections.report_status',2)->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$request->input('inspection_id'))->first();
        // if($ins_data==null || $ins_data=='')
        // {
        //     return response()->json(['response'=>"Kindly Approve before Generating report"], 410);
        // }
        return response()->json(['response'=>"Inspection Report generated"], 200);
    }
    /* RDD Member - Create Site Inspection Report */
    function createSiteinspectionRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'phase_id' => 'required',
            'checklist_id' => 'required',
            // 'previous_inspection_date' => 'required',
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
             //get projects previous site inspection date
             $previousinspectionDetails = SiteInspectionReport::where('project_id',$request->input('project_id'))->where('isDeleted',0)->orderBy('id', 'desc')->first();

             $previous_date = null;


             if($previousinspectionDetails!="")
             {
                $previous_date =  $previousinspectionDetails['inspection_date'];
             }
             $inspection = new SiteInspectionReport();


            $inspection->project_id = $request->input('project_id');
            $inspection->phase_id = $request->input('phase_id');
            $inspection->inspection_date = $request->input('inspection_date');
            $inspection->previous_inspection_date = $previous_date;
            $inspection->checklist_id = $request->input('checklist_id');
            $inspection->comments = $request->input('comments');
            $inspection->created_by = $request->input('user_id');
            $inspection->created_at = date('Y-m-d H:i:s');
            $inspection->updated_at = date('Y-m-d H:i:s');

            if($inspection->save())
            {
                $returnData = SiteInspectionReport::select('id')->find($inspection->id);
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
    /*RDD Member - Get Site Inspection data */
    function rddretrievesiteInspectiondata(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'inspection_id' => 'required',
            'doc_path' => 'required',
            'img_path' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $inspection_type="";
        $phase_id="";
        $phase_name="";
        $project_name="";
        $inspection_date="";
        $comments="";
        $query = Siteinspectionitems::leftjoin('tbl_site_inspection_report','tbl_site_inspection_report.id','=','tbl_site_inspection_items.inspection_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_site_inspection_report.project_id')->leftjoin('tbl_site_inspection_attachments','tbl_site_inspection_attachments.inspection_item_id','=','tbl_site_inspection_items.id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_site_inspection_items.root_id')->select('tbl_inspection_root_categories.root_name','tbl_site_inspection_items.id','tbl_site_inspection_items.checklist_desc','tbl_site_inspection_items.rdd_actuals','tbl_site_inspection_items.isApplicable','tbl_site_inspection_items.remarks',DB::raw("GROUP_CONCAT(tbl_site_inspection_attachments.rdd_file_path) as rdd_file_path"),DB::raw("GROUP_CONCAT(tbl_site_inspection_attachments.rdd_file_name) as rdd_file_name"))->where('tbl_site_inspection_items.project_id',$request->input('project_id'))->where('tbl_site_inspection_items.inspection_id',$request->input('inspection_id'))->where('tbl_site_inspection_items.isDeleted',0)->groupBy('tbl_site_inspection_items.id')->get()->groupBy('root_name');

        $path_details = SiteInspectionReport::leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_site_inspection_report.phase_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_site_inspection_report.project_id')->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_site_inspection_report.checklist_id')->select('tbl_checklisttemplate_master.template_name as inspection_type','tbl_phase_master.phase_name','tbl_phase_master.phase_id','tbl_projects.project_name','tbl_site_inspection_report.inspection_date','tbl_site_inspection_report.comments')->where('tbl_site_inspection_report.project_id',$request->input('project_id'))->where('tbl_site_inspection_report.id',$request->input('inspection_id'))->get();

        if(count($path_details)>0)
        {
            $phase_id = $path_details[0]['phase_id'];
            $inspection_type = $path_details[0]['inspection_type'];
            $phase_name = $path_details[0]['phase_name'];
            $project_name = $path_details[0]['project_name'];
            $inspection_date = $path_details[0]['inspection_date'];
            $comments = $path_details[0]['comments'];
        }

        $doc_path = public_path()."".$request->input('doc_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/site_inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
            $img_path = public_path()."".$request->input('img_path')."".$request->input('project_id')."_".$project_name."/".$phase_name."/site_inspections/".$request->input('inspection_id')."_".$path_details[0]['inspection_type'];
            if(!File::isDirectory($doc_path)){
                File::makeDirectory($doc_path, 0777, true, true);
            }
            if(!File::isDirectory($img_path)){
                File::makeDirectory($img_path, 0777, true, true);
            }

        return Response::json(array('doc_path'=>$doc_path,'image_path'=>$img_path,'inspection_type'=>$inspection_type,'inspection_items' => $query,'phase_id'=>$phase_id,'inspection_date'=>$inspection_date,'comments'=>$comments));
    }
    /* Investor - Get creating Inspection request */
    function investorcreateInspectionRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'phase_id' => 'required',
            'checklist_id' => 'required',
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
            $inspection->requested_time = $request->input('requested_time');
            $inspection->checklist_id = $request->input('checklist_id');
            $inspection->comments = $request->input('comments');
            $inspection->investor_id = $request->input('user_id');  
            $inspection->created_at = date('Y-m-d H:i:s');
            $inspection->updated_at = date('Y-m-d H:i:s');

            if($inspection->save())
            {
                $returnData = Projectinspections::select('inspection_id')->find($inspection->inspection_id);
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
                    $notificationsArray = array();
                    $contactDetails = Projectcontact::leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_contact_details.project_id')->leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_project_contact_details.project_id',$request->input('project_id'))->where('tbl_project_contact_details.isDeleted',0)->whereNotIn('tbl_project_contact_details.member_designation',[13,14])->select('tbl_project_contact_details.*','tbl_projects.project_name','tbl_projects.investor_brand','users.email as rdd_manager_email','users.mem_name','users.mem_last_name','tbl_properties_master.property_name','tbl_units_master.unit_name')->get();
                    $checklistDetails = Checklisttemplate::where('id',$request->input('checklist_id'))->first();
                    for($r=0;$r<count($contactDetails);$r++)
                    {
                        $notificationsArray[]=[
                            "project_id" => $request->input('project_id'),
                            "content" => $checklistDetails['template_name']." for Project ".$contactDetails[0]['project_name']." has been requested",
                            "user" => $contactDetails[$r]['member_id'],
                            "user_type" => 1,
                            "notification_type"=>env('NOTIFY_INSPECTIONS')!=null?env('NOTIFY_INSPECTIONS'):5,
                            "created_at" => $created_at,
                            "updated_at" => $updated_at
                        ];
                    }
                    Notifications::insert($notificationsArray);
                    $data = array();
                    $data = [
                        "rdd_manager_email" => $contactDetails[0]['rdd_manager_email'],
                        "rdd_manager_name" => $contactDetails[0]['mem_name']." ".($contactDetails[0]['mem_last_name']!=null?$contactDetails[0]['mem_last_name']:""),
                        "unit_name" => $contactDetails[0]['unit_name'],
                        "property_name" => $contactDetails[0]['property_name'],
                        "investor_brand" => $contactDetails[0]['investor_brand'],
                        "inspection_type" =>$checklistDetails['template_name']
                    ];
                    // Mail::send('emails.projectinspections', $data, function($message)use($data) {
                    //     $message->to($data['rdd_manager_email'])
                    //             ->subject($data['unit_name']."-".$data['property_name']."-".$data['investor_brand']."- Inspection Request");
                    //     });
                    $inspectionData = Projectinspections::select('inspection_id','requested_time')->find($inspection->inspection_id);
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
    /*RDD Member - Update Site Inspection data with attachments */
    function rddUpdatesiteIspectiondata(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [
            'datas.*.project_id' => 'required',
            'datas.*.inspection_id' => 'required',
            'datas.*.entries.*.id' => 'required',
            'datas.*.user_id' => 'required',
        ]);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $attachmentData = array();
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        //update comment if sent in request
        if($datas[0]['comments']!=null && $datas[0]['comments']!='')
        {
            SiteInspectionReport::where('project_id',$datas[0]['project_id'])->where('id',$datas[0]['inspection_id'])->update(
                array(
                    "comments" => $datas[0]['comments']
                )
            );
        }

        for($i=0;$i<count($datas);$i++)
            {
                for($j=0;$j<count($datas[$i]['entries']);$j++)
                {
                    Siteinspectionitems::where('project_id',$datas[$i]['project_id'])->where('inspection_id',$datas[$i]['inspection_id'])->where('id',$datas[$i]['entries'][$j]['id'])->update(
                        array(
                            "rdd_actuals"=>$datas[$i]['entries'][$j]['rdd_actuals']?$datas[$i]['entries'][$j]['rdd_actuals']:0,
                            "isApplicable"=>isset($datas[$i]['entries'][$j]['isApplicable'])?$datas[$i]['entries'][$j]['isApplicable']:0,
                            "remarks"=>$datas[$i]['entries'][$j]['remarks']?$datas[$i]['entries'][$j]['remarks']:null,
                        )
                    );

                    //upload site inspection attachments
                    for($k=0;$k<count($datas[$i]['entries'][$j]['attachments']);$k++)
                    {
                        $attachmentData[] = [
                            'project_id' => $datas[$i]['project_id'],
                            'inspection_id' => $datas[$i]['inspection_id'],
                            'inspection_item_id' => $datas[$i]['entries'][$j]['id'],
                            "rdd_file_name" => $datas[$i]['entries'][$j]['attachments'][$k]['rdd_file_name'],
                            "rdd_file_path" => $datas[$i]['entries'][$j]['attachments'][$k]['rdd_file_path'],
                            "created_at" => $created_at,
                            "updated_at" => $updated_at,
                            "uploaded_by"=> $datas[$i]['user_id']
                        ];
                    }
                }
            }
            Siteinspectionattachments::insert($attachmentData);
            /* Commented on 25-11-2021 */
            //Mail to Investor
            // $projectDetails = $this->getProjectDetails($datas[0]['project_id']);
            // //get inspection data
            // $inspectionData = SiteInspectionReport::join('tbl_site_inspection_items','tbl_site_inspection_items.inspection_id','=','tbl_site_inspection_report.id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_site_inspection_items.root_id')->where('tbl_site_inspection_report.project_id',$datas[0]['project_id'])->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_site_inspection_report.checklist_id')->where('tbl_site_inspection_report.isDeleted',0)->where('tbl_site_inspection_report.id',$datas[0]['inspection_id'])->select('tbl_site_inspection_items.checklist_desc','tbl_inspection_root_categories.root_name','tbl_site_inspection_report.id as inspection_id','tbl_site_inspection_items.remarks','tbl_site_inspection_items.rdd_actuals','tbl_checklisttemplate_master.template_name')->get()->groupBy('root_name');

            // $checklistdata = Checklisttemplate::leftjoin('tbl_site_inspection_report','tbl_site_inspection_report.checklist_id','=','tbl_checklisttemplate_master.id')->where('tbl_site_inspection_report.id',$datas[0]['inspection_id'])->first();

            // $ccMembers = array();
            // $inv_members = array();
            // $investors = Projectcontact::where('project_id',$datas[0]['project_id'])->where('isDeleted',0)->where('member_designation',13)->get();
            // for($b=0;$b<count($investors);$b++)
            // {
            //     $inv_members[] = $investors[$b]['email'];
            // }
            // $memberDetails = Project::join('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->select('users.email')->where('project_id',$datas[0]['project_id'])->get();
            // $ccMembers[] =  $memberDetails[0]['email'];

            // $ins_data = SiteInspectionReport::where('tbl_site_inspection_report.project_id',$datas[0]['project_id'])->where('tbl_site_inspection_report.isDeleted',0)->where('tbl_site_inspection_report.id',$datas[0]['inspection_id'])->first();

            // $data = array();
            // $data = [
            //     'unit_name' => $projectDetails[0]['unit_name'],
            //     'investor_brand' => $projectDetails[0]['investor_brand'],
            //     "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
            //     "inspection_data" => $inspectionData,
            //     "company_name" => $projectDetails[0]['company_name'],
            //     "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
            //     "inspection_date" => date('d-m-Y',strtotime($ins_data['inspection_date'])),
            //     "pre_date" => date('d-m-Y'),
            //     "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
            //     'property_name' => $projectDetails[0]['property_name']
            // ];

            // $emailData = array();
            // $emailData = [
            //     "cc_members" => $ccMembers,
            //     "investors" => $inv_members,
            //     "unit_name" => $projectDetails[0]['unit_name'],
            //     "property_name" => $projectDetails[0]['property_name'],
            //     "template_name" => $projectDetails[0]['template_name'],
            //     "inspection_date" => date('d-m-Y',strtotime($ins_data['inspection_date'])),
            //     'investor_brand' => $projectDetails[0]['investor_brand'],
            //     "investor_name" => $projectDetails[0]['tenant_name']." ".($projectDetails[0]['tenant_last_name']!=null?$projectDetails[0]['tenant_last_name']:"")
            // ];

            // try
            // {
            //     $pdf = PDF::loadView('siteinspection', $data);
            //     $pre_date = date('d-m-Y H:i:s');
            //     $destination_path = "/var/www/html/rdd_portal_qa/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/site_inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/".$datas[0]['inspection_id']."_".$checklistdata['template_name'].".pdf";
            //     $path = "/var/www/html/rdd_portal_qa/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/site_inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/";
            //     if(!File::isDirectory($path)){
            //         File::makeDirectory($path, 0777, true, true);
            //     }
            //     $fileMove = file_put_contents($destination_path, $pdf->output());
            //     if($fileMove==false)
            //     {
            //         return response()->json(['response'=>"Cannot Send Site Inspection Report"], 410);
            //     }
            //     $files = [
            //         "/var/www/html/rdd_portal_qa/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/site_inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/".$datas[0]['inspection_id']."_".$checklistdata['template_name'].".pdf"
            //     ];
            //     Mail::send('emails.inspection', $emailData, function($message)use($emailData, $files) {
            //         $message->to($emailData["investors"])
            //                 ->cc($emailData["cc_members"])
            //                 ->subject($emailData["unit_name"]."-".$emailData["property_name"]."-Fitout progress/report");

            //         forEach ($files as $file){
            //             $message->attach($file,['as'=>$emailData["unit_name"]."-".$emailData["property_name"].". Site Inspection Report pdf",'mime'=> "application/pdf"]);
            //         }
            //     });
            //     }
            // catch (\Exception $e) {
            //     return $e->getMessage();
            // }

            return response()->json(['response'=>"Site Inspection Data Updated"], 200);
    }
    public function getProjectDetails($project_id)
    {
        return project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->where('tbl_projects.project_id',$project_id)->where('tbl_project_contact_details.member_designation',13)->leftjoin('tbl_company_master','tbl_company_master.company_id','=','tbl_tenant_master.company_id')->leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->select('tbl_projects.project_id','tbl_projects.org_id','tbl_projects.project_name','tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name','tbl_properties_master.property_name','tbl_properties_master.property_id','tbl_company_master.company_name','users.mem_name','users.mem_last_name')->get();
    }
    public function rddSendSiteInspectionReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_id' => 'required',
            'inspection_id' => 'required'
        ]);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');
        $attachmentData = array();
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        
        $inspection_id = $request->input('inspection_id');
        $project_id = $request->input('project_id');;
        //Mail to Investor
            $projectDetails = $this->getProjectDetails($project_id);
            //get inspection data
            $inspectionData = SiteInspectionReport::join('tbl_site_inspection_items','tbl_site_inspection_items.inspection_id','=','tbl_site_inspection_report.id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_site_inspection_items.root_id')->where('tbl_site_inspection_report.project_id',$project_id)->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_site_inspection_report.checklist_id')->where('tbl_site_inspection_report.isDeleted',0)->where('tbl_site_inspection_report.id',$inspection_id)->select('tbl_site_inspection_items.checklist_desc','tbl_inspection_root_categories.root_name','tbl_site_inspection_report.id as inspection_id','tbl_site_inspection_items.remarks','tbl_site_inspection_items.rdd_actuals','tbl_checklisttemplate_master.template_name')->get()->groupBy('root_name');

            $checklistdata = Checklisttemplate::leftjoin('tbl_site_inspection_report','tbl_site_inspection_report.checklist_id','=','tbl_checklisttemplate_master.id')->where('tbl_site_inspection_report.id',$inspection_id)->first();

            $ccMembers = array();
            $inv_members = array();
            $investors = Projectcontact::where('project_id',$project_id)->where('isDeleted',0)->where('member_designation',13)->get();
            for($b=0;$b<count($investors);$b++)
            {
                $inv_members[] = $investors[$b]['email'];
            }
            $memberDetails = Project::join('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->select('users.email')->where('project_id',$project_id)->get();
            $ccMembers[] =  $memberDetails[0]['email'];

            $ins_data = SiteInspectionReport::where('tbl_site_inspection_report.project_id',$project_id)->where('tbl_site_inspection_report.isDeleted',0)->where('tbl_site_inspection_report.id',$inspection_id)->first();

            $data = array();
            $data = [
                'unit_name' => $projectDetails[0]['unit_name'],
                'investor_brand' => $projectDetails[0]['investor_brand'],
                "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
                "inspection_data" => $inspectionData,
                "company_name" => $projectDetails[0]['company_name'],
                "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
                "inspection_date" => date('d-m-Y',strtotime($ins_data['inspection_date'])),
                "pre_date" => date('d-m-Y'),
                "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
                'property_name' => $projectDetails[0]['property_name'],
                'comments' => $ins_data['comments']
            ];

            $emailData = array();
            $emailData = [
                "cc_members" => $ccMembers,
                "investors" => $inv_members,
                "unit_name" => $projectDetails[0]['unit_name'],
                "property_name" => $projectDetails[0]['property_name'],
                "template_name" => $projectDetails[0]['template_name'],
                "inspection_date" => date('d-m-Y',strtotime($ins_data['inspection_date'])),
                'investor_brand' => $projectDetails[0]['investor_brand'],
                "investor_name" => $projectDetails[0]['tenant_name']." ".($projectDetails[0]['tenant_last_name']!=null?$projectDetails[0]['tenant_last_name']:"")
            ];

            try
            {
                $pdf = PDF::loadView('siteinspection', $data);
                $pre_date = date('d-m-Y H:i:s');
             
                $pathss = "/var/www/html/rdd_portal/api";
                $destination_path = "$pathss/public/uploads/ORG00001/documents/".$project_id."_".$projectDetails[0]['project_name']."/Fitout phase/site_inspections/".$inspection_id."_".$checklistdata['template_name']."/sent/".$inspection_id."_".$checklistdata['template_name'].".pdf";
                $path = "$pathss/public/uploads/ORG00001/documents/".$project_id."_".$projectDetails[0]['project_name']."/Fitout phase/site_inspections/".$inspection_id."_".$checklistdata['template_name']."/sent/";



                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }
                $fileMove = file_put_contents($destination_path, $pdf->output());
                if($fileMove==false)
                {
                    return response()->json(['response'=>"Cannot Send Site Inspection Report"], 410);
                }
               
                $files = [
                    "$pathss/public/uploads/ORG00001/documents/".$project_id."_".$projectDetails[0]['project_name']."/Fitout phase/site_inspections/".$inspection_id."_".$checklistdata['template_name']."/sent/".$inspection_id."_".$checklistdata['template_name'].".pdf"
                ];

                $cc = Projectcontact::where('project_id',$request->input('project_id'))->whereIn('member_designation',[2,27])->pluck('email')->toArray();  

                Mail::send('emails.inspection', $emailData, function($message)use($emailData, $files,$cc) {
                    $message->to($emailData["investors"])
                          //  ->cc($emailData["cc_members"])
                            ->cc($cc)
                            ->subject($emailData["unit_name"]."-".$emailData["property_name"]."-".$emailData["investor_brand"]."- Fitout progress/report");

                    forEach ($files as $file){
                        $message->attach($file,['as'=>$emailData["unit_name"]."-".$emailData["property_name"]."Site Inspection Report",'mime'=> "application/pdf"]);
                    }
                });
                }
            catch (\Exception $e) {
                return $e->getMessage();
            }

    }



    function sendmailpdf(Request $request){
        $project_id = $request->get('project_id');
        $inspection_id = $request->get('inspection_id');
        $_files        = $request->get('_files');


        $ins_data = Projectinspections::where('tbl_project_inspections.project_id',$project_id)->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$inspection_id)->first();

        $projectDetails = $this->getProjectDetails($project_id);
        //$inspectionData = Projectinspections::join('tbl_project_inspection_items','tbl_project_inspection_items.inspection_id','=','tbl_project_inspections.inspection_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->where('tbl_project_inspections.project_id',$project_id)->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->where('tbl_project_inspections.report_status',2)->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$inspection_id)->select('tbl_project_inspection_items.checklist_desc','tbl_inspection_root_categories.root_name','tbl_project_inspections.inspection_id','tbl_project_inspection_items.rdd_snags','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.created_at as investor_finished_date','tbl_checklisttemplate_master.template_name')->get()->groupBy('root_name');
        $inspectionData = Projectinspections::join('tbl_project_inspection_items','tbl_project_inspection_items.inspection_id','=','tbl_project_inspections.inspection_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->where('tbl_project_inspections.project_id',$project_id)->leftjoin('tbl_checklisttemplate_master','tbl_checklisttemplate_master.id','=','tbl_project_inspections.checklist_id')->where('tbl_project_inspections.isDeleted',0)->where('tbl_project_inspections.inspection_id',$inspection_id)->select('tbl_project_inspection_items.checklist_desc','tbl_inspection_root_categories.root_name','tbl_project_inspections.inspection_id','tbl_project_inspection_items.rdd_snags','tbl_project_inspection_items.checklist_desc','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.created_at as investor_finished_date','tbl_checklisttemplate_master.template_name')->get()->groupBy('root_name');

       
      
      
        $memberDetails = Project::join('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->select('users.email')->where('project_id',$project_id)->get();
        $checklistdata = Checklisttemplate::leftjoin('tbl_project_inspections','tbl_project_inspections.checklist_id','=','tbl_checklisttemplate_master.id')->where('tbl_project_inspections.inspection_id',$inspection_id)->first();

        $data = array();
        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
            "inspection_data" => $inspectionData,
            "company_name" => $projectDetails[0]['company_name'],
            "rdd_manager" => $projectDetails[0]['mem_name']." ".($projectDetails[0]['mem_last_name']!=null?$projectDetails[0]['mem_last_name']:""),
            "inspection_date" => date('d-m-Y',strtotime($ins_data['requested_time'])),
            "pre_date" => date('d-m-Y'),
            "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:""),
            'property_name' => $projectDetails[0]['property_name']
        ];



        
        $ccMembers = array();
        $inv_members = array();
        $ccMembers[] =  $memberDetails[0]['email'];
        $Mep = Projectcontact::where('project_id',$project_id)->where('isDeleted',0)->where('member_designation',5)->get();
        for($a=0;$a<count($Mep);$a++)
        {
            $ccMembers[] = $Mep[$a]['email'];
        }
        $investors = Projectcontact::where('project_id',$project_id)->where('isDeleted',0)->where('member_designation',13)->get();
        for($b=0;$b<count($investors);$b++)
        {
            $inv_members[] = $investors[$b]['email'];
        }


        $emailData = array();
        $emailData = [
            "cc_members" => $ccMembers,
            "investors" => $inv_members,
            "unit_name" => $projectDetails[0]['unit_name'],
            "property_name" => $projectDetails[0]['property_name'],
            "template_name" => $checklistdata['template_name'],
            "inspection_date" => date('d-m-Y',strtotime($ins_data['requested_time'])),
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".($projectDetails[0]['tenant_last_name']!=null?$projectDetails[0]['tenant_last_name']:""),
            "inspection_date" => date('d-m-Y',strtotime($ins_data['requested_time']))
        ];

          try
            {
                $pdf = PDF::loadView('inspection', $data);
                $pre_date = date('d-m-Y H:i:s');

              // $pathss = "/Applications/XAMPP/xamppfiles/htdocs/rdd";
                $pathss = "/var/www/html/rdd_portal/api";
                $destination_path = "$pathss/public/uploads/ORG00001/documents/".$project_id."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$inspection_id."_".$checklistdata['template_name']."/sent/".$inspection_id."_".$checklistdata['template_name'].".pdf";
                $path = "$pathss/public/uploads/ORG00001/documents/".$project_id."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$inspection_id."_".$checklistdata['template_name']."/sent/";

               // $destination_path = "/var/www/html/rdd_portal/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/".$datas[0]['inspection_id']."_".$checklistdata['template_name'].".pdf";
               // $path = "/var/www/html/rdd_portal/api/public/uploads/ORG00001/documents/".$datas[0]['project_id']."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$datas[0]['inspection_id']."_".$checklistdata['template_name']."/sent/";


                if(!File::isDirectory($path)){
                    File::makeDirectory($path, 0777, true, true);
                }
                $fileMove = file_put_contents($destination_path, $pdf->output());
                if($fileMove==false)
                {
                    return response()->json(['response'=>"Cannot Send Inspection Report"], 410);
                }
                $files = [
                    "/var/www/html/rdd_portal/api/public/uploads/ORG00001/documents/".$project_id."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$inspection_id."_".$checklistdata['template_name']."/sent/".$inspection_id."_".$checklistdata['template_name'].".pdf"
                ];

                $files = [
                    "$pathss/public/uploads/ORG00001/documents/".$project_id."_".$projectDetails[0]['project_name']."/Fitout phase/inspections/".$inspection_id."_".$checklistdata['template_name']."/sent/".$inspection_id."_".$checklistdata['template_name'].".pdf"
                ];

                $_files = json_decode($_files);
               $files =   array_merge($files,$_files);



                $cc = Projectcontact::where('project_id',$project_id)->whereIn('member_designation',[2,27,5])->pluck('email')->toArray();


                Mail::send('emails.inspection', $emailData, function($message)use($emailData, $files,$cc) {
                    $message->to("harihbk95@gmail.com")
                           // ->cc($emailData["cc_members"])
                            ->cc($cc)
                            ->subject($emailData["unit_name"]."-".$emailData["property_name"]."-".$emailData["investor_brand"]."- Inspection Report");

                    forEach ($files as $file){
                        $message->attach($file);
                    }
                });
                }
            catch (\Exception $e) {
                return $e->getMessage();
            }

            return response()->json(['response'=>$emailData["investors"]], 200);

    }



}
