<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('is_publish', true)
            ->latest()
            ->get();
            
        $categories = Category::all();
        
        return view('home', compact('products', 'categories'));
    }
}