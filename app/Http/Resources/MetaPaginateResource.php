<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MetaPaginateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'page' => $this->currentPage(),  // Menampilkan halaman saat ini
            'perpage' => $this->perPage(),  // Menampilkan jumlah item per halaman
            'total_page' => $this->lastPage(),  // Menampilkan jumlah total halaman
            'total_item' => $this->total(),  // Menampilkan total item yang ada
        ];
    }
}
