<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yoeunes\Toastr\Facades\Toastr;
use App\Models\Candidate;
use App\Models\Profile;
use App\Models\IT;
use App\Models\Reference;
use App\Models\Workexperience;

class ProfileController extends Controller
{
    public function addProfile(Request $request){
        $candidate = Auth::guard('candidate')->user(); // Token xác thực sẽ trả về ứng viên đăng nhập
        // Kiểm tra xem ứng viên đã có hồ sơ chưa
        $profile = $candidate->profile;
        $data=$request->all();
        $validator = Validator::make($data, [
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:profile',
            'image' => 'required|string',
            'phone_number'=>'required|regex:/^0[0-9]{9}$/',
            'gender'=>'required|',
            'skills'=>'nullable|text',
            'day_ofbirth'=>'required|',
            'salary'=>'required|',
            'experience'=>'required|',
            'address'=>'required|',
        ]);
        if(!$profile){

        }
        else{
            return response()->json(['message'=>"Đã có profile rồi không thể tạo nữa"]);
        }
    }
}
