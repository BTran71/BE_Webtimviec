<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Academy;

class AcademyController extends Controller
{
    public function getAcademy(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$profile){
            return response()->json(['error'=>'Profile chưa được tạo'],401);
        }
        else{
            $getdata=Academy::where('profile_id',$profile->id)->get();
            return response()->json($getdata,200);
        }
        
    }
    public function addAcademy(Request $request){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $data=$request->all();
        $validator = Validator::make($data, [
            'schoolname'=>'required|regex:/^[^0-9]*$/|max:255',
            'major'=>'required|regex:/^[^0-9]*$/|max:255',
            'degree'=>'required|regex:/^[^0-9]*$/|max:255',
            'start_time'=> 'required|date|date_format:d-m-Y|after_or_equal:1970-01-01|before_or_equal:now',
            'end_time'=> 'nullable|date|date_format:d-m-Y|after:start_time|before_or_equal:now',
        ]);
        $startdate=Carbon::createFromFormat('d-m-Y',$data['start_time']);
        $enddate=Carbon::createFromFormat('d-m-Y',$data['end_time']);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $academy=new Academy();
        $academy->schoolname=$data['schoolname'];
        $academy->major=$data['major'];
        $academy->degree=$data['degree'];
        $academy->start_time=$startdate;
        $academy->end_time=$enddate;
        $academy->profile_id=$profile->id;
        $academy->save();
        return response()->json(['message' => 'Thêm nơi thông tin thành công'], 200);
    }
    public function deleteAcademy($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=Academy::where('id',$id)->where('profile_id',$profile->id)->first();
        if(!$info){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
    public function updateAcademy(Request $request,$id){
        $data = $request->all();
        $academy=Academy::where('id',$id)->first();
        $validator = Validator::make($data, [
            'schoolname'=>'required|regex:/^[^0-9]*$/|max:255',
            'major'=>'required|regex:/^[^0-9]*$/|max:255',
            'degree'=>'required|regex:/^[^0-9]*$/|max:255',
            'start_time'=> 'required|date|date_format:d-m-Y|after_or_equal:1970-01-01|before_or_equal:now',
            'end_time'=> 'nullable|date|date_format:d-m-Y|after:start_time|before_or_equal:now',
        ]);
        $startdate=Carbon::createFromFormat('d-m-Y',$data['start_time']);
        $enddate=Carbon::createFromFormat('d-m-Y',$data['end_time']);
        if(!$academy){
            return response()->json(['message'=>'Không tồn tại thông tin'],401);
        }
        else if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        else{
            $academy->schoolname=$data['schoolname'];
            $academy->major=$data['major'];
            $academy->degree=$data['degree'];
            $academy->start_time=$startdate;
            $academy->end_time=$enddate;
            $academy->save();
            return response()->json(['message' => 'Thêm nơi thông tin thành công'], 200);
        }
    }
}
