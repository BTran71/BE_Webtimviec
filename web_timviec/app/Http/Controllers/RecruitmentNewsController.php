<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yoeunes\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\Employer;
use App\Models\RecruitmentNews;
use App\Models\WorkplaceNews;
use Illuminate\Support\Facades\Log;
use App\Models\Workplace;
use App\Models\Industry;
use App\Models\Language;
use App\Models\IT;
use App\Models\InfoNews;
use App\Models\LanguageNews;
use App\Models\IndustryNews;
use App\Mail\ApplicationStatusMail;
use App\Models\Sending;
use App\Models\Profile;


class RecruitmentNewsController extends Controller
{
    public function getNews($id){
        $news = RecruitmentNews::with([
            'workplacenews',
            'industry',
            'language',
            'information',
            'employer',
        ])->where('id', $id)->first();
        $news->deadline= Carbon::parse($news->deadline)->format('d-m-Y');
        if ($news->employer && $news->employer->image) {
            // Tạo URL từ đường dẫn của hình ảnh nhà tuyển dụng
            $news->image_url = asset('storage/' . $news->employer->image); 
        } else {
            // Nếu không có hình ảnh, gán giá trị null
            $news->image_url = null;
        }
        return response()->json($news,200);
    }
    public function addRecruitmentNews(Request $request){
        
        // $invoice = Invoice::where('employer_id', $user->id)
        //         ->where('status', 'paid')
        //         ->latest()
        //         ->first();

        // if (!$invoice) {
        //     return response()->json(['error' => 'Chưa thực hiện thanh toán'], 400);
        // }

        // Kiểm tra nếu thanh toán thành công
        // if ($invoice->status !== 'paid') {
        //     return response()->json(['error' => 'Thanh toán chưa thành công'], 400);
        // }
        $user=Auth::guard('employer')->user();
        $data=$request->all();
        $validator=Validator::make($data,[
            'title'=>'required|regex:/^[^0-9]*$/',
            'describe'=>'required|regex:/^[^0-9]*$/',
            'posteddate'=>'required|date|date_format:d-m-Y',
            'benefit'=>'required|string',
            'salary'=>'required|numeric',
            'deadline'=>'required|date|date_format:d-m-Y|after:posteddate',
            'experience'=>'required|string',
            'skills'=>'required|string',
            'quantity'=>'required|numeric',
            'workingmodel'=>'required|string',
            'qualifications'=>'required|string',
            'requirements'=>'required|string',
            'rank'=>'required|string',
            'workplacenews'=>'required|array',
            'workplacenews.*.homeaddress'=>'required|string',
            'industry'=>'required|array',
        ]);
        $data['posteddate']=Carbon::now()->format('Y-m-d');
        $deadline=Carbon::createFromFormat('d-m-Y',$data['deadline'])->format('Y-m-d');
        if($validator->failed()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        else{
            DB::beginTransaction();
            try {
                $news=new RecruitmentNews();
                $news->title=$data['title'];
                $news->describe=$data['describe'];
                $news->posteddate=$data['posteddate'];
                $news->benefit=$data['benefit'];
                $news->salary=$data['salary'];
                $news->deadline=$deadline;
                $news->experience=$data['experience'];
                $news->skills=$data['skills'];
                $news->quantity=$data['quantity'];
                $news->workingmodel=$data['workingmodel'];
                $news->qualifications=$data['qualifications'];
                $news->requirements=$data['requirements'];
                $news->rank=$data['rank'];
                $news->employer_id = $user->id;
                $news->save();
               
                foreach ($request->workplacenews as $workplace) {
                    $info=Workplace::where('id',$workplace['workplace_id'])->first();
                    if($info){
                        $news->workplacenews()->create($workplace);
                    }
                }
                foreach ($request->industry as $industry) {
                    $info=Industry::where('id',$industry['industry_id'])->first();
                    if($info){
                        $news->industry()->create($industry);
                    }
                }
                if ($request->has('language')) {
                    foreach ($request->language as $language) {
                        $info=Language::where('id',$language['language_id'])->first();
                        if($info){
                            $news->language()->create($language);
                        }
                    }
                }
                if ($request->has('information')) {
                    foreach ($request->information as $information) {
                        $info=IT::where('id',$information['it_id'])->first();
                        if($info){
                            $news->information()->create($information);
                        }
                    }
                }
                DB::commit();
                return response()->json(['message' => 'Tin tuyển dụng đã được tạo thành công', 'data' => $news], 201);
            } catch (\Throwable $th) {
                DB::rollBack(); // Rollback giao dịch nếu có lỗi
                // Log lỗi nếu cần
                Log::error('Error creating recruitment news: ' . $th->getMessage());
                // Thực hiện xử lý lỗi hoặc trả về thông báo
                return response()->json(['error' => $th->getMessage()], 500);
            }
        }
    }
    public function searchNews(Request $request){
        // Tìm kiếm sản phẩm theo tên hoặc mô tả
        $query = RecruitmentNews::query()
            ->whereHas('employer', function($query) {
                $query->where('is_Lock', 1);
            })
            ->where('deadline', '>=', Carbon::now())
            ->where('isActive',1)
            ->where('title', 'like', '%' . $request->title . '%')
            ->get();
        return response()->json([
            'data' => $query,
        ],200);
    }
    public function filterJobs(Request $request)
    {
        $query = RecruitmentNews::query();

        // Lọc theo ngành nghề (industry)
        $query->whereHas('employer', function($query) use ($request) {
            $query->where('is_Lock',1);
        });
        // Lọc theo nơi làm việc (working model)
        if ($request->has('workingmodel') && !empty($request->workingmodel)) {
            $query->where('workingmodel', $request->workingmodel);
        }
        if ($request->has('qualifications') && !empty($request->qualifications)) {
            $query->where('qualifications', $request->qualifications);
        }
        if ($request->has('rank') && !empty($request->rank)) {
            $query->where('rank', $request->rank);
        }
        if ($request->has('experience') && !empty($request->experience)) {
            $query->where('experience', $request->experience);
        }
        // Lọc theo mức lương (salary)
        if ($request->has('salary_min') && !empty($request->salary_min)) {
            $query->where('salary', '>=', $request->salary_min);
        }   
        if ($request->has('salary_max') && !empty($request->salary_max)) {
            $query->where('salary', '<=', $request->salary_max);
        }

        // Lọc theo hạn nộp đơn (deadline) - chỉ lọc những tin còn hạn         
        $query->where('deadline', '>=', Carbon::now());
        $query->where('isActive',1);
        // Lọc theo workplace_id trong bảng workplacenews
        if ($request->has('workplace_id') && !empty($request->workplace_id)) {
            $query->whereHas('workplacenews', function($query) use ($request) {
                $query->where('workplace_id', $request->workplace_id);
            });
        }
        if ($request->has('industry_id') && !empty($request->industry_id)) {
            $query->whereHas('industry', function($query) use ($request) {
                $query->where('industry_id', $request->industry_id);
            });
        }
        // Lấy kết quả
        $recruitmentNews = $query->get();

        // Trả về kết quả dưới dạng JSON (nếu làm API)
        return response()->json($recruitmentNews, 200);
    }
    public function getMatchingJobs()
    {
        $user = Auth::guard('candidate')->user();
        
        if($user){
            $profile = $user->profile;
            if (!$profile) {
                return $this->getAllJobs();
            }
            // Lọc tin tuyển dụng phù hợp
            $matchingJobs = RecruitmentNews::with(['workplacenews','industry','language','information','employer'])
                ->where('deadline', '>=', Carbon::now()) // Chỉ lấy tin còn hạn
                ->where('isActive',1)
                // Lọc thêm các tin tuyển dụng của nhà tuyển dụng không bị khóa
                ->whereHas('employer', function ($query) {
                    $query->where('is_Lock', 1);  // Kiểm tra nhà tuyển dụng không bị khóa
                })
                ->get();
            // Tính số lượng khớp cho mỗi tin tuyển dụng
            $matchingJobs = $matchingJobs->map(function ($job) use ($profile){
                $matchCount = 0;
                
                // So khớp theo mức lương
                if ($job->salary >= $profile->salary) {
                    $matchCount++;
                } 
                // So khớp theo nơi làm việc
                if ($job->workingmodel == $profile->workingmodel) {
                    $matchCount++;
                }
                // So khớp theo cấp bậc
                if ($job->rank == $profile->rank) {
                    $matchCount++;
                }
                // So khớp theo nơi làm việc
                if ($job->workplacenews->pluck('workplace_id')->intersect($profile->workplaceDetails->pluck('workplace_id'))->isNotEmpty()) {
                    $matchCount+=$profile->workplaceDetails->sum('score')?:1;
                }

                // So khớp theo ngành nghề
                if ($job->industry->pluck('industry_id')->intersect($profile->industries->pluck('industry_id'))->isNotEmpty()) {
                    
                    $profileExperience = $profile->industries->pluck('experience')->map(function ($experience) {
                        // Loại bỏ chữ và chuyển chuỗi thành số
                        return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                    });
                    $newsExperience = $job->industry->pluck('experience')->map(function ($experience) {
                        // Loại bỏ chữ và chuyển chuỗi thành số
                        return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                    });
                    if ($profileExperience->max() >= $newsExperience->min()) {
                        $matchCount+=$profile->industries->sum('score')?:1;
                    }
                }
                if ($job->language->pluck('language_id')->intersect($profile->languageDetails->pluck('language_id'))->isNotEmpty()) {
                    $matchCount+=$profile->languageDetails->sum('score')?:1;
                }
                if ($job->information->pluck('it_id')->intersect($profile->information_Details->pluck('it_id'))->isNotEmpty()) {
                    $matchCount+=$profile->information_Details->sum('score')?:1;
                }
                $job->match_count = $matchCount; 
               
                $job->image_url = $job->employer && $job->employer->image ? asset('storage/' . $job->employer->image) : null;
                return $job;
            });
            // Sắp xếp theo số lượng khớp từ cao xuống thấp
            $matchingJobs = $matchingJobs->sortByDesc('match_count');
            // Trả về kết quả dưới dạng JSON
            
            return response()->json($matchingJobs, 200);
        }
        
        else{
            return $this->getAllJobs();
        }
        
    }
    private function getAllJobs()
    {
        $allJobs = RecruitmentNews::with('employer')
            ->where('deadline', '>=', Carbon::now())
            ->where('isActive',1)
            ->whereHas('employer', function ($query) {
                $query->where('is_Lock', 1);
            })
            ->get()
            ->map(function ($job) {
                $job->image_url = $job->employer && $job->employer->image ? asset('storage/' . $job->employer->image) : null;
                return $job;
            });

        return response()->json($allJobs, 200);
    }
    public function updateNews(Request $request,$id){
        $user = Auth::guard('employer')->user();
        $data = $request->all();
        $news = RecruitmentNews::where('id', $id)->where('employer_id', $user->id)->first();
        $industry=IndustryNews::where('recruitment_news_id',$id)->first();
        $info=InfoNews::where('recruitment_news_id',$id)->first();
        $workplace=WorkplaceNews::where('recruitment_news_id',$id)->first();
        $language=LanguageNews::where('recruitment_news_id',$id)->first();
        if (!$news) {
            return response()->json(['error' => 'Tin tuyển dụng không tồn tại hoặc không thuộc về bạn'], 404);
        }

        $validator = Validator::make($data, [
            'title' => 'required|regex:/^[^0-9]*$/',
            'describe' => 'required|regex:/^[^0-9]*$/',
            'benefit' => 'required|string',
            'salary' => 'required|numeric',
            'deadline' => 'required|date|date_format:d-m-Y|after:posteddate',
            'experience' => 'required|string',
            'skills' => 'required|string',
            'quantity' => 'required|numeric',
            'workingmodel' => 'required|string',
            'qualifications' => 'required|string',
            'requirements' => 'required|string',
            'rank' => 'required|string',
        ]);

        $data['posteddate'] = Carbon::now()->format('Y-m-d');
        $data['deadline'] = Carbon::createFromFormat('d-m-Y', $data['deadline'])->format('Y-m-d');

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        if($news){
            // Cập nhật các trường của `news`
            $news->update([
                'title' => $data['title'],
                'describe' => $data['describe'],
                'posteddate' => $data['posteddate'],
                'benefit' => $data['benefit'],
                'salary' => $data['salary'],
                'deadline' => $data['deadline'],
                'experience' => $data['experience'],
                'skills' => $data['skills'],
                'quantity' => $data['quantity'],
                'workingmodel' => $data['workingmodel'],
                'qualifications' => $data['qualifications'],
                'requirements' => $data['requirements'],
                'rank' => $data['rank'],
            ]);
            return response()->json(['message' => 'Tin tuyển dụng đã được sửa thành công', 'data' => $news], 200);
        }
        else
            return response()->json(['error' => 'Lỗi'], 500);
    }
    public function changeActive($newsid=null){
        $user=Auth::guard('employer')->user();
        $now = Carbon::now();
        // Dò qua tất cả các bản ghi và cập nhật
        if($newsid==null){
            RecruitmentNews::where('deadline', '<', $now)
                ->update(['isActive' => 0]);
            return response()->json(['message'=>'Đã cập nhật thành công'],200);
        }
        if($user && $newsid)
        {
            $news=RecruitmentNews::where('id',$newsid)->first();
            if($news->isActive==1){
                RecruitmentNews::where('id',$newsid)->where('isActive',1)->update([
                    'isActive'=> 0
                ]);
                return  response()->json(['message'=>'cập nhật trạng thái thành công'],200);
            }
            else{
                RecruitmentNews::where('id',$newsid)->where('isActive',0)->update([
                    'isActive'=> 1
                ]);
                return  response()->json(['message'=>'cập nhật trạng thái thành công'],200);
            }
        }
        else
        {
            return  response()->json(['message'=>'Không thể cập nhật trạng thái'],400);
        }
    }
    public function sortProfile($newsid){
        $send=Sending::where('recruitment_news_id',$newsid)->get();
        $news=RecruitmentNews::with(['workplacenews','industry','language','information'])->where('id',$newsid)->first();
       
        $matching = $send->map(function ($sending) use ($news) {
            $profile = Profile::whereIn('id', $sending->pluck('profile_id'))
            ->with(['workplaceDetails', 'industries', 'languageDetails', 'information_Details','academy'])
            ->first();
            $matchCount = 0;
                    
            // So khớp theo mức lương
            if ($profile->salary >= $news->salary) {
                $matchCount++;
            } 
            //so sánh academy
            if($profile->academy!=null){
                foreach($profile->academy as $academies){
                    if (str_contains($academies->degree, $news->qualifications)) {
                        $matchCount++;
                        break;
                    }
                }
            }
            // So khớp theo nơi làm việc
            if ($profile->workingmodel == $news->workingmodel) {
                $matchCount++;
            }
            // So khớp theo cấp bậc
            if ($profile->rank == $news->rank) {
                $matchCount++;
            }
            // So khớp theo nơi làm việc
            if ($profile->workplaceDetails->pluck('workplace_id')->intersect($news->workplacenews->pluck('workplace_id'))->isNotEmpty()) {
                $matchCount+=$news->workplacenews->sum('score')?:1;
            }
    
            // So khớp theo ngành nghề
            if ($profile->industries->pluck('industry_id')->intersect($news->industry->pluck('industry_id'))->isNotEmpty()) {
                $profileExperience = $profile->industries->pluck('experience')->map(function ($experience) {
                    // Loại bỏ chữ và chuyển chuỗi thành số
                    return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                });
                $newsExperience = $news->industry->pluck('experience')->map(function ($experience) {
                    // Loại bỏ chữ và chuyển chuỗi thành số
                    return (int) filter_var($experience, FILTER_SANITIZE_NUMBER_INT);
                });
                if ($profileExperience->max() >= $newsExperience->min()) {
                    $matchCount += $news->industry->sum('score') ?: 1;
                }
            }
            if ($profile->languageDetails->pluck('language_id')->intersect($news->language->pluck('language_id'))->isNotEmpty()) {
                $matchCount+=$news->language->sum('score')?:1;
            }
            if ($profile->information_Details->pluck('it_id')->intersect($news->information->pluck('it_id'))->isNotEmpty()) {
                $matchCount+=$news->information->sum('score')?:1;
            }
           // Gắn match_count vào Sending
            $sending->match_count = $matchCount;
            return $sending;
        });
                // Sắp xếp theo số lượng khớp từ cao xuống thấp
        $sort = $matching->sortByDesc('match_count');
            // Trả về kết quả dưới dạng JSON     
        return response()->json($sort->values(), 200);
    }
}
