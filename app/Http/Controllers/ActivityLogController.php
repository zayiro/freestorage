<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::query();

        // Filtros opcionales
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('log_name')) {
            $query->where('log_name', $request->log_name);
        }

        if ($request->has('from_date')) {
            $query->where('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->where('created_at', '<=', $request->to_date);
        }

        // Cargar relación user y paginar
        $activities = $query
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Obtener usuarios para el filtro
        $users = \App\Models\User::all();

        return view('activity-logs.index', compact('activities', 'users'));
    }

    public function show(Activity $activity)
    {
        $activity->load('user', 'subject');
        return view('activity-logs.show', compact('activity'));
    }
}