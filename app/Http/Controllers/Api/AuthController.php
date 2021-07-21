<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

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
        //$user->api_token = $user->generateToken();

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
        //$user->api_token = $user->generateToken();
        $user->save();

        return response()->json($user, 200);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        $user->api_token = null;
        $user->save();
        return response()->json(['message' => 'User logged out.'], 200);
    }

    public function getUser(Request $request)
    {
        return $request->user();
    }
}
