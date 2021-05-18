<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ForgotPassword; 
use App\Models\Members;
use App\Models\Superuser; 
use Validator;
use Exception;
use Response;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Laravel\Passport\Client as OClient;
use Hash;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


class AuthController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request) {
        $updated_at = date('Y-m-d H:i:s'); 
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) { 
            $oClient = OClient::where('password_client', 1)->where('id',2)->first();
            $finalres = $this->getTokenAndRefreshToken($oClient, request('email'), request('password'),2);
            $members = Members::where("email",request('email'))->update( 
                array( 
                 "access_token" => $finalres->getData()->access_token,
                 "refresh_token" => $finalres->getData()->refresh_token,
                 "updated_at" => $updated_at
                 ));

            if($members>0)
            {
                $returnData = Members::join('tbl_docpath_config_master','users.mem_org_id','=','tbl_docpath_config_master.org_id')->where("users.email",request('email'))->where('tbl_docpath_config_master.isDeleted',0)->first();
                $user_response = array('token_type' =>  $finalres->getData()->token_type,
                            'access_token' => $finalres->getData()->access_token,
                            'refresh_token' => $finalres->getData()->refresh_token,
                            'user_id' => $returnData->mem_id,
                            'first_name' => $returnData->mem_name,
                            'last_name' => $returnData->mem_last_name,
                            'org_id'=> $returnData->mem_org_id,
                            'access_type' => $returnData->access_type,
                            'doc_path' => $returnData->doc_path,
                            'image_path' => $returnData->image_path
                            );
                            return response()->json(['user_info'=>$user_response], 200);
            }
            else
            {
                return response()->json(['error'=>'Login Failed'], 401); 
            }   
        } 
        else { 
            // check if crdentials belongs to super user
            if (Auth::guard('superuser-api')->attempt(['email' => request('email'), 'password' => request('password')])) { 
                $oClient = OClient::where('password_client', 1)->where('id',8)->first();
                $finalres = $this->getTokenAndRefreshToken($oClient, request('email'), request('password'),8);
                $members = Superuser::where("email",request('email'))->update( 
                    array( 
                     "access_token" => $finalres->getData()->access_token,
                     "refresh_token" => $finalres->getData()->refresh_token,
                     "updated_at" => $updated_at
                     ));
                if($members>0)
                {
                    $returnData = Superuser::where("email",request('email'))->first();
                    $user_response = array('token_type' =>  $finalres->getData()->token_type,
                                'access_token' => $finalres->getData()->access_token,
                                'refresh_token' => $finalres->getData()->refresh_token,
                                'user_id' => $returnData->id,
                                'name' => $returnData->mem_name,
                                'access_type' => $returnData->access_type,
                                );
                                return response()->json(['user_info'=>$user_response], 200);
                }
                else
                {
                    return response()->json(['error'=>'Login Failed'], 401); 
                }  
            }

            return response()->json(['error'=>'Invalid Credentials'], 401); 
        } 
    }
    public function getTokenAndRefreshToken(OClient $oClient, $email, $password,$usertype) { 
         $oClient = OClient::where('password_client', 1)->where('id',$usertype)->first();
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
        $oClient = OClient::where('password_client', 1)->where('id',2)->first();
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

       $members = Members::where("email",request('email'))->where("mem_id",request('id'))->update( 
        array( 
         "access_token" => $finalres->getData()->access_token,
         "refresh_token" => $finalres->getData()->refresh_token
         )
         );

        if($members>0)
        {
            $returnData = Members::where("email",request('email'))->where("mem_id",request('id'))->first();
            $user_response = array('token_type' =>  $finalres->getData()->token_type,
                        'access_token' => $finalres->getData()->access_token,
                        'refresh_token' => $finalres->getData()->refresh_token,
                        'user_id' => $returnData->mem_id,
                        'first_name' => $returnData->mem_name,
                        'last_name' => $returnData->mem_last_name,
                        'org_id' => $returnData->mem_org_id,
                        'access_type' => $returnData->access_type,
                        );
                        return response()->json(['user_info'=>$user_response], 200);
        }
        else
        {
            return response()->json(['error'=>'Login Failed'], 401); 
        }

   }
   public function getUser(Request $request)
   {
    return response()->json(request()->user());
   }
    public function emailCheck(Request $request)
    {
        $user = User::select('mem_id','mem_org_id','mem_name','mem_last_name','email','access_type')->where('email', $request->input('email'))->first();
        if ($user === null) {
            return response()->json(['message'=>'User does not exist'], 200);
        }
        else
        {
            $otpCheck = ForgotPassword::where('user_email', $request->input('email'))->where('user_id', $user->mem_id)->where('user_type', 1)->where('otp_status',0)->first();
            if ($otpCheck === null) {
                    $randomNum = mt_rand(100000, 999999);
                    $otpDetails = new ForgotPassword();
        
                    $otpDetails->user_id = $user->mem_id;
                    $otpDetails->user_email = $user->email;
                    $otpDetails->user_type = 1;
                    $otpDetails->otp = $randomNum;
                    $otpDetails->created_at = date('Y-m-d H:i:s');
                    $otpDetails->updated_at = date('Y-m-d H:i:s');
        
                    if($otpDetails->save()) {
                        $returnData = $otpDetails->find($otpDetails->otp_id);
                        $data = array ("message" => 'OTP Sent successfully',"data" => $returnData );
                        $response = Response::json($data,200);
                        echo json_encode($response);
                    }
            }
            else
            {
                return response()->json(['message'=>'Otp already Sent to this email -'.$otpCheck->user_email], 200);
            }
        }
    }
    public function otpVerification(Request $request)
    {
        $otpCheck = ForgotPassword::where('otp_id', $request->input('otp_id'))->where('otp', $request->input('otp'))->where('otp_status',0)->first();
        if($otpCheck == null)
        {
            return response()->json(['message'=>'Please enter valid otp'], 200);
        }
        else
        {
            //check expiration time of the OTP
            $createdTime = $otpCheck->created_at;
            $now = \Carbon\Carbon::now();
            $endDate = \Carbon\Carbon::parse($createdTime)->addMinutes(1);
            $remainingBoostHours = $now->diffInMinutes($endDate);
            if($remainingBoostHours>1)
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
                    $user = User::select('mem_id','mem_org_id','mem_name','mem_last_name','email','access_type')->where('email', $otpCheck->user_email)->where('mem_id', $otpCheck->user_id)->first();
                    return response()->json(['message'=>$user], 200);
                }
            }
        }
    }
    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'mem_id' => 'required', 
            'email' => 'required', 
            'password' => 'required', 
            'c_password' => 'required_with:password|same:password', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $members = User::where("mem_id",$request->mem_id)->where("email",$request->email)->update( 
            array( 
             "password" => Hash::make($request->password),
             "updated_at" => date('Y-m-d H:i:s'),
             ));

        if($members>0)
        {
            $data = array ("message" => 'Password has been reset successfully');
            $response = Response::json($data,200);
            echo json_encode($response); 
        }
    }
    /*Outlook response - login */
    function outlookresponse(Request $request)
    {
        $updated_at = date('Y-m-d H:i:s');
        $validator = Validator::make($request->all(), [ 
            'token_type' => 'required', 
            'access_token' => 'required',
            // 'refresh_token' => 'required'
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        
            try {
                $graph = new Graph();
                $graph->setAccessToken($request->input('access_token'));
                $user = $graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();
                $userCount = Members::where('email',$user->getMail())->first();

                //Login integration with the system
                if ($userCount!=null) { 
                    $user = $graph->createRequest('GET', '/me')->setReturnType(Model\User::class)->execute();
                    $passwordString = env('PASSWORD_STRING');
                    $passwordUpdate = Members::where("email",$user->getMail())->update( 
                        array( 
                        "password" => Hash::make($passwordString),
                        "updated_at" => $updated_at
                        ));
                    if($passwordUpdate!=0)
                    {
                        $oClient = OClient::where('password_client', 1)->where('id',2)->first();
                        $finalres = $this->getoutlookTokenAndRefreshToken($oClient, $user->getMail(), $passwordString,2);
                        $members = Members::where("email",$user->getMail())->update( 
                            array( 
                            "access_token" => $finalres->getData()->access_token,
                            "refresh_token" => $finalres->getData()->refresh_token,
                            "outlook_token" => $request->input('access_token'),
                            "outlook_refresh_token" => null,
                            "updated_at" => $updated_at
                            ));

                        if($members>0)
                        {
                            $returnData = Members::join('tbl_docpath_config_master','users.mem_org_id','=','tbl_docpath_config_master.org_id')->where("users.email",$user->getMail())->where('tbl_docpath_config_master.isDeleted',0)->first();
                            $user_response = array('token_type' =>  $finalres->getData()->token_type,
                                        'access_token' => $finalres->getData()->access_token,
                                        'refresh_token' => $finalres->getData()->refresh_token,
                                        'user_id' => $returnData->mem_id,
                                        'first_name' => $returnData->mem_name,
                                        'last_name' => $returnData->mem_last_name,
                                        'org_id'=> $returnData->mem_org_id,
                                        'access_type' => $returnData->access_type,
                                        'doc_path' => $returnData->doc_path,
                                        'image_path' => $returnData->image_path
                                        );
                                        return response()->json(['user_info'=>$user_response], 200);
                        }
                    }
                    else
                    {
                        return response()->json(['response'=>'Cannot login with Outlook'], 410);
                    }
                }
                else
                {
                    return response()->json(['error'=>'Not a valid User'], 410); 
                }
            }
            catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return redirect('/')
                    ->with('error', 'Error requesting access token')
                    ->with('errorDetail', $e->getMessage());
            }
    }
    /* generating new token and refresh token - outlook scenario */
    public function getoutlookTokenAndRefreshToken(OClient $oClient, $email, $password,$usertype) {  
        $oClient = OClient::where('password_client', 1)->where('id',$usertype)->first();
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
}