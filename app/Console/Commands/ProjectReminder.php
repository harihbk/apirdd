<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Project;
use App\Http\Controllers\ProjectController;
use Carbon\Carbon;

class ProjectReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Reminder for Scheduling Induction Meeting';

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
         $phase_id=1;
         $task_status=0;
         $task_type=1;
         $date_limit = Carbon::today()->subDays(5);

         $projectDetails = Project::leftjoin('tbl_project_template','tbl_projects.project_id', '=', 'tbl_project_template.project_id')->leftjoin('tbl_project_contact_details as a','tbl_projects.project_id','=','a.project_id')->leftjoin('users','a.member_id','=','users.mem_id')->leftjoin('tbl_project_contact_details as b','tbl_projects.project_id','=','b.project_id')->leftjoin('tbl_tenant_master','b.member_id','=','tbl_tenant_master.tenant_id')->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')->where('tbl_project_template.phase_id',$phase_id)->where('tbl_project_template.task_type',$task_type)->where('tbl_project_template.task_status',$task_status)->select('tbl_projects.project_id','tbl_projects.project_name','a.member_designation as rdd_manager_designation','a.email as rdd_manager_email','b.member_designation as investor_designation','b.email as investor_email','tbl_units_master.unit_name','tbl_properties_master.property_name','users.mem_name','users.mem_last_name','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->whereDate('tbl_projects.created_at', '<=', $date_limit)->where('a.member_designation', 2)->where('b.member_designation', 13)->groupBy('tbl_projects.project_id')->get();
         for($i=0;$i<count($projectDetails);$i++)
         {
                   $emaildata = array();
                   $emaildata = [
                       "rdd_manager_email" => $projectDetails[$i]['rdd_manager_email'],
                       "investor_email" => $projectDetails[$i]['investor_email'],
                       "mem_name" => $projectDetails[$i]['mem_name'],
                       "mem_last_name" => $projectDetails[$i]['mem_last_name']!=""?$projectDetails[$i]['mem_last_name']:"",
                       "tenant_name" => $projectDetails[$i]['tenant_name'],
                       "tenant_last_name" => $projectDetails[$i]['tenant_last_name']!=""?$projectDetails[$i]['tenant_last_name']:"",
                       "unit_name" => $projectDetails[$i]['unit_name'],
                       "property_name" => $projectDetails[$i]['property_name'],
                       "investor_brand" => $projectDetails['investor_brand'],

                       "project_name" => $projectDetails[$i]['project_name']
                   ];
                $to = $projectDetails[$i]['rdd_manager_email'];
                Mail::send('reminders.projectreminder', $emaildata, function($message)use($emaildata,$to) {
                    $message->to($to)
                    ->subject($emaildata['unit_name']."-".$emaildata['investor_brand']."-".$emaildata['property_name']."- Remainder");

                        //    ->subject($emaildata['unit_name']."-".$emaildata['property_name']."-".$emaildata['project_name']."_Remainder");
                    }); 
         }
    }
}
