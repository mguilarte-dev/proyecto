<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GerenteController extends Controller
{
    public function dashboard()
    {
        // Call stored procedures
        $departmentMetrics = DB::select('CALL sp_avg_scores_by_department()');
        $courseMetrics = DB::select('CALL sp_course_metrics()');

        return Inertia::render('Gerente/Dashboard', [
            'departmentMetrics' => $departmentMetrics,
            'courseMetrics' => $courseMetrics
        ]);
    }
}
