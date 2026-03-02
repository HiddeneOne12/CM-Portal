<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        if (!validatePermissions('admin/dashboard')) {
            abort(403);
        }
        return view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
        ]);
    }
}
