<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->latest()
            ->get()
            ->map(function($product) {
                $product->created_at = Carbon::parse($product->created_at)->setTimezone('Asia/Jakarta');
                $product->updated_at = Carbon::parse($product->updated_at)->setTimezone('Asia/Jakarta');
                if ($product->published_at) {
                    $product->published_at = Carbon::parse($product->published_at)->setTimezone('Asia/Jakarta');
                }
                return $product;
            });
            
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required' => 'Nama produk wajib diisi',
            'name.max' => 'Masukan data sesuai panjang data yang ada! Nama produk maksimal 255 karakter',
            'description.required' => 'Deskripsi produk wajib diisi',
            'price.required' => 'Harga produk wajib diisi',
            'price.numeric' => 'Harga produk harus berupa angka',
            'price.min' => 'Harga produk minimal 0',
            'category_id.required' => 'Kategori produk wajib dipilih',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'image.required' => 'Gambar produk wajib diisi',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'image.max' => 'Ukuran gambar maksimal 2MB'
        ];

        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_publish' => 'boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], $messages);

        $validated['published_at'] = $request->is_publish ? now() : null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . str_replace(' ', '-', strtolower($request->name)) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = $imageName;
        }

        Product::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil ditambahkan',
            'title' => 'Berhasil!'
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $messages = [
            'name.required' => 'Nama produk wajib diisi',
            'name.max' => 'Masukan data sesuai panjang data yang ada! Nama produk maksimal 255 karakter',
            'description.required' => 'Deskripsi produk wajib diisi',
            'price.required' => 'Harga produk wajib diisi',
            'price.numeric' => 'Harga produk harus berupa angka',
            'price.min' => 'Harga produk minimal 0',
            'category_id.required' => 'Kategori produk wajib dipilih',
            'category_id.exists' => 'Kategori yang dipilih tidak valid',
            'image.required' => 'Gambar produk wajib diisi',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif',
            'image.max' => 'Ukuran gambar maksimal 2MB'
        ];

        $rules = [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_publish' => 'boolean'
        ];

        // Jika sedang mengedit dan tidak ada file gambar baru, tidak perlu validasi gambar
        if ($request->hasFile('image') || !$product->image) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
        }

        $validated = $request->validate($rules, $messages);

        // Update published_at berdasarkan is_publish
        $validated['published_at'] = $request->boolean('is_publish') ? now() : null;

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image && file_exists(public_path('images/products/' . $product->image))) {
                unlink(public_path('images/products/' . $product->image));
            }

            $image = $request->file('image');
            $imageName = time() . '-' . str_replace(' ', '-', strtolower($request->name)) . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/products'), $imageName);
            $validated['image'] = $imageName;
        }

        $product->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil diperbarui',
            'title' => 'Berhasil!'
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Produk berhasil dihapus',
            'title' => 'Berhasil!'
        ]);
    }
}