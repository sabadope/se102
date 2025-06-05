<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isSupervisor()) {
            return redirect()->route('supervisor.dashboard');
        } elseif ($user->isIntern()) {
            return redirect()->route('intern.dashboard');
        }

        return redirect()->route('login');
    }
} 