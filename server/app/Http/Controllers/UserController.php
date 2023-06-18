<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class UserController extends Controller
{
    public function register(Request $request){
        $validation = Validator::make($request->all(),[
            "email" => "required|email|unique:users", // todo: add regex
            "username" => "required|string|unique:users",
            "name" => "required|string",
            "password" =>"required|confirmed|string"
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();
        $user = User::create([
            "email" => $validated["email"],
            "name" => $validated["name"],
            "username" => $validated["username"],
            "password" => Hash::make($validated["password"]),
            "user_uuid" => Uuid::uuid4()
        ]);
        return response()->json(["user"=>$user],201);
    }
    public function authRegister(Request $request){
        $validation = Validator::make($request->all(),[
            "email" => "required|email|unique:users", // todo: add regex
            "username" => "required|string|unique:users",
            "name" => "required|string",
            "password" =>"required|confirmed|string"
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();
        $user = User::create([
            "email" => $validated["email"],
            "name" => $validated["name"],
            "password" => Hash::make($validated["password"]),
            "is_admin" => 1,
            "user_uuid" => Uuid::uuid4(),
            "username" => $validated["username"]

        ]);
        return response()->json(["user"=>$user],201);
    }
    public function authLogin(Request $request){
        $validation = Validator::make($request->all(),[
            "email" => "required|email",
            "password" => "required|string"
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();
        $user = User::where("email",$validated["email"])->first();
        if(!$user){
            return response()->json(["error"=>"User not Found, Please Register"],401);
        }
        if(!Hash::check($validated["password"],$user->password)){
            return response()->json(["error"=>"Incorrect Password"],401);
        }
        if($user->is_admin){
            $userToken = $user->createToken("myapptoken")->plainTextToken;
            return response()->json(["user"=>$user,"token"=>$userToken],200)->withCookie(cookie()->forever('at',$userToken));
        }
        else{
            return response()->json(["you are not a moderator"],403);
        }
    }
    public function login(Request $request){
        $validation = Validator::make($request->all(),[
            "email" => "required|email",
            "password" => "required|string"
        ]);
        if($validation->fails()){
            return response()->json($validation->errors()->all(),400);
        }
        $validated = $validation->validated();
        $user = User::where("email",$validated["email"])->first();
        if(!$user){
            return response()->json(["error"=>"User not Found, Please Register",401]);
        }
        if(!Hash::check($validated["password"],$user->password)){
            return response()->json(["error"=>"Incorrect Password"],401);
        }
            $userToken = $user->createToken("myapptoken")->plainTextToken;
            return response()->json(["user"=>$user,"token"=>$userToken],200)->withCookie(cookie()->forever('at',$userToken));
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();
        $response =  [
            'message' => 'logged out'
        ];
        return response($response,200);
    }

    public function userData(Request $request){
        if(!$request->hasCookie("at")){
            return response()->json([
                'message' => "Unauthenticated"
            ],401);
        }
        if($token = \Laravel\Sanctum\PersonalAccessToken::findToken($request->cookie("at"))){
            $user = $token->tokenable;
        }
        else{
            return response()->json([
                'message' => "invalid token"
            ],401);
        }
        if(is_null($user)){
            return response()->json([
                'message' => "user is null"
            ]);
        }
        return response() -> json([
            'email' => $user->email,
            'username'=>$user->username,
            'name' => $user->name,
            'user_uuid' => $user->user_uuid,
            'access_token' => $request -> cookie('at'),
        ],200);
    }
}

// ? forgot password route