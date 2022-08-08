<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserRepositoryInterface;


class AuthController extends Controller
{
    protected $user;

    public function __construct(UserRepositoryInterface $userInterface) {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->user = $userInterface;
    }

    public function login(Request $request){
    	$response = $this->user->login($request);
        return $response;
    }

    public function register(Request $request) {
        $response = $this->user->register($request);
        return response()->json($response);
    }

    public function logout() {
        $response = $this->user->logout();
        return response()->json($response);
    }

    public function refresh() {
        $response = $this->user->refresh();
        return response()->json($response);
    }

    public function userProfile() {
        $response = $this->user->userProfile();
        return response()->json($response);
    }
}
