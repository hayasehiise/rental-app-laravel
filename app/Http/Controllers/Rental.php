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
            ->when($type, fn($q) => $q->where('type', $type))
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Rental/index', compact('rentals', 'type'));
    }

    public function list($id)
    {
        $rental = ModelsRental::findOrFail($id);
        $units = $rental->units()
            ->with('image')
            ->paginate(10);

        return Inertia::render('Rental/list', compact('rental', 'units'));
    }
}
