<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Product::query();
        if ($request->filled('category')) {
            $q->where('category', $request->category);
        }

        if ($request->filled('name')) {
            $q->where('name', 'like', "%{$request->name}%");
        }

        if ($request->filled('manufacturer')) {
            $q->where('manufacturer', 'like', "%{$request->manufacturer}%");
        }

        if ($request->filled('description')) {
            $q->where('description', 'like', "%{$request->description}%");
        }

        if ($request->filled('price_min')) {
            $q->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $q->where('price', '<=', $request->price_max);
        }

        switch ($request->category) {
            case 'battery':
                if ($request->filled('capacity_min')) {
                    $q->where('capacity', '>=', $request->capacity_min);
                }
                if ($request->filled('capacity_max')) {
                    $q->where('capacity', '<=', $request->capacity_max);
                }
                break;

            case 'panel':
                if ($request->filled('power_min')) {
                    $q->where('power_output', '>=', $request->power_min);
                }
                if ($request->filled('power_max')) {
                    $q->where('power_output', '<=', $request->power_max);
                }
                break;

            case 'connector':
                if ($request->filled('connector_type')) {
                    $q->where('connector_type', '=', $request->connector_type);
                }
                break;
        }

        $q->orderBy('price', 'asc');

        return response()->json([
            'data' => $q->get()
        ]);
    }

    public function connectorTypes(): JsonResponse
    {
        $types = Product::whereNotNull('connector_type')
            ->distinct()
            ->orderBy('connector_type')
            ->pluck('connector_type');

        return response()->json($types);
    }
}
