<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\InfoDetails;
use App\Models\IT;

class ITDetailsController extends Controller
{
    public function getITDetails(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$profile){
            return response()->json(['error'=>'Profile chưa được tạo'],401);
        }
        else{
            $getdata=InfoDetails::with('profile')->where('profile_id',$profile->id)->get();
            return response()->json($getdata,200);
        }
        
    }
    public function addITDetails(Request $request){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $data=$request->all();
        $it=IT::find($data['it_id']);
        if($profile && $it){
            $info=new InfoDetails();
            $info->profile_id=$profile->id;
            $info->it_id=$data['it_id'];
            $info->save();
            return response()->json(['message' => 'Thêm nơi thông tin thành công'], 200);
        }
        else {
            return response()->json(['message' => 'Không thêm thông tin được'], 401);
        }
       
    }
    public function deleteITDetails($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=InfoDetails::where('id',$id)->where('profile_id',$profile->id)->first();
        if(!$info){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
    public function updateITDetails(Request $request,$id){
        $data = $request->all();
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $it=IT::find($data['it_id']);
        $info=InfoDetails::where('id',$id)->where('profile_id',$profile->id)->first();
        if($profile && $it){
            $info->it_id=$data['it_id'];
            $info->save();
            return response()->json(['message' => 'Cập nhật thông tin thành công'], 200);
        }
        else {
            return response()->json(['message' => 'Không sửa thông tin được'], 401);
        }
    }
}
