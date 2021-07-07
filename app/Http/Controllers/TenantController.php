<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Tenant;
use App\Models\Projecttemplate;
use App\Models\Projectdocs;
use App\Models\Designation;
use App\Models\ForgotPassword; 
use Response;
use Validator;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; 
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Mail;

class TenantController extends Controller
{
    public $successStatus = 200;
    public function login(Request $request) { 
        if (Auth::guard('tenant-api')->attempt(['email' => request('email'), 'password' => request('password')])) { 
            $oClient = OClient::where('password_client', 1)->where('id',5)->first();
            $finalres = $this->getTokenAndRefreshToken($oClient, request('email'), request('password'));

            $members = Tenant::where("email",request('email'))->update( 
                array( 
                 "access_token" => $finalres->getData()->access_token,
                 "refresh_token" => $finalres->getData()->refresh_token)
                 );

            if($members>0)
            {
                $returnData = Tenant::leftjoin('tbl_docpath_config_master','tbl_tenant_master.org_id','=','tbl_docpath_config_master.org_id')->leftjoin('tbl_organisations_master','tbl_organisations_master.org_id','=','tbl_tenant_master.org_id')->where("email",request('email'))->first();
                $user_response = array('token_type' =>  $finalres->getData()->token_type,
                            'access_token' => $finalres->getData()->access_token,
                            'refresh_token' => $finalres->getData()->refresh_token,
                            'user_id' => $returnData->tenant_id,
                            'first_name' => $returnData->tenant_name,
                            'last_name' => $returnData->tenant_last_name,
                            'org_id' => $returnData->org_id,
                            'org_code' => $returnData->org_code,
                            'mem_type' => $returnData->tenant_type,
                            'doc_path' => $returnData->doc_path,
                            'image_path' => $returnData->image_path,
                            'invite_status' => $returnData->invite_status,
                            );
                            return response()->json(['user_info'=>$user_response], 200);
            }
            else
            {
                return response()->json(['error'=>'Login Failed'], 401); 
            }       
        } 
        else { 
            return response()->json(['error'=>'Invalid Credentials'], 401); 
        } 
    }
    public function getTokenAndRefreshToken(OClient $oClient, $email, $password) { 
        $oClient = OClient::where('password_client', 1)->where('id',5)->first();
       $http = new Client;
       $response = $http->request('POST', 'https://rdd.octasite.com/rdd_server/public/oauth/token', [
           'form_params' => [
               'grant_type' => 'password',
               'client_id' => $oClient->id,
               'client_secret' => $oClient->secret,
               'username' => $email,
               'password' => $password,
               'scope' => '*',
           ],
       ]);

       $result = json_decode((string) $response->getBody(), true);
       return response()->json($result, $this->successStatus);
   }
   public function getnewToken(Request $request) {
        $oClient = OClient::where('password_client', 1)->where('id',5)->first();
        $http = new Client;
        $response = $http->request('POST', 'http://192.168.221.30/rdd_server/public/oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->input('refresh_token'),
                'client_id' => $oClient->id,
                'client_secret' => $oClient->secret,
                'scope' => '*',
            ],
        ]);

        $result = json_decode((string) $response->getBody(), true);
        $finalres = response()->json($result, $this->successStatus);

        $members = Tenant::where("email",request('email'))->where("tenant_id",request('id'))->update( 
            array( 
            "access_token" => $finalres->getData()->access_token,
            "refresh_token" => $finalres->getData()->refresh_token
            )
            );

            if($members>0)
            {
                $returnData = Tenant::where("email",request('email'))->where("tenant_id",request('id'))->first();
                $user_response = array('token_type' =>  $finalres->getData()->token_type,
                            'access_token' => $finalres->getData()->access_token,
                            'refresh_token' => $finalres->getData()->refresh_token,
                            'user_id' => $returnData->tenant_id,
                            'first_name' => $returnData->tenant_name,
                            'last_name' => $returnData->tenant_last_name,
                            'org_id' => $returnData->org_id,
                            'tenant_type' => $returnData->tenant_type,
                            );
                            return response()->json(['user_info'=>$user_response], 200);
            }
            else
            {
                return response()->json(['error'=>'Login Failed'], 401); 
            }

        }
    function index()
    {
        $tenants = Tenant::all();
        if($tenants!=null) {
            $data = array ("message" => 'Tenants data',"data" => $tenants );
            $response = Response::json($data,200);
            echo json_encode($response); 
        } 
    }
    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'org_id' => 'required', 
            'company_id' => 'required', 
            'tenant_name' => 'required',
            'email' => 'required',
            'tenant_mobile' => 'required',
            'tenant_type' => 'required',
        ]);

        $temp_pass = $this->randomPassword();
        $tenantData = array();
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $tenantCheck = Tenant::where('email', $request->input('email'))->count();
        if($tenantCheck!=0)
        {
            return response()->json(['response'=>"Tenant already exists"], 410); 
        }

        $tenants = new Tenant();


            $tenants->org_id = $request->input('org_id');
            $tenants->company_id = $request->input('company_id');
            $tenants->tenant_name = $request->input('tenant_name');
            $tenants->tenant_last_name = $request->input('tenant_last_name');
            $tenants->email = $request->input('email');
            $tenants->tenant_mobile = $request->input('tenant_mobile');
            $tenants->tenant_designation = $request->input('tenant_designation');
            $tenants->tenant_type = $request->input('tenant_type');
            $tenants->tenant_address = $request->input('tenant_address');
            $tenants->start_date = $request->input('start_date');
            $tenants->end_date = $request->input('end_date');
            $tenants->password = Hash::make($temp_pass);
            $tenants->tenant_gender = $request->input('tenant_gender');
            $tenants->created_at = date('Y-m-d H:i:s');
            $tenants->updated_at = date('Y-m-d H:i:s');
            $tenants->created_by = $request->input('user_id');
            
            if($tenants->save()) {
                $returnData = Tenant::where('tenant_id',$tenants->tenant_id)->get();
                $tenantData = [
                    "tenant_email" => $returnData[0]['email'],
                    "tenant_name" => $returnData[0]['tenant_name'],
                    "tenant_last_name" => $returnData[0]['tenant_last_name'],
                    "tenant_mobile" => $returnData[0]['tenant_mobile'],
                    "start_date" => date("d-m-Y", strtotime($returnData[0]['start_date'])),
                    "end_date" => date("d-m-Y", strtotime($returnData[0]['end_date'])),
                    "temp_pass" => $temp_pass
                ];
                $to =  $returnData[0]['email'];
                Mail::send('emails.investorreg', $tenantData, function($message)use($tenantData,$to) {
                    $message->to($to)
                            ->subject('RDD - Investor Registration Credentials');
                    }); 
                if(Mail::failures())
                {
                    return response()->json(['response'=>"Credentials not sent to Investor"], 410);
                }
                else
                {
                    $data = array ("message" => 'Tenant added successfully',"data" => $returnData );
                    $response = Response::json($data,200);
                    echo json_encode($response);
                } 
            }  
    }
    function update(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'company_id' => 'required', 
            'tenant_name' => 'required',
            'email' => 'required',
            'tenant_mobile' => 'required',
            'tenant_type' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $tenantCheck = Tenant::where('email', $request->input('email'))->where('tenant_id','!=',$request->input('tenant_id'))->count();
        if($tenantCheck!=0)
        {
            return response()->json(['response'=>"Tenant already exists"], 410); 
        }

        $tenants = Tenant::where("tenant_id",$request->input('tenant_id'))->update( 
            array(
             "company_id" => $request->input('company_id'),
             "tenant_name" => $request->input('tenant_name'),
             "tenant_last_name" => $request->input('tenant_last_name'),
             "email" => $request->input('email'),
             "tenant_mobile" => $request->input('tenant_mobile'),
             "tenant_designation" => $request->input('tenant_designation'),
             "tenant_type" => $request->input('tenant_type'),
             "tenant_gender" => $request->input('tenant_gender'),
             "tenant_address" => $request->input('tenant_address'),
             "start_date" => $request->input('start_date'),
             "end_date" => $request->input('end_date'),
             "updated_at" => date('Y-m-d H:i:s'),
             "active_status" => $request->input('active_status'),
             ));
        
             if($tenants>0)
             {
                 $returnData = Tenant::find($request->input('tenant_id'));
                 $data = array ("message" => 'Tenant Updated successfully',"data" => $returnData );
                 $response = Response::json($data,200);
                 echo json_encode($response); 
             }
    }
    function retrieveByOrg(Request $request,$id)
    {
        $searchTerm = $request->input('searchkey');

        $present_date = date('Y-m-d');
        Tenant::where('org_id',$id)->where('end_date','<',$present_date)->update(
            array(
                "active_status" => 2
            )
        );

        $query = Tenant::leftjoin('tbl_company_master','tbl_tenant_master.company_id','=','tbl_company_master.company_id')->where("tbl_tenant_master.org_id",$id)->leftjoin('users','users.mem_id','=','tbl_tenant_master.created_by')->select('tbl_tenant_master.tenant_id','tbl_tenant_master.company_id','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name','tbl_tenant_master.email','tbl_tenant_master.tenant_mobile','tbl_tenant_master.tenant_address','tbl_tenant_master.tenant_gender','tbl_tenant_master.tenant_type','tbl_tenant_master.active_status','tbl_company_master.company_name','tbl_tenant_master.brand_name','tbl_tenant_master.start_date','tbl_tenant_master.end_date','users.mem_name','tbl_tenant_master.tenant_designation');

        if(!empty($request->input('tenant_role')))
        {
            $query->where('tbl_tenant_master.tenant_type',$request->input('tenant_role'));
        }
        if(!empty($request->input('company')))
        {
            $query->where('tbl_tenant_master.company_id',$request->input('company'));
        }

        if (!empty($request->input('searchkey')))
        {
            $query->whereLike(['tbl_tenant_master.tenant_name'], $searchTerm);
        }

        $tenants = $query->orderBy('tbl_tenant_master.tenant_name','ASC')->get();
        return $tenants; 
    }
    function getTenant(Request $request,$id)
    {
        $tenants = Tenant::where("tenant_id",$id)->get();
        echo json_encode($tenants); 
    }
    function getTasksforapproval(Request $request,$pid,$memid)
    {
        $tasks = Projecttemplate::where("project_id",$pid)->where("phase_name",$request->input('phase_name'))->orWhereRaw("find_in_set($memid,approvers)")->orWhereRaw("find_in_set($memid,attendees)")->orWhereRaw("find_in_set($memid,mem_responsible)")->get();
        echo json_encode($tasks);
    }
    function performMeetingaction(Request $request)
    {
        $tasks = Projecttemplate::where("project_id",$request->input('project_id'))->where("id",$request->input('master_id'))->where("task_id",$request->input('task_id'))->where("task_type",$request->input('task_type'))->update(
            array(
                "task_status"=>$request->input('task_status')
            )
        );

            $returnData = Projecttemplate::find($request->input('master_id'));
            $data = array ("message" => 'Action on meeting has been done',"data" => $returnData );
            $response = Response::json($data,200);
            echo json_encode($response);
    }

    function uploaddocAction(Request $request)
    {
        $uploads = $request->get('uploads');

        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');

        for($i=0;$i<count($uploads);$i++) 
        {
            for($k=0;$k<count($uploads[$i]['docs']);$k++)
            {
                $docsData[]=[
                    'project_id' =>  $uploads[$i]['project_id'],
                    'master_task_id' =>  $uploads[$i]['master_task_id'],
                    'task_type' => $uploads[$i]['task_type'],
                    'file_path' => $uploads[$i]['docs'][$k]['file_path'],
                    'doc_status' => $uploads[$i]['doc_status'],
                    'created_at' => $created_at,
                    'updated_at' => $updated_at,
                    'created_by' => $uploads[$i]['user_id']
                ];
            }
        }

        $validator1 = Validator::make($request->all(), [ 
            'uploads.*.project_id' => 'required', 
            'uploads.*.master_task_id' => 'required',
            'uploads.*.task_type' => 'required',
            'uploads.*.docs.*.file_path' => 'required',
            'uploads.*.doc_status' => 'required',
            'uploads.*.user_id' => 'required',
        ]);


        if ($validator1->fails()) { 
            return response()->json(['Uploads'=>$validator1->errors()], 401);            
        }

        if(Projectdocs::insert($docsData))
        {
            $data = array ("message" => 'Docs have been uploaded');
            $response = Response::json($data,200);
            echo json_encode($response);
        }

    }
    function getInvestorByDesignation($org_id,$designation_id)
    {
        $investors = Tenant::join('tbl_designation_master','tbl_designation_master.designation_id','=','tbl_tenant_master.tenant_designation')->where('tbl_tenant_master.tenant_designation',$designation_id)->where('tbl_tenant_master.org_id',$org_id)->select('tbl_tenant_master.tenant_id','tbl_tenant_master.tenant_designation','tbl_tenant_master.tenant_name','tbl_tenant_master.tenant_last_name')->get();
        echo json_encode($investors); 
    }
    function retrieveTenantforprojectcontact($id,$tenanttype)
    {
        $query = Tenant::select('tenant_id','company_id','tenant_name','tenant_last_name','tbl_tenant_master.email','tenant_mobile','tenant_address','tenant_gender','tenant_type','active_status','brand_name','tenant_designation')->where('tenant_type',$tenanttype)->where('active_status',1)->orderBy('tbl_tenant_master.tenant_name', 'ASC')->get();
        return $query;
    }
    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array();
        $alphaLength = strlen($alphabet) - 1;
        for ($i = 0; $i < 6; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); 
    }
    /* Investor checking email for otp generation */
    public function emailCheck(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $user = Tenant::select('tenant_id','org_id','tenant_name','tenant_last_name','email')->where('email', $request->input('email'))->first();
        if ($user === null) {
            return response()->json(['message'=>'User does not exist'], 410);
        }
        else
        {
            $otpCheck = ForgotPassword::where('user_email', $request->input('email'))->where('user_id', $user->tenant_id)->where('user_type', 2)->where('otp_status',0)->first();
            if ($otpCheck === null) {
                    $randomNum = mt_rand(100000, 999999);
                    $otpDetails = new ForgotPassword();
        
                    $otpDetails->user_id = $user->tenant_id;
                    $otpDetails->user_email = $user->email;
                    $otpDetails->user_type = 2;
                    $otpDetails->otp = $randomNum;
                    $otpDetails->created_at = date('Y-m-d H:i:s');
                    $otpDetails->updated_at = date('Y-m-d H:i:s');

                    $data = array();
                    $data = [
                        "tenant_name" => $user->tenant_name,
                        "tenant_last_name" => $user->tenant_last_name,
                        "otp" => $randomNum,
                        "email" => $user->email
                    ];
        
                    if($otpDetails->save()) {
                        Mail::send('emails.investorotp', $data, function($message)use($data) {
                            $message->to($data['email'])
                                    ->subject('RDD -  Investor Password Reset');
                            }); 
                        if(Mail::failures())
                        {
                            return response()->json(['response'=>"Otp not sent to Investor"], 410);
                        }
                        else
                        {
                            $returnData = $otpDetails->find($otpDetails->otp_id);
                            $data = array ("message" => 'OTP Sent successfully',"data" => $returnData );
                            $response = Response::json($data,200);
                            echo json_encode($response);  
                        } 
                    }
            }
            else
            {
                $updateExistingotp = ForgotPassword::where('user_email', $request->input('email'))->where('user_id', $user->tenant_id)->where('user_type', 2)->where('otp_status',0)->update(
                    array(
                        "otp_status"=> 2,
                        "updated_at" => date('Y-m-d H:i:s')
                    )
                );
                if($updateExistingotp>0)
                {
                    $randomNum = mt_rand(100000, 999999);
                    $otpDetails = new ForgotPassword();
        
                    $otpDetails->user_id = $user->tenant_id;
                    $otpDetails->user_email = $user->email;
                    $otpDetails->user_type = 2;
                    $otpDetails->otp = $randomNum;
                    $otpDetails->created_at = date('Y-m-d H:i:s');
                    $otpDetails->updated_at = date('Y-m-d H:i:s');

                    $data = array();
                    $data = [
                        "tenant_name" => $user->tenant_name,
                        "tenant_last_name" => $user->tenant_last_name,
                        "otp" => $randomNum,
                        "email" => $user->email
                    ];
        
                    if($otpDetails->save()) {
                        Mail::send('emails.investorotp', $data, function($message)use($data) {
                            $message->to($data['email'])
                                    ->subject('RDD -  Investor Password Reset');
                            }); 
                        if(Mail::failures())
                        {
                            return response()->json(['response'=>"Otp not sent to Investor"], 410);
                        }
                        else
                        {
                            $returnData = $otpDetails->find($otpDetails->otp_id);
                            $data = array ("message" => 'OTP Sent successfully',"data" => $returnData );
                            $response = Response::json($data,200);
                            echo json_encode($response);  
                        } 
                    }
                }  
                else
                {
                    return response()->json(['response'=>"Not able to Sent Otp"], 410);
                }
            }
        }
    }
     /* Investor OTP Verification   */
     public function otpVerification(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'otp' => 'required'
         ]);
 
         if ($validator->fails()) { 
             return response()->json(['error'=>$validator->errors()], 401);            
         }


         $otpCheck = ForgotPassword::where('otp', $request->input('otp'))->where('otp_status',0)->first();
         if($otpCheck == null)
         {
             return response()->json(['message'=>'Please enter valid otp'], 200);
         }
         else
         {
             //check expiration time of the OTP
             $createdTime = $otpCheck->created_at;
             $now = \Carbon\Carbon::now();
             $endDate = \Carbon\Carbon::parse($createdTime)->addMinutes(10);
             $remainingBoostHours = $now->diffInMinutes($endDate);
             if($remainingBoostHours>10)
             {
                 $updateOtp = ForgotPassword::where("otp_id",$otpCheck->otp_id)->where('otp_status',0)->update( 
                     array( 
                     "otp_status" => 2,
                     "updated_at" => date('Y-m-d H:i:s')
                     ));
                 
                 if($updateOtp>0)
                 {
                     return response()->json(['message'=>'Otp Expired'], 200);
                 }
             }
             else
             {
                 //update otp status
                 $updateOtp = ForgotPassword::where("otp_id",$otpCheck->otp_id)->where('otp_status',0)->update( 
                     array( 
                     "otp_status" => 1,
                     "updated_at" => date('Y-m-d H:i:s')
                     ));
                 
                 if($updateOtp>0)
                 {
                     $user = Tenant::select('tenant_id','org_id','tenant_name','tenant_last_name','email')->where('email', $otpCheck->user_email)->where('tenant_id', $otpCheck->user_id)->first();
                     return response()->json(['message'=>'Otp Verfied Successfully','message'=>$user], 200);
                 }
             }
         }
     }
    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'user_id' => 'required', 
            'password' => 'required', 
            'type' => 'required'
            // 'c_password' => 'required_with:password|same:password', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        if($request->input('type')==1)
        {
            $members = Tenant::where("tenant_id",$request->input('user_id'))->update( 
                array( 
                "password" => Hash::make($request->input('password')),
                "invite_status"=>2,
                "updated_at" => date('Y-m-d H:i:s'),
                ));
        }
        if($request->input('type')==2)
        {
            $members = Tenant::where("tenant_id",$request->input('user_id'))->update( 
                array( 
                "password" => Hash::make($request->input('password')),
                "updated_at" => date('Y-m-d H:i:s'),
                ));
        }

        if($members>0)
        {
            $user = Tenant::select('tenant_id','org_id','tenant_name','tenant_last_name','email')->where('tenant_id', $request->input('user_id'))->first();
            return response()->json(['response'=>'Password has been Reset','user_details'=>$user], 200); 
        }
    }
}
