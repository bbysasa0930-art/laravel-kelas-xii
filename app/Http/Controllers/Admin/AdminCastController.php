<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cast;
use App\Models\Peran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCastController extends Controller
{
    /**
     * Menampilkan daftar semua data cast dengan pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Query dengan Prepared Statements ORM
        $casts = Cast::withCount('films')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.casts.index', compact('casts', 'search'));
    }

    /**
     * Menampilkan form tambah cast baru.
     */
    public function create()
    {
        return view('admin.casts.create');
    }

    /**
     * Menyimpan data cast baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi server-side
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:130',
            'bio' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Maksimal 2 MB
        ], [
            'name.required' => 'Nama cast wajib diisi.',
            'age.required' => 'Umur cast wajib diisi.',
            'age.integer' => 'Umur harus berupa angka.',
            'bio.required' => 'Biografi cast wajib diisi.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal adalah 2 MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('casts', 'public');
            $fotoPath = 'storage/' . $path;
        }

        $cast = Cast::create([
            'name' => $request->name,
            'age' => $request->age,
            'bio' => $request->bio,
            'foto' => $fotoPath,
        ]);

        return redirect()->route('admin.casts.index')->with('success', 'Cast "' . $cast->name . '" berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit data cast.
     */
    public function edit(Cast $cast)
    {
        return view('admin.casts.edit', compact('cast'));
    }

    /**
     * Memperbarui data cast di database.
     */
    public function update(Request $request, Cast $cast)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:1|max:130',
            'bio' => 'required|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama cast wajib diisi.',
            'age.required' => 'Umur cast wajib diisi.',
            'age.integer' => 'Umur harus berupa angka.',
            'bio.required' => 'Biografi cast wajib diisi.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto yang diperbolehkan hanya JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal adalah 2 MB.',
        ]);

        $data = [
            'name' => $request->name,
            'age' => $request->age,
            'bio' => $request->bio,
        ];

        // Ganti foto jika ada file foto baru
        if ($request->hasFile('foto')) {
            if ($cast->foto) {
                $oldPath = str_replace('storage/', '', $cast->foto);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('foto')->store('casts', 'public');
            $data['foto'] = 'storage/' . $path;
        }

        $cast->update($data);

        return redirect()->route('admin.casts.index')->with('success', 'Data cast "' . $cast->name . '" berhasil diperbarui.');
    }

    /**
     * Menghapus data cast dan relasi perannya di tabel perans.
     */
    public function destroy(Cast $cast)
    {
        $name = $cast->name;

        // 1. Hapus relasi cast pada tabel perans
        Peran::where('cast_id', $cast->id)->delete();

        // 2. Hapus file foto jika ada
        if ($cast->foto) {
            $oldPath = str_replace('storage/', '', $cast->foto);
            Storage::disk('public')->delete($oldPath);
        }

        // 3. Hapus data cast
        $cast->delete();

        return redirect()->route('admin.casts.index')->with('success', 'Cast "' . $name . '" beserta relasi perannya berhasil dihapus.');
    }
}

