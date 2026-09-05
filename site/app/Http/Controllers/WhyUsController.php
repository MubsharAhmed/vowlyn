<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

final class WhyUsController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.why-us');
    }
}
