<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndustryController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkexperienceController;
use App\Http\Controllers\WorkplaceController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\ITController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
//api reset password
// Route::post('/reset-password', 'AuthController@sendMailCandidate');
// Route::put('/reset-password/{token}', 'AuthController@resetCandidate');
// Route::post('/reset-password', 'AuthController@sendMailEmployer');
// Route::put('/reset-password/{token}', 'AuthController@resetEmployer');

//api đổi mật khẩu 
Route::post('/reset-password', [AuthController::class,'sendmailCandidate']);
Route::put('/reset-password/{token}', [AuthController::class,'resetCandidate']);
Route::post('/reset-password', [AuthController::class,'sendmailEmployer']);
Route::put('/reset-password/{token}', [AuthController::class,'sendmailEmployer']);

//api logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
//api auth
Route::post('/loginCandidate', [AuthController::class,'loginCandidate']);
Route::post('/registerCandidate', [AuthController::class,'registerCandidate']);
Route::post('/loginEmployer', [AuthController::class,'loginEmployer']);
Route::post('/registerEmployer', [AuthController::class,'registerEmployer']);
Route::post('/loginAdmin', [AuthController::class,'loginAdmin']);
Route::post('/registerAdmin', [AuthController::class,'registerAdmin']);
Route::post('/logout',[AuthController::class,'logout']);



//api nơi làm việc

Route::middleware('role:admin')->group(function () {
    //khóa tài khoản (các api liên quan đến acc employer)
    Route::put('/admin/{id}/changeLock',[AuthController::class,'changeLock']);
    Route::get('/admin/employer',[AuthController::class,'getAllEmployer']);
    Route::post('/admin/employerSearch',[AuthController::class,'searchEmployer']);
   //api lĩnh vực
    Route::get('/admin/getIndustry',[IndustryController::class,'getAllIndustry']);
    Route::post('/admin/addIndustry',[IndustryController::class,'addIndustry']);
    Route::put('/admin/updateIndustry/{id}',[IndustryController::class,'updateIndustry']);
    Route::delete('/admin/deleteIndustry/{id}',[IndustryController::class,'deleteIndustry']);
    Route::post('/admin/searchIndustry',[IndustryController::class,'searchIndustry']);

    //api nơi làm việc
    Route::get('/admin/getWorkplace',[WorkplaceController::class,'getAllWorkplace']);
    Route::post('/admin/addWorkplace',[WorkplaceController::class,'addWorkplace']);
    Route::put('/admin/updateWorkplace/{id}',[WorkplaceController::class,'updateWorkplace']);
    Route::delete('/admin/deleteWorkplace/{id}',[WorkplaceController::class,'deleteWorkplace']);
    Route::post('/admin/searchWorkplace',[WorkplaceController::class,'searchWorkplace']);
    //api ngôn ngữ
    Route::get('/admin/getLanguage',[LanguageController::class,'getAllLanguage']);
    Route::post('/admin/addLanguage',[LanguageController::class,'addLanguage']);
    Route::put('/admin/updateLanguage/{id}',[LanguageController::class,'updateLanguage']);
    Route::delete('/admin/deleteLanguage/{id}',[LanguageController::class,'deleteLanguage']);
    Route::post('/admin/searchLanguage',[LanguageController::class,'searchLanguage']);
    //api công nghệ
    Route::get('/admin/getIT',[ITController::class,'getAllIT']);
    Route::post('/admin/addIT',[ITController::class,'addIT']);
    Route::put('/admin/updateIT/{id}',[ITController::class,'updateIT']);
    Route::delete('/admin/deleteIT/{id}',[ITController::class,'deleteIT']);
    Route::post('/admin/searchIT',[ITController::class,'searchIT']);
});
Route::get('/admin/getIndustry',[IndustryController::class,'getAllIndustry']);

Route::middleware('role:employer')->group(function () {
    
    
    Route::get('/employer/dashboard', [EmployerController::class, 'dashboard']);
        // Các route khác cho employer
});

Route::middleware('role:api')->group(function () {
    Route::get('/candidate/dashboard', [CandidateController::class, 'dashboard']);
    Route::post('/candidate/profile/add',[ProfileController::class,'addProfile']);
   
});

