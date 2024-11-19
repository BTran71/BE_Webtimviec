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
    //gửi mail nhà tuyển dụng
    public function sendMailEmployer(Request $request)
    {
        $user = Employer_account::where('email', $request->email)->firstOrFail();
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
        $user = Employer_account::where('email', $passwordReset->email)->firstOrFail();
        $updatePasswordUser = $user->update($request->only('password'));
        $passwordReset->delete();

        return response()->json([
            'success' => $updatePasswordUser,
        ]);
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
        $admin = Admin::where('email', $request->email)->first();
        // Thực hiện xác thực bằng Auth
        if (Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])) {
            // Lấy thông tin người dùng đã đăng nhập
            $user = Auth::guard('admin')->user();

            $responseData = [
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
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
            $candidate = Admin::create([
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
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $candidate = Auth::user();
            $responseData = [
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'id' =>$candidate->id,
                    'email' => $candidate->email,
                ]
            ];
            return response()->json($responseData, 200);
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
    
            return response()->json(['message' => 'Đăng ký thành công'],200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->error()], 500);
        }
    }
    //login nhà tuyển dụng
    public function loginEmployer(Request $request){
        $employer = Employer::where('email', $request->email)->first();
        // Kiểm tra tài khoản có tồn tại và có bị khóa không
        if (!$employer || $employer->is_Lock == 0) {
            return response()->json(['error' => 'Tài khoản đã bị khóa'], 401);
        }

        // Thực hiện xác thực bằng Auth
        if (Auth::guard('employer')->attempt(['email' => $request->email, 'password' => $request->password])) {
            // Lấy thông tin người dùng đã đăng nhập
            $employer = Auth::guard('employer')->user();

            $responseData = [
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'id' => $employer->id,
                    'email' => $employer->email,
                    'name' => $employer->company_name, // Nếu muốn lấy thêm thông tin khác
                ]
            ];

            return response()->json($responseData, 200);
        } else {
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
            $candidate = Employer::create([
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
        Auth::logout();
        return response()->json(['message' => 'Đăng xuất thành công']);
    }
}
