<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Api\Clients;
use App\Models\Main\Archives;
use App\Models\Main\Document_file;
use App\Models\Main\Document_history;
use App\Models\Master\Categories;
use App\Models\Master\location;
use App\Models\Master\Tags;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class TokenController extends Controller
{
    public function Get_token(Request $request)
    {
        // Validasi client_id dan client_secret
        $request->validate([
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        // Simulasikan mendapatkan pengguna berdasarkan client_id dan client_secret
        $client = Clients::where('client_id', $request->client_id)
            ->where('client_secret', $request->client_secret)
            ->first();

        if (!$client) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Membuat token menggunakan Sanctum
        $token = $client->createToken('API Token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    public function getcodearsip(Request $request)
    {
        // Autentikasi via Sanctum
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validasi input
        $request->validate([
            'file'         => 'required|file|mimes:pdf|max:5048',
            'date'         => 'required|date',
            'title'        => 'required|string',
            'category_id'  => 'required|string',
            'tag_ids'      => 'required|array',
            'location_id'  => 'required|string',
            'keterangan'   => 'required|string',
            'note'         => 'required|string',
        ]);

        // Simpan arsip
        $code = generateKodeAcak();
        $data = [
            'code'         => $code,
            'number'       => generateNomorArsip(),
            'ref_number'   => _escape($request->input('ref_nomor')),
            'tag_ids'      => implode(',', array_map('htmlspecialchars', $request->input('tag_ids', []))),
            'date'         => $request->input('date') . date(' H:i:s'),
            'description'  => _escape($request->input('keterangan')),
            'note'         => _escape($request->input('note')),
            'title'        => _escape($request->input('title')),
            'category_id'  => _escape($request->input('category_id')),
            'location_id'  => _escape($request->input('location_id')),
            'status_id'    => 1,
            'created_from' => 'API',
            'created_by'   => $user->id,
        ];

        $document = Archives::create($data);
        $document_id = $document->id;

        // Simpan file PDF
        if ($request->hasFile('file')) {
            $pdfFile = $request->file('file');
            $namaFilePdf = $pdfFile->hashName();
            $pdfFile->storeAs('main/arsip', $namaFilePdf, 'uploads');
        }

        // Simpan metadata file
        Document_file::create([
            'document_id'   => $document_id,
            'name'          => $namaFilePdf,
            'description'   => _escape($request->input('keterangan')),
            'sort'          => Document_file::max('sort') + 1,
            'created_from'  => 'API',
            'created_by'    => $user->id,
        ]);

        // Simpan riwayat
        Document_history::create([
            'document_id'   => $document_id,
            'description'   => 'Arsip baru ditambahkan lewat API',
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->header('User-Agent'),
            'created_from'  => 'API',
            'created_by'    => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Arsip berhasil disimpan',
            'code' => $code,
        ], 201);
    }

    public function getFormOptions(Request $request)
    {
        $user = $request->user();

        $categories = Categories::select('id', 'name')->get();
        $tags = Tags::select('id', 'name')->get();
        $locations = location::select('id', 'name')->get();

        return response()->json([
            'success' => true,
            'categories' => $categories,
            'tags' => $tags,
            'locations' => $locations,
        ]);
    }
}
