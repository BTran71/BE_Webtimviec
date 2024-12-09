<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yoeunes\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\Candidate;
use App\Models\Profile;
use App\Models\IT;
use App\Models\Reference;
use App\Models\Workexperience;
use App\Models\Academy;
use App\Models\LanguageDetails;
use App\Models\InfoDetails;
use App\Models\Workplace;
use App\Models\Industry;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function getProfile(){
        $user=Auth::guard('candidate')->user();
        $profile = Profile::with([
            'work_ex',
            'reference',
            'academy',
            'languageDetails',
            'information_Details',
            'workplaceDetails',
            'industries',
        ])->where('candidate_id', $user->id)->first();
        if ($profile && $profile->image) {
            $profile->image_url = asset('storage/' . $profile->image); // Tạo URL từ đường dẫn
        } else {
            $profile->image_url = null; // Nếu không có hình ảnh, trả về null
        }
        $profile->day_ofbirth= Carbon::parse($profile->day_ofbirth)->format('d-m-Y');
        foreach ($profile->work_ex as $item) {
            $item->start_time = Carbon::parse($item->start_time)->format('d-m-Y');
            $item->end_time = Carbon::parse($item->end_time)->format('d-m-Y');
        }
        foreach ($profile->academy as $item) {
            $item->start_time = Carbon::parse($item->start_time)->format('d-m-Y');
            $item->end_time = Carbon::parse($item->end_time)->format('d-m-Y');
        }
        if (!$profile) {
            return response()->json(['error' => 'Hồ sơ không tồn tại.'], 404);
        }
    
        return response()->json($profile, 200);
    }
    public function addProfile(Request $request){
        $candidate = Auth::guard('candidate')->user(); // Token xác thực sẽ trả về ứng viên đăng nhập
        // Kiểm tra xem ứng viên đã có hồ sơ chưa
        $profiles = $candidate->profile;
        $data=$request->all();
        $validator = Validator::make($data, [
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:profile',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'phone_number'=>'required|regex:/^0[0-9]{9}$/',
            'gender'=>'required|in:Nam,Nữ',
            'skills'=>'nullable|string',
            'day_ofbirth'=>'required|date|date_format:d-m-Y',
            'salary'=>'nullable|numeric|min:0',
            'experience'=>'nullable|string|min:0',
            'address'=>'required|string|max:255',

            // Validation cho bảng liên quan
            'work_ex' => 'nullable|array',
            'work_ex.*.company_name' => 'required|string|max:255',
            'work_ex.*.job_position' => 'required|string|max:255',
            'work_ex.*.start_time' => 'required|date|date_format:d-m-Y|after_or_equal:1970-01-01|before_or_equal:now',
            'work_ex.*.end_time' => 'nullable|date|date_format:d-m-Y|after:work_ex.*.start_time|before_or_equal:now',
            'work_ex.*.description' => 'required|string',

            'reference' => 'nullable|array',
            'reference.*.name' => 'required|regex:/^[^0-9]*$/|max:255',
            'reference.*.company_name' => 'required|regex:/^[^0-9]*$/|max:255',
            'reference.*.phone_number'=> 'required|regex:/^0[0-9]{9}$/',
            'reference.*.position'=> 'required|regex:/^[^0-9]*$/|max:255',

            'academy' => 'nullable|array',
            'academy.*.schoolname'=>'required|regex:/^[^0-9]*$/|max:255',
            'academy.*.major'=>'required|regex:/^[^0-9]*$/|max:255',
            'academy.*.degree'=>'required|regex:/^[^0-9]*$/|max:255',
            'academy.*.start_time'=> 'required|date|date_format:d-m-Y|after_or_equal:1970-01-01|before_or_equal:now',
            'academy.*.end_time'=> 'nullable|date|date_format:d-m-Y|after:academy.*.start_time|before_or_equal:now',

            'languageDetails' => 'nullable|array',
            'languageDetails.*.level'=>'required|regex:/^[^0-9]*$/|max:255',
        ]);
        $day_ofbirth = Carbon::createFromFormat('d-m-Y',$data['day_ofbirth'])->format('Y-m-d');
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        else if(!$profiles){
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
                if($data['gender']=="Nữ"){
                    $profile->gender=1;
                }
                else{
                    $profile->gender=0;
                }
                $profile->skills=$data['skills'];
                $profile->day_ofbirth=$day_ofbirth;
                $profile->salary=$data['salary'];
                $profile->experience=$data['experience'];
                $profile->address=$data['address'];
                $profile->isLock=0;
                $profile->candidate_id=$candidate->id;
                $profile->save();
                // Tạo các bảng liên quan
                if ($request->has('work_ex')) {
                   
                    foreach ($data['work_ex'] as $work) {
                        $starttime = Carbon::createFromFormat('d-m-Y', $work['start_time'])->format('Y-m-d');
                        $endtime = isset($work['end_time']) ? Carbon::createFromFormat('d-m-Y', $work['end_time'])->format('Y-m-d') : null;
                        $workex=new Workexperience();
                        $workex->company_name=$work['company_name'];
                        $workex->job_position=$work['job_position'];
                        $workex->start_time=$starttime;
                        $workex->end_time=$endtime;
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
                        $starttime = Carbon::createFromFormat('d-m-Y', $academy['start_time'])->format('Y-m-d');
                        $endtime = isset($academy['end_time']) ? Carbon::createFromFormat('d-m-Y', $academy['end_time']) : null;
                        $academy['start_time']=$starttime;
                        $academy['end_time']=$endtime;
                        $profile->academy()->create($academy);    
                    }
                }
    
                if ($request->has('languageDetails')) {
                    foreach ($request->languageDetails as $lang) {
                        $info=Language::where('id',$lang['language_id'])->first();
                        if($info){
                            $profile->languageDetails()->create($lang);
                        }
                    }
                }
    
                if ($request->has('information_Details')) {  
                    foreach ($request->information_Details as $info) {
                        $it=IT::where('id',$info['it_id'])->first();
                        if($it){
                            $profile->information_Details()->create($info);
                        }  
                    }
                }
                if ($request->has('workplaceDetails')) {
                    foreach ($request->workplaceDetails as $workplace) {
                        $info=Workplace::where('id',$workplace['workplace_id'])->first();
                        if($info){
                            $profile->workplaceDetails()->create($workplace);
                        }
                    }
                }
                if ($request->has('industries')) {
                    foreach ($request->industries as $industry) {
                        $info=Industry::where('id',$industry['industry_id'])->first();
                        if($info){
                            $profile->industries()->create($industry);
                        }
                    }
                }
                
                DB::commit();
                return response()->json(['message' => 'Tạo thành công',], 200);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create profile',
                    'error' => $e->getMessage(),
                ], 500);
            }
        }
    }
    public function updateProfile(Request $request){
        $user=Auth::guard('candidate')->user();
        $profile=Profile::where('candidate_id',$user->id)->first();
        $data=$request->all();
        $validator = Validator::make($data, [
            'fullname' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('profile', 'email')->ignore($profile->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'phone_number'=>'required|regex:/^0[0-9]{9}$/',
            'gender'=>'required|in:Nam,Nữ',
            'skills'=>'nullable|string',
            'day_ofbirth'=>'required|date|date_format:d-m-Y',
            'salary'=>'nullable|numeric|min:0',
            'experience'=>'nullable|string|min:0',
            'address'=>'required|string|max:255',
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
        }
        $day_ofbirth = Carbon::createFromFormat('d-m-Y',$data['day_ofbirth'])->format('Y-m-d');
        if($profile){
            if($validator->fails()){
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
            else{
                $profile->fullname=$data['fullname'];
                $profile->email=$data['email'];
                $profile->phone_number=$data['phone_number'];
                if($data['gender']=="Nữ"){
                    $profile->gender=1;
                }
                else{
                    $profile->gender=0;
                }
                $profile->skills=$data['skills'];
                $profile->day_ofbirth=$day_ofbirth;
                $profile->salary=$data['salary'];
                $profile->experience=$data['experience'];
                $profile->address=$data['address'];
                if ($request->hasFile('image')) {
                    // Lấy đường dẫn của ảnh cũ
                    $oldImagePath = $profile->image;
                
                    // Lưu ảnh mới
                    $imagePath = $request->file('image')->store('profiles', 'public');
                
                    // Xóa ảnh cũ nếu có
                    if ($oldImagePath) {
                        Storage::disk('public')->delete($oldImagePath);
                    }
                
                    // Cập nhật ảnh mới vào profile
                    $profile->image = $imagePath;
                }
                $profile->save();
                return response()->json(['message'=>'Cập nhật thông tin thành công'],200);
            }
        }
        else{
            return response()->json(['error'=>'Chưa tạo profile không cập nhật được'],404);
        }
    }
    public function changeLock(){
        $user = Auth::guard('candidate')->user();
        $profile=Profile::where('candidate_id',$user->id)->first();
        if($profile)
        {
            if($profile->isLock==1){
                Profile::where('candidate_id',$user->id)->where('isLock',1)->update([
                    'isLock'=> 0
                ]);
                return  response()->json(['message'=>'Cập nhật trạng thái thành công'],200);
            }
            else{
                Profile::where('candidate_id',$user->id)->where('isLock',0)->update([
                    'isLock'=> 1
                ]);
                return  response()->json(['message'=>'Cập nhật trạng thái thành công'],200);
            }
        }
        else
        {
            return  response()->json(['message'=>'Không thể cập nhật trạng thái'],400);
        }
    }
}
