<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecruitmentNews;
use App\Models\Profile;
use App\Models\Sending;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Mail\ApplicationStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;


class SendController extends Controller
{
    public function sendProfile(Request $request,$id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $check=Sending::where('profile_id',$profile->id)->where('recruitment_news_id',$id)->first();
        if(!$user || !$profile){
            return response()->json(['error'=>'Chưa đăng nhập hoặc chưa có hồ sơ'],401);
        }
        else if($check){
            return response()->json(['message'=>'Không thể gửi tin tuyển dụng nữa'],500);
        }
        $date=Carbon::now()->format('Y-m-d H:i:s');
        $send=new Sending();
        $send->profile_id=$profile->id;
        $send->name=$request->name;
        $send->recruitment_news_id=$id;
        $send->senddate=$date;
        $send->save();
        return response()->json(['message'=>'Đã gửi hồ sơ'],200);
    }
    public function profileList($newsid){
        $user = Auth::guard('employer')->user();
        // Eager load các quan hệ cần thiết
        $info=RecruitmentNews::where('id',$newsid)->first();
        $data = Sending::where('recruitment_news_id',$newsid)->get();
        return response()->json([
            'title'=>$info->title, // Tiêu đề tin tuyển dụng
            'send'=>$data,
        ],200);
    }
    //lấy thông tin từng hồ sơ
    public function getDetailInfo($sendid){
        $user = Auth::guard('employer')->user();
        $send=Sending::where('id',$sendid)->first();
        // Eager load các quan hệ cần thiết
        $data = Profile::with([
            'work_ex',
            'reference',
            'academy',
            'languageDetails',
            'information_Details',
            'workplaceDetails',
            'industries',
        ])->where('id', $send->profile_id)->first();
        if ($data && $data->image) {
            $data->image_url = asset('storage/' . $data->image); // Tạo URL từ đường dẫn
        } else {
            $data->image_url = null; // Nếu không có hình ảnh, trả về null
        }
        return response()->json([
            'profile' => $data,
        ],200);
    }
    // public function updateStatus(Request $request, $sendid)
    // {
    //     $user=Auth::guard('employer')->user();
    //     $companyname=$user->company_name;
    //     // Validate input
    //     $request->validate([
    //         'status' => 'required|in:accepted,rejected',
    //         'interview_date' => 'required_if:status,accepted|date|after:now',
    //     ]);

    //     // Find sending details record
    //     $sendingDetails = Sending::where('id',$sendid)->first();
    //     $news=RecruitmentNews::where('id',$sendingDetails->recruitment_news_id)->first();
    //     // Fetch associated profile
    //     $profile = Profile::where('id',$sendingDetails->profile_id)->first();

    //     // Update the status
    //     $status = $request->input('status');
    //     $sendingDetails->status = $status;
    //     $sendingDetails->save();

    //     // Fetch email details
    //     $applicantEmail = $profile->email; // Ensure the `email` field exists in the `profiles` table
    //     $applicantName = $profile->name;  // Ensure the `name` field exists in the `profiles` table
    //     $jobTitle = $news->title; // Assuming `news` relationship exists in `SendingDetails`

    //     $interviewDate = $status === 'accepted' ? Carbon::parse($request->input('interview_date'))->format('d-m-Y H:i') : null;
    //     // Send email to the applicant
    //     Mail::to($applicantEmail)->send(new ApplicationStatusMail($status, $jobTitle, $applicantName,$companyname,$interviewDate));

    //     // Return response
    //     return response()->json([
    //         'message' => 'Application status updated and email sent successfully.',
    //         'status' => $sendingDetails->status,
    //         'interview_date' => $interviewDate,
    //         'company'=>$companyname,
    //     ], 200);
    // }

    public function acceptStatus(Request $request, $sendid)
    {
        $user=Auth::guard('employer')->user();
        $companyname=$user->company_name;
        // Validate input
        $request->validate([
            'interview_date' => 'required_if:status,accepted|date|after:now',
        ]);

        // Find sending details record
        $sendingDetails = Sending::where('id',$sendid)->first();
        $news=RecruitmentNews::where('id',$sendingDetails->recruitment_news_id)->first();
        // Fetch associated profile
        $profile = Profile::where('id',$sendingDetails->profile_id)->first();

        // Update the status
        $status = 'accepted';
        $sendingDetails->status = $status;
        $sendingDetails->save();

        // Fetch email details
        $applicantEmail = $profile->email; // Ensure the `email` field exists in the `profiles` table
        $applicantName = $profile->name;  // Ensure the `name` field exists in the `profiles` table
        $jobTitle = $news->title; // Assuming `news` relationship exists in `SendingDetails`

        $interviewDate = Carbon::parse($request->input('interview_date'))->format('d-m-Y H:i');
        // Send email to the applicant
        Mail::to($applicantEmail)->send(new ApplicationStatusMail($status, $jobTitle, $applicantName,$companyname,$interviewDate));

        // Return response
        return response()->json([
            'message' => 'Application status updated and email sent successfully.',
            'status' => $sendingDetails->status,
            'interview_date' => $interviewDate,
            'company'=>$companyname,
        ], 200);
    }
    public function rejectedStatus($sendid)
    {
        $user=Auth::guard('employer')->user();
        $companyname=$user->company_name;
        
        // Find sending details record
        $sendingDetails = Sending::where('id',$sendid)->first();
        $news=RecruitmentNews::where('id',$sendingDetails->recruitment_news_id)->first();
        // Fetch associated profile
        $profile = Profile::where('id',$sendingDetails->profile_id)->first();

        // Update the status
        $status = 'rejected';
        $sendingDetails->status = $status;
        $sendingDetails->save();

        // Fetch email details
        $applicantEmail = $profile->email; // Ensure the `email` field exists in the `profiles` table
        $applicantName = $profile->name;  // Ensure the `name` field exists in the `profiles` table
        $jobTitle = $news->title; // Assuming `news` relationship exists in `SendingDetails`

        $interviewDate = null;
        // Send email to the applicant
        Mail::to($applicantEmail)->send(new ApplicationStatusMail($status, $jobTitle, $applicantName,$companyname,$interviewDate));

        // Return response
        return response()->json([
            'message' => 'Application status updated and email sent successfully.',
            'status' => $sendingDetails->status,
            'interview_date' => $interviewDate,
            'company'=>$companyname,
        ], 200);
    }
    public function getSendNews(){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $send=Sending::where('profile_id',$profile->id)->get();
        foreach ($send as $item) {
            $news = RecruitmentNews::where('id', $item->recruitment_news_id)->first();
            $newsList[] = [
                'news' => $news ? $news->title : null,
                'send' => $item,     
            ];
        }
        if(!$send){
            return response()->json(['message'=>'Chưa gửi hồ sơ'],401);
        }
        else{
            return response()->json($newsList, 200);
        }
    }
}
