<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ContentVideo;
use App\Models\ContentWork;
use Illuminate\Contracts\View\View;

final class ContentCreationController extends Controller
{
    public function __invoke(): View
    {
        $published = ContentVideo::query()->where('is_published', true);

        return view('pages.content-creation', [
            'featuredFilm' => (clone $published)->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('id')->first(),
            'films' => $published->orderBy('sort_order')->orderBy('id')->paginate(9)->fragment('work'),
            'photographyWorks' => ContentWork::query()->where('is_published', true)->where('discipline', 'photography')->orderBy('sort_order')->orderBy('id')->get(),
            'designWorks' => ContentWork::query()->where('is_published', true)->where('discipline', 'design')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }
}
