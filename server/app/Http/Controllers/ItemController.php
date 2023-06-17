<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ItemController extends Controller
{

    public function getUserId($userUuid)
    {
        $actualUserId = User::where("user_uuid", $userUuid)->first("id")->id;
        return $actualUserId;
    }


   public function sellItem(Request $request){
    $validation = Validator::make($request->all(),[
        "name" =>"required|string",
        "image"=>"required|mimes:png,jpeg,jpg",
        "price" => "required|integer",
        "seller_user_uuid" => "required|uuid"
    ]);

    if($validation->fails()){
        return response()->json($validation->errors()->all(),400);
    }

    $validated = $validation->validated();

    try {
        $actualUserId = $this->getUserId($validated["seller_user_uuid"]);
    } catch (Exception $e) {
        return response()->json(["error" => "invalid User UUID"], 400);
    }

    if($request->has('image')){
        try{
            $image = $request->file('image');
            $img_name = time().'.'.$image->getClientOriginalExtension();
            Storage::disk('public')->put("/items/".$img_name,file_get_contents($image));
            $url = Storage::url("items/".$img_name);
        }catch(Exception $e){ 
            //! use throw exception for future
           return $e->getMessage();
        }
    }
    $item = Item::create([
        "name" => $validated["name"],
        "image" => $url,
        "price" => $validated["price"],
        "item_uuid" => Uuid::uuid4(),
        "seller_user_id" => $actualUserId
    ]);
    return response()->json(["item"=>$item,"message"=>"item created!"],201);
   } 

   public function getItems(Request $request){
    return response()->json(["items"=>Item::all()],200);
   }
   
   public function getItem(Request $request){
    $validation = Validator::make($request->all(),[
        "item_uuid" => "required|uuid"
    ]);

    if($validation->fails()){   
        return response()->json($validation->errors()->all(),400);
    }
    $validated = $validation->validated();

    $item = Item::where("item_uuid",$validated["item_uuid"])->first();
    return response()->json(["item"=>$item],200);
   }
}