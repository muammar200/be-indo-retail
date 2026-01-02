<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IzinSakitRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan untuk melakukan request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Mendapatkan aturan validasi yang berlaku pada request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'image_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png'], // Validasi file (wajib diisi, harus berupa file dan format yang valid)
            'keterangan' => ['nullable', 'string'], // Keterangan bersifat opsional dan harus berupa string jika ada
            'kategori' => ['required', 'in:Izin,Sakit'], // Kategori harus diisi dan hanya boleh 'Izin' atau 'Sakit'
        ];
    }

    /**
     * Mendapatkan pesan kesalahan validasi.
     *
     * @return array<string, string>
     */
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
