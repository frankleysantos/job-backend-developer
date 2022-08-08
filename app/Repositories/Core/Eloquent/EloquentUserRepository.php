<?php

namespace App\Repositories\Core\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Core\BaseEloquentRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;


class EloquentUserRepository extends BaseEloquentRepository implements UserRepositoryInterface
{
    public function entity()
    {
        return User::class;
    }

    public function login($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return ['error' => $validator->errors(), 'statusCode' => Response::HTTP_UNPROCESSABLE_ENTITY];
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return ['error' => 'Unauthorized', 'statusCode' => Response::HTTP_UNAUTHORIZED];
        }
        return $this->createNewToken($token);
    }

    public function register($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return ['error' => $validator->errors(), 'statusCode' => Response::HTTP_BAD_REQUEST];
        }
        $user = $this->entity()::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return [
            'message' => 'User successfully registered',
            'user' => $user,
            'statusCode' => Response::HTTP_CREATED
        ];
    }

    public function logout()
    {
        auth()->logout();
        return ['message' => 'User successfully signed out'];
    }

    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    public function userProfile()
    {
        return auth()->user();
    }

    public function createNewToken($token){
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }
}