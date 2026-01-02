<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermintaanBarangRequest extends FormRequest
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
            'nama_barang' => 'required|string', // Nama barang wajib diisi dan harus berupa string
            'tanggal_permintaan' => 'required|date_format:d-m-Y', // Tanggal permintaan wajib diisi dan harus menggunakan format dd-mm-yyyy
            'jumlah_permintaan' => 'required|integer', // Jumlah permintaan wajib diisi dan harus berupa angka bulat
            'modal' => 'required', // Modal wajib diisi
            'nomor_npwp' => 'required|string', // Nomor NPWP wajib diisi dan harus berupa string
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
            'nama_barang.required' => 'Nama barang harus diisi.',
            'nama_barang.string' => 'Nama barang harus berupa teks.',
            'tanggal_permintaan.required' => 'Tanggal permintaan harus diisi.',
            'tanggal_permintaan.date_format' => 'Tanggal permintaan harus menggunakan format dd-mm-yyyy.',
            'jumlah_permintaan.required' => 'Jumlah permintaan harus diisi.',
            'jumlah_permintaan.integer' => 'Jumlah permintaan harus berupa angka bulat.',
            'modal.required' => 'Modal harus diisi.',
            'nomor_npwp.required' => 'Nomor NPWP harus diisi.',
            'nomor_npwp.string' => 'Nomor NPWP harus berupa teks.',
        ];
    }
}
