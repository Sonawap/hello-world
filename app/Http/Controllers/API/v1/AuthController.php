<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\ResponseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUser;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\OTPVerifyRequest;
use App\Models\User;
use App\Notifications\LoginNotification;
use App\Notifications\RegisterNotification;
use App\Services\AppConfig;
use App\Services\OTPService;
use Illuminate\Http\Request;
use App\Services\AppMessages;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function store(CreateUser $request) : mixed{
        $user = new User();
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        $token =  $user->createToken($user->email)->plainTextToken;
        new OTPService($user);
        // $generate_otp->generate();
        Notification::send($user, new RegisterNotification($user));
        return ResponseController::response(true,[
            ResponseController::MESSAGE => AppMessages::ACCOUNT_CREATED,
            'user' => $user,
            'token' => $token
        ], Response::HTTP_OK);
    }

    public function verifyOTP(OTPVerifyRequest $request) : mixed{
        if($request->type === AppConfig::VERIFY_EMAIL || $request->type === AppConfig::VERIFY_PHONE){
            $user = auth()->user();
            $otp = new OTPService($user);
            $otp_status = $otp->verify($request->code, $request->type);
            if($otp_status){
                if($request->type === AppConfig::VERIFY_EMAIL){
                    $user->email_verified_at = now();
                    $user->save();
                    return ResponseController::response(false,[
                        ResponseController::MESSAGE => AppMessages::EMAIL_VERIFIED,
                        'user' => $user
                    ], Response::HTTP_OK);
                }
                if($request->type === AppConfig::VERIFY_PHONE){
                    $user->phone_verified_at = now();
                    $user->save();
                    return ResponseController::response(false,[
                        ResponseController::MESSAGE => AppMessages::PHONE_VERIFIED,
                        'user' => $user
                    ], Response::HTTP_OK);
                }
            }else{
                return ResponseController::response(false,[
                    ResponseController::MESSAGE => AppMessages::TOKEN_EXPIRED,
                ], Response::HTTP_FORBIDDEN);
            }
        }else{
            return ResponseController::response(false,[
                ResponseController::MESSAGE => AppMessages::WRONG_OTP_TYPE,
            ], Response::HTTP_FORBIDDEN);
        }
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
                AppMessages::EMAIL_AVALIABLE,
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
                Notification::send($user, new LoginNotification($user, $request->loginDevice));
                return ResponseController::response(true,[
                    ResponseController::MESSAGE => AppMessages::VERIFY_EMAIL,
                    'user' => $user,
                    'accounts' => $user->accounts,
                    'token' => $token
                ], Response::HTTP_OK);
            }else{
                Notification::send($user, new LoginNotification($user, $request->loginDevice));
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

    public function resend_otp(OTPRequest $request) :mixed{
        $generate_otp = new OTPService(auth()->user());
        if($generate_otp->generate($request->type)){
            if($request->type === AppConfig::VERIFY_EMAIL){
                return ResponseController::response(false,[
                    ResponseController::MESSAGE => AppMessages::OTP_EMAIL_RESENT,
                ], Response::HTTP_OK);
            }
            if($request->type === AppConfig::VERIFY_PHONE){
                return ResponseController::response(false,[
                    ResponseController::MESSAGE => AppMessages::OTP_PHONE_RESENT,
                ], Response::HTTP_OK);
            }
        }
    }
}
