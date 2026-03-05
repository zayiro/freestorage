<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Solo usuarios logueados
    }

    public function index()
    {
        // Mostrar solo productos de la compañía del usuario
        $companyId = auth()->user()->company_id;
        return view('administration.index', compact('companyId'));
    }
}
