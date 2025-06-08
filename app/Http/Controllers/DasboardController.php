<?php

namespace App\Http\Controllers;

use App\Models\Desa;
use Illuminate\Http\Request;

class DasboardController extends Controller
{
    public function index()
    {
        $desas = Desa::getDesaByAccess()->get();

        return view('home', compact('desas'));
    }
}
