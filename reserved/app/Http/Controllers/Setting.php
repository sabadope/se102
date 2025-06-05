<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class Setting extends Controller
{
    // index page setting
    public function index()
    {
        return view('setting.settings');
    }

    public function encapsulation()
    {
        $users = User::all();
        return view('security.encapsulation', compact('users'));
    }
}
