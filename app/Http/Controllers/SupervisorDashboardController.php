<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SupervisorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('supervisor');
    }

    public function index()
    {
        $supervisor = Auth::user();
        $assignedInterns = User::where('role_id', function($query) {
            $query->select('id')
                ->from('roles')
                ->where('name', 'intern')
                ->first();
        })->get();

        return view('dashboard.supervisor', [
            'supervisor' => $supervisor,
            'assignedInterns' => $assignedInterns
        ]);
    }

    public function viewIntern($id)
    {
        $intern = User::findOrFail($id);
        return view('dashboard.supervisor.intern-details', [
            'intern' => $intern
        ]);
    }
} 