<?php

namespace App\Http\Resources\Absensi;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuktiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->name,
            // 'tanggal' => $this->tanggal,
            'tanggal' => Carbon::parse($this->tanggal)->format('d-m-Y'),
            'kategori' => $this->kategori,
            'keterangan' => $this->keterangan,
            'image' => url('storage/images/absensi/'.$this->image_proof),
        ];
    }
}
