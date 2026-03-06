<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::all(); // O usa paginación: Company::paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dni' => 'nullable|string|max:50',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',            
            'admin_password' => 'required|string|min:8|confirmed',
        ]);
        
        // Crear la compañía
        $company = Company::create(attributes: $request->only(['name', 'address', 'phone', 'email', 'dni'])); 

        $imagePath = null;
        if ($request->hasFile('image')) {         
            $imagePath = $request->file('image')->store("companies/{$company->id}", 'public');
            $company->update(['image' => $imagePath]); // Actualiza la ruta de la imagen en la compañía
        }        
        
        // Crear el usuario administrador
        User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'company_id' => $company->id,
            'is_admin' => true,
        ]);
                
        return redirect()->route('companies.index')->with('success', 'Compañía creada.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Company $company) {
        if (!auth()->user()->is_admin) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:companies,email,' . $company->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dni' => 'nullable|string|max:50',
        ]);

        $companyId = auth()->user()->company_id;
        $imagePath = $company->image;

        if ($request->hasFile('image')) {
            // Elimina la imagen anterior si existe
            if ($company->image) {
                Storage::disk('public')->delete($company->image);
            }
            $imagePath = $request->file('image')->store("companies/{$companyId}", 'public');
        }

        $company->update([
            'name' => $request->name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'image' => $imagePath,
            'dni' => $request->dni,
        ]);

        return redirect()->route('companies.index')->with('success', 'Compañía actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        if (!auth()->user()->is_admin) {
            abort(403);
        }
        
        //$company->delete(); //para eliminar descomente esta línea
        return redirect()->route('companies.index')->with('success', 'Compañia eliminada correctamente.');
    }
}
