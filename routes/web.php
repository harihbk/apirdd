<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrganisationsController;
use App\Http\Controllers\PropertiesController;
use App\Http\Controllers\UnitsController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FitoutdepositController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\ProjecttypeController;
use App\Http\Controllers\MailfrequencyController;
use App\Http\Controllers\WorkpermitController;
use App\Http\Controllers\DefaultdoclistController;
use App\Http\Controllers\InspectionrootController;
use App\Http\Controllers\InspectionchecklistController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ChecklisttemplateController;
use App\Http\Controllers\PhaseController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\AuthorizationgrpController;
use App\Http\Controllers\FileuploadController;
use App\Http\Controllers\InspectionrequestController;



Route::get('/clear-cache', function() {
     if(Artisan::call('cache:clear'))
     {
          return "Cache is cleared";
     }   
 });

 Route::get('/config-cache', function() {
     if(Artisan::call('config:cache'))
     {
          return "Cache is cleared";
     }
 });


 Route::get('/router-cache', function() {
     $exitCode = Artisan::call('route:cache');
     return "Cache is cleared";
 });
 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/tenantlogin', [TenantController::class, 'login']);
Route::post('/emailcheck', [AuthController::class, 'emailCheck']);
Route::post('/otpcheck', [AuthController::class, 'otpVerification']);
Route::post('/passwordreset', [AuthController::class, 'passwordReset']);

/*For creating Super User*/
// Route::post('/superuser', [MembersController::class, 'createSuperuser']);

/*Super User routes */
Route::group(['prefix' => '/superuser','middleware' => 'superuserauth:api'], function() {
    

});

Route::group(['middleware' => 'userauth:api'], function() {
    //Refresh Token
    Route::post('/refresh', [AuthController::class, 'getnewToken']);
    
     //organisations
     Route::get('/org', [OrganisationsController::class, 'index']);
     Route::get('/org/{id}',[OrganisationsController::class, 'getOrgbyid']);
     Route::post('/org', [OrganisationsController::class, 'store']);
     Route::patch('/org/update',[OrganisationsController::class, 'update']);
     Route::delete('/org/{id}',[OrganisationsController::class, 'deleteOrg']);

    //properties
    Route::get('/properties', [PropertiesController::class, 'index']);
    Route::post('/properties', [PropertiesController::class, 'store']);
    Route::post('/properties/update',[PropertiesController::class, 'update']);
    Route::post('/properties/org/{id}',[PropertiesController::class, 'retrieve']);

    //units
    Route::get('/units', [UnitsController::class, 'index']);
    Route::post('/units', [UnitsController::class, 'store']);
    Route::post('/units/update',[UnitsController::class, 'update']);
    Route::post('/units/org/{id}/{propid}',[UnitsController::class, 'retrieveByOrg']);
    Route::post('/units/prop/{propid}/{floorid}',[UnitsController::class, 'retrieveByFloor']);

    //members
    Route::get('/members', [MembersController::class, 'index']);
    Route::post('/members', [MembersController::class, 'store']);
    Route::get('/members/{id}',[MembersController::class, 'getMember']);
    Route::post('/members/update',[MembersController::class, 'update']);
    Route::post('/members/org/{id}',[MembersController::class, 'retrieveByOrg']);
    Route::post('/members/org/{id}/type/{tid}',[MembersController::class, 'getMemberByType']);
    Route::get('/members/bydesignation/{org_id}/{designation_id}',[MembersController::class, 'getMemberByDesignation']);
    Route::post('/members/getMembers',[MembersController::class, 'retreiveMembersforProject']);
    


    //designation
    Route::get('/designation', [DesignationController::class, 'index']);
    Route::get('/designation/user/{org_id}/{usertype}',[DesignationController::class, 'retrieveByUsertype']);
    Route::post('/designation', [DesignationController::class, 'store']);
    Route::get('/designation/{id}',[DesignationController::class, 'getDesignation']);
    Route::post('/designation/update',[DesignationController::class, 'update']);
    Route::get('/designation/org/{id}',[DesignationController::class, 'retrieveByOrg']);
    

    //memberlevel
    Route::get('/level', [LevelController::class, 'index']);
    Route::post('/level', [LevelController::class, 'store']);
    Route::post('/level/update',[LevelController::class, 'update']);
    Route::post('/level/org/{id}',[LevelController::class, 'retrieveByOrg']);


    //currency
    // Route::get('/currency', [CurrencyController::class, 'index']);
    // Route::post('/currency', [CurrencyController::class, 'store']);
    // Route::post('/currency/{id}',[CurrencyController::class, 'update']);

    //company
    Route::get('/company', [CompanyController::class, 'index']);
    Route::post('/company', [CompanyController::class, 'store']);
    Route::get('/company/{id}',[CompanyController::class, 'getCompany']);
    Route::post('/company/update',[CompanyController::class, 'update']);
    Route::post('/company/org/{id}',[CompanyController::class, 'retrieveByOrg']);

    //Fitout Deposit Status
    Route::get('/dtstatus', [FitoutdepositController::class, 'index']);
    Route::post('/dtstatus', [FitoutdepositController::class, 'store']);
    Route::post('/dtstatus/update',[FitoutdepositController::class, 'update']);
    Route::get('/dtstatus/org/{id}',[FitoutdepositController::class, 'retrieveByOrg']);

    //tenant
    Route::get('/tenant', [TenantController::class, 'index']);
    Route::post('/tenant', [TenantController::class, 'store']);
    Route::get('/tenant/{id}',[TenantController::class, 'getTenant']);
    Route::get('/tenant/bydesignation/{org_id}/{designation_id}',[TenantController::class, 'getInvestorByDesignation']);
    Route::post('/tenant/update',[TenantController::class, 'update']);
    Route::post('/tenant/org/{id}',[TenantController::class, 'retrieveByOrg']);
    Route::get('/tenantlist/org/{id}/{tenanttype}',[TenantController::class, 'retrieveTenantforprojectcontact']);

    //Floor
    Route::get('/floor', [FloorController::class, 'index']);
    Route::post('/floor', [FloorController::class, 'store']);
    Route::get('/floor/{id}',[FloorController::class, 'getFloor']);
    Route::post('/floor/update',[FloorController::class, 'update']);
    Route::post('/floor/org/{id}',[FloorController::class, 'retrieveByOrg']);
    Route::post('/floor/property/{id}',[FloorController::class, 'retrieveByProperty']);

    //Project Types
    Route::get('/prtypes', [ProjecttypeController::class, 'index']);
    Route::post('/prtypes', [ProjecttypeController::class, 'store']);
    Route::get('/prtypes/{id}',[ProjecttypeController::class, 'getFloor']);
    Route::post('/prtypes/update',[ProjecttypeController::class, 'update']);
    Route::get('/prtypes/org/{id}',[ProjecttypeController::class, 'retrieveByOrg']);

    //Mail Frequency
    Route::get('/freq', [MailfrequencyController::class, 'index']);
    Route::post('/freq', [MailfrequencyController::class, 'store']);
    Route::post('/freq/update',[MailfrequencyController::class, 'update']);
    Route::post('/freq/del/{id}',[MailfrequencyController::class, 'updateDeletion']);
    Route::get('/freq/org/{id}',[MailfrequencyController::class, 'retrieveByOrg']);

    //Work Permit
    Route::get('/permit', [WorkpermitController::class, 'index']);
    Route::post('/permit', [WorkpermitController::class, 'store']);
    Route::post('/permit/update',[WorkpermitController::class, 'update']);
    Route::post('/permit/del/{id}',[WorkpermitController::class, 'updateDeletion']);
    Route::get('/permit/org/{id}',[WorkpermitController::class, 'retrieveByOrg']);

    //Default Doc list
    Route::get('/doc', [DefaultdoclistController::class, 'index']);
    Route::post('/doc', [DefaultdoclistController::class, 'store']);
    Route::post('/doc/update',[DefaultdoclistController::class, 'update']);
    Route::post('/doc/del/{id}',[DefaultdoclistController::class, 'updateDeletion']);
    Route::post('/doc/org/{id}',[DefaultdoclistController::class, 'retrieveByOrg']);

    //Inspection root Categories
    Route::get('/inroot', [InspectionrootController::class, 'index']);
    Route::post('/inroot', [InspectionrootController::class, 'store']);
    Route::post('/inroot/update',[InspectionrootController::class, 'update']);
    Route::post('/inroot/del/{id}',[InspectionrootController::class, 'updateDeletion']);
    Route::post('/inroot/org/{id}',[InspectionrootController::class, 'retrieveByOrg']);
    Route::post('/inroot/updatemember',[InspectionrootController::class, 'updateMember']);

    //Inspection Checklist
    Route::post('/inchecklist', [InspectionchecklistController::class, 'store']);
    Route::post('/inchecklist/update',[InspectionchecklistController::class, 'update']);
    Route::post('/inchecklist/del/{id}',[InspectionchecklistController::class, 'updateDeletion']);
    Route::post('/inchecklist/org/{id}/{tempid}',[InspectionchecklistController::class, 'retrievebyOrg']);
   


    //Template Controller

    /* For master template data edit and adding phase details*/
    Route::get('/template/{template_id}/{phaseid}', [TemplateController::class, 'getTemplateData']);
    Route::get('/templatelist/{org_id}', [TemplateController::class, 'getTemplatelist']);
    Route::post('/template', [TemplateController::class, 'store']);
    Route::patch('/template/{template_id}', [TemplateController::class, 'update']);
    Route::post('/template/org/{id}/{pid}', [TemplateController::class, 'retrievebyTemplate']);
    Route::post('/template/phase/{id}/{tid}/', [TemplateController::class, 'getTemplatePhase']);

    //Checklist template master
    Route::post('/checklisttemplate', [ChecklisttemplateController::class, 'store']);
    Route::get('/checklisttemplate/{orgid}', [ChecklisttemplateController::class, 'retrievebyOrg']);


    //phase master
    Route::get('/phase', [PhaseController::class, 'retrieveByorg']);
    Route::post('/phase', [PhaseController::class, 'store']);
    Route::delete('/phase/{phase_id}', [PhaseController::class, 'deletePhase']);
    
    /**Transactional Data Api's **/

    //Project Controller

    /*Create Project */
    Route::post('/project', [ProjectController::class, 'store']);
    /*get project lists for orgaanisation */
    Route::get('/project/{id}', [ProjectController::class, 'retrieveByorg']);
    /*get project fitout form,investor dates,milestone dates */
    Route::get('/project/fitoutdetails/{projectid}', [ProjectController::class, 'retrieveProjectworkspace']);
    /* get project phase wise tasks details */
    Route::get('/project/phase/{projectid}/{phase_id}', [ProjectController::class, 'retrieveProjectPhase']);
    /* update fitout details,project workspace details */
    Route::patch('/project/{project_id}', [ProjectController::class, 'updateFitoutdetails']);
    /* Scheduling meeting*/
    Route::patch('/project/schedulemeeting/{project_id}', [ProjectController::class, 'rddscheduleMeeting']);
    /* approvers action on scheduled meeting tasks*/
    Route::patch('/project/meetingapprovalaction/{project_id}', [ProjectController::class, 'rddmeetingApprovalaction']);
    /* attendees action on scheduled meeting tasks*/
    Route::patch('/project/attendeesmeetingaction/{project_id}', [ProjectController::class, 'rddattendeeMeetingsaction']);
    /*retrieve assigned project lists for orgaanisation */
    Route::post('/project/lists', [ProjectController::class, 'getMemberProjects']);
    //get active meeting tasks for the assigned projects -- For dashboard
    Route::get('/getActivetasks/{memid}/{tasktype}', [ProjectController::class, 'getActivetasks']);
    /* update Phase details,project phase details */
    Route::patch('/project/phase/{project_id}/{phase_id}', [ProjectController::class, 'updatePhasedetails']);
    /* update Comment,actaul date for docs type tasks */
    Route::patch('/docdetails', [ProjectController::class, 'updatedocDetails']);
    /*retrieve template designations */
    Route::get('/project/templatedesignations/{templateid}',[ProjectController::class, 'retrieveTemplateDesignations']);
    /* Send Mail [project workspace & on creation] - project contact details */
    Route::patch('/project/sendmail/{project_id}/{type}', [ProjectController::class, 'sendMail']);
    /* Send Mail [project Meeting task - Mom detail] */
    Route::patch('/project/sendmommail/{project_id}/{taskid}', [ProjectController::class, 'sendMommail']);
    /* Send Mail [project Meeting task - Reminder detail] */
    Route::patch('/project/sendremindermail/{project_id}/{task_id}', [ProjectController::class, 'sendRemindermail']);
    /*Create Work Permit for project */
    Route::post('/project/workpermit/{projectid}', [ProjectController::class, 'rddcreateWorkpermit']);
    /*retrieve tasks list - meeting,todo,documents*/
    Route::get('/project/tasklist/{tasktype}/{memid}/{memname}',[ProjectController::class, 'retrieveMembertasklists']);
    /*retrieve tasks approval status - meeting,todo,documents*/
    Route::get('/project/approvalstatus/{projectid}/{taskid}',[ProjectController::class, 'retrievetaskApprovalstatus']);
    /*get document history - drawer*/
    Route::get('/project/dochistory/{docid}',[ProjectController::class, 'getDochistory']);
    /*Approval action on uploaded documents */
    Route::patch('/docapproval', [ProjectController::class, 'rddperformApprovaldocaction']);


    //fileupload controller
    /* Upload documents */
    Route::post('/uploadfile', [FileuploadController::class, 'fileUploadPost']);


    //Inspection checklist controller
     /*create inspection request */
     Route::post('/project/inspectionrequest',[InspectionrequestController::class, 'createInspectionRequest']);







    
    //project status - chart section
    Route::get('/getprojectstatus/{pid}', [ProjectController::class, 'getProjectstatus']);
    //performing action on uploaded documents
    Route::post('/memdocAction', [ProjectController::class, 'performDocaction']);
    //forwarding tasks
    Route::post('/forwardTasks', [ProjectController::class, 'forwardTasks']);
    //completing tasks
    Route::post('/completetask', [ProjectController::class, 'completetask']);
    


    //configurations
    /*Create a Mail config*/
    Route::post('/config/mail', [ConfigController::class, 'createMailconfig']);
    /*Update a Mail config*/
    Route::patch('/config/mail', [ConfigController::class, 'updateMailconfig']);
    /*Get all config for organisation*/
    Route::get('/config/{org_id}', [ConfigController::class, 'getConfig']);
    /*Create a Milestone config*/
    Route::post('/config/milestone', [ConfigController::class, 'createMilestoneconfig']);
    /*Update a Milestone config*/
    Route::patch('/config/milestone', [ConfigController::class, 'updateMilestoneconfig']);
    /*Create a doc path config*/
    Route::post('/config/docpath', [ConfigController::class, 'createDocpathconfig']);
    /*Update a doc path config*/
    Route::patch('/config/docpath', [ConfigController::class, 'updateDocpathconfig']);
    /*Get all config  master*/
    Route::get('/config/milestone/master', [ConfigController::class, 'getMilestonemaster']);

    
    //authorization Group
    /*Get auth group workspace sections master */
    Route::get('/authgrp/workspacesections', [AuthorizationgrpController::class, 'getWorkspacesections']);
    /*Get auth group workspace fields master */
    Route::get('/authgrp/workspacemaster', [AuthorizationgrpController::class, 'getWorkspacemaster']);
    /*Get auth group lists */
    Route::get('/authgrp', [AuthorizationgrpController::class, 'getAuthgrplists']);
    /*Get created auth group data */
    Route::get('/authgrp/{id}', [AuthorizationgrpController::class, 'getAuthgrpData']);
    /*Get content for creating authorization group */
    Route::get('/authgrpcontent', [AuthorizationgrpController::class, 'getMastercontent']);
    /*Creating authorization group */
    Route::post('/authgrp', [AuthorizationgrpController::class, 'createAuthorizationgrp']);
});


Route::group(['middleware' => 'tenantauth:api'], function() {
     //Refresh Token
     Route::post('/tenantrefresh', [TenantController::class, 'getnewToken']);

     //meeting approvals
     Route::post('/gettasksforapproval/{pid}/{memid}', [TenantController::class, 'getTasksforapproval']);
     Route::post('/meetingAction', [TenantController::class, 'performMeetingaction']);
     Route::post('/uploadoc', [TenantController::class, 'uploaddocAction']);


     //project controller
     //work permit
     /* request work permit */
     Route::post('/investor/workpermit/{projectid}', [ProjectController::class, 'investorrequestWorkpermit']);
     /* Get requested work permit */
     Route::post('/investor/workpermitlist', [ProjectController::class, 'investorWorkpermitlist']);
     /* Get work permit types master list */
     Route::get('investor/permit/org/{id}',[WorkpermitController::class, 'retrieveByOrg']);
     /* request inspection */
     Route::post('/investor/inspection/{projectid}', [ProjectController::class, 'investorrequestInspection']);
     /* attendees action on scheduled meeting tasks*/
     Route::patch('/investor/attendeesmeetingaction/{project_id}', [ProjectController::class, 'investormeetingAction']);
     /* Get project workspace data */
     Route::get('/investor/projectworkspace/{projectid}/{propertyid}',[ProjectController::class, 'retrieveInvestorProjectworkspace']);
     /* Get Attendee assigned project doc task lists */
     Route::post('/investor/projectdoctasks',[ProjectController::class, 'retrieveInvestorDoctasks']);
     /* Uploading docs and updating status */
     Route::patch('/investor/updatedocstatus', [ProjectController::class, 'investordocActions']);
     /* Get Attendee assigned property lists */
     Route::get('/investor/propertylists/{memid}/{memname}',[ProjectController::class, 'retrievetenantPropertylists']);
     /* Get Attendee assigned project lists */
     Route::get('/investor/projectlists/{memid}/{memname}/{propertyid}',[ProjectController::class, 'retrievetenantProjectlists']);
     

     //Inspection Request controller
     /* Get requested Inspection */
     Route::post('/investor/inspectionrequestlist', [InspectionrequestController::class, 'investorinspectionList']);
     /* Get selected Inspection data  */
     Route::post('/investor/inspectionrequest', [InspectionrequestController::class, 'investorretrieveInspectiondata']);
     /* update Inspection file upload details data  */
     Route::patch('/investor/inspectionfiledetails', [InspectionrequestController::class, 'updateInspectionfiledetails']);
     /* update selected Inspection data  */
     Route::patch('/investor/inspectionRequestdetails', [InspectionrequestController::class, 'updateInspectionrequestdetails']);


});