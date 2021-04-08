<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mailfrequency;
use Response;
use Validator;

class MailfrequencyController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        //$types = Mailfrequency::offset($offset)->limit($limit)->get();
        $types = Mailfrequency::all();
        if($types!=null) {
            $data = array ("message" => 'Mail Frequency data',"data" => $types );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'notification_frequency' => 'required', 
            'interval_days' => 'required|numeric', 
            'due_date_percentage' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = new Mailfrequency();

        $types->org_id = $request->input('org_id');
        $types->notification_frequency = $request->input('notification_frequency');
        $types->interval_days = $request->input('interval_days');
        $types->esc_level = $request->input('esc_level');
        $types->due_date_percentage = $request->input('due_date_percentage');
        $types->created_at = date('Y-m-d H:i:s');
        $types->updated_at = date('Y-m-d H:i:s');
        $types->created_by = $request->input('user_id');        
        
        if($types->save()) {
            $returnData = $types->find($types->fre_id);
            $data = array ("message" => 'Mail Frequency added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'freq_id' => 'required',
            'notification_frequency' => 'required', 
            'interval_days' => 'required|numeric', 
            'due_date_percentage' => 'required',
            'user_id' => 'required',
            'active_status' => 'required',
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $types = Mailfrequency::where("fre_id",$request->input('freq_id'))->update( 
            array(
             "notification_frequency" => $request->input('notification_frequency'), 
             "esc_level" => $request->input('esc_level'),
             "interval_days" => $request->input('interval_days'),
             "due_date_percentage" => $request->input('due_date_percentage'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status'),
             ));
        
             if($types>0)
             {
                 $returnData = Mailfrequency::find($request->input('freq_id'));
                 $data = array ("message" => 'Project Type Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $limit = 1;
        $offset = 1;
        //$types = Mailfrequency::where("org_id",$id)->offset($offset)->limit($limit)->get();
        $types = Mailfrequency::where("org_id",$id)->get();
        return Response::json(["response"=>$types],200);
    }
    function updateDeletion(Request $request,$id)
    {
        $types = Mailfrequency::where("fre_id",$id)->update( 
        array("isDeleted" => $request->input('isDeleted'), "updated_at" => date('Y-m-d H:i:s')));
            if($types>0)
            {
                $returnData = Mailfrequency::find($id);
                $data = array ("message" => 'Project Type Deleted successfully',"data" => $returnData );
                $response = Response::json($data,200);
                echo json_encode($response); 
            }
    }
}
