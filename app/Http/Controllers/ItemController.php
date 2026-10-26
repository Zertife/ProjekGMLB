<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Item;
use App\Http\Requests\SaveItemRequest;
use App\Http\Requests\ItemIndexRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Tampilkan daftar barang dengan filter kategori, kondisi, pencarian, dan pagination.
     */
    public function index(ItemIndexRequest $request)
    {
        $filters = $request->validated();
        $query = Item::with('category');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (! empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories'));
    }

    /**
     * Form tambah barang baru (Admin).
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('items.create', compact('categories'));
    }

    /**
     * Simpan barang baru ke database (Admin).
     */
    public function store(SaveItemRequest $request)
    {
        $item = Item::create($request->validated());

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'create_item',
            'description' => "Menambahkan barang baru: {$item->name} [{$item->code}] dengan stok {$item->stock}",
        ]);

        return redirect()->route('items.index')
            ->with('success', "Barang \"{$item->name}\" berhasil ditambahkan.");
    }

    /**
     * Form edit barang (Admin).
     */
    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();
        return view('items.edit', compact('item', 'categories'));
    }

    /**
     * Perbarui data barang (Admin).
     */
    public function update(SaveItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'update_item',
            'description' => "Memperbarui barang: {$item->name} [{$item->code}] (Stok: {$item->stock}, Kondisi: {$item->condition})",
        ]);

        return redirect()->route('items.index')
            ->with('success', "Barang \"{$item->name}\" berhasil diperbarui.");
    }

    /**
     * Hapus barang dengan Soft Delete (Admin).
     */
    public function destroy(Item $item)
    {
        // Cek jika ada peminjaman aktif
        $activeLoans = $item->loans()->whereIn('status', ['dipinjam', 'terlambat'])->count();
        if ($activeLoans > 0) {
            return redirect()->route('items.index')
                ->with('error', "Barang \"{$item->name}\" tidak dapat dihapus karena masih sedang dipinjam ({$activeLoans} transaksi aktif).");
        }

        $itemName = $item->name;
        $item->delete(); // Soft delete bawaan

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'delete_item',
            'description' => "Menghapus barang (soft delete): {$itemName}",
        ]);

        return redirect()->route('items.index')
            ->with('success', "Barang \"{$itemName}\" berhasil dihapus.");
    }
}
