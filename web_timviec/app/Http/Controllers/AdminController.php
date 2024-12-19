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
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
        }
        $user->email=$data['email'];
        $user->image=$imagePath;
        $user->name=$data['name'];
        $user->password=Hash::make($data['password']);
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
        ]);
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
        }
        $user->email=$data['email'];
        $user->image=$imagePath;
        $user->company_name=$data['name'];
        $user->password=Hash::make($data['password']);
        $user->phone_number=$data['phone_number'];
        $user->address=$data['address'];
        $user->company_size=$data['company_size'];
        $user->save();
        return response()->json(['message'=>'Sửa thành công'],200);
    }
    public function getCandidate(){
        
    }
    public function getEmployer(){
        
    }
    public function getAdmin(){
        
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
