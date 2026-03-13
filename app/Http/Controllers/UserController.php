<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Solo usuarios logueados
    }

    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => auth()->user()->company_id, // Asigna la compañía del usuario logueado
            'is_admin' => false, // Por defecto no admin; ajusta si es necesario
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario registrado exitosamente.');
    }  

    public function show(User $user)
    {
        // Verificar que el usuario pertenezca a la compañía del usuario logueado
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'No tienes permiso para ver este usuario.');
        }
        
        return view('users.show', compact('user'));
    }
    
    public function destroy(User $user)
    {
        // Verificar que el usuario a eliminar pertenezca a la compañía del usuario logueado
        if ($user->company_id !== auth()->user()->company_id) {
            abort(403, 'No tienes permiso para eliminar este usuario.');
        }

        // Solo admins pueden eliminar, y no a sí mismos
        if (!auth()->user()->is_admin || $user->id === auth()->id()) {
            abort(403, 'No puedes eliminar este usuario.');
        }

        $user->delete();

        // Registrar actividad antes de eliminar
        auth()->user()->logActivity('Eliminó al usuario ' . $user->name, [
            'usuario_eliminado' => $user->name,
            'email' => $user->email,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario eliminado exitosamente.');
    }
}