<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    
    public function index()
    {
        try {
            $jobs = Job::where('status', 'published')->with('user')->get();

            return response()->json([
                'status' => true,
                'pesan' => 'Daftar job berhasil diambil.',
                'data' => $jobs
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Gagal mengambil data job.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
            ]);

            $job = Job::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'status' => 'draft', // default status
            ]);

            return response()->json([
                'status' => true,
                'pesan' => 'Job berhasil dibuat.',
                'data' => $job
            ], Response::HTTP_CREATED);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Validasi gagal.',
                'error' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);

        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Gagal menyimpan data ke database.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Terjadi kesalahan saat membuat job.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function publish($id)
    {
        try {
            $job = Job::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            $job->status = 'published';
            $job->save();

            return response()->json([
                'status' => true,
                'pesan' => 'Job berhasil dipublish.',
                'data' => $job
            ], Response::HTTP_OK);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Job tidak ditemukan atau bukan milik Anda.'
            ], Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Gagal mempublish job.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function myJobs()
    {
        try {
            $jobs = Auth::user()->jobs()->get();

            return response()->json([
                'status' => true,
                'pesan' => 'Daftar job Anda berhasil diambil.',
                'data' => $jobs
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'pesan' => 'Gagal mengambil job Anda.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}

