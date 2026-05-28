<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoryCluster;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoryClusterController extends Controller
{
    public function index(Request $request): View
    {
        $clusters = StoryCluster::query()
            ->with(['category', 'mainSourceAccount'])
            ->withCount('items')
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->latest('last_updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.story-clusters.index', compact('clusters'));
    }
}
