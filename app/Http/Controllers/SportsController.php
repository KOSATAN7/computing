<?php

namespace App\Http\Controllers;

use App\Services\SportsApiService;
use Illuminate\Http\Request;

class SportsController extends Controller
{
    private $sportsService;

    public function __construct(SportsApiService $sportsService)
    {
        $this->sportsService = $sportsService;
    }

    public function getCategories()
    {
        $categories = [
            'football' => 'Sepak Bola',
            'basketball' => 'Basket',
            'volleyball' => 'Bola Voli',
        ];

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
    public function getCountriesByCategory($sport)
    {
        try {

            $data = $this->sportsService->makeRequest($sport, 'countries');

            if (empty($data['response'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data negara untuk kategori ini.',
                ], 404);
            }


            $countries = collect($data['response'])->map(function ($country) {
                return [
                    'name' => $country['name'],
                    'code' => $country['code'],
                    'flag' => $country['flag'],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $countries,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function getLeaguesByCategoryCountryAndSeason(Request $request, $sport)
    {
        $season = $request->input('season', now()->year); // Default ke tahun sekarang jika tidak diberikan
        $countryCode = $request->input('country_code'); // Filter berdasarkan kode negara (opsional)

        try {
            // Panggil API untuk mendapatkan daftar liga berdasarkan kategori, kode negara, dan musim
            $params = [
                'season' => $season,
            ];

            if ($countryCode) {
                $params['code'] = $countryCode; // Tambahkan filter kode negara jika disediakan
            }

            $data = $this->sportsService->makeRequest($sport, 'leagues', $params);

            if (empty($data['response'])) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak ada data liga untuk kategori '{$sport}' pada musim ini dengan kode negara '{$countryCode}'.",
                ], 404);
            }

            // Format data liga
            $leagues = collect($data['response'])->map(function ($league) {
                return [
                    'league_id' => $league['league']['id'],
                    'name' => $league['league']['name'],
                    'type' => $league['league']['type'], // League type (e.g., Cup or League)
                    'logo' => $league['league']['logo'],
                    'country' => $league['country']['name'],
                    'country_flag' => $league['country']['flag'] ?? null,
                    'country_code' => $league['country']['code'],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $leagues,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    public function getTeamsByLeague(Request $request, $sport)
    {
        $leagueId = $request->input('league_id');
        $season = $request->input('season', now()->year); // Default ke tahun saat ini

        if (!$leagueId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter league_id wajib diisi.',
            ], 400);
        }

        try {
            // Pastikan season disertakan dalam permintaan
            $data = $this->sportsService->makeRequest($sport, 'teams', [
                'league' => $leagueId,
                'season' => $season,
            ]);

            if (empty($data['response'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data tim untuk liga dan musim ini.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $data['response'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function createSchedule(Request $request)
    {
        $request->validate([
            'home_team' => 'required|string',
            'away_team' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'venue' => 'required|string',
        ]);

        $schedule = [
            'home_team' => $request->home_team,
            'away_team' => $request->away_team,
            'date' => $request->date,
            'time' => $request->time,
            'venue' => $request->venue,
        ];

        return response()->json([
            'success' => true,
            'message' => 'Jadwal berhasil dibuat.',
            'data' => $schedule,
        ]);
    }

    public function getFixturesBySeason(Request $request, $sport)
    {
        $leagueId = $request->input('league_id'); // ID liga (wajib)

        if (!$leagueId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter league_id wajib diisi.',
            ], 400);
        }

        $season = $request->input('season', 2023); // Default ke season 2023

        try {
            // Ambil data fixtures dari API
            $data = $this->sportsService->makeRequest($sport, 'fixtures', [
                'league' => $leagueId,
                'season' => $season,
            ]);

            if (empty($data['response'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada data pertandingan untuk liga dan musim ini.',
                ], 404);
            }

            // Format data pertandingan (jika diperlukan)
            $fixtures = collect($data['response'])->map(function ($fixture) {
                return [
                    'fixture_id' => $fixture['fixture']['id'],
                    'home_team' => $fixture['teams']['home']['name'],
                    'home_team_logo' => $fixture['teams']['home']['logo'],
                    'away_team' => $fixture['teams']['away']['name'],
                    'away_team_logo' => $fixture['teams']['away']['logo'],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $fixtures,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
