<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobPostingController extends Controller
{
     //hiện tất cả các JobPosting
     public function getAllJobPosting(){
        $getinfo = JobPosting::all();
        return response()->json($getinfo,200);
    }
    //thêm JobPosting
    public function addJobPosting(Request $request){
        $data = $request->all();
        $validator = Validator::make($data,[
            'name' => 'required|regex:/^[^0-9]*$/|max:255',
            'price'=> 'required|numeric',
            'describe'=> 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $info=new JobPosting();
        $info->name=$data['name'];
        $info->type=$data['type'];
        $info->price=$data['price'];
        $info->describe=$data['describe'];
        $info->save();
        return response()->json(['message' => 'Thêm thành công'], 200);
    }
    //cập nhật thông tin
    public function updateJobPosting(Request $request,$id){
        $data = $request->all(); 
        $info=JobPosting::find($id);
        
        $validator = Validator::make($data, [
            'name' => 'required|regex:/^[^0-9]*$/|max:255',
            'price'=> 'required|numeric',
            'describe'=> 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()], 422);
        }
        if($info){
            $info->name=$data['name'];
            $info->type=$data['type'];
            $info->price=$data['price'];
            $info->describe=$data['describe'];
            $info->save();
            return response()->json(['message' => 'Cập nhật thành công'], 200);
        }
    }
    //xóa lĩnh vực
    public function deleteJobPosting($id){
        $info=JobPosting::find($id);
        if($info){
            $info->delete();
            return response()->json(['message' => 'Xóa thành công'], 200);
        }
        else{
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
    }
    //tìm kiếm
    public function searchJobPosting(Request $request){
        $info = JobPosting::query()
            ->where('name', 'LIKE', "%{$request->input('name')}%")
            ->get();

        return response()->json([
            'data' => $info,
        ]);
    }
}
