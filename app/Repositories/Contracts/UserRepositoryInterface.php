<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    
    public function login($request);

    public function register($request);

    public function logout();

    public function refresh();

    public function userProfile();

    public function createNewToken($token);

}