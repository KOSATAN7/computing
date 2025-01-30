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
        'latitude' => $this->latitude,
        'longitude' => $this->longitude,
        'fasilitas' => $this->fasilitas,
        'status' => $this->status,
        'kapasitas' => $this->kapasitas,
        'foto_utama' => $this->foto_utama,
        'foto_foto' => $this->foto_foto,
        'video' => $this->video,
        ];
    }
}
