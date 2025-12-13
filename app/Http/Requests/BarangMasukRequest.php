<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BarangMasukRequest extends FormRequest
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
            'kode_barang' => 'required|string',
            'nama' => 'required|string',
            'harga' => 'required',
            'jumlah' => 'required|integer',
            'sub_kategori' => 'required|string',
            'tanggal_masuk' => 'required|date_format:d-m-Y',
        ];

        return $rules;
    }

    /**
     * Get custom error messages.
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
