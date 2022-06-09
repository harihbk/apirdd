<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Project;
use App\Models\Projectdocs;
use App\Http\Controllers\ProjectController;
use Carbon\Carbon;

class DesignSubmissionRemainder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'designsubmission:remainder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date_limit1 = Carbon::today()->addDays(7);
       $date_limit2 = Carbon::today()->addDays(2);
        $projectDetails1 = Projectdocs::
        leftjoin('tbl_projects','tbl_projecttasks_docs.project_id','=','tbl_projects.project_id')
         ->leftjoin('tbl_project_contact_details as a','tbl_projecttasks_docs.project_id','=','a.project_id')
        ->leftjoin('users as g','a.member_id','=','g.mem_id')
        ->leftjoin('tbl_project_contact_details as b','tbl_projecttasks_docs.project_id','=','b.project_id')
        ->leftjoin('tbl_tenant_master as d','b.member_id','=','d.tenant_id')
        ->leftjoin('tbl_project_contact_details as c','tbl_projecttasks_docs.project_id','=','c.project_id')
        ->leftjoin('tbl_tenant_master as e','c.member_id','=','e.tenant_id')
        ->leftjoin('tbl_project_contact_details as f','tbl_projecttasks_docs.project_id','=','f.project_id')
        ->leftjoin('users as h','f.member_id','=','h.mem_id')
        ->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')
        ->where('a.member_designation', 2)
        ->where('b.member_designation', 13)
        ->where('c.member_designation', 14)
        ->where('f.member_designation', 27)
        ->whereIn('tbl_projecttasks_docs.doc_status', [0])
        ->whereDate('tbl_projecttasks_docs.due_date', '=', $date_limit1)
        ->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projecttasks_docs.doc_header','a.member_designation as rdd_manager_designation','a.email as rdd_manager_email','b.member_designation as investor_designation','b.email as investor_email','c.member_designation as contractor_designation','c.email as contractor_email','tbl_units_master.unit_name','tbl_properties_master.property_name','g.mem_name','g.mem_last_name','d.tenant_name as investor_first_name','d.tenant_last_name as investor_last_name','e.tenant_name as contractor_first_name','e.tenant_last_name as contractor_last_name','tbl_projecttasks_docs.due_date','f.member_designation as rdd_admin_designation','f.email as rdd_admin_email')
        ->groupBy('tbl_projecttasks_docs.doc_header')->get();

        $projectDetails2 = Projectdocs::
        leftjoin('tbl_projects','tbl_projecttasks_docs.project_id','=','tbl_projects.project_id')
         ->leftjoin('tbl_project_contact_details as a','tbl_projecttasks_docs.project_id','=','a.project_id')
        ->leftjoin('users as g','a.member_id','=','g.mem_id')
        ->leftjoin('tbl_project_contact_details as b','tbl_projecttasks_docs.project_id','=','b.project_id')
        ->leftjoin('tbl_tenant_master as d','b.member_id','=','d.tenant_id')
        ->leftjoin('tbl_project_contact_details as c','tbl_projecttasks_docs.project_id','=','c.project_id')
        ->leftjoin('tbl_tenant_master as e','c.member_id','=','e.tenant_id')
        ->leftjoin('tbl_project_contact_details as f','tbl_projecttasks_docs.project_id','=','f.project_id')
        ->leftjoin('users as h','f.member_id','=','h.mem_id')
        ->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')
        ->where('a.member_designation', 2)
        ->where('b.member_designation', 13)
        ->where('c.member_designation', 14)
        ->where('f.member_designation', 27)
        ->whereIn('tbl_projecttasks_docs.doc_status', [0])
        ->whereDate('tbl_projecttasks_docs.due_date', '=', $date_limit2)
        ->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projecttasks_docs.doc_header','a.member_designation as rdd_manager_designation','a.email as rdd_manager_email','b.member_designation as investor_designation','b.email as investor_email','c.member_designation as contractor_designation','c.email as contractor_email','tbl_units_master.unit_name','tbl_properties_master.property_name','g.mem_name','g.mem_last_name','d.tenant_name as investor_first_name','d.tenant_last_name as investor_last_name','e.tenant_name as contractor_first_name','e.tenant_last_name as contractor_last_name','tbl_projecttasks_docs.due_date','f.member_designation as rdd_admin_designation','f.email as rdd_admin_email')
        ->groupBy('tbl_projecttasks_docs.doc_header')->get();

         for($i=0;$i<count($projectDetails1);$i++)
         {
                   $emaildata = array();
                   $emaildata = [
                       "rdd_manager_email" => $projectDetails1[$i]['rdd_manager_email'],
                       "rdd_admin_email" => $projectDetails1[$i]['rdd_admin_email'],
                       "investor_email" => $projectDetails1[$i]['investor_email'],
                       "mem_name" => $projectDetails1[$i]['mem_name'],
                       "mem_last_name" => $projectDetails1[$i]['mem_last_name']!=""?$projectDetails1[$i]['mem_last_name']:"",
                       "investor_first_name" => $projectDetails1[$i]['investor_first_name'],
                       "investor_last_name" => $projectDetails1[$i]['investor_last_name']!=""?$projectDetails1[$i]['investor_last_name']:"",
                       "unit_name" => $projectDetails1[$i]['unit_name'],
                       "property_name" => $projectDetails1[$i]['property_name'],
                       "project_name" => $projectDetails1[$i]['project_name'],
                       "contractor_email" => $projectDetails1[$i]['contractor_email'],
                       "contractor_first_name" => $projectDetails1[$i]['contractor_first_name'],
                       "contractor_last_name" => $projectDetails1[$i]['contractor_last_name'],
                       "due_date"=>date('d-m-Y', strtotime($projectDetails1[$i]['due_date']))
                       
                   ];
                $to = array();
                $cc = array();
                array_push($to,$projectDetails1[$i]['investor_email'],$projectDetails1[$i]['contractor_email']);
                array_push($cc,$projectDetails1[$i]['rdd_manager_email'],$projectDetails1[$i]['rdd_admin_email']);
                Mail::send('reminders.designsubmissionremainder', $emaildata, function($message)use($emaildata,$to,$cc) {
                    $message->to($to)
                            ->cc($cc)
                            ->subject($emaildata['unit_name']."-".$emaildata['property_name']."-Design Submission_Reminder");
                    }); 
         }

         for($j=0;$j<count($projectDetails2);$j++)
         {
                   $emaildata = array();
                   $emaildata = [
                       "rdd_manager_email" => $projectDetails2[$j]['rdd_manager_email'],
                        "rdd_admin_email" => $projectDetails2[$j]['rdd_admin_email'],
                       "investor_email" => $projectDetails2[$j]['investor_email'],
                       "mem_name" => $projectDetails2[$j]['mem_name'],
                       "mem_last_name" => $projectDetails2[$j]['mem_last_name']!=""?$projectDetails2[$j]['mem_last_name']:"",
                       "investor_first_name" => $projectDetails2[$j]['investor_first_name'],
                       "investor_last_name" => $projectDetails2[$j]['investor_last_name']!=""?$projectDetails2[$j]['investor_last_name']:"",
                       "unit_name" => $projectDetails2[$j]['unit_name'],
                       "property_name" => $projectDetails2[$j]['property_name'],
                       "project_name" => $projectDetails2[$j]['project_name'],
                       "contractor_email" => $projectDetails2[$j]['contractor_email'],
                       "contractor_first_name" => $projectDetails2[$j]['contractor_first_name'],
                       "contractor_last_name" => $projectDetails2[$j]['contractor_last_name'],
                       "investor_brand" => $projectDetails2['investor_brand'],
                       "due_date"=>date('d-m-Y', strtotime($projectDetails2[$j]['due_date']))
                       
                   ];
                $to = array();
                $cc = array();
                array_push($to,$projectDetails2[$j]['investor_email'],$projectDetails2[$j]['contractor_email']);
                array_push($cc,$projectDetails2[$j]['rdd_manager_email'],$projectDetails2[$j]['rdd_admin_email']);
                Mail::send('reminders.designsubmissionremainder', $emaildata, function($message)use($emaildata,$to,$cc) {
                    $message->to($to)
                            ->cc($cc)
                            ->subject($emaildata['unit_name']."-".$emaildata['investor_brand']."-".$emaildata['property_name']."-Design Submission Remainder");

                         //   ->subject($emaildata['unit_name']."-".$emaildata['property_name']."-Design Submission_Remainder");
                    }); 
         }
    }
}
