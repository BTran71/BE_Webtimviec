<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workexperience;

class WorkexperienceController extends Controller
{
    //hien cac kinh nghiem lam viec thuoc profile
    public function getWorkExperience($profile_id){
        $getdata=Work_experience::with('Profile')->where('profile_id',$profile_id)->get();
        return response()->json($getdata,200);
    }
    public function addWorkExperience(Request $request){
        $data=$request->all();
        $validator = Validator::make($data, [
            'company_name' => 'required|string|max:255',
            'job_position' => 'required|string|max:255',
            'start_time' => 'required|date_format:d/m/Y',
            'end_time' => 'required|date_format:d/m/Y',
            'description' => 'nullable|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $experience=new Work_experience();
        $experience->company_name=$data['company_name'];
        $experience->job_position=$data['job_position'];
        $experience->start_time=$data['start_time'];
        $experience->end_time=$data['end_time'];
        $experience->description=$data['description'];
        $experience->save();
        return response()->json(['message' => 'Thêm nơi làm việc thành công'], 200);
    }
}
