<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::when($request->search, fn ($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('sku', 'like', "%$s%");
            }))
            ->when($request->category, fn ($q, $c) => $q->where('category', $c))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $categories = Product::distinct()->pluck('category')->filter()->sort()->values();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku'         => 'nullable|string|max:100|unique:products,sku',
            'price'       => 'required|numeric|min:0',
            'tax_rate'    => 'required|numeric|min:0|max:100',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product aangemaakt.');
    }

    public function show(Product $product): View
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku'         => 'nullable|string|max:100|unique:products,sku,'.$product->id,
            'price'       => 'required|numeric|min:0',
            'tax_rate'    => 'required|numeric|min:0|max:100',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        return redirect()->route('products.show', $product)->with('success', 'Product bijgewerkt.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product verwijderd.');
    }
}
