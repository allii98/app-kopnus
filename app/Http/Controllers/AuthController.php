<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            // Validasi data yang dikirim user
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:freelancer,employer',
            ]);

            // Buat user baru di database
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            // Buat token untuk autentikasi
            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'status' => true,
                'pesan' => 'Registrasi berhasil.',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Validasi gagal.',
                'error' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Terjadi kesalahan pada database.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Terjadi kesalahan tidak terduga.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function login(Request $request)
    {
        try {
            // Validasi input
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Cari user berdasarkan email
            $user = User::where('email', $credentials['email'])->first();

            // Periksa kredensial
            if (!$user || !Hash::check($credentials['password'], $user->password)) {
                return response()->json([
                    'status' => false,
                    'pesan' => 'Email atau password salah.'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Buat token
            $token = $user->createToken('token')->plainTextToken;

            return response()->json([
                'status' => true,
                'pesan' => 'Login berhasil.',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ]
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Validasi gagal.',
                'error' => $e->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Kesalahan pada database.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Terjadi kesalahan tidak terduga.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();

            return response()->json([
                'status' => true,
                'pesan' => 'Berhasil logout.'
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Gagal logout.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

