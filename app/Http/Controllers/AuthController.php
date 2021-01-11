<?php

namespace App\Http\Controllers;

use App\Models\User; 
use Validator;
use Exception;
use GuzzleHttp\Client;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Laravel\Passport\Client as OClient;

class AuthController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request) { 
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) { 
            $oClient = OClient::where('password_client', 1)->first();
            return $this->getTokenAndRefreshToken($oClient, request('email'), request('password'));
        } 
        else { 
            return response()->json(['error'=>'Unauthorised'], 401); 
        } 
    }
    public function getTokenAndRefreshToken(OClient $oClient, $email, $password) { 
         $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        $response = $http->request('POST', 'http://127.0.0.1:8001/oauth/token', [
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

    public function getnewToken() {
        $oClient = OClient::where('password_client', 1)->first();
       $http = new Client;
       $response = $http->request('POST', 'http://127.0.0.1:8001/oauth/token', [
           'form_params' => [
               'grant_type' => 'refresh_token',
               'refresh_token' => 'def502005c40dbffe4fab3cc7493e3ae0fb8a89c7b2c0dd73b257a46497fe9bd33c50c7c30939a60d6709cfff7229f85afc05b0ae9c5f2f2d1fc5bd9a067767ed97c0f4a8cbf799ce990292935179f50ca1f1efd0cd798560f1f85d95bf6a1b2bc12efd070b4c4527e076ad0b93516b91f45a777d217c2c4d40ad1db5cc9ba5968b0a37cd36725a8bc99e83d10055d5a5ab7d5c6fc738fd4896785e7c3bf3f1d3477739ec398f070f72a19d64649abb227b19dbfef8d0dc2c654123183dda5ea21ebca3fa54e1adff09786524e9e48143908141f9b5fc07d117c2f1a2abfce5aad11dc475e52c5105478e0a0c872d9494dd83ee8a7e1bf632dc7d2fd976bd2d2c6d060a5c4e33ef8f1af1feaef25c41cc7bf24660deefdea6df38afb94ef2cc6cbab3e91e6d09d46b982a13f821e1a79995f5cc4c119249830c5bf9f5f0014d24741eb75372e1589b952219ad1383ee64d0c78067d39f0884e7dfe8d13bff9bd61de14fd',
               'client_id' => $oClient->id,
               'client_secret' => $oClient->secret,
               'scope' => '*',
           ],
       ]);

       $result = json_decode((string) $response->getBody(), true);
       return response()->json($result, $this->successStatus);
   }
   public function getUser(Request $request)
   {
    return response()->json(request()->user());
   }
    public function emailCheck(Request $request)
    {
        $user = User::select('mem_id','mem_org_id','mem_name','mem_last_name','mem_email','access_type')->where('mem_email', $request->input('email'))->first();
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
                    $otpDetails->user_email = $user->mem_email;
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
                    $user = User::select('mem_id','mem_org_id','mem_name','mem_last_name','mem_email','access_type')->where('mem_email', $otpCheck->user_email)->where('mem_id', $otpCheck->user_id)->first();
                    return response()->json(['message'=>$user], 200);
                }
            }
        }
    }
    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [ 
            'mem_id' => 'required', 
            'mem_email' => 'required', 
            'password' => 'required', 
            'c_password' => 'required_with:password|same:password', 
        ]);

        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $members = User::where("mem_id",$request->mem_id)->where("mem_email",$request->mem_email)->update( 
            array( 
             "mem_password" => Hash::make($request->password),
             "updated_at" => date('Y-m-d H:i:s'),
             ));

        if($members>0)
        {
            $data = array ("message" => 'Password has been reset successfully');
            $response = Response::json($data,200);
            echo json_encode($response); 
        }
    }
}