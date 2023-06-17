<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class RequestController extends Controller
{
    public function addRequest(Request $request){
        $validation = Validator::make($request->all(),[
            "user_uuid" => "required|uuid",
            "title" => "required|string",
            "description" => "required|string"
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

        $itemRequest = ModelsRequest::create([
            "user_id" => $actualUserId,
            "title" => $validated["title"],
            "description" => $validated["description"],
            "request_uuid" => Uuid::uuid4()
        ]);
        return response()->json(["request" => $itemRequest],201);
     
    }

    public function getAllRequests(Request $request){
        return response()->json(["requests"=>ModelsRequest::all()],200);

    }
    public function getUserRequests(Request $request){
        $validation = Validator::make($request->all(),[
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

        $itemRequest = ModelsRequest::where("user_id",$actualUserId)->get();

        return response()->json(["requests" => $itemRequest],200);

    }

    public function getRequest(Request $request){
        $validation = Validator::make($request->all(),[
            "request_uuid" => "required|uuid"
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }

        $validated = $validation->validated();
        $itemRequest = ModelsRequest::where("request_uuid",$validated["request_uuid"])->first();

        return response()->json(["request" => $itemRequest],200);
    }
    // ! add UD routes for each model
}