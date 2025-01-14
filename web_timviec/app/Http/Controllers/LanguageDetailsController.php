<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\LanguageDetails;
use App\Models\Language;

class LanguageDetailsController extends Controller
{
    public function getAllDetails(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$profile){
            return response()->json(['error'=>'Hồ sơ chưa được tạo'],401);
        }
        else{
            $getdata=LanguageDetails::where('profile_id',$profile->id)->get();
            return response()->json($getdata,202); 
        }
    }
    public function addLanguageDetails(Request $request){
        $data=$request->all();
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=Language::where('id',$data['language_id'])->first();
        if(!$info){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $details=new LanguageDetails();
            $details->profile_id=$profile->id;
            $details->language_id=$data['language_id'];
            $details->level=$data['level'];
            $details->profile_id=$profile->id;
            $details->score=$data['score'];
            $details->save();
            return response()->json(['message'=>'Thêm thành công'],200);
        }
    }
    public function updateLanguageDetails(Request $request,$id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=LanguageDetails::where('id',$id)->where('profile_id',$profile->id)->first();
        $language=Language::where('id',$request->language_id)->first();
        if($info && $language){
            $info->language_id=$request->language_id;
            $info->level=$request->level;
            $info->score=$data['score'];
            $info->save();
            return response()->json(['message'=>'Cập nhật thông tin thành công'],200);
        }
        else{
            return response()->json(['error'=>'Không tồn tại thông tin'],401);
        }
    }
    public function deleteLanguageDetails($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=LanguageDetails::where('id',$id)->where('profile_id',$profile->id)->first();
        if(!$info){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
}
