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
        $rentals = ModelsRental::with(['units.image', 'category'])
            ->when($type, function ($q) use ($type) {
                $q->whereHas('category', fn($q2) => $q2->where('slug', $type));
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Rental/index', compact('rentals', 'type'));
    }

    public function list($id)
    {
        $rental = ModelsRental::with('category')->findOrFail($id);
        $units = $rental->units()
            ->with('image')
            ->paginate(10);

        return Inertia::render('Rental/list', compact('rental', 'units'));
    }
}
