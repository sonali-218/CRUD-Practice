<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (!$this->has('hiring_date')) {
            $this->merge([
                'hiring_date' => now()->format('Y-m-d'),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        // valores que necesito que me devuelva
        if ($this->isMethod('put')) {
            // PUT → todos los campos obligatorios
            return [
                'name'         => ['required', 'string', 'max:255'],
                'lastname'     => ['required', 'string', 'max:255'],
                'username'     => ['required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
                'email'        => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'hiring_date'  => ['required', 'date'], 
                'dui'          => ['required', 'regex:/^\d{8}-\d{1}$/', Rule::unique('users', 'dui')->ignore($userId)],
                'phone_number' => ['nullable', 'string'],
                'birth_date'   => ['required', 'date', 'before:today'],
            ];
        } else {
            // PATCH → actualización parcial
            return [
                'name'         => ['sometimes','required', 'string', 'max:255'],
                'lastname'     => ['sometimes','required', 'string', 'max:255'],
                'username'     => ['sometimes','required', 'string', 'max:255', Rule::unique('users', 'username')->ignore($userId)],
                'email'        => ['sometimes','required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
                'hiring_date'  => ['sometimes', 'date'], 
                'dui'          => ['sometimes','required', 'regex:/^\d{8}-\d{1}$/', Rule::unique('users', 'dui')->ignore($userId)],
                'phone_number' => ['nullable', 'string'],
                'birth_date'   => ['sometimes','required', 'date', 'before:today'],
            ];
        }
    }

    // lo mejor es centralizar los mensajes en un solo archivo para la próxima
    public function messages(): array
    {
        return [
            'dui.regex'         => 'El DUI debe tener el formato 00000000-0.',
            'dui.unique'        => 'Este DUI ya está registrado en otro usuario.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}
