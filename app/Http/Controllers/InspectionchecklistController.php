<?php

namespace App\Http\Controllers;
use App\Models\Inspectionchecklistmaster;
use App\Models\Inspectionroot;
use App\Models\Checklisttemplate;
use Response;
use Validator;

use Illuminate\Http\Request;

class InspectionchecklistController extends Controller
{
    function retrievebyOrg(Request $request,$id,$tempid)
    {
        $name = Checklisttemplate::where('id',$tempid)->where('isDeleted',0)->get();
        $types = Inspectionchecklistmaster::join('tbl_inspection_root_categories', 'tbl_checklist_master.root_id','=','tbl_inspection_root_categories.root_id')->get(['tbl_checklist_master.*', 'tbl_inspection_root_categories.root_name'])->where("org_id",$id)->where("template_id",$tempid)->where("isDeleted",0)->groupBy('root_name');
        // $types = Inspectionchecklistmaster::join('tbl_inspection_root_categories', 'tbl_checklist_master.root_id','=','tbl_inspection_root_categories.root_id')->get(['tbl_checklist_master.*', 'tbl_inspection_root_categories.root_name'])->where("org_id",$id)->where("template_id",$tempid)->where("isDeleted",0);
        return Response::json(["data"=>$types,"template_name"=>$name[0]['template_name']],200);
    }
    function store(Request $request)
    {
        $datas = $request->get('template');

        $types = new Checklisttemplate();

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $types->org_id = $datas['org_id'];
        $types->template_name = $datas['template_name'];
        $types->created_at = $created_at;
        $types->updated_at = $updated_at;
        $types->created_by = $datas['user_id'];      

        if($types->save()) {
        $template_id = $types->find($types->id);
        for($k=0;$k<count($datas['entries']);$k++) 
        {
            //insert template activities
            $data[] = [
                'org_id' => $datas['org_id'],
                'template_id' => $template_id->id,
                'root_id' => $datas['entries'][$k]['root_id'],
                'checklist_desc' => $datas['entries'][$k]['checklist_desc'],
                "created_at" => $created_at,
                "updated_at" => $updated_at,
                'created_by' => $datas['user_id']
                ];
        }

        $validator = Validator::make($request->all(), [ 
            'template.org_id' => 'required', 
            'template.entries.*.root_id' => 'required',
            // 'template.entries.*.checklist_desc' => 'required',
            'template.user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if(Inspectionchecklistmaster::insert($data))
        {
            $types = Inspectionchecklistmaster::join('tbl_inspection_root_categories', 'tbl_checklist_master.root_id','=','tbl_inspection_root_categories.root_id')->get(['tbl_checklist_master.*', 'tbl_inspection_root_categories.root_name'])->where("template_id",$template_id->id)->where("isDeleted",0)->groupBy('root_name');

            $data = array ("message" => 'Template added successfully',"data"=>$types);
            $response = Response::json($data,200);
            echo json_encode($response);
        }
        }
    }
    function update(Request $request)
    {
        $datas = $request->get('template');
        $data = array();

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        $validator = Validator::make($request->all(), [ 
            'template.org_id' => 'required',
            'template.id' => 'required', 
            'template.entries.*.root_id' => 'required',
            'template.user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        for($k=0;$k<count($datas['entries']);$k++) 
        {
            if($datas['entries'][$k]['ch_id']==intval(0))
            {
                    //insert template activities
                    $data[] = [
                        'org_id' => $datas['org_id'],
                        'template_id' => $datas['template_id'],
                        'root_id' => $datas['entries'][$k]['root_id'],
                        'checklist_desc' => $datas['entries'][$k]['checklist_desc'],
                        "created_at" => $created_at,
                        "updated_at" => $updated_at,
                        'created_by' => $datas['user_id']
                        ];
            }
            else
            {
                Inspectionchecklistmaster::where('template_id',$datas['template_id'])->where('ch_id',$datas['entries'][$k]['ch_id'])->update(array(
                    "isDeleted" => $datas['entries'][$k]['isDeleted']
                ));
            }
        }

            Inspectionchecklistmaster::insert($data);
            $types = Inspectionchecklistmaster::join('tbl_inspection_root_categories', 'tbl_checklist_master.root_id','=','tbl_inspection_root_categories.root_id')->get(['tbl_checklist_master.*', 'tbl_inspection_root_categories.root_name'])->where("template_id",$datas['template_id'])->where("isDeleted",0)->groupBy('root_name');

            $data = array ("message" => 'Template updated successfully',"data"=>$types);
            $response = Response::json($data,200);
            return $response;
    }
    
    function updateDeletion(Request $request,$id)
    {
        $types = Inspectionchecklistmaster::where("ch_id",$id)->update( 
        array("isDeleted" => $request->input('isDeleted'), "updated_at" => date('Y-m-d H:i:s')));
            if($types>0)
            {
                $returnData = Inspectionchecklistmaster::find($id);
                $data = array ("message" => 'Inspection ChecklistDoc  Deleted successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
            }
    }
}
