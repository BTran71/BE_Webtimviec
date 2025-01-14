<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Workplace_Profile;
use App\Models\Workplace;

class WorkplaceDetailsController extends Controller
{
    public function getWorkplaceDetails(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$profile){
            return response()->json(['error'=>'Profile chưa được tạo'],401);
        }
        else{
            $getdata=Workplace_Profile::where('profile_id',$profile->id)->get();
            return response()->json($getdata,200);
        }
        
    }
    public function addWorkplaceDetails(Request $request){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $data=$request->all();
        $info=Workplace::find($data['workplace_id']);
        if($profile && $info){
            $details=new Workplace_Profile();
            $details->profile_id=$profile->id;
            $details->workplace_id=$data['workplace_id'];
            $details->score=$data['score'];
            $details->save();
            return response()->json(['message' => 'Thêm thông tin thành công'], 200);
        }
        else{
            return response()->json(['error'=>'Không thêm được'],403);
        }
        
    }
    public function deleteWorkplaceDetails($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=Workplace_Profile::where('id',$id)->where('profile_id',$profile->id)->first();
        if(!$info){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
    public function updateWorkplaceDetails(Request $request,$id){
        $data = $request->all();
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $details=Workplace_Profile::where('id',$id)->where('profile_id',$profile->id)->first();
        $info=Workplace::find($data['workplace_id']);
        if($profile && $info){
            $details->workplace_id=$data['workplace_id'];
            $details->score=$data['score'];
            $details->save();
            return response()->json(['message' => 'Cập nhật thông tin thành công'], 200);
        }
        else{
            return response()->json(['error'=>'Không sửa được'],403);
        }
    }
}
