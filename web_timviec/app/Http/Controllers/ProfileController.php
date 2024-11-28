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
use App\Models\Academy;
use App\Models\LanguageDetails;
use App\Models\InfoDetails;
use Illuminate\Support\Facades\DB;

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
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'phone_number'=>'required|regex:/^0[0-9]{9}$/',
            'gender'=>'required|in:male,female,other',
            'skills'=>'nullable|text',
            'day_ofbirth'=>'required|date',
            'salary'=>'nullable|float|min:0',
            'experience'=>'nullable|integer|min:0',
            'address'=>'required|string|max:255',

            // Validation cho bảng liên quan
            'work_ex' => 'nullable|array',
            'work_ex.*.company_name' => 'required|string|max:255',
            'work_ex.*.job_position' => 'required|string|max:255',
            'work_ex.*.start_time' => 'required|date',
            'work_ex.*.end_time' => 'nullable|date|after_or_equal:start_time',
            'work_ex.*.description' => 'nullable|text',

            'reference' => 'nullable|array',
            'reference.*.name' => 'required|regex:/^[^0-9]*$/|max:255',
            'reference.*.company_name' => 'required|regex:/^[^0-9]*$/|max:255',
            'reference.*.phone_number'=> 'required|regex:/^0[0-9]{9}$/',
            'reference.*.position'=> 'required|regex:/^[^0-9]*$/|max:255',

            'academy' => 'nullable|array',
            'academy.*.schoolname'=>'required|regex:/^[^0-9]*$/|max:255',
            'academy.*.company_name'=>'required|regex:/^[^0-9]*$/|max:255',
            'academy.*.start_time'=> 'required|date',
            'academy.*.end_time'=> 'nullable|date|after_or_equal:start_time',

            'languageDetails' => 'nullable|array',
            'languageDetails.*.level'=>'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        else if(!$profile){
            DB::beginTransaction();
            try {
                    // Upload ảnh nếu có
                $imagePath = null;
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('profiles', 'public');
                }
                //tạo profile
                $profile=new Profile();
                $profile->fullname=$data['fullname'];
                $profile->email=$data['email'];
                $profile->image=$imagePath;
                $profile->phone_number=$data['phone_number'];
                $profile->gender=$data['gender'];
                $profile->skills=$data['skills'];
                $profile->day_ofbirth=$data['day_ofbirth'];
                $profile->salary=$data['salary'];
                $profile->salary=$data['salary'];
                $profile->experience=$data['experience'];
                $profile->address=$data['address'];
                $profile->isLock=0;
                $profile->save();

                // Tạo các bảng liên quan
                if ($request->has('work_ex')) {
                    foreach ($request->work_ex as $work) {
                        $workex=new Workexperience();
                        $workex->company_name=$work['company_name'];
                        $workex->job_position=$work['job_position'];
                        $workex->start_time=$work['start_time'];
                        $workex->end_time=$work['end_time'];
                        $workex->description=$work['description'];
                        $workex->profile_id=$profile->id;
                        $workex->save();
                        // $profile->work_ex()->create($work);
                    }
                }
                if ($request->has('reference')) {
                    foreach ($request->reference as $ref) {
                         $profile->reference()->create($ref);
                    }
                }
    
                if ($request->has('academy')) {
                    foreach ($request->academy as $academy) {
                        $profile->academy()->create($academy);
                    }
                }
    
                if ($request->has('languageDetails')) {
                    foreach ($request->languageDetails as $lang) {
                        $profile->languageDetails()->create($lang);
                    }
                }
    
                if ($request->has('information_Details')) {
                    foreach ($request->information_Details as $info) {
                        $profile->information_Details()->create($info);
                    }
                }
                if ($request->has('workplaceDetails')) {
                    foreach ($request->workplaceDetails as $info) {
                        $profile->information_Details()->create($info);
                    }
                }
                if ($request->has('industries')) {
                    foreach ($request->industries as $info) {
                        $profile->information_Details()->create($info);
                    }
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create profile',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
        else{
            return response()->json(['message'=>"Đã có profile rồi không thể tạo nữa"]);
        }
    }
}
