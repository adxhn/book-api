<?php

namespace App\Http\Requests\Identity;

use App\Services\Identity\PasswordService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public int $minLength = 6;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => PasswordService::passwordRules($this->minLength)
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
        ]);
    }

    public function attributes(): array
    {
        return [
            'email' => 'e-posta',
        ];
    }

    public function messages(): array
    {
        $minLength = $this->minLength;

        return [
            'password.min' => "Şifre en az {$minLength} karakterden oluşmalıdır.",
            'password.confirmed' => "Şifre doğrulaması eşleşmiyor.",
        ];
    }
}
