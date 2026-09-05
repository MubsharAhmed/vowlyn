<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\ServiceCatalog;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ServicesController extends Controller
{
    /** Services index (/services) */
    public function index(): View
    {
        return view('pages.services', [
            'catalog' => ServiceCatalog::list(),
        ]);
    }

    /** Individual service detail page (/services/{slug}) */
    public function show(string $service): View
    {
        $data = ServiceCatalog::find($service);

        if ($data === null) {
            throw new NotFoundHttpException('Service not found.');
        }

        return view('pages.service-detail', [
            'service' => $data,
            'related' => array_values(array_filter(array_map(
                static fn (string $slug): ?array => ServiceCatalog::find($slug),
                $data['related'] ?? []
            ))),
        ]);
    }
}
