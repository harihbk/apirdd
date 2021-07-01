<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\FitoutCompletionCertificates;
use App\Models\FitoutDepositrefund;
use Validator;
use PDF;
use File;
use Mail;


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
    public function generateFCC(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $projectDetails = $this->getProjectDetails($request->input('project_id'));
        $data = array();
        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name']
        ];

        try
        {
            $pdf = PDF::loadView('fccPDF', $data);
    
            return  $pdf->download('fcc.pdf');
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

        $projectDetails = $this->getProjectDetails($request->input('project_id'));

        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name']
        ];

        try
        {
            $pdf = PDF::loadView('fdrPDF', $data);
    
            return $pdf->download('testing.pdf');
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
    }
    public function sendFcc(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required',
            'fcc_doc_path' => 'required',
            'fcc_id' => 'required'
        ]);


        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $actual_date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        FitoutCompletionCertificates::where('project_id',$request->input('project_id'))->where('id',$request->input('fcc_id'))->update(array(
            "actual_date" => $actual_date,
            "updated_at" => $updated_at
        ));

        $memberDetails = $this->getProjectMembers($request->input('project_id'));
        $projectDetails = $this->getProjectDetails($request->input('project_id'));
        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name'],
        ];
        $emailData = [
            "investor" => $memberDetails[0]["investor"],
            "rdd_manager" => $memberDetails[0]["rdd_manager"],
            "project_name" => $memberDetails[0]["project_name"],
            "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:"")
        ];
        try
        {
            $pdf = PDF::loadView('fccPDF', $data);
            $pre_date = date('d-m-Y H:i:s');
            $destination_path = $request->input('fcc_doc_path')."/sent/FCC_".$pre_date.".pdf";
            if(!File::isDirectory($request->input('fcc_doc_path')."/sent")){
                File::makeDirectory($request->input('fcc_doc_path')."/sent", 0777, true, true);
            }
            $fileMove = file_put_contents($destination_path, $pdf->output());
            if($fileMove==false)
            {
                return response()->json(['response'=>"Cannot generate FCC document"], 410);
            }
            $files = [
                $request->input('fcc_doc_path')."/sent/FCC_".$pre_date.".pdf"            
            ];
            Mail::send('emails.sendFcc', $emailData, function($message)use($emailData, $files) {
                $message->to($emailData["investor"])
                        ->cc($emailData["rdd_manager"])
                        ->subject("RDD - FCC Attachment");
     
                forEach ($files as $file){
                    $message->attach($file,['as'=>'FCC_'.$emailData['project_name'].".pdf",'mime'=> "application/pdf"]);
                }      
            });
            if(Mail::failures())
            {
                return response()->json(['response'=>"FCC Mail Not Sent"], 410);
            }
            else
            {
                return response()->json(['response'=>"FCC Mail Sent"], 200);
            }
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }
        
    }
    public function sendDrf(Request $request)
    {
        $validator = Validator::make($request->all(), [  
            'project_id' => 'required',
            'drf_doc_path' => 'required',
            'drf_id' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $memberDetails = $this->getProjectMembers($request->input('project_id'));
        $projectDetails = $this->getProjectDetails($request->input('project_id'));

        $actual_date = date('Y-m-d');
        $updated_at = date('Y-m-d H:i:s');
        FitoutDepositrefund::where('project_id',$request->input('project_id'))->where('id',$request->input('drf_id'))->update(array(
            "actual_date" => $actual_date,
            "updated_at" => $updated_at
        ));

        $data = [
            'unit_name' => $projectDetails[0]['unit_name'],
            'investor_brand' => $projectDetails[0]['investor_brand'],
            "investor_name" => $projectDetails[0]['tenant_name']." ".$projectDetails[0]['tenant_last_name']
        ];

        $emailData = [
            "investor" => $memberDetails[0]["investor"],
            "rdd_manager" => $memberDetails[0]["rdd_manager"],
            "project_name" => $memberDetails[0]["project_name"],
            "rdd_manager_name" => $memberDetails[0]["mem_name"]." ".($memberDetails[0]["mem_last_name"]!=''?$memberDetails[0]["mem_last_name"]:"")
        ];

        try
        {
            $pdf = PDF::loadView('fdrPDF', $data);
            $pre_date = date('d-m-Y H:i:s');
            $destination_path = $request->input('drf_doc_path')."/sent/FDR_".$pre_date.".pdf";
            if(!File::isDirectory($request->input('drf_doc_path')."/sent")){
                File::makeDirectory($request->input('drf_doc_path')."/sent", 0777, true, true);
            }
            $fileMove = file_put_contents($destination_path, $pdf->output());
            if($fileMove==false)
            {
                return response()->json(['response'=>"Cannot generate FDR document"], 410);
            }
            $files = [
                $request->input('drf_doc_path')."/sent/FDR_".$pre_date.".pdf"            
            ];
            Mail::send('emails.sendFdr', $emailData, function($message)use($emailData, $files) {
                $message->to($emailData["investor"])
                        ->cc($emailData["rdd_manager"])
                        ->subject("RDD - FDR Attachment");
     
                forEach ($files as $file){
                    $message->attach($file,['as'=>'FDR_'.$emailData['project_name'].".pdf",'mime'=> "application/pdf"]);
                }      
            });
            if(Mail::failures())
            {
                return response()->json(['response'=>"Deposit Refund Mail Not Sent"], 410);
            }
            else
            {
                return response()->json(['response'=>"Deposit Refund Sent"], 200);
            }
            
        }
        catch (\Exception $e) {
             return $e->getMessage();
        }

    }
    public function getProjectMembers($project_id)
    {
        //retrieving project investor and assigned manager
        return Project::leftjoin('users','users.mem_id','=','tbl_projects.assigned_rdd_members')->leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->where('tbl_projects.project_id',$project_id)->where('tbl_project_contact_details.member_designation',13)->select('users.email as rdd_manager','tbl_project_contact_details.email as investor','tbl_projects.project_name','users.mem_name','users.mem_last_name')->get();
    }
    public function getProjectDetails($project_id)
    {
        return project::leftjoin('tbl_project_contact_details','tbl_project_contact_details.project_id','=','tbl_projects.project_id')->leftjoin('tbl_tenant_master','tbl_tenant_master.tenant_id','=','tbl_project_contact_details.member_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_projects.project_id',$project_id)->where('tbl_project_contact_details.member_designation',13)->select('tbl_projects.project_id','tbl_projects.project_name','tbl_units_master.unit_id','tbl_units_master.unit_name','tbl_projects.investor_brand','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->get();
    }
}
