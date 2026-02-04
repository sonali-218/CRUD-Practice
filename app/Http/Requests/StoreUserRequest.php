<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Para validar los datos de entrada de forma limpia y ordenada, se recomienda usar un FormRequest.

class StoreUserRequest extends FormRequest
{
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
            'name' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'hiring_date'  => ['sometimes', 'date'], 
            'dui'          => ['required', 'unique:users,dui', 'regex:/^\d{8}-\d{1}$/'],
            'phone_number' => ['nullable', 'string'], // solo este es opcional
            'birth_date'   => ['required', 'date', 'before:today'],
        ];
    }

    // LÓGICA DE NEGOCIO
    protected function prepareForValidation()
    {
        // confirmar si trae fecha de hire
        if (!$this->has('hiring_date')) {
            $this->merge([
                'hiring_date' => now()->format('Y-m-d'),
            ]);
        }
    }

    // centralizar reglas de validación + mensajes
    public function messages(): array 
    {
        return [
            'dui.regex' => 'El DUI debe tener el formato 00000000-0',
            'dui.unique'        => 'Este DUI ya existe y está en uso',
            'birth_date.before' => 'La fecha de nacimiento debe ser una fecha pasada',
        ];
    }
}

