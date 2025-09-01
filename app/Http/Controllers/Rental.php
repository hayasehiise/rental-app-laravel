<?php

namespace App\Http\Controllers;

use App\Models\Rental as ModelsRental;
use Illuminate\Http\Request;
use Inertia\Inertia;

class Rental extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');

        $rentals = ModelsRental::with('units.image')
            ->when($type, function ($query, $type) {
                $query->whereHas('units', function ($q) use ($type) {
                    $q->where('type', $type);
                });
            })
            ->get();
        return Inertia::render('Rental/index', compact('rentals', 'type'));
    }
}
