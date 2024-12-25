<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yoeunes\Toastr\Facades\Toastr;
use App\Models\Admin;
use App\Models\Employer;
use App\Models\Candidate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function updateCandidate(Request $request){
        $data=$request->all();
        $user=Auth::guard('candidate')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('candidate_account', 'email')->ignore($user->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'password'=>'nullable|string',
        ]);
        $imagePath = null;
        $user->email=$data['email'];
        $user->name=$data['name'];
        $user->password=Hash::make($data['password']);
        if ($request->hasFile('image')) {
            // Lấy đường dẫn của ảnh cũ
            $oldImagePath = $user->image;
        
            // Lưu ảnh mới
            $imagePath = $request->file('image')->store('profiles', 'public');
        
            // Xóa ảnh cũ nếu có
            if ($oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        
            // Cập nhật ảnh mới vào profile
            $user->image = $imagePath;
        }
        $user->save();
        return response()->json(['message'=>'Sửa thành công'],200);
    }
    public function updateAdmin(Request $request){
        $data=$request->all();
        $user=Auth::guard('admin')->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('candidate_account', 'email')->ignore($user->id),
            ],
            'password'=>'nullable|string',
        ]);
        $user->email=$data['email'];
        $user->name=$data['name'];
        $user->password=Hash::make($data['password']);
        $user->save();
        return response()->json(['message'=>'Sửa thành công'],200);
    }
    public function updateEmployer(Request $request){
        $data=$request->all();
        $user=Auth::guard('employer')->user();
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'phone_number' => 'required|regex:/^0[0-9]{9}$/',
            'address' => 'required|string|max:255',
            'company_size' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('employer_account', 'email')->ignore($user->id),
            ],
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
            'password'=>'nullable|string',
            'discription'=>'nullable|string',
        ]);
        $imagePath = null;
        $user->email=$data['email'];
        $user->company_name=$data['company_name'];
        $user->password=Hash::make($data['password']);
        $user->phone_number=$data['phone_number'];
        $user->address=$data['address'];
        $user->company_size=$data['company_size'];
        $user->discription=$data['discription'];
        if ($request->hasFile('image')) {
            // Lấy đường dẫn của ảnh cũ
            $oldImagePath = $user->image;
        
            // Lưu ảnh mới
            $imagePath = $request->file('image')->store('profiles', 'public');
        
            // Xóa ảnh cũ nếu có
            if ($oldImagePath) {
                Storage::disk('public')->delete($oldImagePath);
            }
        
            // Cập nhật ảnh mới vào profile
            $user->image = $imagePath;
        }
        $user->save();
        return response()->json(['message'=>'Sửa thành công'],200);
    }
    public function getCandidate(){
        $user=Auth::guard('candidate')->user();
        $info=Candidate::where('id',$user->id)->first();
        if ($info && $info->image) {
            $info->image_url = asset('storage/' . $info->image); // Tạo URL từ đường dẫn
        } else {
            $info->image_url = null; // Nếu không có hình ảnh, trả về null
        }
        return response()->json($info,200);
    }
    public function getEmployer(){
        $user=Auth::guard('employer')->user();
        $info=Employer::where('id',$user->id)->first();
        if ($info && $info->image) {
            $info->image_url = asset('storage/' . $info->image); // Tạo URL từ đường dẫn
        } else {
            $info->image_url = null; // Nếu không có hình ảnh, trả về null
        }
        return response()->json($info,200);
    }
    public function getAdmin(){
        $user=Auth::guard('admin')->user();
        $info=Admin::where('id',$user->id)->first();
        return response()->json($info,200);
    }
    public function follow($id){
        $user=Auth::guard('candidate')->user();
        $follow=new Follow();
        $follow->candidate_id=$user->id;
        $follow->employer_id=$id;
        $follow->save();
        return response()->json(['message'=>'Đang theo dõi'],200);
    }
}
