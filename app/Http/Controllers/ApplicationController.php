<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\Application;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{
    public function apply(Request $request, $job_id)
    {
        try {
            // Pastikan job valid dan sudah dipublikasikan
            $job = Job::where('id', $job_id)
                ->where('status', 'published')
                ->firstOrFail();

            // Cek apakah user sudah melamar sebelumnya
            $existing = Application::where('job_id', $job_id)
                ->where('user_id', Auth::id())
                ->first();

            if ($existing) {
                return response()->json([
                    'status' => false,
                    'pesan' => 'Anda sudah melamar pada pekerjaan ini.'
                ], Response::HTTP_CONFLICT);
            }

            // Validasi file CV
            $validated = $request->validate([
                'cv_file' => 'required|file|mimes:pdf,doc,docx|max:2048',
            ]);

            // Simpan file
            $filePath = $request->file('cv_file')->store('cv', 'public');

            // Simpan data lamaran
            $application = Application::create([
                'job_id' => $job_id,
                'user_id' => Auth::id(),
                'cv_file_path' => $filePath,
            ]);

            return response()->json([
                'status' => true,
                'pesan' => 'Lamaran berhasil dikirim.',
                'cv_url' => asset('storage/' . $filePath)
            ], Response::HTTP_CREATED);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Pekerjaan tidak ditemukan atau belum dipublikasikan.'
            ], Response::HTTP_NOT_FOUND);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Validasi gagal.',
                'error' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Gagal menyimpan data lamaran ke database.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Terjadi kesalahan saat memproses lamaran.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function viewApplicants($job_id)
    {
        try {
            // Hanya employer pemilik job yang boleh melihat
            $job = Job::where('id', $job_id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $applications = $job->applications()->with('user')->get();

            return response()->json([
                'status' => true,
                'pesan' => 'Daftar pelamar berhasil diambil.',
                'data' => $applications
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Job tidak ditemukan atau bukan milik Anda.'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Gagal mengambil daftar pelamar.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}

