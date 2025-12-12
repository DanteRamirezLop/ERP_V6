<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Services\Turnstile;

class Turnstile implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $token = (string) $value;
        $result = app(Turnstile::class)->verify($token, request()->ip());
        return (bool)($result['success'] ?? false);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Verification required';
    }
}
