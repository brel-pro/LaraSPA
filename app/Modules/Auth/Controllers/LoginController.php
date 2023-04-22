<?php

namespace App\Modules\Auth\Controllers;

use App\Exceptions\VerifyEmailException;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected string $token;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => [
            'logout', 'login',
        ]]);
    }

    protected function setToken(string $token): void
    {
        $this->token = $token;
    }

     /**
      * Attempt to log the user into the application.
      *
      * @param  Request  $request
      * @return bool
      */
     public function login(Request $request)
     {
         $this->validateLogin($request);

         // If the class is using the ThrottlesLogins trait, we can automatically throttle
         // the login attempts for this application. We'll key this by the username and
         // the IP address of the client making these requests into this application.
         if (method_exists($this, 'hasTooManyLoginAttempts') &&
             $this->hasTooManyLoginAttempts($request)) {
             $this->fireLockoutEvent($request);

             return $this->sendLockoutResponse($request);
         }

         if ($this->attemptLogin($request)) {
             if ($request->hasSession()) {
                 $request->session()->put('auth.password_confirmed_at', time());
             }

             return $this->sendLoginResponse($request);
         }

         // If the login attempt was unsuccessful we will increment the number of attempts
         // to login and redirect the user back to the login form. Of course, when this
         // user surpasses their maximum number of attempts they will get locked out.
         $this->incrementLoginAttempts($request);

         return $this->sendFailedLoginResponse($request);
     }

    protected function attemptLogin(Request $request)
    {
        $user = User::where('email', strtolower($request->input($this->username())))->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return false;
        }

        $this->guard()->setUser($user);

        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return false;
        }

        $this->setToken($user->createToken($request->device_name)->plainTextToken);

        if (method_exists($this->guard(), 'attempt')) {
            $resp = $this->guard()->attempt(
                $this->credentials($request), $request->filled('remember')
            );
        } else {
            $resp = true;
        }

        return $resp;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        return response()->json([
            'token' => $this->token,
        ])->header('Authorization', $this->token);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  Request  $request
     * @return void
     *
     * @throws ValidationException
     * @throws VerifyEmailException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $user = $this->guard()->user();
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            throw VerifyEmailException::forUser($user);
        }

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return void
     */
    public function logout(Request $request)
    {
        $session = auth()->guard('web')->getsession();
        $cookieName = $session->getName();

        if (method_exists($request->user()->currentAccessToken(), 'delete')) {
            $request->user()->currentAccessToken()->delete();
        } else {

        }

        app()->get('auth')->forgetGuards();
        auth('web')->logout();
        auth()->guard('web')->logout();

        $delete_cookie = \Cookie::forget($cookieName);  // TODO: wrong, think about another way

        return response()->noContent()->withCookie($delete_cookie);
    }

    /**
     * Validate the user login request.
     *
     * @param  Request  $request
     * @return void
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);
    }
}
