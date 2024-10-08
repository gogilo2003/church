<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        $data = [];

        if (session('success') || session('danger') || session('warning') || session('info')) {
            if (session("success")) {
                $data["notification"]["success"] = session('success');
            };
            if (session("danger")) {
                $data["notification"]["danger"] = session('danger');
            };
            if (session("warning")) {
                $data["notification"]["warning"] = session('warning');
            };
            if (session("info")) {
                $data["notification"]["info"] = session('info');
            };
        }

        return array_merge(
            parent::share($request),
            ['appName' => config('app.name')],
            $data,
            ['logo' => Storage::disk('public')->exists('logo.png') ? Storage::disk('public')->url('logo.png') : (file_exists(public_path('logo.png')) ? asset('logo.png') : null)]
        );
    }
}
