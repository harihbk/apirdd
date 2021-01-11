<?php

namespace App\Http\Controllers;
use App\Models\Defaultdoclist;
use Response;
use Validator;

use Illuminate\Http\Request;

class DefaultdoclistController extends Controller
{
    function index()
    {
        $types = Defaultdoclist::all();
        if($types!=null) {
            $data = array ("message" => 'Default doc types data',"data" => $types );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'doc_name' => 'required', 
            'doc_path' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = new Defaultdoclist();

        $types->org_id = $request->input('org_id');
        $types->doc_name = $request->input('doc_name');
        $types->doc_path = $request->input('doc_path');
        $types->created_at = date('Y-m-d H:i:s');
        $types->updated_at = date('Y-m-d H:i:s');
        $types->created_by = $request->input('user_id');        
        
        if($types->save()) {
            $returnData = $types->find($types->doc_id);
            $data = array ("message" => 'Document added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'doc_name' => 'required', 
            'doc_path' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = Defaultdoclist::where("doc_id",$id)->where("org_id",$request->input('org_id'))->update( 
            array(
             "doc_name" => $request->input('doc_name'), 
             "doc_path" => $request->input('doc_path'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($types>0)
             {
                 $returnData = Defaultdoclist::find($id);
                 $data = array ("message" => 'Project Type Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $types = Defaultdoclist::where("org_id",$id)->get();
        echo json_encode($types); 
    }
    function updateDeletion(Request $request,$id)
    {
        $types = Defaultdoclist::where("doc_id",$id)->update( 
        array("isDeleted" => $request->input('isDeleted'), "updated_at" => date('Y-m-d H:i:s')));
            if($types>0)
            {
                $returnData = Defaultdoclist::find($id);
                $data = array ("message" => 'Doc  Deleted successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
            }
    }
}
