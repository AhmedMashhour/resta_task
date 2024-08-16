<?php

namespace App\Http\Controllers;

use App\DomainData\UserDto;
use App\Services\UserService;
use App\Traits\Validators;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use UserDto, Validators;

    public function __construct(private readonly UserService $userService)
    {
    }

    public function login(array $request, \stdClass &$output): void
    {
        $rules = $this->getRules(['email', 'password']);

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return;
        }

        $request = $validator->validate();

        $this->userService->login($request, $output);
    }


}
