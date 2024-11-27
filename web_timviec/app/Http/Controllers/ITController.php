<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IT;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ITController extends Controller
{
     //hiện tất cả các IT
     public function getAllIT(){
        $getIT = IT::all();
        return response()->json($getIT,200);
    }
    //thêm IT
    public function addIT(Request $request){
        $data = $request->all();
        $validator = Validator::make($data,[
            'name' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        $IT=new IT();
        $IT->name=$data['name'];
        $IT->save();
        return response()->json(['message' => 'Thêm thành công'], 200);
    }
    //cập nhật thông tin
    public function updateIT(Request $request,$id){
        $data = $request->all(); 
        $info=IT::find($id);
        
        $validator = Validator::make($data, [
            'name' => 'required|regex:/^[^0-9]*$/|max:255',
        ]);
        if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()], 422);
        }
        if($info){
            $info->name=$data['name'];
            $info->save();
            return response()->json(['message' => 'Cập nhật thành công'], 200);
        }
    }
    //xóa lĩnh vực
    public function deleteIT($id){
        $IT=IT::find($id);
        if($IT){
            $IT->delete();
            return response()->json(['message' => 'Xóa thành công'], 200);
        }
        else{
            return response()->json(['message' => 'Không tìm thấy'], 404);
        }
    }
    //tìm kiếm
    public function searchIT(Request $request){
        $info = IT::query()
            ->where('name', 'LIKE', "%{$request->input('name')}%")
            ->get();

        return response()->json([
            'data' => $info,
        ]);
    }
}
