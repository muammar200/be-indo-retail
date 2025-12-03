<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangKeluarRequest extends FormRequest
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
            'barang_id' => 'required|exists:stok,id',
            'tanggal_keluar' => 'required|date',
            'toko_tujuan' => 'required|string',
            'jumlah' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'barang_id.required' => 'ID barang harus diisi.',
            'barang_id.exists' => 'ID barang yang dimasukkan tidak ditemukan di stok.',
            'tanggal_keluar.required' => 'Tanggal keluar barang harus diisi.',
            'tanggal_keluar.date' => 'Tanggal keluar barang harus berupa format tanggal yang valid.',
            'toko_tujuan.required' => 'Toko tujuan harus diisi.',
            'toko_tujuan.string' => 'Toko tujuan harus berupa teks.',
            'jumlah.required' => 'Jumlah barang harus diisi.',
            'jumlah.integer' => 'Jumlah barang harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah barang harus lebih dari atau sama dengan 1.',
        ];
    }
}
