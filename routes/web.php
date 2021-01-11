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
Route::post('/emailcheck', [AuthController::class, 'emailCheck']);
Route::post('/otpcheck', [AuthController::class, 'otpVerification']);
Route::post('/passwordreset', [AuthController::class, 'passwordReset']);

Route::group(['middleware' => 'auth:api'], function() {
    //Refresh Token
    Route::post('/refresh', [AuthController::class, 'getnewToken']);

    //organisations
    Route::get('/org', [OrganisationsController::class, 'index']);
    Route::post('/org', [OrganisationsController::class, 'store']);
    Route::post('/org/{id}',[OrganisationsController::class, 'update']);


    //properties
    Route::get('/properties', [PropertiesController::class, 'index']);
    Route::post('/properties', [PropertiesController::class, 'store']);
    Route::post('/properties/{id}',[PropertiesController::class, 'update']);
    Route::post('/properties/org/{id}',[PropertiesController::class, 'retrieve']);

    //units
    Route::get('/units', [UnitsController::class, 'index']);
    Route::post('/units', [UnitsController::class, 'store']);
    Route::post('/units/{id}',[UnitsController::class, 'update']);
    Route::post('/units/org/{id}/{propid}',[UnitsController::class, 'retrieveByOrg']);


    //members
    Route::get('/members', [MembersController::class, 'index']);
    Route::post('/members', [MembersController::class, 'store']);
    Route::get('/members/{id}',[MembersController::class, 'getMember']);
    Route::post('/members/{id}',[MembersController::class, 'update']);
    Route::post('/members/org/{id}',[MembersController::class, 'retrieveByOrg']);
    Route::post('/members/org/{id}/type/{tid}',[MembersController::class, 'getMemberByType']);


    //designation
    Route::get('/designation', [DesignationController::class, 'index']);
    Route::post('/designation', [DesignationController::class, 'store']);
    Route::get('/designation/{id}',[DesignationController::class, 'getDesignation']);
    Route::post('/designation/{id}',[DesignationController::class, 'update']);
    Route::post('/designation/org/{id}',[DesignationController::class, 'retrieveByOrg']);

    //memberlevel
    Route::get('/level', [LevelController::class, 'index']);
    Route::post('/level', [LevelController::class, 'store']);
    Route::post('/level/{id}',[LevelController::class, 'update']);
    Route::post('/level/org/{id}',[LevelController::class, 'retrieveByOrg']);


    //currency
    Route::get('/currency', [CurrencyController::class, 'index']);
    Route::post('/currency', [CurrencyController::class, 'store']);
    Route::post('/currency/{id}',[CurrencyController::class, 'update']);

    //company
    Route::get('/company', [CompanyController::class, 'index']);
    Route::post('/company', [CompanyController::class, 'store']);
    Route::get('/company/{id}',[CompanyController::class, 'getCompany']);
    Route::post('/company/{id}',[CompanyController::class, 'update']);
    Route::post('/company/org/{id}',[CompanyController::class, 'retrieveByOrg']);

    //Fitout Deposit Status
    Route::get('/dtstatus', [FitoutdepositController::class, 'index']);
    Route::post('/dtstatus', [FitoutdepositController::class, 'store']);
    Route::post('/dtstatus/{id}',[FitoutdepositController::class, 'update']);
    Route::post('/dtstatus/org/{id}',[FitoutdepositController::class, 'retrieveByOrg']);

    //tenant
    Route::get('/tenant', [TenantController::class, 'index']);
    Route::post('/tenant', [TenantController::class, 'store']);
    Route::get('/tenant/{id}',[TenantController::class, 'getTenant']);
    Route::post('/tenant/{id}',[TenantController::class, 'update']);
    Route::post('/tenant/org/{id}',[TenantController::class, 'retrieveByOrg']);

    //Floor
    Route::get('/floor', [FloorController::class, 'index']);
    Route::post('/floor', [FloorController::class, 'store']);
    Route::get('/floor/{id}',[FloorController::class, 'getFloor']);
    Route::post('/floor/{id}',[FloorController::class, 'update']);
    Route::post('/floor/org/{id}',[FloorController::class, 'retrieveByOrg']);
    Route::post('/floor/property/{id}',[FloorController::class, 'retrieveByProperty']);

    //Project Types
    Route::get('/prtypes', [ProjecttypeController::class, 'index']);
    Route::post('/prtypes', [ProjecttypeController::class, 'store']);
    Route::get('/prtypes/{id}',[ProjecttypeController::class, 'getFloor']);
    Route::post('/prtypes/{id}',[ProjecttypeController::class, 'update']);
    Route::post('/prtypes/org/{id}',[ProjecttypeController::class, 'retrieveByOrg']);

    //Mail Frequency
    Route::get('/freq', [MailfrequencyController::class, 'index']);
    Route::post('/freq', [MailfrequencyController::class, 'store']);
    Route::post('/freq/{id}',[MailfrequencyController::class, 'update']);
    Route::post('/freq/del/{id}',[MailfrequencyController::class, 'updateDeletion']);
    Route::post('/freq/org/{id}',[MailfrequencyController::class, 'retrieveByOrg']);

    //Work Permit
    Route::get('/permit', [WorkpermitController::class, 'index']);
    Route::post('/permit', [WorkpermitController::class, 'store']);
    Route::post('/permit/{id}',[WorkpermitController::class, 'update']);
    Route::post('/permit/del/{id}',[WorkpermitController::class, 'updateDeletion']);
    Route::post('/permit/org/{id}',[WorkpermitController::class, 'retrieveByOrg']);

    //Default Doc list
    Route::get('/doc', [DefaultdoclistController::class, 'index']);
    Route::post('/doc', [DefaultdoclistController::class, 'store']);
    Route::post('/doc/{id}',[DefaultdoclistController::class, 'update']);
    Route::post('/doc/del/{id}',[DefaultdoclistController::class, 'updateDeletion']);
    Route::post('/doc/org/{id}',[DefaultdoclistController::class, 'retrieveByOrg']);

    //Inspection root Categories
    Route::get('/inroot', [InspectionrootController::class, 'index']);
    Route::post('/inroot', [InspectionrootController::class, 'store']);
    Route::post('/inroot/{id}',[InspectionrootController::class, 'update']);
    Route::post('/inroot/del/{id}',[InspectionrootController::class, 'updateDeletion']);
    Route::post('/inroot/org/{id}',[InspectionrootController::class, 'retrieveByOrg']);

    //Inspection Checklist
    Route::post('/inchecklist', [InspectionchecklistController::class, 'store']);
    Route::post('/inchecklist/{id}',[InspectionchecklistController::class, 'update']);
    Route::post('/inchecklist/del/{id}',[InspectionchecklistController::class, 'updateDeletion']);
    Route::post('/inchecklist/org/{id}',[InspectionchecklistController::class, 'retrievebyOrg']);


    //Template Controller
    Route::post('/template', [TemplateController::class, 'store']);
    Route::post('/template/{id}', [TemplateController::class, 'update']);
    Route::post('/template/org/{id}/{pid}', [TemplateController::class, 'retrievebyTemplate']);
    Route::post('/template/phase/{id}/{tid}/', [TemplateController::class, 'getTemplatePhase']);
    
    
    


    /**Transactional Data Api's **/

    //Project Controller
    Route::post('/project', [ProjectController::class, 'store']);
    Route::get('/project/{id}', [ProjectController::class, 'retrieveByorg']);
    Route::post('/project/{id}', [ProjectController::class, 'update']);
    Route::post('/project/{id}', [ProjectController::class, 'update']);
    Route::get('/getprojects/{id}', [ProjectController::class, 'getMemberProjects']);
    


});
