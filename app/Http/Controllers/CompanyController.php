<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Response;
use Validator;

class CompanyController extends Controller
{
    function index()
    {
        $limit = 1;
        $offset = 1;
        $companies = Company::all();
        //$companies = Company::offset($offset)->limit($limit)->get();
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
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $companyCheck = Company::where('company_name', $request->input('company_name'))->count();
        if($companyCheck!=0)
        {
            return response()->json(['response'=>"Company name already exists"], 410); 
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
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required',
            'company_id' => 'required', 
            'company_name' => 'required',
            'user_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $companyCheck = Company::where('company_name', $request->input('company_name'))->where('company_id','!=',$request->input('company_id'))->count();
        if($companyCheck!=0)
        {
            return response()->json(['response'=>"Company name already exists"], 410); 
        }

        $companies = Company::where("company_id",$request->input('company_id'))->update( 
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
                 $returnData = Company::find($request->input('company_id'));
                 $data = array ("message" => 'Company Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $query = Company::where("org_id",$id);
        if (!empty($request->input('searchkey')))
        {
            $query->whereLike(['company_name'], $request->input('searchkey'));
        }
        if ($request->has('active_status'))
        {
            $query->where('active_status',$request->input('active_status'));
        }

        $companies = $query->orderBy('company_name', 'ASC')->get();
        return $companies; 
    }
    function getCompany(Request $request,$id)
    {
        $companies = Company::where("company_id",$id)->get();
        echo json_encode($companies); 
    }
}
