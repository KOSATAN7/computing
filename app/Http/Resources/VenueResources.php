<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResources extends JsonResource
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
        'alamat' => $this->alamat,
        'kontak' => $this->kontak,
        'kota' => $this->kota,
        'fasilitas' => $this->fasilitas,
        'status' => $this->status,
        'kapasitas' => $this->kapasitas,
        'foto' => $this->foto,
        'video' => $this->video,

        
        ];
    }
}
