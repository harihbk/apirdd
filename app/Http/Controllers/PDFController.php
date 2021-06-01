<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Validator;
use PDF;

class PDFController extends Controller
{
    public function generateHOC(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $projectDetails = project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_projects.project_id',$request->input('project_id'))->where('tbl_project_contact_details.member_designation',13)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->get();

        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name']
        ];

        try
        {
            $pdf = PDF::loadView('hocPDF', $data);
    
            return $pdf->download('testing.pdf');
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }

    public function checking()
    {
        $data = array();
        $pdf = PDF::loadView('test', $data);
    
        return $pdf->download('testing.pdf');
    }

    public function generateFCC(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $projectDetails = project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_projects.project_id',$request->input('project_id'))->where('tbl_project_contact_details.member_designation',13)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->get();

        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name']
        ];

        try
        {
            $pdf = PDF::loadView('hocPDF', $data);
    
            return $pdf->download('testing.pdf');
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }

    public function generateFDR(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $projectDetails = project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_projects.project_id',$request->input('project_id'))->where('tbl_project_contact_details.member_designation',13)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->get();

        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name']
        ];

        try
        {
            $pdf = PDF::loadView('hocPDF', $data);
    
            return $pdf->download('testing.pdf');
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }
}
