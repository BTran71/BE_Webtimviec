<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reference;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReferenceController extends Controller
{
    public function getReference(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$profile){
            return response()->json(['message'=>'Hồ sơ chưa được tạo'],401);
        }else{
            $getdata=Reference::where('profile_id',$profile->id)->get();
            return response()->json($getdata,200);
        }
    }
    public function addReference(Request $request){
        $user=Auth::guard('candidate')->user();
        $profile=Profile::where('candidate_id',$user->id)->first();
        $data = $request->all();
        $validator = Validator::make($data, [
            'company_name' => 'required|regex:/^[^0-9]*$/|max:255',
            'name'=> 'required|regex:/^[^0-9]*$/|max:255',
            'phone_number'=> 'required|regex:/^0[0-9]{9}$/',
            'position'=> 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if(!$profile){
            return response()->json(['error' => 'Không tồn tại profile'], 401);
        }
        else if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        else{
            $reference=new Reference();
            $reference->company_name=$data['company_name'];
            $reference->name=$data['name'];
            $reference->phone_number=$data['phone_number'];
            $reference->position=$data['position'];
            $reference->profile_id=$profile->id;
            $reference->save();
            return response()->json(['message' => 'Thêm nơi thông tin thành công'], 200);
        }
    }
    public function updateReference(Request $request,$id){
        $data = $request->all();
        $reference=Reference::where('id',$id)->first();
        $validator = Validator::make($data, [
            'company_name' => 'required|regex:/^[^0-9]*$/|max:255',
            'name'=> 'required|regex:/^[^0-9]*$/|max:255',
            'phone_number'=> 'required|regex:/^0[0-9]{9}$/',
            'position'=> 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if(!$reference){
            return response()->json(['message'=>'Không tồn tại thông tin'],401);
        }
        else if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        else{
            $reference->company_name=$data['company_name'];
            $reference->name=$data['name'];
            $reference->phone_number=$data['phone_number'];
            $reference->position=$data['position'];
            $reference->save();
            return response()->json(['message' => 'Thêm nơi thông tin thành công'], 200);
        }
    }

    //xóa thông tin 
    public function deleteReference($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $info=Reference::where('id',$id)->where('profile_id',$profile->id)->first();
        if(!$info){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
}
