<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use Response;

class TenantController extends Controller
{
    function index()
    {
        $tenants = Tenant::all();
        if($tenants!=null) {
            $data = array ("message" => 'Tenants data',"data" => $tenants );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $tenants = new Tenant();

        $tenants->org_id = $request->input('org_id');
        $tenants->company_id = $request->input('company_id');
        $tenants->brand_name = $request->input('brand_name');
        $tenants->tenant_name = $request->input('tenant_name');
        $tenants->tenant_last_name = $request->input('tenant_last_name');
        $tenants->tenant_email = $request->input('tenant_email');
        $tenants->tenant_mobile = $request->input('tenant_mobile');
        $tenants->tenant_designation = $request->input('tenant_designation');
        $tenants->tenant_type = $request->input('tenant_type');
        $tenants->tenant_address = $request->input('tenant_address');
        $tenants->start_date = date('Y-m-d H:i:s');
        $tenants->end_date = date('Y-m-d H:i:s');
        $tenants->tenant_password = Hash::make($request->input('tenant_password'));
        $tenants->tenant_gender = $request->input('tenant_gender');
        $tenants->created_at = date('Y-m-d H:i:s');
        $tenants->updated_at = date('Y-m-d H:i:s');
        $tenants->created_by = $request->input('user_id');
        
        if($tenants->save()) {
            $returnData = $tenants->find($tenants->tenant_id);
            $data = array ("message" => 'Tenant added successfully',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response); 
        }  
    }
    function update(Request $request,$id)
    {
        $tenants = Tenant::where("tenant_id",$id)->update( 
            array(
             "company_id" => $request->input('company_id'),
             "brand_name" => $request->input('brand_name'),
             "tenant_name" => $request->input('tenant_name'),
             "tenant_last_name" => $request->input('tenant_last_name'),
             "tenant_email" => $request->input('tenant_email'),
             "tenant_mobile" => $request->input('tenant_mobile'),
             "tenant_designation" => $request->input('tenant_designation'),
             "tenant_type" => $request->input('tenant_type'),
             "tenant_gender" => $request->input('tenant_gender'),
             "tenant_address" => $request->input('tenant_address'),
             "start_date" => $request->input('start_date'),
             "end_date" => $request->input('end_date'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status'),
             ));
        
             if($tenants>0)
             {
                 $returnData = Tenant::find($id);
                 $data = array ("message" => 'Tenant Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $tenants = Tenant::where("org_id",$id)->get();
        echo json_encode($tenants); 
    }
    function getTenant(Request $request,$id)
    {
        $tenants = Tenant::where("tenant_id",$id)->get();
        echo json_encode($tenants); 
    }

}
