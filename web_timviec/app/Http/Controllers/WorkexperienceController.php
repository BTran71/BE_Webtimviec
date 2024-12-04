<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workexperience;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Yoeunes\Toastr\Facades\Toastr;
use Carbon\Carbon;

class WorkexperienceController extends Controller
{
    //hien cac kinh nghiem lam viec thuoc profile
    public function getWorkExperience(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$profile){
            return response()->json(['error'=>'Profile chưa được tạo'],401);
        }
        else{
            $getdata=Workexperience::with('profile')->where('profile_id',$profile->id)->get();
            return response()->json($getdata,200);
        }
        
    }
    public function addWorkExperience(Request $request){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $data=$request->all();
        $validator = Validator::make($data, [
            'company_name' => 'required|string|max:255',
            'job_position' => 'required|string|max:255',
            'start_time' =>'required|date|date_format:d-m-Y|after_or_equal:1970-01-01|before_or_equal:now',   
            'end_time' => 'nullable|date|date_format:d-m-Y|after:start_time|before_or_equal:now',
            'description' => 'required|string|max:255',
        ]);
        $startdate=Carbon::createFromFormat('d-m-Y',$data['start_time']);
        $enddate=Carbon::createFromFormat('d-m-Y',$data['end_time']);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $experience=new Workexperience();
        $experience->company_name=$data['company_name'];
        $experience->job_position=$data['job_position'];
        $experience->start_time=$startdate;
        $experience->end_time=$enddate;
        $experience->description=$data['description'];
        $experience->profile_id=$profile->id;
        $experience->save();
        return response()->json(['message' => 'Thêm nơi làm việc thành công'], 200);
    }
    public function deleteWorkExperience($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=Workexperience::where('id',$id)->where('profile_id',$profile->id)->first();
        if(!$info){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
    public function updateWorkExperience(Request $request,$id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $data = $request->all();
        $info=Workexperience::where('id',$id)->where('profile_id',$profile->id)->first();
        $validator = Validator::make($data, [
            'company_name' => 'required|string|max:255',
            'job_position' => 'required|string|max:255',
            'start_time' =>'required|date|date_format:d-m-Y|after_or_equal:1970-01-01|before_or_equal:now',   
            'end_time' => 'nullable|date|date_format:d-m-Y|after:start_time|before_or_equal:now',
            'description' => 'required|string|max:255',
        ]);
        $startdate=Carbon::createFromFormat('d-m-Y',$data['start_time']);
        $enddate=Carbon::createFromFormat('d-m-Y',$data['end_time']);
        if(!$info){
            return response()->json(['message'=>'Không tồn tại thông tin'],401);
        }
        else if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        else{
            $info->company_name=$data['company_name'];
            $info->job_position=$data['job_position'];
            $info->start_time=$startdate;
            $info->end_time=$enddate;
            $info->description=$data['description'];
            $info->save();
            return response()->json(['message' => 'Thêm nơi thông tin thành công'], 200);
        }
    }
}
