<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\DenominationalPresetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class OnboardingWizardController extends Controller
{
    public function __construct(
        private readonly DenominationalPresetService $presetService,
    ) {}

    public function wizard(): Response
    {
        return Inertia::render('Central/Auth/OnboardingWizard', [
            'presets' => $this->presetService->getPresets(),
        ]);
    }

    public function applyPreset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'preset_key' => 'required|string',
            'organization_id' => 'required|integer',
        ]);

        $success = $this->presetService->applyPreset(
            $validated['preset_key'],
            (int) $validated['organization_id']
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Preset polity applied successfully!' : 'Failed to apply preset.',
        ]);
    }
}
