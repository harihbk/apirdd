<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisations;
use Response;
use Validator;

class OrganisationsController extends Controller
{
    function index(Request $request)
    {
        $limit = 20;
        $offset = 0;
        $organisations = Organisations::where('isDeleted',0)->get();
        echo json_encode($organisations);  
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_name' => 'required', 
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $organisations = new Organisations();
        $count = Organisations::where('isDeleted',0)->count();
        $code = "ORG" . sprintf("%05d", $count+1);
        $organisations->org_name = $request->input('org_name');
        $organisations->org_code = $code;
        $organisations->created_at = date('Y-m-d H:i:s');
        $organisations->updated_at = date('Y-m-d H:i:s');
        $organisations->created_by = $request->input('user_id');


        if($organisations->save()) {
            echo json_encode($organisations->find($organisations->org_id)); 
        } 
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required',
            'org_name' => 'required', 
            'user_id' => 'required',
            'active_status' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $organisations = Organisations::where("org_id",$request->input('org_id'))->update( 
                            array( 
                             "org_name" => $request->input('org_name'),
                             "updated_at" => date('Y-m-d H:i:s'),
                             "created_by" => $request->input('user_id'),
                             "active_status" => $request->input('active_status')
                             ));
        echo json_encode(Organisations::find($request->input('org_id')));
    }
    function getOrgbyid(Request $request,$id)
    {
        $org = Organisations::where("org_id",$id)->get();
        echo json_encode($org); 
    }
    function deleteOrg($id)
    {
        $organisations = Organisations::where("org_id",$id)->update( 
            array( 
             "updated_at" => date('Y-m-d H:i:s'),
             "isdeleted" => 1
             ));
        echo json_encode(Organisations::find($id));
    }
}
