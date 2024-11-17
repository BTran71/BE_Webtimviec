<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Workplace;

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
            'cityname' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $workplace=new Workplace();
        $workplace->cityname=$data['cityname'];
        $workplace->save();
        return response()->json(['message' => 'Thêm nơi làm việc thành công'], 200);
    }
    //cập nhật thông tin
    public function updateWorkplace(Request $request,$id){
        $data = $request->all();
        $workplace=Workplace::where('id',$id)->get();
        $validator = Validator::make($data, [
            'cityname' => 'required|string|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $workplace->cityname=$data['cityname'];
        $workplace->save();
        return response()->json(['message' => 'Thêm nơi làm việc thành công'], 200);
    }
    //xóa workplace
    public function deleteWorkplace($id){
        $workplace=Workplace::where('id',$id)->get();
        if($workplace){
            $workplace->delete();
            return response()->json(['message' => 'Xóa nơi làm việc thành công'], 200);
        }
        else{
            return response()->json(['message' => 'Không tìm thấy nơi làm việc'], 404);
        }
    }
}
