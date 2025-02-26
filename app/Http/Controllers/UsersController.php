<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function index()
    {
        $user = Auth::user(); // Get the currently logged-in user
        return view('users', compact('user')); // Pass user data to the view
    }
}
