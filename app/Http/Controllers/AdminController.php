<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

// app/Http/Controllers/AdminController.php
class AdminController extends Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => User::count(),
                'total_products' => Product::count(),
                'total_categories' => Category::count(),
            ],
        ]);
    }

    public function users()
    {
        $users = User::select('id', 'name', 'email', 'is_admin', 'created_at')
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return Inertia::render('Admin/Users', [
            'users' => $users,
        ]);
    }
}