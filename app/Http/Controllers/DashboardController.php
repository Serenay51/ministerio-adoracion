<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Culto;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index($year = null, $month = null)
    {
        $fecha = Carbon::createFromDate($year ?? now()->year, $month ?? now()->month, 1);
        $fecha->locale('es');

        $cultos = Culto::with('rolCultos.user')
            ->whereMonth('fecha', $fecha->month)
            ->whereYear('fecha', $fecha->year)
            ->get();

        return view('dashboard', compact('fecha', 'cultos'));
    }

    
}
