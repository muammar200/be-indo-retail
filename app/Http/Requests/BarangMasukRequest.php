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
            'harga' => 'required|numeric',
            'jumlah' => 'required|integer',
            'sub_kategori' => 'required|string',
            'tanggal_masuk' => 'required|date',
        ];

        return $rules;
    }
}
