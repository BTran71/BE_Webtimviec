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

class AuthController extends Controller
{
    //gửi mail reset password nhà tuyển dụng
    public function sendMailEmployer(Request $request)
    {
        $user = Employer::where('email', $request->email)->firstOrFail();
        $passwordReset = PasswordReset::updateOrCreate([
            'email' => $user->email,
        ], [
            'token' => Str::random(60),
        ]);
        if ($passwordReset) {
            $user->notify(new ResetPasswordRequest($passwordReset->token));
        }
  
        return response()->json([
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }
    //reset mk ntd
    public function resetEmployer(Request $request, $token)
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => 'This password reset token is invalid.',
            ], 422);
        }
        $user = Employer::where('email', $passwordReset->email)->firstOrFail();
        $updatePasswordUser = $user->update($request->only('password'));
        $passwordReset->delete();

        return response()->json([
            'success' => $updatePasswordUser,
        ],200);
    }
    //gửi mail ứng viên
    public function sendMailCandidate(Request $request)
    {
        $user = Candidate::where('email', $request->email)->firstOrFail();
        $passwordReset = PasswordReset::updateOrCreate([
            'email' => $user->email,
        ], [
            'token' => Str::random(60),
        ]);
        if ($passwordReset) {
            $user->notify(new ResetPasswordRequest($passwordReset->token));
        }
  
        return response()->json([
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }
    //reset mk ứng viên
    public function resetCandidate(Request $request, $token)
    {
        $passwordReset = PasswordReset::where('token', $token)->firstOrFail();
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => 'This password reset token is invalid.',
            ], 422);
        }
        $user = Candidate::where('email', $passwordReset->email)->firstOrFail();
        $updatePasswordUser = $user->update($request->only('password'));
        $passwordReset->delete();

        return response()->json([
            'success' => $updatePasswordUser,
        ]);
    }
    //login admin
    public function loginAdmin(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $admin=Admin::where('email',$request->email)->first();
        // Thực hiện xác thực bằng Auth
        if ($admin && Hash::check($request->password, $admin->password)) {
            $token = $admin->createToken('AdminApp')->plainTextToken;
            $responseData = [
                'message' => 'Đăng nhập thành công',
                'token'=>$token,
                'user' => [
                    'id' => $admin->id,
                    'email' => $admin->email,
                    'name'=>$admin->name,
                ]
            ];
            return response()->json($responseData, 200);
        } else {
            return response()->json(['error' => 'Thông tin đăng nhập không chính xác'], 401);
        }
    }
    public function registerAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admin',
            'password' => 'required|string|min:6|',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        try {
            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            return response()->json(['message' => 'Đăng ký thành công'],200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->error()], 500);
        }
    }
    //login ứng viên
    public function loginCandidate(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $candidate=Candidate::where('email',$request->email)->first();
        if ($candidate && Hash::check($request->password, $candidate->password)) {
            $token = $candidate->createToken('CandidateApp')->plainTextToken;
            return response()->json([
                'message' => 'Đăng nhập thành công',
                'email' => $candidate->email,
                'token' => $token,  // Trả về token
            ], 200);
        } else {
            return response()->json(['error' => 'Tài khoản mật khẩu không chính xác'], 401);
        }
    }

    //đăng ký ứng viên
    public function registerCandidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:candidate_account',
    
            'password' => 'required|string|min:6|',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        
        try {
            $candidate = Candidate::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'Đăng ký tài khoản thành công',
                ],200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->error()], 500);
        }
    }
    //login nhà tuyển dụng
    public function loginEmployer(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        $employer = Employer::where('email', $request->email)->first();
        // Kiểm tra tài khoản có tồn tại và có bị khóa không
        if (!$employer || $employer->is_Lock==0) {
            return response()->json(['error' => 'Tài khoản đã bị khóa hoặc không tồn tại'], 403);
        }
        // Thực hiện xác thực bằng Auth
        else if (Hash::check($request->password, $employer->password)) {
            // Tạo token cho employer
            $token = $employer->createToken('EmployerApp')->plainTextToken;
            $responseData = [
                'message' => 'Đăng nhập thành công',
                'token' => $token,
                'user' => [
                    'id' => $employer->id,
                    'email' => $employer->email, 
                    'company_name' => $employer->company_name,
                ],
            ];
            return response()->json($responseData, 200);
        } 
       else {
            return response()->json(['error' => 'Thông tin đăng nhập không chính xác'], 401);
        }
    }
    
    //đăng ký ntd
    public function registerEmployer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:employer_account',
            'password' => 'required|string|min:6|',
            'phone_number' => 'required|regex:/^0[0-9]{9}$/',
            'address' => 'required|string|max:255',
            'company_size' => 'required|string|max:255',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        try {
            $employer = Employer::create([
                'company_name' => $request->company_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'isLock' => 1,
                'company_size' => $request->company_size,
            ]);
    
            return response()->json(['message' => 'Đăng ký thành công'],200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->error()], 500);
        }
    }
    public function logout(Request $request)
    {
        $guard = $request->header('guard', 'api');
        $user = Auth::guard($guard)->user();

        if (!$user) {
            return response()->json(['message' => 'Không tìm thấy người dùng hoặc token không hợp lệ'], 401);
        }
        else{
            $user->currentAccessToken()->delete();
            return response()->json(['message' => 'Đăng xuất thành công']);
        }
    }
    public function changeLock($id)
    {
        $user = Employer::where('id',$id)->first();
        // var_dump($user);
        if($user)
        {
            if($user->is_Lock==1){
                Employer::where('id',$id)->where('is_Lock',1)->update([
                    'is_Lock'=> 0
                ]);
                return  response()->json(['message'=>'cập nhật trạng thái thành công'],200);
            }
            else{
                Employer::where('id',$id)->where('is_Lock',0)->update([
                    'is_Lock'=> 1
                ]);
                return  response()->json(['message'=>'cập nhật trạng thái thành công'],200);
            }
        }
        else
        {
            return  response()->json(['message'=>'không thể cập nhật trạng thái'],400);
        }
    }
    public function getAllEmployer(){
        $getInfo = Employer::all();
        return response()->json($getInfo,200);
    }
    public function searchEmployer(Request $request){
        $info = Employer::query()
            ->where('company_name', 'LIKE', "%{$request->input('company_name')}%")
            ->get();
        return response()->json([
            'data' => $info,
        ],200);
    }
}
