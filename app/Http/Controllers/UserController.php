<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class UserController extends Controller
{
    public function ambilSemuaUser()
    {
        $user = User::all();

        if ($user->isEmpty()) {
            return response()->json([
                'message' => 'Belum ada user',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Berhasil mendapatkan data user',
            'code' => 200,
            'data' => $user
        ]);
    }

    public function ubahUser(Request $request, $id)
    {
        // Temukan pengguna berdasarkan ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan.',
                'code' => 404,
            ], 404);
        }

        // Validasi data
        $validatedData = $request->validate([
            'username' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|nullable',
            'role' => 'sometimes|in:super_admin,admin_venue,user', // Sesuaikan role yang tersedia
        ]);

        // Update data pengguna
        $user->update([
            'username' => $validatedData['username'] ?? $user->username,
            'email' => $validatedData['email'] ?? $user->email,
            'password' => isset($validatedData['password']) ? Hash::make($validatedData['password']) : $user->password,
            'role' => $validatedData['role'] ?? $user->role,
        ]);

        return response()->json([
            'message' => 'Sukses mengupdate data user.',
            'code' => 200,
            'data' => $user,
        ], 200);
    }

    public function userById($id)
    {
        $user = User::find($id);


        return response()->json([
            'message' => 'Sukses mengupdate data user.',
            'code' => 200,
            'data' => $user,
        ], 200);
    }
}
