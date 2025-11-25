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
            'page' => $this->currentPage(),
            'perpage' => $this->perPage(),
            'total_page' => $this->lastPage(),
            'total_item' => $this->total(),
        ];
    }
}
