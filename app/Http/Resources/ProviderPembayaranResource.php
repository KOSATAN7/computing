<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderPembayaranResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "metode-pembayaran" => [

                "metode_pembayaran_id" => $this->metode_pembayaran_id,
                "metode_pembayaran" => $this->metodePembayaran->nama ?? null,
            ],
            "provider" => [

                "id" => $this->id,
                "nama" => $this->nama,
                "no_rek" => $this->no_rek,
                "penerima" => $this->penerima,
                "deskripsi" => $this->deskripsi,
                "foto" => $this->foto,
                "status" => $this->aktif ? 'aktif' : 'tidak_aktif',
            ],
        ];
    }
}
