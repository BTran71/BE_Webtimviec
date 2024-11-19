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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//api reset password
// Route::post('/reset-password', 'AuthController@sendMailCandidate');
// Route::put('/reset-password/{token}', 'AuthController@resetCandidate');
// Route::post('/reset-password', 'AuthController@sendMailEmployer');
// Route::put('/reset-password/{token}', 'AuthController@resetEmployer');

//api auth
Route::post('/loginCandidate', [AuthController::class,'loginCandidate']);
Route::post('/registerCandidate', [AuthController::class,'registerCandidate']);
Route::post('/loginEmployer', [AuthController::class,'loginEmployer']);
Route::post('/registerEmployer', [AuthController::class,'registerEmployer']);
Route::post('/loginAdmin', [AuthController::class,'loginAdmin']);
Route::post('/registerAdmin', [AuthController::class,'registerAdmin']);
Route::post('/logout',[AuthController::class,'logout']);

//api lĩnh vực
Route::get('/getIndustry',[IndustryController::class,'getAllIndustry']);
Route::post('/addIndustry',[IndustryController::class,'addIndustry']);
Route::put('/updateIndustry/{id}',[IndustryController::class,'updateIndustry']);
Route::delete('/deleteIndustry/{id}',[IndustryController::class,'deleteIndustry']);
Route::post('industry/search',[IndustryController::class,'searchIndustry']);

