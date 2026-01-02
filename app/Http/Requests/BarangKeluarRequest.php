<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangKeluarRequest extends FormRequest
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
            'barang_id' => 'required|exists:stok,id', // ID barang harus diisi dan ada di tabel stok
            'tanggal_keluar' => 'required|date_format:d-m-Y', // Format tanggal keluar harus dd-mm-yyyy
            'toko_tujuan' => 'required|string', // Toko tujuan harus diisi dan berupa teks
            'jumlah' => 'required|integer|min:1', // Jumlah barang harus diisi, berupa angka bulat dan minimal 1
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
            'barang_id.required' => 'ID barang harus diisi.',
            'barang_id.exists' => 'ID barang yang dimasukkan tidak ditemukan di stok.',
            'tanggal_keluar.required' => 'Tanggal keluar barang harus diisi.',
            'tanggal_keluar.date_format' => 'Tanggal keluar barang harus menggunakan format dd-mm-yyyy.',
            'toko_tujuan.required' => 'Toko tujuan harus diisi.',
            'toko_tujuan.string' => 'Toko tujuan harus berupa teks.',
            'jumlah.required' => 'Jumlah barang harus diisi.',
            'jumlah.integer' => 'Jumlah barang harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah barang harus lebih dari atau sama dengan 1.',
        ];
    }
}
