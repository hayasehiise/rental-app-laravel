<?php

namespace App\Http\Controllers;

use App\Models\RentalUnit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Homepage extends Controller
{
    public function index()
    {
        $totalUnits = RentalUnit::count();

        return Inertia::render('index', compact('totalUnits'));
    }
}
