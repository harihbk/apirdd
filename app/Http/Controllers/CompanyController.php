<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Response;

class CompanyController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        $companies = Company::offset($offset)->limit($limit)->get();
        if($companies!=null) {
            $data = array ("message" => 'Companies data',"data" => $companies );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'company_name' => 'required', 
            'contact_person' => 'required', 
            'contact_email' => 'required', 
            'mobile_no' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $companies = new Company();

        $companies->org_id = $request->input('org_id');
        $companies->company_name = $request->input('company_name');
        $companies->contact_person = $request->input('contact_person');
        $companies->contact_email = $request->input('contact_email');
        $companies->mobile_no = $request->input('mobile_no');
        $companies->created_at = date('Y-m-d H:i:s');
        $companies->updated_at = date('Y-m-d H:i:s');
        $companies->created_by = $request->input('user_id');        
        
        if($companies->save()) {
            $returnData = $companies->find($companies->company_id);
            $data = array ("message" => 'Company added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function update(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'company_name' => 'required', 
            'contact_person' => 'required', 
            'contact_email' => 'required', 
            'mobile_no' => 'required',
            'user_id' => 'required',
            'active_status' => 'required',

        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $companies = Company::where("company_id",$id)->update( 
            array( 
             "company_name" => $request->input('company_name'),
             "contact_person" => $request->input('contact_person'),
             "contact_email" => $request->input('contact_email'),
             "mobile_no" => $request->input('mobile_no'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status')
             ));
        
             if($companies>0)
             {
                 $returnData = Company::find($id);
                 $data = array ("message" => 'Company Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $limit = 1;
        $offset = 1;
        $companies = Company::where("org_id",$id)->offset($offset)->limit($limit)->get();
        echo json_encode($companies); 
    }
    function getCompany(Request $request,$id)
    {
        $companies = Company::where("company_id",$id)->get();
        echo json_encode($companies); 
    }
}
