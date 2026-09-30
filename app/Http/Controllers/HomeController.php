<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::with(['translations', 'category.translations'])->where('status', 'active')->take(8)->get();
        return view('home', compact('products'));
    }}
