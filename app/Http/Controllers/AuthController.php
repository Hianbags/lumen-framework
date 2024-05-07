<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = User::where('email', $request->input('email'))->first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }
        if (Hash::check($request->input('password'), $user->password)) {
            $token = $user->createToken('users')->accessToken;
            return response()->json(['status' => 'success', 'data' => $token]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }
    }
    
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        if (User::where('email', '=', $request->email)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'User already exists with this email']);
        }
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            if ($user->save()) {
                return $this->login($request);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function logout()
    {
        try {
            auth()->user()->tokens->each(function ($token) {
                $token->delete();
            });
            return response()->json(['status' => 'success', 'message' => 'Logged out successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function editPermissions(Request $request)
    {
        $this->authorize('editPermissions' , Post::class);
        $this->validate($request, [
            'role' => 'required',
            'email' => 'required|email'
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->role = $request->role;
            $user->save();
            return response()->json(['status' => 'success', 'message' => 'Permissions updated successfully']);
        }
        return response()->json(['status' => 'error', 'message' => 'User not found']);
    }


}
