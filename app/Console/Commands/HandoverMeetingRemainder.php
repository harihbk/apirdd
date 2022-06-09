<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Project;
use App\Http\Controllers\ProjectController;
use Carbon\Carbon;


class HandoverMeetingRemainder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'handovermeeting:remainder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remainder for Scheduling Handover Meeting';

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
        $date_limit = Carbon::today()->addDays(7);
        $projectDetails = Project::
        leftjoin('tbl_project_milestone_dates','tbl_projects.project_id', '=', 'tbl_project_milestone_dates.project_id')
        ->leftjoin('tbl_project_template','tbl_projects.project_id','=','tbl_project_template.project_id')
        ->leftjoin('tbl_project_contact_details','tbl_projects.project_id','=','tbl_project_contact_details.project_id')
        ->leftjoin('users','tbl_project_contact_details.member_id','=','users.mem_id')
        ->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')
        ->whereDate('tbl_project_milestone_dates.unit_handover', '=', $date_limit)
        ->where('tbl_project_milestone_dates.active_status',1)
        ->where('tbl_project_contact_details.member_designation', 2)
        ->where('tbl_project_template.phase_id', 3)
        ->where('tbl_project_template.task_type', 1)
        ->where('tbl_project_template.isDeleted', 0)
        ->where('tbl_project_template.task_status', 0)
        ->select('tbl_projects.project_id','tbl_projects.project_name','tbl_project_contact_details.member_designation as rdd_manager_designation','tbl_project_contact_details.email as rdd_manager_email','users.mem_name','users.mem_last_name','tbl_units_master.unit_name','tbl_properties_master.property_name')
        ->groupBy('tbl_projects.project_id')->get();
        for($i=0;$i<count($projectDetails);$i++)
         {
                   $emaildata = array();
                   $emaildata = [
                       "rdd_manager_email" => $projectDetails[$i]['rdd_manager_email'],
                       "mem_name" => $projectDetails[$i]['mem_name'],
                       "mem_last_name" => $projectDetails[$i]['mem_last_name']!=""?$projectDetails[$i]['mem_last_name']:"",
                       "unit_name" => $projectDetails[$i]['unit_name'],
                       "property_name" => $projectDetails[$i]['property_name'],
                       "investor_brand" => $projectDetails['investor_brand'],
                       "project_name" => $projectDetails[$i]['project_name']
                   ];
                $to = $projectDetails[$i]['rdd_manager_email'];
                Mail::send('reminders.handovermeetingremainder', $emaildata, function($message)use($emaildata,$to) {
                    $message->to($to)
                    ->subject($emaildata['unit_name']."-".$emaildata['investor_brand']."-".$emaildata['property_name']."-Handover Meeting Remainder");

                         //   ->subject($emaildata['unit_name']."-".$emaildata['property_name']."- Handover Meeting Remainder");
                    }); 
         }
    }
}
