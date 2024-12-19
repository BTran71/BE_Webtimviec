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
        $info = RecruitmentNews::query()
            ->where('title', 'LIKE', "%{$request->input('title')}%")
            ->get();
        return response()->json([
            'data' => $info,
        ],200);
    }
    public function filterJobs(Request $request)
    {
            // Khởi tạo truy vấn
        $query = RecruitmentNews::query();

        // Tạo cột phụ trợ đếm số điều kiện thỏa mãn
        $query->select('*')->selectRaw('
            (
                (CASE WHEN ? IS NULL OR EXISTS (
                    SELECT 1 
                    FROM industry 
                    WHERE industry.recruitment_news_id = recruitment_news.id 
                    AND industry.industry_id = ?
                ) THEN 1 ELSE 0 END) +
                (CASE WHEN ? IS NULL OR workingmodel LIKE ? THEN 1 ELSE 0 END) +
                (CASE WHEN ? IS NULL OR salary >= ? THEN 1 ELSE 0 END) +
                (CASE WHEN ? IS NULL OR salary <= ? THEN 1 ELSE 0 END) +
                (CASE WHEN ? IS NULL OR deadline >= NOW() THEN 1 ELSE 0 END) +
                (CASE WHEN ? IS NULL OR EXISTS (
                    SELECT 1 
                    FROM workplacenews 
                    WHERE workplacenews.recruitment_news_id = recruitment_news.id 
                    AND workplacenews.workplace_id = ?
                ) THEN 1 ELSE 0 END) +
                (CASE WHEN ? IS NULL OR rank = ? THEN 1 ELSE 0 END) +
                (CASE WHEN ? IS NULL OR experience = ? THEN 1 ELSE 0 END)
            ) AS match_count
        ', [
            $request->industry_id, $request->industry_id,
            $request->workingmodel, '%' . $request->workingmodel . '%',
            $request->salary_min, $request->salary_min,
            $request->salary_max, $request->salary_max,
            $request->deadline,
            $request->workplace_id, $request->workplace_id,
            $request->rank, $request->rank,
            $request->experience, $request->experience,
        ]);

        // Lọc điều kiện như ban đầu
        if ($request->has('industry_id') && !empty($request->industry_id)) {
            $query->where('industry', function($query) use ($request) {
                $query->where('industry_id', $request->industry_id);
            });
        }
        if ($request->has('workingmodel') && !empty($request->workingmodel)) {
            $query->where('workingmodel', 'like', '%' . $request->workingmodel . '%');
        }
        if ($request->has('salary_min') && !empty($request->salary_min)) {
            $query->where('salary', '>=', $request->salary_min);
        }
        if ($request->has('salary_max') && !empty($request->salary_max)) {
            $query->where('salary', '<=', $request->salary_max);
        }
        // Chỉ lấy các tin chưa hết hạn
        $query->where('deadline', '>=', Carbon::now());

        if ($request->has('workplace_id') && !empty($request->workplace_id)) {
            $query->where('workplacenews', function($query) use ($request) {
                $query->where('workplace_id', $request->workplace_id);
            });
        }
        if ($request->has('rank') && !empty($request->rank)) {
            $query->where('rank', $request->rank);
        }
        if ($request->has('experience') && !empty($request->experience)) {
            $query->where('experience', $request->experience);
        }

        // Sắp xếp theo số điều kiện đáp ứng nhiều nhất (match_count giảm dần)
        $recruitmentNews = $query->orderByDesc('match_count')->get();

        // Trả về kết quả dạng JSON
        return response()->json($recruitmentNews, 200);
    }
    public function showActiveRecruitments(Request $request)
    {
        // Lấy tất cả các tin tuyển dụng còn hạn và employer không bị khóa
        $recruitmentNews = RecruitmentNews::whereHas('employer', function ($query) {
            // Kiểm tra employer không bị khóa
            $query->where('is_Lock', 1); // 0 là không bị khóa, có thể tùy chỉnh theo cơ sở dữ liệu của bạn
        })
            ->where('deadline', '>=', Carbon::now()) // Tin tuyển dụng còn hạn
            ->get();
        // Trả về kết quả dưới dạng JSON hoặc trả về view
        
        return response()->json($recruitmentNews,200);
    }
    public function getMatchingJobs()
    {
        // Lấy thông tin ứng viên
        $user = Auth::guard('candidate')->user();
        $profile = $user->profile;
        if (!$profile) {
            return response()->json(['message' => 'Hồ sơ của bạn chưa được cập nhật.'], 400);
        }
        // Lọc tin tuyển dụng phù hợp
        $matchingJobs = RecruitmentNews::query()->where('deadline', '>=', Carbon::now()) // Chỉ lấy tin còn hạn
        ->where(function ($query) use ($profile) {
            // So khớp theo mức lương
        $query->where('salary', '>=', $profile->salary);
            // So khớp theo kinh nghiệm
        $query->orWhere('experience', '<=', $profile->experience);
        // So khớp theo nơi làm việc
        $query->orWhereHas('workplacenews', function ($subQuery) use ($profile) {
            $subQuery->whereIn('workplace_id', $profile->workplaceDetails->pluck('workplace_id'));
        });
        // So khớp theo ngành nghề
        $query->orWhereHas('industry', function ($subQuery) use ($profile) {
            $subQuery->whereIn('industry_id', $profile->industries->pluck('industry_id'));
            });
        })->get();
        return response()->json($matchingJobs,200);
    }
    // public function getNews($id){
    //     $data=RecruitmentNews::with('employer')->where('id',$id)->where('deadline','>=',Carbon::now())->first();
    //     if(!$data){
    //         return response()->json(['error'=>'Không lấy được thông tin'],401);
    //     }
    //     return response()->json($data,200);
    // }
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
                'posteddate'=>$data['posteddate'],
            ]);
            return response()->json(['message' => 'Tin tuyển dụng đã được sửa thành công', 'data' => $news], 200);
        }
        else
            return response()->json(['error' => 'Lỗi'], 500);
    }
}
