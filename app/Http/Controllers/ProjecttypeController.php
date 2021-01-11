<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projecttype;
use Response;
use Validator;

class ProjecttypeController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        $types = Projecttype::offset($offset)->limit($limit)->get();
        if($types!=null) {
            $data = array ("message" => 'Project types data',"data" => $types );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'attachment_path' => 'required', 
            'template_id' => 'required', 
            'type_name' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = new Projecttype();

        $types->org_id = $request->input('org_id');
        $types->attachment_path = $request->input('attachment_path');
        $types->template_id = $request->input('template_id');
        $types->type_name = $request->input('type_name');
        $types->created_at = date('Y-m-d H:i:s');
        $types->updated_at = date('Y-m-d H:i:s');
        $types->created_by = $request->input('user_id');        
        
        if($types->save()) {
            $returnData = $types->find($types->floor_id);
            $data = array ("message" => 'Project type added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'attachment_path' => 'required', 
            'template_id' => 'required', 
            'type_name' => 'required', 
            'active_status' => 'required',
            'isDeleted' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = Projecttype::where("type_id",$id)->update( 
            array(
             "attachment_path" => $request->input('attachment_path'), 
             "template_id" => $request->input('template_id'),
             "type_name" => $request->input('type_name'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status'),
             "isDeleted" => $request->input('isdeleted')
             ));
        
             if($types>0)
             {
                 $returnData = Projecttype::find($id);
                 $data = array ("message" => 'Project Type Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $limit = 1;
        $offset = 1;
        $types = Projecttype::where("org_id",$id)->offset($offset)->limit($limit)->get();
        echo json_encode($types); 
    }
    function getType(Request $request,$id)
    {
        $types = Projecttype::where("type_id",$id)->get();
        echo json_encode($types); 
    }
}
