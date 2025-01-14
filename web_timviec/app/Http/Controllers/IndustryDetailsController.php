<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Industry_Profile;
use App\Models\Industry;

class IndustryDetailsController extends Controller
{
    public function getIndustryProfile(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$profile){
            return response()->json(['error'=>'Profile chưa được tạo'],401);
        }
        else{
            $getdata=Industry_Profile::where('profile_id',$profile->id)->get();
            return response()->json($getdata,200);
        }
        
    }
    public function addIndustryProfile(Request $request){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $data=$request->all();
        $industry=Industry::find($data['industry_id']);
        if($profile && $industry){
            $info=new Industry_Profile();
            $info->profile_id=$profile->id;
            $info->industry_id=$data['industry_id'];
            $info->experience=$data['experience'];
            $info->score=$data['score'];
            $info->save();
            return response()->json(['message' => 'Thêm nơi thông tin thành công'], 200);
        }
        else {
            return response()->json(['message' => 'Không thêm thông tin được'], 401);
        }
       
    }
    public function deleteIndustryProfile($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=Industry_Profile::where('id',$id)->where('profile_id',$profile->id)->first();
        if(!$info){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
    public function updateIndustryProfile(Request $request,$id){
        $data = $request->all();
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $industry=Industry::find($data['industry_id']);
        $info=Industry_Profile::where('id',$id)->where('profile_id',$profile->id)->first();
        if($profile && $industry){
            $info->industry_id=$data['industry_id'];
            $info->experience=$data['experience'];
            $info->score=$data['score'];
            $info->save();
            return response()->json(['message' => 'Cập nhật thông tin thành công'], 200);
        }
        else {
            return response()->json(['message' => 'Không sửa thông tin được'], 401);
        }
    }
}
