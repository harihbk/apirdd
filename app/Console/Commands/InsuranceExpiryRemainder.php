<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Project;
use App\Http\Controllers\ProjectController;
use Carbon\Carbon;

class InsuranceExpiryRemainder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insuranceexpiry:remainder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remainder for Insurance Expiry';

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
        $date_limit = Carbon::today()->addDays(10);
        $projectDetails = Project::
        leftjoin('tbl_project_contact_details as a','tbl_projects.project_id','=','a.project_id')
        ->leftjoin('users as g','a.member_id','=','g.mem_id')
        ->leftjoin('tbl_project_contact_details as b','tbl_projects.project_id','=','b.project_id')
        ->leftjoin('tbl_tenant_master as d','b.member_id','=','d.tenant_id')
        ->leftjoin('tbl_project_contact_details as c','tbl_projects.project_id','=','c.project_id')
        ->leftjoin('tbl_tenant_master as e','c.member_id','=','e.tenant_id')
        ->leftjoin('tbl_project_contact_details as f','tbl_projects.project_id','=','f.project_id')
        ->leftjoin('users as h','f.member_id','=','h.mem_id')
        ->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')
        ->where('a.member_designation', 2)
        ->where('b.member_designation', 13)
        ->where('c.member_designation', 14)
        ->where('f.member_designation', 27)
        ->whereDate('tbl_projects.insurance_validity_date', '=', $date_limit)
        ->select('tbl_projects.project_id','tbl_projects.project_name','a.member_designation as rdd_manager_designation','a.email as rdd_manager_email','b.member_designation as investor_designation','b.email as investor_email','c.member_designation as contractor_designation','c.email as contractor_email','tbl_units_master.unit_name','tbl_properties_master.property_name','g.mem_name','g.mem_last_name','d.tenant_name as investor_first_name','d.tenant_last_name as investor_last_name','e.tenant_name as contractor_first_name','e.tenant_last_name as contractor_last_name','f.email as rdd_admin_email')
        ->groupBy('tbl_projects.project_id')->get();

        for($i=0;$i<count($projectDetails);$i++)
         {
                   $emaildata = array();
                   $emaildata = [
                       "rdd_manager_email" => $projectDetails[$i]['rdd_manager_email'],
                       "investor_email" => $projectDetails[$i]['investor_email'],
                       "mem_name" => $projectDetails[$i]['mem_name'],
                       "mem_last_name" => $projectDetails[$i]['mem_last_name']!=""?$projectDetails[$i]['mem_last_name']:"",
                       "investor_first_name" => $projectDetails[$i]['investor_first_name'],
                       "investor_last_name" => $projectDetails[$i]['investor_last_name']!=""?$projectDetails[$i]['investor_last_name']:"",
                       "unit_name" => $projectDetails[$i]['unit_name'],
                       "property_name" => $projectDetails[$i]['property_name'],
                       "investor_brand" => $projectDetails['investor_brand'],

                       "project_name" => $projectDetails[$i]['project_name'],
                       "contractor_email" => $projectDetails[$i]['contractor_email'],
                       "contractor_first_name" => $projectDetails[$i]['contractor_first_name'],
                       "contractor_last_name" => $projectDetails[$i]['contractor_last_name'],
                   ];
                $to = array();
                $cc = array();
                array_push($to,$projectDetails[$i]['investor_email'],$projectDetails[$i]['contractor_email']);
                array_push($cc,$projectDetails[$i]['rdd_manager_email'],$projectDetails[$i]['rdd_admin_email']);
                Mail::send('reminders.insuranceexpiryremainder', $emaildata, function($message)use($emaildata,$to,$cc) {
                    $message->to($to)
                            ->cc($cc)
                            ->subject($emaildata['unit_name']."-".$emaildata['investor_brand']."-".$emaildata['property_name']."-Insurance Expiry Remainder");

                          //  ->subject($emaildata['unit_name']."-".$emaildata['property_name']."-".$emaildata['project_name']."- Insurance Expiry Remainder");
                    }); 
         }
    }
}
