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

class RecruitmentNewsController extends Controller
{
    public function addRecruitmentNews(Request $request){
        $user=Auth::guard('employer')->user();
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
        $data=$request->all();
        $validator=Validator::make($data,[
            'title'=>'required|regex:/^[^0-9]*$/|max:255',
            'describe'=>'required|regex:/^[^0-9]*$/|max:255',
            'posteddate'=>'required|date|date_format:d-m-Y',
            'benefit'=>'required|string|max:255',
            'salary'=>'required|numeric',
            'deadline'=>'required|date|date_format:d-m-Y|after:posteddate',
            'experience'=>'required|string|max:255',
            'skills'=>'required|string',
            'quantity'=>'required|numeric',
            'workingmodel',
            'qualifications'=>'required|string',
            'requirements'=>'required|string',
            'workplacenews'=>'required',
            'workplacenews.*.homeaddress'=>'required|string',
        ]);
        $postdate=Carbon::now()->format('Y-m-d H:i:s');
        $deadline=Carbon::createFromFormat('d-m-Y',$data['deadline']);
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
                $news->posteddate=$postdate;
                $news->benefit=$data['benefit'];
                $news->salary=$data['salary'];
                $news->deadline=$deadline;
                $news->experience=$data['experience'];
                $news->skills=$data['skills'];
                $news->quantity=$data['quantity'];
                $news->status=$data['status'];
                $news->workingmodel=$data['workingmodel'];
                $news->qualifications=$data['qualifications'];
                $news->requirements=$data['requirements'];
                $news->employer_id = $user->id;
                $news->industry_id=$data['industry_id'];
                $news->save();
               
                foreach ($request->workplacenews as $workplace) {
                    $info=Workplace::where('id',$workplace['workplace_id'])->first();
                    if($info){
                        $news->workplacenews()->create($workplace);
                    }
                }
                return response()->json(['message' => 'Tin tuyển dụng đã được tạo thành công', 'data' => $news], 201);
                DB::commit(); // Xác nhận giao dịch
            } catch (\Throwable $th) {
                DB::rollBack(); // Rollback giao dịch nếu có lỗi
                // Log lỗi nếu cần
                \Log::error('Error creating recruitment news: ' . $th->getMessage());
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
        // Lọc theo ngành nghề (industry)
        if ($request->has('industry_id') && !empty($request->industry_id)) {
            $query->where('industry_id', '=', $request->industry_id);
        }
        // Lọc theo nơi làm việc (working model)
        if ($request->has('workingmodel') && !empty($request->workingmodel)) {
            $query->where('workingmodel', 'like', '%' . $request->workingmodel . '%');
        }
        // Lọc theo mức lương (salary)
        if ($request->has('salary_min') && !empty($request->salary_min)) {
            $query->where('salary', '>=', $request->salary_min);
        }   
        if ($request->has('salary_max') && !empty($request->salary_max)) {
            $query->where('salary', '<=', $request->salary_max);
        }
        // Lọc theo hạn nộp đơn (deadline) - chỉ lọc những tin còn hạn         
        if ($request->has('deadline') && !empty($request->deadline)) {
            $query->where('deadline', '>=', Carbon::now());
        }
        $recruitmentNews = $query->get();
        // Trả về kết quả dưới dạng JSON (nếu làm API)
        return response()->json($recruitmentNews,200);
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
        $matchingJobs = RecruitmentNews::query()->where('deadline', '>=', Carbon::now())->where(function ($query) use ($profile) {
            $query->where('required_skills', 'LIKE', "%{$profile->skills}%") // Khớp kỹ năng
                  ->orWhere('experience_required', '<=', $profile->experience) // Kinh nghiệm
                  ->orWhere('salary', '>=', $profile->desired_salary) // Mức lương mong muốn
                  ->orWhere('location','like',"%{$profile->desired_location}%"); // Địa điểm làm việc
        })->get();
        return response()->json($matchingJobs,200);
    }
    public function getNews($id){
        $data=RecruitmentNews::where('id',$id)->where('deadline','>=',Carbon::now())->get();
        if(!$data){
            return response()->json(['error'=>'Không lấy được thông tin'],401);
        }
        return response()->json($data,200);
    }
}
