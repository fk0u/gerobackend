<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocsController extends Controller
{
    public function index(): View
    {
        $changelogPath = base_path('CHANGELOG.md');
        $changelogHtml = File::exists($changelogPath)
            ? Str::markdown(File::get($changelogPath))
            : '<p><em>No changelog entries found.</em></p>';

        $specUrl = URL::to('/openapi.yaml');

        $servers = [
            [
                'key' => 'local',
                'label' => 'Local',
                'description' => 'Laravel artisan serve environment for local development.',
                'url' => config('app.url', 'http://127.0.0.1:8000'),
                'docs' => URL::to('/'),
            ],
            [
                'key' => 'staging',
                'label' => 'Staging',
                'description' => 'Shared QA sandbox for QA & stakeholder review.',
                'url' => config('services.gerobaks.staging_url', env('GEROBAKS_STAGING_URL', 'https://staging-gerobaks.dumeg.com')),
                'docs' => config('services.gerobaks.staging_docs_url', env('GEROBAKS_STAGING_DOCS_URL', 'https://staging-gerobaks.dumeg.com/docs')),
            ],
            [
                'key' => 'production',
                'label' => 'Production',
                'description' => 'Live environment consumed by mobile & partner apps.',
                'url' => config('services.gerobaks.production_url', env('GEROBAKS_PRODUCTION_URL', 'https://gerobaks.dumeg.com')),
                'docs' => config('services.gerobaks.production_docs_url', env('GEROBAKS_PRODUCTION_DOCS_URL', 'https://gerobaks.dumeg.com/docs')),
            ],
        ];

        return view('docs.index', [
            'changelogHtml' => $changelogHtml,
            'specUrl' => $specUrl,
            'servers' => $servers,
        ]);
    }

    public function openapi(): BinaryFileResponse
    {
        $path = public_path('openapi.yaml');
        abort_unless(File::exists($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/yaml',
        ]);
    }
}