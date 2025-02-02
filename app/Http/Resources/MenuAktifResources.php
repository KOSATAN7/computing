<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuAktifResources extends JsonResource
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
        'nama' => $this->nama,
        'deskripsi' => $this->deskripsi,
        'harga' => $this->harga,
        'foto' => $this->foto,
        'kategori' => $this->kategori,
        'kesediaan' => $this->kesediaan ? 'tersedia' : 'tidak_tersedia'
       
        ];
    }
}
