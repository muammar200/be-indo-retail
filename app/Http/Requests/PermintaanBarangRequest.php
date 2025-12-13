<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermintaanBarangRequest extends FormRequest
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
            'nama_barang' => 'required|string',
            'tanggal_permintaan' => 'required|date_format:d-m-Y',
            'jumlah_permintaan' => 'required|integer',
            'modal' => 'required',
            'nomor_npwp' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama barang harus diisi.',
            'nama_barang.string' => 'Nama barang harus berupa teks.',
            'tanggal_permintaan.required' => 'Tanggal permintaan harus diisi.',
            'tanggal_permintaan.date_format' => 'Tanggal keluar barang harus menggunakan format dd-mm-yyyy.',
            'jumlah_permintaan.required' => 'Jumlah permintaan harus diisi.',
            'jumlah_permintaan.integer' => 'Jumlah permintaan harus berupa angka bulat.',
            'modal.required' => 'Modal harus diisi.',
            'nomor_npwp.required' => 'Nomor NPWP harus diisi.',
            'nomor_npwp.string' => 'Nomor NPWP harus berupa teks.',
        ];
    }
}
