<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function getAllLanguage(){
        $getInfo = Language::all();
        return response()->json($getInfo,200);
    }
    //thêm Industry
    public function addLanguage(Request $request){
        $data = $request->all();
        $validator = Validator::make($data,[
            'language_name' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $industry=new Language();
        $industry->language_name=$data['language_name'];
        $industry->save();
        return response()->json(['message' => 'Thêm thành công'], 200);
    }
    //cập nhật thông tin
    public function updateLanguage(Request $request,$id){
        $data = $request->all(); 
        $language=Language::find($id);
        
        $validator = Validator::make($data, [
            'language_name' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()], 422);
        }
        if($language){
            $language->language_name=$data['language_name'];
            $language->save();
            return response()->json(['message' => 'Cập nhật thành công'], 200);
        }
    }
    //xóa 
    public function deleteLanguage($id){
        $info=Language::find($id);
        if($info){
            $info->delete();
            return response()->json(['message' => 'Xóa thành công'], 200);
        }
        else{
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
    }
    //tìm kiếm
    public function searchLanguage(Request $request){
        // Tìm kiếm sản phẩm theo tên hoặc mô tả
        $infos = Language::query()
            ->where('language_name', 'LIKE', "%{$request->input('language_name')}%")
            ->get();

        return response()->json([
            'data' => $info,
        ],200);
    }
}
