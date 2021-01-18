<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function master()
    {
        return view('layouts.master');
    }

    public function auth()
    {
        return view('layouts.auth');
    }
}
