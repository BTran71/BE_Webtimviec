<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yoeunes\Toastr\Facades\Toastr;
use Carbon\Carbon;
use App\Models\Follow;
use App\Models\FollowNews;
use App\Models\Employer;
use App\Models\RecruitmentNews;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    public function addFollow($employer_id){
        $user=Auth::guard('candidate')->user();
        $employer=Employer::where('id',$employer_id)->where('is_Lock',1)->first();
        if(!$user){
            return response()->json(['message'=>'Chưa đăng nhập'],401);
        }
        if(!$employer){
            return response()->json(['message'=>'Tài khoản đã bị khóa'],401);
        }
        $find=Follow::where('employer_id',$employer_id)->where('candidate_id',$user->id)->first();
        if($find){
            return response()->json(['message'=>'Đã tạo follow'],401);
        }
        $follow=new Follow();
        $follow->candidate_id=$user->id;
        $follow->employer_id=$employer_id;
        $follow->save();
        return response()->json(['message'=>'Theo dõi thành công'],200);
    }

    public function followList(){
        $user=Auth::guard('candidate')->user();
        if(!$user){
            return response()->json(['message'=>'Chưa đăng nhập'],401);
        }
        $follow = Follow::where('candidate_id', $user->id)->where('status', 1)->get();
        // Lấy danh sách `employer_id`
        $employers = $follow->map(function ($follows){
            $employer=Employer::where('id',$follows->employer_id)->first();
            if($employer){
                $employer->image_url = asset('storage/' . $employer->image)?:null;
                return $employer;
            }
        });
        return response()->json(['data'=>$employers],200);
    }

    public function changeFollow($employer_id){
        $user=Auth::guard('candidate')->user();
        if(!$user){
            return response()->json(['message'=>'Chưa đăng nhập'],401);
        }
        $follow=Follow::where('employer_id',$employer_id)->where('candidate_id',$user->id)->first();
        $follow->status = $follow->status == 1 ? 0 : 1; // Đổi trạng thái
        $follow->save();

        return response()->json([
            'message' => 'Đã đổi trạng thái thành công',
        ], 200);
    }

    public function getFollow(){
        $employers = Employer::select('employer_account.*', DB::raw('COUNT(follow.id) as follow_count'))->join('follow', 'employer_account.id', '=', 'follow.employer_id')
                    ->where('follow.status', 1) // Điều kiện chỉ lấy các follow có is_Lock = 1
                    ->groupBy('employer_account.id')
                    ->orderByDesc('follow_count') // Sắp xếp giảm dần theo số lượng follow
                    ->get();
        // Trả về kết quả
        $employers->transform(function ($employer) {
            $employer->image_url = $employer->image? asset('storage/' . $employer->image): null; // Nếu không có ảnh, trả về null        
            return $employer;
        });
        return response()->json($employers,200);
    }

    //theo dõi tin tuyển dụng
    public function addFollowNews($newsid){
        $user=Auth::guard('candidate')->user();
        $news=RecruitmentNews::where('id',$newsid)->where('isActive',1)->first();
        if(!$user){
            return response()->json(['message'=>'Chưa đăng nhập'],401);
        }
        if(!$news){
            return response()->json(['message'=>'Tài khoản đã bị khóa'],401);
        }
        $find=FollowNews::where('recruitment_news_id',$newsid)->where('candidate_id',$user->id)->first();
        if($find){
            return response()->json(['message'=>'Đã tạo follow'],401);
        }
        $follow=new FollowNews();
        $follow->candidate_id=$user->id;
        $follow->recruitment_news_id=$newsid;
        $follow->save();
        return response()->json(['message'=>'Theo dõi thành công'],200);
    }

    public function followNewsList(){
        $user=Auth::guard('candidate')->user();
        if(!$user){
            return response()->json(['message'=>'Chưa đăng nhập'],401);
        }
        $follow = FollowNews::where('candidate_id', $user->id)->where('status', 1)->get();
        // Lấy danh sách `employer_id`
        $news = $follow->map(function ($follows){
            $new=RecruitmentNews::where('id',$follows->recruitment_news_id)->first();
            return $new;
        });
        return response()->json(['data'=>$news],200);
    }

    public function changeFollowNews($newsid){
        $user=Auth::guard('candidate')->user();
        if(!$user){
            return response()->json(['message'=>'Chưa đăng nhập'],401);
        }
        $follow=FollowNews::where('recruitment_news_id',$newsid)->where('candidate_id',$user->id)->first();
        $follow->status = $follow->status == 1 ? 0 : 1; // Đổi trạng thái
        $follow->save();

        return response()->json([
            'message' => 'Đã đổi trạng thái thành công',
        ], 200);
    }

    // public function getFollowNews(){
    //     $news = FollowNews::select('follownews.*', DB::raw('COUNT(follownews.id) as follow_count'))->join('follownews', 'recruitment_news.id', '=', 'follownews.recruitment_news_id')
    //                 ->where('follownews.status', 1) // Điều kiện chỉ lấy các follow có is_Lock = 1
    //                 ->groupBy('recruitment_news_id.id')
    //                 ->orderByDesc('follow_count') // Sắp xếp giảm dần theo số lượng follow
    //                 ->get();
    //     // Trả về kết quả
    //     return response()->json($news,200);
    // }
}
