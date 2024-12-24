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


class SendController extends Controller
{
    public function sendProfile($id){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        if(!$user || !$profile){
            return response()->json(['error'=>'Chưa đăng nhập hoặc chưa có hồ sơ'],401);
        }
        $date=Carbon::now()->format('Y-m-d H:i:s');
        $send=new Sending();
        $send->profile_id=$profile->id;
        $send->recruitment_news_id=$id;
        $send->senddate=$date;
        $send->save();
        return response()->json(['message'=>'Đã gửi hồ sơ'],200);
    }
    public function getSendProfile($id){
        $user=Auth::guard('employer')->user();
        $data=RecruitmentNews::with('send')->where('employer_id',$user->id)->findOrFail($id);
        return response()->json([
            'news'=>$data->title,
            'profile'=>$data->send,
        ]);
    }
    public function updateStatus(Request $request, $id)
    {
        // Validate input
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'interview_date' => 'required_if:status,accepted|date|after:now',
        ]);

        // Find sending details record
        $sendingDetails = Sending::findOrFail($id);

        // Fetch associated profile
        $profile = Profile::findOrFail($sendingDetails->profile_id);

        // Update the status
        $status = $request->input('status');
        $sendingDetails->status = $status;
        $sendingDetails->save();

        // Fetch email details
        $applicantEmail = $profile->email; // Ensure the `email` field exists in the `profiles` table
        $applicantName = $profile->name;  // Ensure the `name` field exists in the `profiles` table
        $jobTitle = $sendingDetails->news->title; // Assuming `news` relationship exists in `SendingDetails`

        $interviewDate = $status === 'accepted' ? Carbon::parse($request->input('interview_date'))->format('d-m-Y H:i') : null;
        // Send email to the applicant
        Mail::to($applicantEmail)->send(new ApplicationStatusMail($status, $jobTitle, $applicantName,$interviewDate));

        // Return response
        return response()->json([
            'message' => 'Application status updated and email sent successfully.',
            'status' => $sendingDetails->status,
            'interview_date' => $interviewDate,
        ], 200);
    }
}
