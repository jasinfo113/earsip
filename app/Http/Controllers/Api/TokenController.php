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
<<<<<<< HEAD
        // $user = Auth::guard('sanctum')->user();
        // if (!$user) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        //dd($request->all());
        $client_id = $request->header('client_id');
        $client_secret = $request->header('client_secret');

        // Cek apakah client_id dan client_secret valid
        $client = Clients::where('client_id', $client_id)
            ->where('client_secret', $client_secret)
            ->first();
        if (!$client) {
=======
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
>>>>>>> cb2dd717936d9f5d488e7ae409d797bb07f518c3
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        // $client_id = $request->header('client_id');
        // $client_secret = $request->header('client_secret');

        // // Cek apakah client_id dan client_secret valid
        // $client = Clients::where('client_id', $client_id)
        //     ->where('client_secret', $client_secret)
        //     ->first();

        // if (!$client) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }
        // Validasi input
        $request->validate([
            'file'         => 'required|file|mimes:pdf|max:5048',
            'date'         => 'required|date',
            'title'        => 'required|string',
            'category_id'  => 'required|string',
            'location_id'  => 'required|string',
            'keterangan'   => 'required|string',
            'note'         => 'required|string',
        ]);

        $isUpdate = $request->has('code');

        if ($isUpdate) {
            // UPDATE arsip
            $document = Archives::findOrFail($request->code);
            $document->update([
                'ref_number'   => _escape($request->input('ref_nomor')),
                'tag_ids'      => implode(',', array_map('htmlspecialchars', $request->input('tag_ids', []))),
                'date'         => $request->input('date') . date('H:i:s'),
                'description'  => _escape($request->input('keterangan')),
                'note'         => _escape($request->input('note')),
                'title'        => _escape($request->input('title')),
                'category_id'  => _escape($request->input('category_id')),
                'location_id'  => _escape($request->input('location_id')),
                'updated_from' => 'API',
                'updated_by'   => $request->input('nrk'),
            ]);
        } else {
            // Simpan arsip baru
            $code = generateKodeAcak();
            $document = Archives::create([
                'code'         => $code,
                'number'       => generateNomorArsip(),
                'ref_number'   => _escape($request->input('ref_nomor')),
                'tag_ids'      => implode(',', array_map('htmlspecialchars', $request->input('tag_ids', []))),
                'date'         => $request->input('date') . date('H:i:s'),
                'description'  => _escape($request->input('keterangan')),
                'note'         => _escape($request->input('note')),
                'title'        => _escape($request->input('title')),
                'category_id'  => _escape($request->input('category_id')),
                'location_id'  => _escape($request->input('location_id')),
                'status_id'    => 1,
                'created_from' => 'API',
                'created_by'   => $request->input('nrk'),
            ]);
        }

        $document_id = $document->id;

        // Simpan file PDF
        if ($request->hasFile('file')) {
            $pdfFile = $request->file('file');
            $namaFilePdf = $pdfFile->hashName();
            $pdfFile->storeAs('main/arsip', $namaFilePdf, 'uploads');

            // Simpan metadata file (baru)
            Document_file::create([
                'document_id'   => $document_id,
                'name'          => $namaFilePdf,
                'description'   => _escape($request->input('keterangan')),
                'sort'          => Document_file::max('sort') + 1,
                'created_from'  => 'API',
                'created_by'    => $request->input('nrk'),
            ]);
        }

        // Simpan riwayat
        Document_history::create([
            'document_id'   => $document_id,
            'description'   => $isUpdate
                ? 'Arsip diperbarui lewat API'
                : 'Arsip baru ditambahkan lewat API',
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->header('User-Agent'),
            'created_from'  => 'API',
            'created_by'    => $request->input('nrk'),
        ]);

        return response()->json([
            'success' => true,
            'message' => $isUpdate ? 'Arsip berhasil diperbarui' : 'Arsip berhasil disimpan',
            'code'    => $document->code,
        ], $isUpdate ? 200 : 201);
    }


    public function getFormOptions(Request $request)
    {
        // $client_id = $request->header('client_id');
        // $client_secret = $request->header('client_secret');

        // // Cek apakah client_id dan client_secret valid
        // $client = Clients::where('client_id', $client_id)
        //     ->where('client_secret', $client_secret)
        //     ->first();

        // if (!$client) {
        //     return response()->json(['error' => 'Unauthorized'], 401);
        // }

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
