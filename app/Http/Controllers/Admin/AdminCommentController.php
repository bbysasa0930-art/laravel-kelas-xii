<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kritik;
use Illuminate\Http\Request;

class AdminCommentController extends Controller
{
    /**
     * Menampilkan daftar semua komentar pengguna dengan pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Query dengan Prepared Statements ORM dan eager loading
        $comments = Kritik::with(['user', 'film'])
            ->when($search, function ($query, $search) {
                return $query->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('film', function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.comments.index', compact('comments', 'search'));
    }

    /**
     * Menghapus data komentar pengguna.
     */
    public function destroy(Kritik $comment)
    {
        $comment->delete();

        return redirect()->route('admin.comments.index')->with('success', 'Komentar berhasil dihapus.');
    }
}

