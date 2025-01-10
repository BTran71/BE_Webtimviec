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

class UpdateNewsController extends Controller
{
    public function getIndustryNews($newsid){
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        if(!$news){
            return response()->json(['error'=>'Tin tuyển dụng không tồn tại'],401);
        }
        else{
            $getdata=IndustryNews::where('recruitment_news_id',$news->id)->get();
            return response()->json($getdata,202); 
        }
    }
    public function addIndustryNews(Request $request,$newsid){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        $info=Industry::where('id',$data['industry_id'])->first();
        if(!$info || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $details=new IndustryNews();
            $details->recruitment_news_id=$newsid;
            $details->industry_id=$data['industry_id'];
            $details->score=$data['score'];
            $details->experience=$data['experience'];
            $details->save();
            return response()->json(['message'=>'Thêm thành công'],200);
        }
    }
    public function updateIndustryNews(Request $request,$industry){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $info=IndustryNews::where('id',$industry)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        $industry=Industry::where('id',$data['industry_id'])->first();
        if(!$industry || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->industry_id=$data['industry_id'];
            $info->score=$data['score'];
            $info->experience=$data['experience'];
            $info->save();
            return response()->json(['message'=>'Sửa thành công'],200);
        }
    }
    public function deleteIndustryNews($industry){
        $user=Auth::guard('employer')->user();
        $info=IndustryNews::where('id',$industry)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        $count=IndustryNews::where('recruitment_news_id',$info->recruitment_news_id)->count();
        if(!$info || !$news){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else if($count>1){
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
        else{
            return response()->json(['message'=>'Không xóa được'],500);
        }
    }

    //ngôn ngữ
    public function getLanguageNews($newsid){
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        if(!$news){
            return response()->json(['error'=>'Tin tuyển dụng không tồn tại'],401);
        }
        else{
            $getdata=LanguageNews::where('recruitment_news_id',$news->id)->get();
            return response()->json($getdata,202); 
        }
    }
    public function addLanguageNews(Request $request,$newsid){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        $info=Language::where('id',$data['language_id'])->first();
        if(!$info || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $details=new LanguageNews();
            $details->recruitment_news_id=$newsid;
            $details->language_id=$data['language_id'];
            $details->score=$data['score'];
            $details->save();
            return response()->json(['message'=>'Thêm thành công'],200);
        }
    }
    public function updateLanguageNews(Request $request,$language){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $info=LanguageNews::where('id',$language)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        $industry=Language::where('id',$data['language_id'])->first();
        if(!$industry || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->language_id=$data['language_id'];
            $details->score=$data['score'];
            $info->save();
            return response()->json(['message'=>'Sửa thành công'],200);
        }
    }
    public function deleteLanguageNews($language){
        $user=Auth::guard('employer')->user();
        $info=LanguageNews::where('id',$language)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        if(!$info || !$news){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else {
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }

    //nơi làm việc
    public function getWorkplaceNews($newsid){
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        if(!$news){
            return response()->json(['error'=>'Tin tuyển dụng không tồn tại'],401);
        }
        else{
            $getdata=WorkplaceNews::where('recruitment_news_id',$news->id)->get();
            return response()->json($getdata,202); 
        }
    }
    public function addWorkplaceNews(Request $request,$newsid){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        $info=Workplace::where('id',$data['workplace_id'])->first();
        if(!$info || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $details=new WorkplaceNews();
            $details->recruitment_news_id=$newsid;
            $details->workplace_id=$data['workplace_id'];
            $details->homeaddress=$data['homeaddress'];
            $details->score=$data['score'];
            $details->save();
            return response()->json(['message'=>'Thêm thành công'],200);
        }
    }
    public function updateWorkplaceNews(Request $request,$workplace){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $info=WorkplaceNews::where('id',$workplace)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        $industry=Workplace::where('id',$data['workplace_id'])->first();
        if(!$industry || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->workplace_id=$data['workplace_id'];
            $info->homeaddress=$data['homeaddress'];
            $details->score=$data['score'];
            $info->save();
            return response()->json(['message'=>'Sửa thành công'],200);
        }
    }
    public function deleteWorkplaceNews($workplace){
        $user=Auth::guard('employer')->user();
        $info=WorkplaceNews::where('id',$workplace)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        $count=WorkplaceNews::where('recruitment_news_id',$info->recruitment_news_id)->count();
        if(!$info || !$news){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else if($count>1){
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
        else{
            return response()->json(['message'=>'Không xóa được'],500);
        }
    }

    //tin học
    public function getInfoNews($newsid){
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        if(!$news){
            return response()->json(['error'=>'Tin tuyển dụng không tồn tại'],401);
        }
        else{
            $getdata=InfoNews::where('recruitment_news_id',$news->id)->get();
            return response()->json($getdata,202); 
        }
    }
    public function addInfoNews(Request $request,$newsid){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('employer_id',$user->id)->first();
        $info=IT::where('id',$data['it_id'])->first();
        if(!$info || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $details=new InfoNews();
            $details->recruitment_news_id=$newsid;
            $details->it_id=$data['it_id'];
            $details->score=$data['score'];
            $details->save();
            return response()->json(['message'=>'Thêm thành công'],200);
        }
    }
    public function updateInfoNews(Request $request,$it){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $info=InfoNews::where('id',$it)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        $industry=IT::where('id',$data['it_id'])->first();
        if(!$industry || !$news){
            return response()->json(['error'=>'Không tìm thấy thông tin'],401);
        }
        else{
            $info->it_id=$data['it_id'];
            $details->score=$data['score'];
            $info->save();
            return response()->json(['message'=>'Sửa thành công'],200);
        }
    }
    public function deleteInfoNews($it){
        $user=Auth::guard('employer')->user();
        $info=InfoNews::where('id',$it)->first();
        $news=RecruitmentNews::where('id',$info->recruitment_news_id)->where('employer_id',$user->id)->first();
        if(!$info || !$news){
            return response()->json(['message'=>'Không tìm thấy thông tin'],401);
        }
        else {
            $info->delete();
            return response()->json(['message'=>'Xóa thành công'],200);
        }
    }
}
