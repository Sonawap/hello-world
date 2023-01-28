<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\ResponseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CreateUser;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\AppMessages;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function store(CreateUser $request) : mixed{
        $user = new User();
        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->password = bcrypt($request->password);
        $user->save();
        $token =  $user->createToken($user->email)->plainTextToken;

        return ResponseController::response(true,[
            ResponseController::MESSAGE => AppMessages::ACCOUNT_CREATED,
            'user' => $user,
            'token' => $token
        ], Response::HTTP_OK);
    }

    public function validateEmail(Request $request) : mixed{
        $check = User::where('email', $request->email)->exists();
        if($check){
            return ResponseController::response(
                false,
                AppMessages::EMAIL_EXISTS,
                Response::HTTP_NOT_FOUND
            );
        }else{
            return ResponseController::response(
                true,
                AppMessages::EMAIL_AVAILABLE,
                Response::HTTP_OK
            );
        }
    }

    public function user() : mixed{
        $user = auth()->user();
        return ResponseController::response(true,[
            'user' => $user,
        ], Response::HTTP_OK);
    }

    public function login(LoginRequest $request) : mixed{
        if(auth()->attempt(['email' => $request->email, 'password' => $request->password])){
            $user = auth()->user();
            $token =  $user->createToken($user->email)->plainTextToken;
            if(!$user->email_verified_at){
                return ResponseController::response(true,[
                    ResponseController::MESSAGE => AppMessages::VERIFY_EMAIL,
                    'user' => $user,
                    'accounts' => $user->accounts,
                    'token' => $token
                ], Response::HTTP_OK);
            }else{
                return ResponseController::response(true,[
                    'user' => $user,
                    'token' => $token
                ], Response::HTTP_OK);
            }
        }
        else{
            return ResponseController::response(false,[
                ResponseController::MESSAGE => AppMessages::LOGIN_FAILED,
            ], Response::HTTP_FORBIDDEN);
        }
    }
}
