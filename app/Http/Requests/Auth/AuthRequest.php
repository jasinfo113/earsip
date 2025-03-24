<?php

namespace App\Http\Requests\Auth;

use App\Models\Users\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate()
    {
        $this->ensureIsNotRateLimited();
        $username = Str::lower(preg_replace('/\s+/', '', $this->string('username')));
        $ref = $this->_ref($username);
        $user = User::firstWhere($ref, $username);
        if (!$user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.username'),
            ]);
        }
        if (!Hash::check($this->string('password'), $user->password)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => trans('auth.password'),
            ]);
        }
        if (!$user->is_valid) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.suspend'),
            ]);
        }
        /*
        if (!Auth::attempt([$ref => $username, 'password' => $this->string('password')], $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }
        */
        Auth::login($user, $this->boolean('remember'));
        RateLimiter::clear($this->throttleKey());
    }

    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }
        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());
        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')) . '|' . $this->ip());
    }

    private function _ref($_username)
    {
        $ref = "username";

        if (filter_var($_username, FILTER_VALIDATE_EMAIL)) {
            $ref = "email";
        }
        return $ref;
    }
}
