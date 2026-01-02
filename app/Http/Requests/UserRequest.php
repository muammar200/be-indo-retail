<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'], // Nama wajib diisi dan harus berupa string dengan panjang maksimal 255 karakter
            'no_hp' => [
                'required', // Nomor handphone wajib diisi
                Rule::unique('users', 'no_hp')->ignore($this->route('user')), // Nomor handphone harus unik, kecuali untuk pengguna yang sedang diupdate
                'regex:/^62\d{8,15}$/' // Nomor handphone harus dimulai dengan 62 dan terdiri dari 8 hingga 15 digit
            ],
            'jabatan' => ['required', 'in:Pimpinan,Staff,Karyawan Pelapor,Karyawan Biasa'], // Jabatan wajib diisi dan hanya boleh salah satu dari nilai yang valid
            'password' => ['required', 'string', 'min:8'], // Password wajib diisi, harus berupa string dan minimal 8 karakter
        ];

        // Jika metode request adalah PUT (untuk update), password dapat dikosongkan
        if ($this->isMethod('PUT')) {
            $rules['password'] = ['nullable'];
        }

        return $rules;
    }

    /**
     * Mendapatkan pesan kesalahan validasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama harus diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'no_hp.required' => 'Nomor handphone harus diisi.',
            'no_hp.unique' => 'Nomor handphone sudah terdaftar.',
            'no_hp.regex' => 'Nomor handphone harus dimulai dengan 62 dan terdiri dari 8 hingga 15 digit.',
            'jabatan.required' => 'Jabatan harus diisi.',
            'jabatan.in' => 'Jabatan yang dimasukkan tidak valid. Pilih salah satu dari: Pimpinan, Staff, Karyawan Pelapor, Karyawan Biasa.',
            'password.required' => 'Password harus diisi.',
            'password.string' => 'Password harus berupa teks.',
            'password.min' => 'Password harus terdiri dari minimal 8 karakter.',
            'password.nullable' => 'Password dapat dikosongkan.',
        ];
    }
}
