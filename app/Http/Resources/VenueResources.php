<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VenueResources extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'alamat' => $this->alamat,
            'kontak' => $this->kontak,
            'kota' => $this->kota,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'fasilitas' => is_array($this->fasilitas) ? $this->fasilitas : json_decode($this->fasilitas, true), // Cek jika sudah array
            'status' => $this->status,
            'kapasitas' => $this->kapasitas,
            'foto_utama' => $this->foto_utama ? asset('storage/' . $this->foto_utama) : null,
            'foto_foto' => is_array($this->foto_foto) 
                ? collect($this->foto_foto)->map(fn ($foto) => asset('storage/' . $foto)) 
                : collect(json_decode($this->foto_foto, true))->map(fn ($foto) => asset('storage/' . $foto)),
            'video' => $this->video ? asset('storage/' . $this->video) : null,
        ];
    }
}
