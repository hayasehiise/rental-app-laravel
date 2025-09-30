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
        $rental = ModelsRental::with(['category'])->findOrFail($id);
        $units = $rental->units()
            ->with(['image', 'lapanganPrice', 'gedungPrice', 'kendaraanPrice'])
            ->paginate(10);


        $units->getCollection()->transform(function ($unit) {
            $prices = [];

            switch ($unit->rental->category->slug) {
                case 'lapangan':
                    $prices = [
                        [
                            'label' => 'Guest',
                            'price' => $unit->lapanganPrice?->guest_price ?? 0,
                        ],
                        [
                            'label' => 'Member',
                            'price' => $unit->lapanganPrice?->member_price ?? 0,
                        ],
                    ];
                    break;

                case 'gedung':
                    $prices = $unit->gedungPrice->map(fn($gp) => [
                        'label' => $gp->type === 'pax' ? 'Per PAX' : 'Per Hari',
                        'price' => $gp->price,
                    ])->toArray();
                    break;

                case 'kendaraan':
                    $prices = [
                        [
                            'label' => 'Harga',
                            'price' => $unit->kendaraanPrice?->price ?? 0,
                        ],
                    ];
                    break;
            }

            $unit->formattedPrices = $prices;
            return $unit;
        });

        return Inertia::render('Rental/list', compact('rental', 'units'));
    }
}
