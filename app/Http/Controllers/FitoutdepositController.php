<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fitoutdeposit;
use Response;

class FitoutdepositController extends Controller
{
    function index()
    {
        $fitouts = Fitoutdeposit::where('active_status',1)->get();
        if($fitouts!=null) {
            $data = array ("message" => 'Status data',"data" => $fitouts );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $fitouts = new Fitoutdeposit();

        $fitouts->org_id = $request->input('org_id');
        $fitouts->status_name = $request->input('status_name');
        $fitouts->created_at = date('Y-m-d H:i:s');
        $fitouts->updated_at = date('Y-m-d H:i:s');
        $fitouts->created_by = $request->input('user_id');
        
        if($fitouts->save()) {
            $returnData = $fitouts->find($fitouts->Status_id);
            $data = array ("message" => 'Fitout Deposit Status added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        }  
    }
    function update(Request $request)
    {
        $fitouts = Fitoutdeposit::where("status_id",$request->input('status_id'))->update( 
            array( 
             "status_name" => $request->input('status_name'),
             "updated_at" => date('Y-m-d H:i:s'),
             "created_by" => $request->input('user_id'),
             "active_status" => $request->input('active_status')
             ));
        
             if($fitouts>0)
             {
                 $returnData = Fitoutdeposit::find($request->input('status_id'));
                 $data = array ("message" => 'Status Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $fitouts = Fitoutdeposit::where("org_id",$id)->where("active_status",1)->get();
        echo json_encode($fitouts); 
    }

}
