<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    /**
     * Search issues with advanced filters
     */
    public function index(Request $request)
    {
        $query = Issue::with(['category', 'user'])
            ->where('is_verified', true);

        // Keyword search
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        // Location-based search (nearby)
        if ($request->filled(['lat', 'lng'])) {
            $radius = $request->radius ?? 5; // Default 5km
            $query->nearby($request->lat, $request->lng, $radius);
        }

        // Minimum votes filter
        if ($request->filled('min_votes')) {
            $query->where('votes_count', '>=', $request->min_votes);
        }

        // Sort options
        $sortBy = $request->sort_by ?? 'created_at';
        $sortOrder = $request->sort_order ?? 'desc';
        
        switch ($sortBy) {
            case 'votes':
                $query->orderBy('votes_count', $sortOrder);
                break;
            case 'comments':
                $query->orderBy('comments_count', $sortOrder);
                break;
            case 'priority':
                $query->orderBy('priority', $sortOrder);
                break;
            default:
                $query->orderBy('created_at', $sortOrder);
        }

        $issues = $query->paginate(20)->withQueryString();

        // Return JSON for API requests
        if ($request->wantsJson()) {
            return response()->json($issues);
        }

        // Return Inertia view for web requests
        return Inertia::render('Search/Index', [
            'issues' => $issues,
            'filters' => $request->only(['q', 'category', 'status', 'from', 'to', 'lat', 'lng', 'radius', 'min_votes', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Search suggestions (autocomplete)
     */
    public function suggestions(Request $request)
    {
        $query = $request->q;
        
        if (empty($query)) {
            return response()->json([]);
        }

        $suggestions = Issue::where('is_verified', true)
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('address', 'like', "%{$query}%");
            })
            ->select('id', 'title', 'address')
            ->limit(10)
            ->get();

        return response()->json($suggestions);
    }

    /**
     * Popular searches
     */
    public function popular()
    {
        $popular = Issue::where('is_verified', true)
            ->popular(10)
            ->with('category')
            ->take(10)
            ->get();

        return response()->json($popular);
    }
}
