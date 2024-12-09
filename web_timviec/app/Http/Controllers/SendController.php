<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecruitmentNews;
use App\Models\Profile;
use App\Models\Sending;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class SendController extends Controller
{
    public function sendProfile($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$user || !$profile){
            return response()->json(['error'=>'Chưa đăng nhập hoặc chưa có hồ sơ'],401);
        }
        $date=Carbon::now()->format('Y-m-d H:i:s');
        $send=new Sending();
        $send->profile_id=$profile->id;
        $send->recruitment_news_id=$id;
        $send->senddate=$date;
        $send->save();
        return response()->json(['message'=>'Đã gửi hồ sơ'],200);
    }
    public function getSendProfile($id){
        $user=Auth::guard('employer')->user();
        $data=RecruitmentNews::with('send')->where('employer_id',$user->id)->findOrFail($id);
        return response()->json([
            'news'=>$data->title,
            'profile'=>$data->send,
        ]);
    }
}
