<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VisionMision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;

class VisionMisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = VisionMision::orderBy('created_at', 'DESC');
        if ($search) {
            # code...
            $vision_misions = VisionMision::where('visi', 'like', '%' . $search . '%')->orderBy('visi', 'DESC')->paginate(4)->appends(['search' => $search]);
        } else {

            $vision_misions = VisionMision::orderBy('visi', 'DESC')->paginate(4); // Ganti 10 dengan jumlah item per halaman yang diinginkan
        }
        $vision_misions = $query->paginate(10)->appends($request->query());
        return view('pages.admin.vision_mision.index', compact('vision_misions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.vision_mision.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'visi' => 'required|string|max:500|min:3|unique:vision_misions',
            'misi' => 'required|string|max:500|min:10',
        ]);

        // Jika validasi gagal, kembali dengan pesan kesalahan
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $slug = Str::slug($request->visi);

        VisionMision::create([
            'visi' => $request->visi,
            'misi' => $request->misi,
            'slug' => $slug,
        ]);
        return redirect()->back()->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VisionMision $visionmision)
    {
        // Variabel $visionMision sudah berisi data yang dicari secara otomatis
        return view('pages.admin.vision_mision.edit', compact('visionmision'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $vision_mision = VisionMision::where('slug', $slug)->first();
        $validator = Validator::make($request->all(), [
            'visi' => 'required|string|max:500|min:3|unique:vision_misions,slug,' . $vision_mision->slug,
            'misi' => 'required|string|min:10',
        ]);

        // Jika validasi gagal, kembali dengan pesan kesalahan
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $vision_mision->update([
            'visi' => $request->visi,
            'misi' => $request->misi,
            'slug' => $vision_mision->slug,
        ]);
        return redirect()->back()->with('success', 'Data berhasil diubah!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VisionMision $visionmision)
    {
        // Hapus data yang sudah ditemukan secara otomatis oleh Laravel.
        $visionmision->delete();

        // Untuk hapus permanen jika menggunakan SoftDeletes, gunakan:
        // $visionmision->forceDelete();

        // Redirect kembali ke halaman index dengan pesan sukses.
        return redirect()->route('admin.visionmision.index')
                         ->with('success', 'Data Visi & Misi berhasil dihapus!');
    }
}
