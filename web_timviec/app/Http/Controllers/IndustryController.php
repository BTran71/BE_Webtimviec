<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Industry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IndustryController extends Controller
{
    //hiện tất cả các Industry
    public function getAllIndustry(){
        $getIndustry = Industry::all();
        return response()->json($getIndustry,200);
    }
    //thêm Industry
    public function addIndustry(Request $request){
        $data = $request->all();
        $validator = Validator::make($data,[
            'industry_name' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $industry=new Industry();
        $industry->industry_name=$data['industry_name'];
        $industry->save();
        return response()->json(['message' => 'Thêm thành công'], 200);
    }
    //cập nhật thông tin
    public function updateIndustry(Request $request,$id){
        $data = $request->all();
        $industry=Industry::find($id);
        $validator = Validator::make($data, [
            'industry_name' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        // if ($validator->fails()) {
        //     return response()->json(['error' => $validator->errors()], 422);
        // }
        if($industry){
            $industry->industry_name=$request->industry_name;
            $industry->save();
            return response()->json(['message' => 'Cập nhật thành công'], 200);
        }     
        // $data = $request->validate([
        //     'industry_name' => 'required|string|max:255',
        // ]);
        // $industry = Industry::find($id);
        // if (!$industry) {
        //     return response()->json(['message' => 'Industry not found'], 404);
        // }
        // // Kiểm tra key 'industry_name' tồn tại trong $data   
        // // if (!array_key_exists('industry_name', $data)) {
        // //     return response()->json(['message' => 'Industry name is missing'], 400);
        // // }
                
        // $industry->industry_name = $data['industry_name'];
        // $industry->save();
        // return response()->json(['message' => 'Industry updated successfully'], 200);
    }
    //xóa lĩnh vực
    public function deleteIndustry($id){
        $industry=Industry::find($id);
        if($industry){
            $industry->delete();
            return response()->json(['message' => 'Xóa thành công'], 200);
        }
        else{
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
    }
    //tìm kiếm
    public function searchIndustry(Request $request){
        // Tìm kiếm sản phẩm theo tên hoặc mô tả
        $industrys = Industry::query()
            ->where('industry_name', 'LIKE', "%{$request->input('industry_name')}%")
            ->get();

        return response()->json([
            'data' => $industrys,
        ]);
    }
}
