<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserService extends CrudService
{
    public function __construct()
    {
        parent::__construct("User");
    }

    public function login(array $request, \stdClass &$output): void
    {

        $user = $this->repository->getByKey('email', $request['email'])->first();

        if ($user && Hash::check($request['password'], $user->password)) {

            $token = JWTAuth::fromUser($user);
            auth()->login($user);
            $output->users = $user;
            $output->token = $token;
        } else {
            $output->Error = [__('errors.wrong_username_password')];
        }
    }

}
