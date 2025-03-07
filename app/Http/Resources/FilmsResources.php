<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FilmsResources extends JsonResource
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
            'judul' => $this->judul,
            'kategori' => [
                'id' => $this->id,
                'nama_kategori' => $this->kategori()->first()?->nama,
            ],
            'jadwal' => $this->jadwal,
            'harga' => $this->harga,
            'status' => $this->status,
            ];
    }
}
