<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $data = [];

        if (session('success') || session('danger') || session('warning') || session('info')) {
            if (session('success')) {
                $data['notification']['success'] = session('success');
            }
            if (session('danger')) {
                $data['notification']['danger'] = session('danger');
            }
            if (session('warning')) {
                $data['notification']['warning'] = session('warning');
            }
            if (session('info')) {
                $data['notification']['info'] = session('info');
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'appName' => config('app.name'),
            'logo' => Storage::disk('public')->exists('logo.png')
                ? Storage::disk('public')->url('logo.png')
                : (file_exists(public_path('logo.png')) ? asset('logo.png') : null),
            ...$data,
        ];
    }
}
