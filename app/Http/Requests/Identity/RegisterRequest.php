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
            'display_name' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9 ]+$/'],
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
            'display_name' => 'İsim',
        ];
    }

    public function messages(): array
    {
        $minLength = $this->minLength;

        return [
            'password.min' => "Şifre en az {$minLength} karakterden oluşmalıdır.",
            'password.confirmed' => "Şifre doğrulaması eşleşmiyor.",
            'password.required' => "Şifre zorunludur.",
            'email.required' => "E-posta zorunludur.",
            'display_name.required' => "İsim zorunludur.",
            'display_name.min' => "İsim en az 3 karakterli olmalıdır.",
            'display_name.max' => "İsim en fazla 50 karakterli olmalıdır.",
            'display_name.regex' => "İsim yalnızca harf, rakam ve boşluk içermelidir.",
        ];
    }
}
