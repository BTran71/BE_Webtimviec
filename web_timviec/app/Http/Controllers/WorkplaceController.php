<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workplace;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WorkplaceController extends Controller
{
     //hiện tất cả các workplace
    public function getAllWorkplace(){
        $getWorkplace = Workplace::all();
        return response()->json($getWorkplace,200);
    }
    //thêm workplace
    public function addWorkplace(Request $request){
        $data = $request->all();
        $validator = Validator::make($data, [
            'city' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $workplace=new Workplace();
        $workplace->city=$data['city'];
        $workplace->save();
        return response()->json(['message' => 'Thêm nơi làm việc thành công'], 200);
    }
    //cập nhật thông tin
    public function updateWorkplace(Request $request,$id){
        $data = $request->all();
        $workplace=Workplace::where('id',$id)->first();
        $validator = Validator::make($data, [
            'city' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $workplace->city=$data['city'];
        $workplace->save();
        return response()->json(['message' => 'Cập nhật nơi làm việc thành công'], 200);
    }
    //xóa workplace
    public function deleteWorkplace($id){
        $workplace=Workplace::where('id',$id)->first();
        if($workplace){
            $workplace->delete();
            return response()->json(['message' => 'Xóa nơi làm việc thành công'], 200);
        }
        else{
            return response()->json(['message' => 'Không tìm thấy nơi làm việc'], 404);
        }
    }
    public function searchWorkplace(Request $request){
        // Tìm kiếm sản phẩm theo tên hoặc mô tả
        $workplaces = Workplace::query()
            ->where('city', 'LIKE', "%{$request->input('city')}%")
            ->get();
        return response()->json([
            'data' => $workplaces,
        ]);
    }
}
