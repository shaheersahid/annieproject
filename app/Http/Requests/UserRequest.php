<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $userId = $this->route('user')?->id;
        $isUpdate = $userId !== null;

        return [
            'name'                => ['required', 'string', 'max:255'],
            'email'               => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone'               => ['nullable', 'string', 'max:20', 'regex:/^(?:03\d{9}|\+923\d{9}|00923\d{9})$/'],
            'password'            => [$isUpdate ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'role'                => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid Pakistan mobile number, e.g. 03xxxxxxxxx, +923xxxxxxxxx, or 00923xxxxxxxxx.',
        ];
    }
}
