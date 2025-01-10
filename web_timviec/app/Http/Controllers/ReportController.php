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
use App\Models\Report;
use App\Models\ReportDetails;
use App\Models\RecruitmentNews;
use App\Models\Sending;

class ReportController extends Controller
{
    public function addReport(Request $request,$sendid){
        $user=Auth::guard('candidate')->user();
        $data=$request->all();
        $validator=Validator::make($data,[
            'content'=>'required|string',
        ]);
        if(!$validator){
            return response()->json(['message'=>'Chưa nhập nội dung'],401);
        }
        $report=new Report();
        $report->content=$data['content'];
        $report->save();
        $reportdetails=new ReportDetails();
        $reportdetails->report_id=$report->id;
        $reportdetails->sending_details_id=$sendid;
        $reportdetails->save();
        return response()->json(['message'=>'Đã báo cáo thành công'],200);
    }

    public function createReport(Request $request,$newsid){
        $user=Auth::guard('candidate')->user();
        $profile=$user->profile;
        $news=RecruitmentNews::where('id',$newsid)->first();
        $send=Sending::where('profile_id',$profile->id)->where('recruitment_news_id',$newsid)->first();
        $data=$request->all();
        $validator=Validator::make($data,[
            'content'=>'required|string',
        ]);
        if(!$validator || !$send){
            return response()->json(['message'=>'Chưa nhập hoặc chưa ứng tuyển'],401);
        }
        $report=new Report();
        $report->content=$data['content'];
        $report->save();
        $reportdetails=new ReportDetails();
        $reportdetails->report_id=$report->id;
        $reportdetails->sending_details_id=$send->id;
        $reportdetails->save();
        return response()->json(['message'=>'Đã báo cáo thành công'],200);
    }

    public function getAllReport(){
        $getData = Report::all();
        $results = $getData->map(function ($report) {
            $reportDetails = ReportDetails::where('report_id',$report->id)->first();
            if ($reportDetails) { // Kiểm tra nếu $reportDetails không phải null
                $send = Sending::where('id', $reportDetails->sending_details_id)->first();
            } else {
                $send = null; // Xử lý trường hợp không tìm thấy reportDetails
            }

            return [
                'report' => $report,
                'send' => $send,
            ];
        });
        return response()->json(['data' => $results], 200);
    }
}
