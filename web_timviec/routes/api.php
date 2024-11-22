<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndustryController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WorkexperienceController;
use App\Http\Controllers\WorkplaceController;


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
   //api lĩnh vực
    Route::get('/admin/getIndustry',[IndustryController::class,'getAllIndustry']);
    Route::post('/admin/addIndustry',[IndustryController::class,'addIndustry']);
    Route::put('/admin/updateIndustry/{id}',[IndustryController::class,'updateIndustry']);
    Route::delete('/admin/deleteIndustry/{id}',[IndustryController::class,'deleteIndustry']);
    Route::post('/admin/searchIndustry',[IndustryController::class,'searchIndustry']);

    //api nơi làm việc
    Route::get('/admin/getWorkplace',[IndustryController::class,'getAllWorkplace']);
    Route::post('/admin/addWorkplace',[IndustryController::class,'addWorkplace']);
    Route::put('/admin/updateWorkplace/{id}',[IndustryController::class,'updateWorkplace']);
    Route::delete('/admin/deleteWorkplace/{id}',[IndustryController::class,'deleteWorkplace']);
    Route::post('/admin/searchWorkplace',[IndustryController::class,'searchWorkplace']);
});

Route::middleware('role:employer')->group(function () {
    
    
    Route::get('/employer/dashboard', [EmployerController::class, 'dashboard']);
        // Các route khác cho employer
});

Route::middleware('role:api')->group(function () {
    Route::get('/candidate/dashboard', [CandidateController::class, 'dashboard']);
    
   
});

