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
use App\Models\RecruitmentNews;
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'phone_number'=>'required|regex:/^0[0-9]{9}$/',
            'gender'=>'required|in:Nam,Nữ',
            'skills'=>'nullable|string',
            'day_ofbirth'=>'required|date|date_format:d-m-Y',
            'salary'=>'nullable|numeric|min:0',
            'experience'=>'nullable|string|min:0',
            'address'=>'required|string',
            'rank'=>'required|string',

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
                $profile->rank=$data['rank'];
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'phone_number'=>'required|regex:/^0[0-9]{9}$/',
            'gender'=>'required|in:Nam,Nữ',
            'skills'=>'nullable|string',
            'day_ofbirth'=>'required|date|date_format:d-m-Y',
            'salary'=>'nullable|numeric|min:0',
            'experience'=>'nullable|string|min:0',
            'address'=>'required|string',
            'rank'=>'required|string',
        ]);
        $imagePath = null;
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
                $profile->rank=$data['rank'];
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

    //hiện các hồ sơ theo điểm
    public function getMatchingProfile(){
        $user=Auth::guard('employer')->user();
        if(!$user){
            return response()->json(['message'=>'Chưa đăng nhập'],401);
        }
            // Lấy tất cả các tin tuyển dụng từ nhà tuyển dụng không bị khóa
        $news = RecruitmentNews::with(['workplacenews', 'industry', 'language', 'information', 'employer'])
                    ->whereHas('employer', function ($query) {
                        $query->where('is_Lock', 1); // Nhà tuyển dụng không bị khóa
                    })
                    ->where('deadline', '>=', Carbon::now()) // Chỉ lấy tin còn hạn
                    ->where('isActive', 1) // Tin phải đang hoạt động
                    ->where('employer_id',$user->id)
                    ->get();
        $profiles = Profile::with(['workplaceDetails', 'industries', 'languageDetails', 'information_Details'])
                    ->where('isLock', 1) // Chỉ lấy profile đang hoạt động
                    ->get();            
    // Lọc các tin tuyển dụng theo tiêu chí profile
        $matchingProfiles = $profiles->filter(function ($profiles) use ($news) {
            foreach ($news as $job) {
                $count = 0;

                // So khớp các tiêu chí
                if ($job->salary >= $profiles->salary) {
                    $count++;
                }
                if ($job->workingmodel == $profiles->workingmodel) {
                    $count++;
                }
                if ($job->rank == $profiles->rank) {
                    $count++;
                }
                if ($job->workplacenews->pluck('workplace_id')->intersect($profiles->workplaceDetails->pluck('workplace_id'))->isNotEmpty()) {
                    $count++;
                }
                if ($job->industry->pluck('industry_id')->intersect($profiles->industries->pluck('industry_id'))->isNotEmpty()) {
                    $profileExperience = $profiles->industries->pluck('experience')->map(function ($experience) {
                        // Loại bỏ chữ và chuyển chuỗi thành số
                        return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                    });
                    $newsExperience = $job->industry->pluck('experience')->map(function ($experience) {
                        // Loại bỏ chữ và chuyển chuỗi thành số
                        return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                    });
                    if ($profileExperience->max() >= $newsExperience->min()) {
                        $count++;
                    }

                }
                if ($job->language->pluck('language_id')->intersect($profiles->languageDetails->pluck('language_id'))->isNotEmpty()) {
                    $count++;
                }
                if ($job->information->pluck('it_id')->intersect($profiles->information_Details->pluck('it_id'))->isNotEmpty()) {
                    $count++;
                }
                // Nếu có ít nhất 1 tiêu chí khớp thì thêm vào danh sách
            if ($count > 0) {
                return true;
            }
        }
        return false; // Không có profile nào khớp
    });
     // Tính điểm khớp và sắp xếp danh sách hồ sơ
     $list = $matchingProfiles->map(function ($profile) use ($news) {
        $profile->match_count = 0;

        foreach ($news as $job) {
            $matchCount = 0;

            if ($job->salary >= $profile->salary) $matchCount++;
            if ($job->workingmodel == $profile->workingmodel) $matchCount++;
            if ($job->rank == $profile->rank) $matchCount++;
            if ($job->workplacenews->pluck('workplace_id')->intersect($profile->workplaceDetails->pluck('workplace_id'))->isNotEmpty()) 
                $matchCount+=$job->workplacenews->sum('score')?:1;
            if ($job->industry->pluck('industry_id')->intersect($profile->industries->pluck('industry_id'))->isNotEmpty()){
                $profileExperience = $profile->industries->pluck('experience')->map(function ($experience) {
                    // Loại bỏ chữ và chuyển chuỗi thành số
                    return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                });
                $newsExperience = $job->industry->pluck('experience')->map(function ($experience) {
                    // Loại bỏ chữ và chuyển chuỗi thành số
                    return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                });
                if ($profileExperience->max() >= $newsExperience->min()) {
                    $matchCount += $job->industry->sum('score') ?: 1;
                }
            }
            if ($job->language->pluck('language_id')->intersect($profile->languageDetails->pluck('language_id'))->isNotEmpty())
                $matchCount+=$job->language->sum('score')?:1;
            if ($job->information->pluck('it_id')->intersect($profile->information_Details->pluck('it_id'))->isNotEmpty()) 
                $matchCount+=$job->information->sum('score')?:1;

            $profile->match_count += $matchCount;
        }
        $profile->image_url = $profile->image? asset('storage/' . $profile->image) : null;
        return $profile;
    })->sortByDesc('match_count');
        return response()->json($list, 200);
    }
    
}
