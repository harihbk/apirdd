<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifications;
use Validator;

class NotificationController extends Controller
{
    function getNotifications($memid,$usertype)
    {
        $notifications = Notifications::where('user',$memid)->where('user_type',$usertype)->where('isDeleted',0)->where('visited_status',0)->orderBy('updated_at','DESC')->get();
        return $notifications;
    }
    function updateNotifications(Request $request)
    {
        $datas = $request->get('datas');
        $validator = Validator::make($request->all(), [ 
            'datas.*.memid' => 'required', 
            'datas.*.usertype' => 'required',
            'datas.*.notifications.*.id' => 'required', 
        ]);
        $updated_at = date('Y-m-d H:i:s');
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        for($j=0;$j<count($datas);$j++)
        {
            for($k=0;$k<count($datas[$j]['notifications']);$k++)
            {
                Notifications::where('user',$datas[$j]['memid'])->where('user_type',$datas[$j]['usertype'])->where('id',$datas[$j]['notifications'][$k]['id'])->update(
                    array(
                        "visited_status"=>1,
                        "updated_at" => $updated_at
                    )
                );
            }
        }
        return 1;
    }
}
