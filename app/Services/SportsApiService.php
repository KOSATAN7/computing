<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SportsApiService
{
    private $apiConfigs;

    public function __construct()
    {
        $this->apiConfigs = [
            'football' => [
                'baseUrl' => 'https://v3.football.api-sports.io',
                'apiKey' => config('app.sport_api_key'),
            ],
            'basketball' => [
                'baseUrl' => 'https://v1.basketball.api-sports.io',
                'apiKey' => config('app.sport_api_key'),
            ],
            'volleyball' => [
                'baseUrl' => 'https://v1.volleyball.api-sports.io',
                'apiKey' => config('app.sport_api_key'),
            ],
        ];
    }

    public function makeRequest($sport, $endpoint, $params = [])
    {
        $config = $this->apiConfigs[$sport] ?? null;

        if (!$config || !$config['apiKey']) {
            throw new \Exception("API untuk {$sport} tidak tersedia atau API Key tidak ditemukan.");
        }

        $response = Http::withHeaders([
            'x-rapidapi-host' => $config['baseUrl'],
            'x-rapidapi-key' => $config['apiKey'],
        ])->get("{$config['baseUrl']}/{$endpoint}", $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception("Gagal mengambil data dari API {$sport}: " . $response->body());
    }
}
