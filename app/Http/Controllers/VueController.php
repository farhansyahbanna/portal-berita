<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VueController extends Controller
{
    public function index()
    {
        return view('app');
    }

    public function showPost($id)
    {
        return view('app');
    }

    public function admin()
    {
        return view('app');
    }

    public function editor()
    {
        return view('app');
    }

    public function login()
    {
        return view('app');
    }
}
