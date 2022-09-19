<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
            'password_confirmation' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        // $request['password'] = Hash::make($request['password']);
        // $user = User::create($request->toArray());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth_token')->accessToken;
        $response = ['token' => $token];
        return response()->json([
            'data' => $user,
            'token_type' => 'Bearer',
            'token' => $token,
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);
        if (!Auth::attempt($loginData)) {
            return response(['message' => 'Credenciales inválidas'], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::where('email', $request['email'])->firstOrFail();
        $accessToken = $user->createToken('authToken')->plainTextToken;
        $cookie = cookie('jwt', $accessToken, 60 * 24);
        return response(['user' => $user, 'access_token' => $accessToken], Response::HTTP_OK)->withoutCookie($cookie);
    }

    public function userProfile()
    {
        $user = auth()->user();
        return response()->json([
            "menssaje" => "Información del Perfil del Usuario Ok",
            "userData" => $user
        ], Response::HTTP_OK);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        $cookie = Cookie::forget('jwt');
        return response(['message' => 'Sesión cerrada OK'], Response::HTTP_OK)->withCookie($cookie);
    }

    public function allUsers()
    {
        $users = User::all();
        return response()->json([
            'message' => 'Todos los usuarios',
            'users' => $users
        ]);
    }
}
