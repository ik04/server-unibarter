<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class PurchaseController extends Controller
{

    public function getItemId($itemUuid)
    {
        $actualItemId = Item::where("item_uuid", $itemUuid)->first("id")->id;
        return $actualItemId;
    }
    public function getUserId($userUuid)
    {
        $actualUserId = User::where("user_uuid", $userUuid)->first("id")->id;
        return $actualUserId;
    }

    public function addPurchase(Request $request){
        $validation = Validator::make($request->all(),[
            "item_uuid" => "required|uuid",
            "user_uuid" => "required|uuid"
        ]);

        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();

        try {
            $actualUserId = $this->getUserId($validated["user_uuid"]);
        } catch (Exception $e) {
            return response()->json(["error" => "invalid User UUID"], 400);
        }
        try {
            $actualItemId = $this->getItemId($validated["item_uuid"]);
        } catch (Exception $e) {
            return response()->json(["error" => "invalid Item UUID"], 400);
        }

        $purchase = Purchase::create([
            "item_id" => $actualItemId,
            "user_id" => $actualUserId,
            "purchase_uuid" => Uuid::uuid4()
        ]);

        return response()->json(["purchase"=>$purchase,"message"=>"purchase completed"],201);
        // ! change this route if we're adding a shopping cart, it'll also take a status column which would need another route
    }

    public function getUserPurchases(Request $request){
        $validation = Validator::make($request->all(),[
            "user_uuid" => "required|uuid"
        ]);

        $validated = $validation->validated();
        
        try {
            $actualUserId = $this->getUserId($validated["user_uuid"]);
        } catch (Exception $e) {
            return response()->json(["error" => "invalid User UUID"], 400);
        }

        $purchases = Purchase::join("items","purchases.item_id","=","items.id")->where("user_id",$actualUserId)->select("items.name","items.image","items.price","items.item_uuid")->get();
        return response()->json(["purchases"=>$purchases],200);

    }
}