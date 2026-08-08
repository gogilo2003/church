<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\VoteHead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class VoteHeadController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Setup/VoteHeads', [
            'voteHeads' => VoteHead::with('parent')->orderBy('code')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:vote_heads,code',
            'name' => 'required|string|max:191',
            'type' => 'required|in:income,expense,asset,liability',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:vote_heads,id',
        ]);

        VoteHead::create($validated);

        return redirect()->back()->with('success', 'Vote Head / Account created successfully.');
    }
}
