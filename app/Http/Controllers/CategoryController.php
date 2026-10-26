<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Http\Requests\SaveCategoryRequest;
use App\Http\Requests\CategoryIndexRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Daftar kategori dengan search & pagination.
     */
    public function index(CategoryIndexRequest $request)
    {
        $filters = $request->validated();
        $query = Category::withCount(['items' => fn ($items) => $items->withTrashed()]);

        if (! empty($filters['search'])) {
            $query->where('name', 'like', '%'.trim($filters['search']).'%');
        }

        $categories = $query->latest()->paginate(10)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Form tambah kategori.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Simpan kategori baru.
     */
    public function store(SaveCategoryRequest $request)
    {
        $category = Category::create($request->validated());

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'create_category',
            'description' => 'Menambahkan kategori baru: ' . $category->name,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil ditambahkan.');
    }

    /**
     * Form edit kategori.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update kategori.
     */
    public function update(SaveCategoryRequest $request, Category $category)
    {
        $oldName = $category->name;
        $category->update($request->validated());

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'update_category',
            'description' => 'Memperbarui kategori: "' . $oldName . '" → "' . $category->name . '"',
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori "' . $category->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus kategori (cek jika masih ada barang).
     */
    public function destroy(Category $category)
    {
        if ($category->items()->withTrashed()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori "' . $category->name . '" tidak dapat dihapus karena masih memiliki barang terkait.');
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'delete_category',
            'description' => 'Menghapus kategori: ' . $name,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori "' . $name . '" berhasil dihapus.');
    }
}
