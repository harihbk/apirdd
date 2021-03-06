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
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InspectionrequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TemplatePreOpeningdocsController;
use App\Http\Controllers\ReportController;




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
Route::post('/outlooklogin', [AuthController::class, 'outlookresponse']);
Route::post('/tenantlogin', [TenantController::class, 'login']);
Route::post('/emailcheck', [AuthController::class, 'emailCheck']);
Route::post('/investoremailcheck', [TenantController::class, 'emailCheck']);
Route::post('/otpcheck', [AuthController::class, 'otpVerification']);
Route::post('/investorotpcheck', [TenantController::class, 'otpVerification']);
Route::post('/passwordreset', [AuthController::class, 'passwordReset']);
Route::post('/investorpasswordreset', [TenantController::class, 'passwordReset']);

/*For creating Super User*/
// Route::post('/superuser', [MembersController::class, 'createSuperuser']);

/*Super User routes */
// Route::group(['prefix' => '/superuser','middleware' => 'superuserauth:api'], function() {


// });

Route::get('/testing', [ProjectController::class, 'testing']);

Route::group(['middleware' => 'userauth:api'], function() {
    //Refresh Token
    Route::post('/refresh', [AuthController::class, 'getnewToken']);

    //fileupload controller
    /* Upload documents */
    Route::post('/uploadfile',[DocumentController::class, 'docUpload']);
    Route::post('/multipleuploadfile',[DocumentController::class, 'multipledocUpload']);
    Route::post('/generateupload', [DocumentController::class, 'generateUpload']);

    Route::post('/propertyfileupload',[DocumentController::class, 'propertyfileupload']);


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
    Route::get('/properties/members/{orgid}/{propid}',[PropertiesController::class, 'getMembers']);
    Route::post('/properties/members',[PropertiesController::class, 'addMembers']);
    Route::get('/properties/user/{memid}',[PropertiesController::class, 'retrievememberProperties']);
    Route::post('/properties/user/org/{id}',[PropertiesController::class, 'retrieveForUser']);

    //units
    Route::get('/units', [UnitsController::class, 'index']);
    Route::post('/units', [UnitsController::class, 'store']);
    Route::post('/units/update',[UnitsController::class, 'update']);
    Route::post('/units/org/{id}/{propid}',[UnitsController::class, 'retrieveByOrg']);
    Route::post('/units/prop/{propid}/{floorid}',[UnitsController::class, 'retrieveByFloor']);
    Route::post('/unitslist',[UnitsController::class, 'retrieveUnitsforprojectcreation']);


    //members
    Route::get('/members', [MembersController::class, 'index']);
    Route::post('/members', [MembersController::class, 'store']);
    Route::get('/members/{id}',[MembersController::class, 'getMember']);
    Route::post('/members/update',[MembersController::class, 'update']);
    Route::post('/members/org/{id}',[MembersController::class, 'retrieveByOrg']);
    Route::post('/members/org/{id}/type/{tid}',[MembersController::class, 'getMemberByType']);
    Route::get('/members/bydesignation/{org_id}/{designation_id}',[MembersController::class, 'getMemberByDesignation']);
    Route::post('/members/getMembers',[MembersController::class, 'retreiveMembersforProject']);
    Route::post('/members/getdesignation',[MembersController::class, 'getdesignationdetails']);
    Route::get('/members/project/{org_id}',[MembersController::class, 'getallMembersForProject']);
    Route::get('/members/projectdes/{org_id}/{des_id}/{companyID_id}',[MembersController::class, 'getallMembersForProjectdes']);

    Route::post('/members/designationtypes',[MembersController::class, 'getMembersByDesignationTypes']);



    //designation
    Route::get('/designation', [DesignationController::class, 'index']);
    Route::get('/designation/user/{org_id}/{usertype}',[DesignationController::class, 'retrieveByUsertype']);
    Route::post('/designation', [DesignationController::class, 'store']);
    Route::get('/designation/{id}',[DesignationController::class, 'getDesignation']);
    Route::post('/designation/update',[DesignationController::class, 'update']);
    Route::get('/designation/org/{id}',[DesignationController::class, 'retrieveByOrg']);
    Route::get('/attendeedesignation/org/{id}',[DesignationController::class, 'retrieveAttendeedesignation']);


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
    Route::delete('/removefloor/{propertyid}/{floorid}',[FloorController::class, 'removeFloor']);

    //Project Types
    Route::get('/prtypes', [ProjecttypeController::class, 'index']);
    Route::post('/prtypes', [ProjecttypeController::class, 'store']);
    Route::get('/prtypes/{id}',[ProjecttypeController::class, 'getFloor']);
    Route::post('/prtypes/update',[ProjecttypeController::class, 'update']);
    Route::post('/prtypes/org/{id}',[ProjecttypeController::class, 'retrieveByOrg']);

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
    Route::post('/templatelist/{org_id}', [TemplateController::class, 'getTemplatelist']);
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


    //Template Pre Opening docs
    Route::patch('/templatepreopeningdocs', [TemplatePreOpeningdocsController::class, 'store']);
    Route::get('/templatepreopeningdocs/list/{id}', [TemplatePreOpeningdocsController::class, 'retrieveList']);

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
    Route::post('/getactivetaskscount', [ProjectController::class, 'getActivetasks']);
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
    Route::post('/project/tasklist',[DashboardController::class, 'retrieveMembertasklists']);
    /*retrieve tasks approval status - meeting,todo,documents*/
    Route::get('/project/approvalstatus/{projectid}/{taskid}',[ProjectController::class, 'retrievetaskApprovalstatus']);
    /*get document history - drawer*/
    Route::get('/project/dochistory/{docid}',[ProjectController::class, 'getDochistory']);
    /*Approval action on uploaded documents */
    Route::patch('/docapproval', [ProjectController::class, 'rddperformApprovaldocaction']);
    /* workpermit checklist status */
    Route::get('/workpermitstatus/{projectid}', [ProjectController::class, 'rddRetrieveworkpermitstatus']);
    /* Completion Phase - Generating FCC checklist status */
    Route::get('/fcccheckliststatus/{projectid}', [ProjectController::class, 'rddRetrievefcccheckliststatus']);
    /* Completion Phase - Generating Fitout deposit refund status */
    Route::get('/fitoutdepositcheckliststatus/{projectid}', [ProjectController::class, 'rddRetrievefitoutdepositcheckliststatus']);
    /* Request payment from finance team  */
    Route::get('/requestpayment/{project_id}', [ProjectController::class, 'rddrequestPayment']);
    /*Approval action on work permit */
    Route::patch('/permitapproval', [ProjectController::class, 'rddworkPermitApproval']);
    /*Approval action on Inspections */
    Route::patch('/inspectionsapproval', [ProjectController::class, 'rddinspectionsApproval']);
    //project Progress count - chart section
    Route::get('/getprogresscount/{pid}', [ProjectController::class, 'getProjectstatus']);
    //forwarding meeting tasks
    Route::patch('/forwardmeeting', [ProjectController::class, 'forwardMeeting']);
    /* retrieve meeting task -- dashboard calendar  */
    Route::post('/getmeetings', [ProjectController::class, 'rddretrieveMeetings']);
    /* mark project as completed  */
    Route::patch('/completeproject/{pid}/{type}', [ProjectController::class, 'rddprojectComplete']);
    /* Send Mail to investor -for docs*/
    Route::patch('/docnotifyinvestor', [ProjectController::class, 'rddSenddocmailtoinvestor']);
    /* Send Mail to rdd manager -for docs*/
    Route::patch('/docnotifymanager', [ProjectController::class, 'rddSenddocmailtomanager']);
    /* get Mom template for mail*/
    Route::post('/momtemplate/{project_id}/{task_id}', [ProjectController::class, 'getMomtemplate']);
    /* get property document directories - Document section */
    Route::post('/propertydocs', [ProjectController::class, 'getPropertydocs']);
    /* get project document directories - Document section */
    Route::post('/projectdocs', [ProjectController::class, 'getProjectdocs']);
    //forwarding meeting tasks
    Route::patch('/forwarddocument', [ProjectController::class, 'forwardDocument']);
    /* Retrieving file version for uploading */
    Route::post('/fileversion',[ProjectController::class, 'getFileVersion']);
    /* Adding comments - Project milestone & Investor dates  */
    Route::post('/addcomment',[ProjectController::class, 'addComments']);
    /* Adding task comments - Meeting tasks */
    Route::post('/addtaskcomment',[ProjectController::class, 'addTaskcomment']);





    //Inspection checklist controller
     /*create inspection request */
     Route::post('/project/inspectionrequest',[InspectionrequestController::class, 'createInspectionRequest']);
     /* Get selected Inspection data  */
     Route::post('/inspectionrequestdata', [InspectionrequestController::class, 'rddretrieveInspectiondata']);
     /* Reschedule Inspection request */
     Route::patch('/inspectionreschedule', [InspectionrequestController::class, 'rddperforminspectionreschedule']);
     /* Save and Send Inspection report  */
     Route::patch('/sendinspectiondata', [InspectionrequestController::class, 'rddSendinspectiondata']);
     /* Generate Inspection report  */
     Route::patch('/inspectionReport', [InspectionrequestController::class, 'rddGenerateinspectionReport']);
     /* Create Site Inspection report  */
     Route::post('/siteinspectionrequest',[InspectionrequestController::class, 'createSiteinspectionRequest']);
     /*Get selected Site Inspection data */
     Route::post('/siteinspectiondata', [InspectionrequestController::class, 'rddretrievesiteInspectiondata']);
     /* Update site Inspection report data with attachments  */
     Route::patch('/updatesiteinspectiondetails', [InspectionrequestController::class, 'rddUpdatesiteIspectiondata']);
     /* Send site Inspection report data with attachments  */
     Route::post('/sendsiteinspectionreport', [InspectionrequestController::class, 'rddSendSiteInspectionReport']);


     

     Route::get('/getprojectcontacts/{project_id}', [MembersController::class, 'getprojectcontacts']);





    //performing action on uploaded documents
    Route::post('/memdocAction', [ProjectController::class, 'performDocaction']);

    //completing tasks
    Route::post('/completetask', [ProjectController::class, 'completetask']);



    //configurations
    /*Create config*/
    Route::post('/config', [ConfigController::class, 'configAction']);
    /*Get all config for organisation*/
    Route::get('/config/{org_id}', [ConfigController::class, 'getConfig']);
     /* ------------------- */
    /*Create a Mail config*/
    Route::post('/config/mail', [ConfigController::class, 'createMailconfig']);
    /*Update a Mail config*/
    Route::patch('/config/mail', [ConfigController::class, 'updateMailconfig']);
    /*Create a Milestone config*/
    Route::post('/config/milestone', [ConfigController::class, 'createMilestoneconfig']);
    /*Update a Milestone config*/
    Route::patch('/config/milestone', [ConfigController::class, 'updateMilestoneconfig']);
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
    Route::get('/authgrpcontent/{phaseid}', [AuthorizationgrpController::class, 'getMastercontent']);
    /*Creating authorization group */
    Route::post('/authgrp', [AuthorizationgrpController::class, 'createAuthorizationgrp']);
    /* update auth group data */
    Route::patch('/updateauthgrp', [AuthorizationgrpController::class, 'editAuthorizationgrp']);


    //notification controller
    /*Get Active notifications for logged in user */
    Route::get('/notifications/{memid}/{usertype}', [NotificationController::class, 'getNotifications']);
    /*Update Active notifications for logged in user as visited*/
    Route::patch('/updatenotifications', [NotificationController::class, 'updateNotifications']);

    //PDF controller
    /* Hoc pdf generation */
    Route::post('/hoc', [PDFController::class, 'generateHOC']);
    /* fcc pdf generation */
    Route::post('/fcc', [PDFController::class, 'generateFCC']);
    /* fdr pdf generation */
    Route::post('/fdr', [PDFController::class, 'generateFDR']);
    /* Send FCC */
    Route::post('/sendfcc',[PDFController::class, 'sendFcc']);
    /* Send FDR */
    Route::post('/senddrf',[PDFController::class, 'sendDrf']);
    /* Send FCC */
    Route::post('/sendhoc',[PDFController::class, 'sendHoc']);

    //document section
    /*project docs zip API*/
    Route::post('/projectzip', [DocumentController::class, 'projectDocszip']);
    /* project phase docs zip API*/
    Route::post('/phasezip', [DocumentController::class, 'phaseDocszip']);
    /* All document docs zip API*/
    Route::post('/alldocumentszip', [DocumentController::class, 'alldocumentzip']);


    //Report Section
    Route::post('/report', [ReportController::class, 'generateReport']);
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
     /*retrieve tasks list - meeting,todo,documents*/
    Route::get('/investor/tasklist/{projectid}/{tasktype}/{memid}/{memname}',[DashboardController::class, 'retrieveinvestortasklists']);
    /* retrieve project phase wise details */
    Route::get('investor/project/phase/{projectid}/{phase_id}', [ProjectController::class, 'investorretrieveProjectPhase']);
    //get active request count -- For dashboard
    Route::post('/investor/getactivetaskscount', [ProjectController::class, 'investorgetActivetasks']);
    //project Progress count - chart section
    Route::get('/investor/getprogresscount/{pid}', [ProjectController::class, 'getProjectstatus']);
    //project pre docs path retrieval
    Route::post('/investor/getPredocspath', [ProjectController::class, 'investorgetPredocspath']);
    //project update predocs
    Route::post('/investor/updatepredoc', [ProjectController::class, 'investorcreatePredoc']);
    //get uploaded pre opening docs list
    Route::get('/investor/predocslist/{projectid}', [ProjectController::class, 'investorgetPredocslist']);
    //forwarding meeting tasks
    Route::patch('/investor/forwardmeeting', [ProjectController::class, 'forwardMeeting']);
    /*retrieve tasks approval status - meeting*/
    Route::get('/investor/meetingapprovalstatus/{projectid}/{taskid}',[ProjectController::class, 'investorretrievetaskApprovalstatus']);
    /* Investor retrieve doc history */
    Route::get('/investor/dochistory/{docid}',[ProjectController::class, 'getDochistory']);
    Route::get('/investor/dochistory/{docid}/{inv}',[ProjectController::class, 'getDochistoryinv']);

    /* Document tasks send notify to manager */
    Route::patch('/investor/docnotifymanager', [ProjectController::class, 'investorSenddocmailtomanager']);
    /* get project document directories - Document section */
    Route::post('/investor/projectdocs', [ProjectController::class, 'getProjectdocs']);
    /* Investor removing Uploaded file */
    Route::patch('/investor/removedoc', [ProjectController::class, 'investordocRemovalActions']);





     //Inspection Request controller
     /* Get requested Inspection */
     Route::post('/investor/inspectionrequestlist', [InspectionrequestController::class, 'investorinspectionList']);
     /* Get selected Inspection data  */
     Route::post('/investor/inspectionrequest', [InspectionrequestController::class, 'investorretrieveInspectiondata']);
     /* update Inspection file upload details data  */
     Route::patch('/investor/inspectionfiledetails', [InspectionrequestController::class, 'updateInspectionfiledetails']);
     /* Investor Creating Inspection request */
     Route::post('/investor/createInspectionrequest', [InspectionrequestController::class, 'investorCreateinspectionrequest']);


     //Checklist template master
    Route::get('/investor/checklisttemplate/{orgid}', [ChecklisttemplateController::class, 'retrievebyOrg']);



    //fileupload controller
    /* Upload documents */
    Route::post('/investor/uploadfile',[DocumentController::class, 'docUpload']);
    /* Multiple documents Upload  */
    Route::post('/investor/multipleuploadfile',[DocumentController::class, 'multipledocUpload']);

    //notification controller
    /*Get Active notifications for logged in user */
    Route::get('/investor/notifications/{memid}/{usertype}', [NotificationController::class, 'getNotifications']);
    /*Update Active notifications for logged in user as visited*/
    Route::patch('/investor/updatenotifications', [NotificationController::class, 'updateNotifications']);

    //document section
    /*project docs zip API*/
    Route::post('/investor/projectzip', [DocumentController::class, 'projectDocszip']);
    /* project phase docs zip API*/
    Route::post('/investor/phasezip', [DocumentController::class, 'phaseDocszip']);


});

//cache cleaning routes
Route::get('/clear-cache', function() {
    if(Artisan::call('cache:clear'))
    {
         return "Cache is cleared";
    }
});

Route::get('/config-cache', function() {
    if(Artisan::call('config:clear'))
    {
         return "Cache is cleared";
    }
});


Route::get('/router-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return "Cache is cleared";
});


Route::get('/router-clear', function() {
   $exitCode = Artisan::call('route:clear');
   return $exitCode;
});


Route::get('/gethtml',[ProjectController::class, 'gethtml']);
Route::get('/getapprovedstatus/{doc_id}/{project_id}/{user_id}',[ProjectController::class, 'getapprovedstatus']);
Route::get('/getdesignation/{user_id}',[ProjectController::class, 'getdesignation']);


Route::get('/getpath/{project_id}/{property_id}',[DocumentController::class, 'getpath']);

// Route::get('/investorgetpath/{property_id}',[DocumentController::class, 'investorgetpath']);
Route::post('downloadbob',[DocumentController::class, 'downloadbob']);

Route::get('/_invgetpath/{project_id}',[DocumentController::class, '_invgetpath']);

Route::get('/getfloor_by_property/{property_id}',[DocumentController::class, 'getfloor_by_property']);
Route::get('/getunit_by_floor/{floor_id}/{property_id}',[DocumentController::class, 'getunit_by_floor']);
Route::get('/investorgetpath/{property_id}/{floor_id}/{unit_id}',[DocumentController::class, 'investorgetpath']);
Route::post('/sendmailpdf', [InspectionrequestController::class, 'sendmailpdf']);

Route::post('/copytemplate',[TemplateController::class,'copystore']);
Route::post('/deleteemplate',[TemplateController::class,'deleteemplate']);
Route::get('/project/dochistoryinv/{docid}',[ProjectController::class, 'getDochistory']);
Route::get('/getapprovemeetings/{projecttemplateid}/{status}',[ProjectController::class, 'getapprovemeetings']);
