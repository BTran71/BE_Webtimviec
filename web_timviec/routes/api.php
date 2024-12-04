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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\WorkplaceDetailsController;
use App\Http\Controllers\ITDetailsController;
use App\Http\Controllers\LanguageDetailsController;
use App\Http\Controllers\IndustryDetailsController;
use App\Http\Controllers\AcademyController;

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


//api hiện các thông tin
Route::get('/admin/getIndustry',[IndustryController::class,'getAllIndustry']);
Route::get('/admin/getWorkplace',[WorkplaceController::class,'getAllWorkplace']);
Route::get('/admin/getLanguage',[LanguageController::class,'getAllLanguage']);
Route::get('/admin/getIT',[ITController::class,'getAllIT']);
//api nơi làm việc

Route::middleware('role:admin')->group(function () {
    //khóa tài khoản (các api liên quan đến acc employer)
    Route::put('/admin/{id}/changeLock',[AuthController::class,'changeLock']);
    Route::get('/admin/employer',[AuthController::class,'getAllEmployer']);
    Route::post('/admin/employerSearch',[AuthController::class,'searchEmployer']);
   //api lĩnh vực
    // Route::get('/admin/getIndustry',[IndustryController::class,'getAllIndustry']);
    Route::post('/admin/addIndustry',[IndustryController::class,'addIndustry']);
    Route::put('/admin/updateIndustry/{id}',[IndustryController::class,'updateIndustry']);
    Route::delete('/admin/deleteIndustry/{id}',[IndustryController::class,'deleteIndustry']);
    Route::post('/admin/searchIndustry',[IndustryController::class,'searchIndustry']);

    //api nơi làm việc
    // Route::get('/admin/getWorkplace',[WorkplaceController::class,'getAllWorkplace']);
    Route::post('/admin/addWorkplace',[WorkplaceController::class,'addWorkplace']);
    Route::put('/admin/updateWorkplace/{id}',[WorkplaceController::class,'updateWorkplace']);
    Route::delete('/admin/deleteWorkplace/{id}',[WorkplaceController::class,'deleteWorkplace']);
    Route::post('/admin/searchWorkplace',[WorkplaceController::class,'searchWorkplace']);
    //api ngôn ngữ
    // Route::get('/admin/getLanguage',[LanguageController::class,'getAllLanguage']);
    Route::post('/admin/addLanguage',[LanguageController::class,'addLanguage']);
    Route::put('/admin/updateLanguage/{id}',[LanguageController::class,'updateLanguage']);
    Route::delete('/admin/deleteLanguage/{id}',[LanguageController::class,'deleteLanguage']);
    Route::post('/admin/searchLanguage',[LanguageController::class,'searchLanguage']);
    //api công nghệ
    // Route::get('/admin/getIT',[ITController::class,'getAllIT']);
    Route::post('/admin/addIT',[ITController::class,'addIT']);
    Route::put('/admin/updateIT/{id}',[ITController::class,'updateIT']);
    Route::delete('/admin/deleteIT/{id}',[ITController::class,'deleteIT']);
    Route::post('/admin/searchIT',[ITController::class,'searchIT']);

    //api gói dịch vụ
    Route::get('/admin/getPosting',[JobPostingController::class,'getAllJobPosting']);
    Route::post('/admin/addPosting',[JobPostingController::class,'addJobPosting']);
    Route::put('/admin/updatePosting/{id}',[JobPostingController::class,'updateJobPosting']);
    Route::delete('/admin/deletePosting/{id}',[JobPostingController::class,'deleteJobPosting']);
    Route::post('/admin/searchPosting',[JobPostingController::class,'searchJobPosting']);
});

Route::middleware('role:employer')->group(function () {
    Route::post('/employer/add',[ProfileController::class,'addProfile']);
    Route::put('/employer/lock',[ProfileController::class,'changeLock']);
    Route::put('/employer/update',[ProfileController::class,'updateProfile']);
    
    // Route::get('/employer/dashboard', [EmployerController::class, 'dashboard']);
        // Các route khác cho employer
});

Route::middleware('role:candidate')->group(function () {
    //Route::get('/candidate/dashboard', [CandidateController::class, 'dashboard']);
    Route::post('/candidate/profile/add',[ProfileController::class,'addProfile']);
    Route::put('/candidate/profile/lock',[ProfileController::class,'changeLock']);
    Route::put('/candidate/profile/update',[ProfileController::class,'updateProfile']);
    
    //api kinh nghiệm
    Route::get('/candidate/getWorkExperience',[WorkexperienceController::class,'getWorkExperience']);
    Route::post('/candidate/addWorkExperience',[WorkexperienceController::class,'addWorkExperience']);
    Route::put('/candidate/updateWorkExperience/{id}',[WorkexperienceController::class,'updateWorkExperience']);
    Route::delete('/candidate/deleteWorkExperience/{id}',[WorkexperienceController::class,'deleteWorkExperience']);

    //api công nghệ
    Route::get('/candidate/getITDetails',[ITDetailsController::class,'getITDetails']);
    Route::post('/candidate/addITDetails',[ITDetailsController::class,'addITDetails']);
    Route::put('/candidate/updateITDetails/{id}',[ITDetailsController::class,'updateITDetails']);
    Route::delete('/candidate/deleteITDetails/{id}',[ITDetailsController::class,'deleteITDetails']);

    //api ngôn ngữ
    Route::get('/candidate/getLanguageDetails',[LanguageDetailsController::class,'getAllDetails']);
    Route::post('/candidate/addLanguageDetails',[LanguageDetailsController::class,'addLanguageDetails']);
    Route::put('/candidate/updateLanguageDetails/{id}',[LanguageDetailsController::class,'updateLanguageDetails']);
    Route::delete('/candidate/deleteLanguageDetails/{id}',[LanguageDetailsController::class,'deleteLanguageDetails']);

    //api người liên lạc
    Route::get('/candidate/getReference',[ReferenceController::class,'getReference']);
    Route::post('/candidate/addReference',[ReferenceController::class,'addReference']);
    Route::put('/candidate/updateReference/{id}',[ReferenceController::class,'updateReference']);
    Route::delete('/candidate/deleteReference/{id}',[ReferenceController::class,'deleteReference']);

    //api trường học
    Route::get('/candidate/getAcademy',[AcademyController::class,'getAcademy']);
    Route::post('/candidate/addAcademy',[AcademyController::class,'addAcademy']);
    Route::put('/candidate/updateAcademy/{id}',[AcademyController::class,'updateAcademy']);
    Route::delete('/candidate/deleteAcademy/{id}',[AcademyController::class,'deleteAcademy']);

    //api ngành nghề
    Route::get('/candidate/getIndustryProfile',[IndustryDetailsController::class,'getIndustryProfile']);
    Route::post('/candidate/addIndustryProfile',[IndustryDetailsController::class,'addIndustryProfile']);
    Route::put('/candidate/updateIndustryProfile/{id}',[IndustryDetailsController::class,'updateIndustryProfile']);
    Route::delete('/candidate/deleteIndustryProfile/{id}',[IndustryDetailsController::class,'deleteIndustryProfile']);

    //api nơi làm việc
    Route::get('/candidate/getWorkplaceDetails',[WorkplaceDetailsController::class,'getWorkplaceDetails']);
    Route::post('/candidate/addWorkplaceDetails',[WorkplaceDetailsController::class,'addWorkplaceDetails']);
    Route::put('/candidate/updateWorkplaceDetails/{id}',[WorkplaceDetailsController::class,'updateWorkplaceDetails']);
    Route::delete('/candidate/deleteWorkplaceDetails/{id}',[WorkplaceDetailsController::class,'deleteWorkplaceDetails']);
});

