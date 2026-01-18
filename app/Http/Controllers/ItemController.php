<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ItemController extends Controller
{
    /**
     * Display a listing of the items.
     */
    public function index(): View
    {
        $items = Item::with('supplier')->paginate(10);
        return view('items.index', compact('items'));
    }

    /**
     * Show the form for creating a new item.
     */
    public function create(): View
    {
        $suppliers = Supplier::all();
        return view('items.create', compact('suppliers'));
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_number' => 'required|string|unique:items|max:100',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Item berhasil ditambahkan');
    }

    /**
     * Display the specified item.
     */
    public function show(Item $item): View
    {
        $stocks = $item->stocks()->with('location')->get();
        $totalStock = $item->getTotalStock();
        return view('items.show', compact('item', 'stocks', 'totalStock'));
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item): View
    {
        $suppliers = Supplier::all();
        return view('items.edit', compact('item', 'suppliers'));
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'item_number' => 'required|string|unique:items,item_number,' . $item->id . '|max:100',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'supplier_id' => 'required|exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
        ]);

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Item berhasil diupdate');
    }

    /**
     * Remove the specified item from storage.
     */
    public function destroy(Item $item): RedirectResponse
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item berhasil dihapus');
    }
}
