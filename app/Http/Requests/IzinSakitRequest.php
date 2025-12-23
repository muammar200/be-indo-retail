<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IzinSakitRequest extends FormRequest
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
            'image_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png'],
            'keterangan' => ['nullable', 'string'],
            'kategori' => ['required', 'in:Izin,Sakit'],
        ];
    }

    public function messages(): array
    {
        return [
            'image_proof.required' => 'Bukti izin/sakit harus diunggah.',
            'image_proof.file' => 'Bukti izin/sakit harus berupa file yang valid.',
            'image_proof.mimes' => 'Bukti izin/sakit harus berformat PDF, JPG, JPEG, atau PNG.',
            'keterangan.string' => 'Keterangan harus berupa teks.',
            'kategori.required' => 'Kategori harus diisi.',
            'kategori.in' => 'Kategori yang valid adalah Sakit atau Izin.',
        ];
    }
}
