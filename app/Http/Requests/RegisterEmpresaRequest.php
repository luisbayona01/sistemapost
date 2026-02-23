<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterEmpresaRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Plan
            'plan_id' => ['required', 'exists:saas_plans,id'],

            // Empresa
            'empresa_nombre' => ['required', 'string', 'max:255'],
            'nit' => ['required', 'string', 'max:50', 'unique:empresa,ruc'],
            'empresa_email' => ['nullable', 'email', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'moneda_id' => ['required', 'exists:monedas,id'],

            // Usuario admin
            'nombre_contacto' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                //'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'plan_id.required' => 'Debe seleccionar un plan.',
            'plan_id.exists' => 'El plan seleccionado no es válido.',
            'empresa_nombre.required' => 'El nombre de la empresa es requerido.',
            'nit.required' => 'El NIT es requerido.',
            'nit.unique' => 'Ya existe una empresa registrada con este NIT.',
            'email.required' => 'El email es requerido.',
            'email.unique' => 'Este email ya está registrado.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener mayúsculas, minúsculas y números.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nombre_contacto.required' => 'El nombre del contacto es requerido.',
            'moneda_id.required' => 'Debe seleccionar una moneda.',
            'moneda_id.exists' => 'La moneda seleccionada no es válida.',
        ];
    }
}
