<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangMasukRequest extends FormRequest
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
            'kode_barang' => 'required|string', // Kode barang harus diisi dan berupa string
            'nama' => 'required|string', // Nama barang harus diisi dan berupa string
            'harga' => 'required', // Harga barang harus diisi
            'jumlah' => 'required|integer', // Jumlah barang harus diisi dan berupa angka bulat
            'sub_kategori' => 'required|string', // Sub kategori barang harus diisi dan berupa string
            'tanggal_masuk' => 'required|date_format:d-m-Y', // Tanggal masuk barang harus diisi dengan format dd-mm-yyyy
        ];

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
            'kode_barang.required' => 'Kode barang harus diisi.',
            'kode_barang.string' => 'Kode barang harus berupa teks.',
            'nama.required' => 'Nama barang harus diisi.',
            'nama.string' => 'Nama barang harus berupa teks.',
            'harga.required' => 'Harga barang harus diisi.',
            'jumlah.required' => 'Jumlah barang harus diisi.',
            'jumlah.integer' => 'Jumlah barang harus berupa angka bulat.',
            'sub_kategori.required' => 'Sub kategori barang harus diisi.',
            'sub_kategori.string' => 'Sub kategori barang harus berupa teks.',
            'tanggal_masuk.required' => 'Tanggal masuk barang harus diisi.',
            'tanggal_masuk.date_format' => 'Tanggal masuk barang harus menggunakan format dd-mm-yyyy.',
        ];
    }
}
