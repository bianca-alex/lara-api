<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Redis;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:15',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:2|max:15|confirmed',
        ]); 

        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json($errors, 401);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'api_token' => Str::random(64),
        ]);
        // $user->api_token = $user->generateToken();

        Redis::setex($user->id, 3600, $user->api_token);

        return response()->json($user, 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:2|max:15',
        ]); 
        
        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json($errors, 401);
        }

        if(!Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            return response()->json(['message' => '401 Unauthenticated.'], 401);
        }

        $user = Auth::user();
        $user->api_token = Str::random(64);
        // $user->api_token = $user->generateToken();
        $user->save();

        Redis::setex($user->id, 3600, $user->api_token);

        return response()->json($user, 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->api_token = null;
        $user->save();

        Redis::del($user->id);
        return response()->json(['message' => 'User logged out.'], 200);
    }

    public function resetPass(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            're_password' => 'required|string',
            're_confirmed' => 'required|string',
        ]);

        if($validator->fails()){
            $errors = $validator->errors();
            return response()->json($errors, 401);
        }

        if($request->re_password != $request->re_confirmed){
            return response()->json(['re_confirmed' => 'The re_password confirmation does not match.'], 401);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json(['message' => 'wrong password'], 401);
        }
        $password = Hash::make($request->re_password);
        $user->password = $password;
        $user->api_token = Str::random(64);
        $user->save();

        Redis::setex($user->id, 3600, $user->api_token);

        return response()->json($user, 200);
    }

    public function getUser(Request $request)
    {
        return $request->user();
    }
}
