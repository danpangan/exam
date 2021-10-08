<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    private $loginResponse;

    public function register(Request $request) {

        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed'
        ]);

        // $this->checkEmail($request);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password'])
        ]);

        return response(['message' => 'User successfully registered',], 201);
    }

    // protected function checkEmail(Request $request) {

    //     $email = User::where('email', $request->input('email'))->first();

    //     if($email) {
    //         return response(['message' => 'Email already taken'], 400);
    //     }
    // }

    public function login(Request $request) {

        $this->validateLogin($request);

        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);

    }

    protected function validateLogin(Request $request) {

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
    }

    protected function attemptLogin(Request $request) {

        $user = User::where('email', $request->input('email'))->first();

        if(!$user || !Hash::check($request->input('password'), $user->password)) {

            $this->loginResponse = ['message' => 'Invalid credentials'];

            return false;
        }

        $this->guard()->login($user);
        return true;
    }

    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return response($response, 201);
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended($this->redirectPath());
    }

    protected function authenticated(Request $request, $user)
    {
        $token = $user->createToken('dishtansya_token')->plainTextToken;

        return $this->loginResponse = ['access_token' => $token];
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return response($this->loginResponse, 400);
    }

    public function logout(Request $request) {

        $request->user()->currentAccessToken()->delete();

        return response(['message' => 'user successfully logged out'], 200);
    }

    public function maxAttempts()
    {
        return $maxAttempts = 5;
    }

    public function decayMinutes()
    {
        return $decayMinutes = 5;
    }
}
