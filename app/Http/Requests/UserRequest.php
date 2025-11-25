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
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'no_hp' => [
                'required',
                Rule::unique('users', 'no_hp')->ignore($this->route('user')),
            ],
            'jabatan' => ['required', 'in:Pimpinan,Staff,Karyawan - Pelapor,Karyawan - Biasa'],
            'password' => ['required', 'string', 'min:8'],
        ];

        if ($this->isMethod('PUT')) {
            $rules['password'] = ['nullable'];
        }

        return $rules;
    }
}
