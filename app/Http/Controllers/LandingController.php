<?php

namespace App\Http\Controllers;

use App\Models\SaaSPlan;
use Illuminate\Contracts\View\View; // Correct return type for Laravel 12 view() is usually View or Factory, but View is safe.
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    /**
     * Display the landing page with active plans.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        // If user is already logged in, redirect to the panel
        if (Auth::check()) {
            return redirect()->route('admin.dashboard.index');
        }

        // Get currently active plans for the pricing section
        $planes = SaaSPlan::activos()
            ->orderBy('precio_mensual_cop', 'asc')
            ->get();

        return view('landing', compact('planes'));
    }
}
