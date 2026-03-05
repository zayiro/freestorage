<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Sanitizar el término de búsqueda
        $search = trim($request->search ?? '');
        $search = htmlspecialchars(strip_tags($search)); // Elimina HTML/PHP tags
        $search = preg_replace('/[<>\"\'%;()&]/', '', $search); // Elimina caracteres peligrosos
        $search = strip_tags($search); // Elimina cualquier etiqueta HTML remanente
        
        // Validar longitud máxima
        $search = mb_strimwidth($search, 0, 100, ''); // Máximo 100 caracteres
                
        if (auth()->check()) {
            // Obtener el company_id del usuario logueado
            $companyId = auth()->user()->company_id;
            $query = Product::where('company_id', auth()->user()->company_id)
                ->with(['category', 'brand', 'presentations']);

            // Filtrar si hay búsqueda
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('brand', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                });
            }
            
            $result = $query->get();
            $products = $result->sortByDesc('created_at')->values();
                
            return view('home', compact('companyId', 'products', 'search'));          
        }

        return view('home');
    }
}
