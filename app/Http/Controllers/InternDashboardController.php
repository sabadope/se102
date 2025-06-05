<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InternDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('intern');
    }

    public function index()
    {
        $intern = Auth::user();
        $supervisor = User::where('role_id', function($query) {
            $query->select('id')
                ->from('roles')
                ->where('name', 'supervisor')
                ->first();
        })->first();

        return view('dashboard.intern', [
            'intern' => $intern,
            'supervisor' => $supervisor
        ]);
    }

    public function profile()
    {
        $intern = Auth::user();
        return view('dashboard.intern.profile', [
            'intern' => $intern
        ]);
    }
} 