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

class RecruitmentNewsController extends Controller
{
    public function addRecruitmentNews(Request $request){
        $user=Auth::guard('employer')->user();
        $data=$request->all();
        $validator=Validator::make($data,[
            'title'=>'required|regex:/^[^0-9]*$/|max:255',
            'describe'=>'required|regex:/^[^0-9]*$/|max:255',
            'posteddate'=>'required|date|date_format:d-m-Y|after_of_equal:1970-01-01|before_of_equal:now',
            'benefit'=>'required|string|max:255',
            'salary'=>'required|numeric',
            'deadline'=>'required|date|date_format:d-m-Y|after:posteddate',
            'experience'=>'required|string|max:255',
            'skills'=>'required|string',
            'quantity'=>'required|numeric',
            'workingmodel',
            'qualifications'=>'required|string',
            'requirements'=>'required|string',
        ]);
        $postdate=Carbon::createFromFormat('d-m-Y',$data['posteddate']);
        $deadline=Carbon::createFromFormat('d-m-Y',$data['deadline']);
        if($validator->fail()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        else{
            DB::beginTransaction();
            try {
                $news=new RecruitmentNews();
                $news->titile=$data['title'];
                $news->describe=$data['describe'];
                $news->posteddate=$postdate;
                $news->benefit=$data['benefit'];
                $news->salary=$data['salary'];
                $news->deadline=$deadline;
                $news->experience=$data['experience'];
                $news->skills=$data['skills'];
                $news->quantity=$data['quantity'];
                $news->workingmodel=$data['workingmodel'];
                $news->qualifications=$data['qualifications'];
                $news->requirements=$data['requirements'];
                $news->save();
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }
}
