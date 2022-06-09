<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\Projectdocs;
use App\Http\Controllers\ProjectController;
use Carbon\Carbon;

class DesignActionRemainder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'desingactions:remainder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remainder for actions on design Submission';

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
        $date_limit = Carbon::today()->subDays(3);
        $projectDetails = Projectdocs::
        leftjoin('tbl_projects','tbl_projecttasks_docs.project_id','=','tbl_projects.project_id')
        ->leftjoin('tbl_tenant_master','tbl_projecttasks_docs.uploaded_by','=','tbl_tenant_master.tenant_id')
         ->leftjoin('tbl_project_contact_details as a','tbl_projecttasks_docs.project_id','=','a.project_id')
        ->leftjoin('users','a.member_id','=','users.mem_id')
        ->leftjoin('tbl_properties_master','tbl_properties_master.property_id','=','tbl_projects.property_id')->leftjoin('tbl_units_master','tbl_units_master.unit_id','=','tbl_projects.unit_id')
        ->where('a.member_designation', 2)
        ->whereIn('tbl_projecttasks_docs.doc_status', [1])
        ->whereDate('tbl_projecttasks_docs.updated_at', '>=', $date_limit)
        ->select('tbl_projects.project_id','tbl_projects.project_name','tbl_projecttasks_docs.doc_header','a.member_designation as rdd_manager_designation','a.email as rdd_manager_email','tbl_units_master.unit_name','tbl_properties_master.property_name','users.mem_name','users.mem_last_name','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')
        ->groupBy('tbl_projecttasks_docs.doc_header')->get();

        for($i=0;$i<count($projectDetails);$i++)
         {
                   $emaildata = array();
                   $emaildata = [
                       "rdd_manager_email" => $projectDetails[$i]['rdd_manager_email'],
                       "mem_name" => $projectDetails[$i]['mem_name'],
                       "mem_last_name" => $projectDetails[$i]['mem_last_name']!=""?$projectDetails[$i]['mem_last_name']:"",
                       "unit_name" => $projectDetails[$i]['unit_name'],
                       "property_name" => $projectDetails[$i]['property_name'],
                       "project_name" => $projectDetails[$i]['project_name'],
                       "doc_header"=>$projectDetails[$i]['doc_header'],
                       "tenant_name" => $projectDetails[$i]['tenant_name'],
                       "investor_brand" => $projectDetails['investor_brand'],
                       "tenant_last_name" => $projectDetails[$i]['tenant_last_name']!=""?$projectDetails[$i]['tenant_last_name']:"",
                   ];
                Mail::send('reminders.designactionremainder', $emaildata, function($message)use($emaildata) {
                    $message->to($emaildata['rdd_manager_email'])
                    
                    ->subject($emaildata['unit_name']."-".$emaildata['investor_brand']."-".$emaildata['property_name']."-Design Submission Reminder");

                      //      ->subject($emaildata['unit_name']."-".$emaildata['property_name']."-".$emaildata['project_name']."-Design Submission_Remainder");
                    }); 
         }
    }
}
