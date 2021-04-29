<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projectinspections;
use App\Models\Checklisttemplate;
use App\Models\Inspectionchecklistmaster;
use App\Models\Projectinspectionitems;
use App\Models\Project;
use App\Models\Inspectionroot;
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
        return response()->json(['inspections'=>$phase_name], 200);
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

        $query = Projectinspectionitems::leftjoin('tbl_project_inspections','tbl_project_inspections.inspection_id','=','tbl_project_inspection_items.inspection_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->join('tbl_inspection_root_categories','tbl_inspection_root_categories.root_id','=','tbl_project_inspection_items.root_id')->select('tbl_inspection_root_categories.root_name','tbl_project_inspection_items.checklist_desc','tbl_project_inspection_items.rdd_actuals','tbl_project_inspection_items.remarks')->where('tbl_project_inspection_items.project_id',$request->input('project_id'))->where('tbl_project_inspection_items.inspection_id',$request->input('inspection_id'))->groupBy('tbl_project_inspection_items.id')->get()->groupBy('root_name');
        
        $path_details = ProjectInspections::leftjoin('tbl_phase_master','tbl_phase_master.phase_id','=','tbl_project_inspections.phase_id')->leftjoin('tbl_projects','tbl_projects.project_id','=','tbl_project_inspections.project_id')->select('tbl_project_inspections.inspection_type','tbl_phase_master.phase_name','tbl_projects.project_name')->where('tbl_project_inspections.project_id',$request->input('project_id'))->get();

        $phase_name = $path_details[0]['phase_name'];
        $project_name = $path_details[0]['project_name'];
        $inspection_type = $path_details[0]['inspection_type'];
       
        $doc_path = public_path()."".$request->input('doc_path')."".$request->input('project_id')."_".$project_name."/".$phase_name;
        $img_path = public_path()."".$request->input('img_path')."".$request->input('project_id')."_".$project_name."/".$phase_name;
        if(!File::isDirectory($doc_path)){
            File::makeDirectory($doc_path, 0777, true, true);
           }
           if(!File::isDirectory($img_path)){
               File::makeDirectory($img_path, 0777, true, true);
           }
        
        return Response::json(array('doc_path' => $doc_path, 'image_path' => $img_path,'inspection_type'=>$inspection_type,'inspection_items' => $query));
    }
}
