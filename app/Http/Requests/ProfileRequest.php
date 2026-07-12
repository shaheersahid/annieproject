<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = auth()->user();

        return [
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'              => ['nullable', 'string', 'max:30'],
            'password'           => ['nullable', 'confirmed', Password::defaults()],
            'avatar'             => ['nullable', 'image', 'max:2048'],
            'two_factor_enabled' => ['nullable', 'integer', 'in:1,0'],
            'two_factor_method'  => ['required_if:two_factor_enabled,1', 'nullable', 'string', 'in:email,sms'],
        ];
    }
}
