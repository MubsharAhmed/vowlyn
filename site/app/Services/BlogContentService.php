<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BlogPost;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

final class BlogContentService
{
    /** @return array{html:HtmlString,toc:array<int,array{id:string,label:string,level:int}>,reading_minutes:int} */
    public function prepare(BlogPost $post): array
    {
        $html = $post->renderRichContent('content');
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="blog-content-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('blog-content-root');
        $toc = [];
        $usedIds = [];

        if ($root instanceof DOMElement) {
            $headings = (new DOMXPath($document))->query('.//h2 | .//h3', $root);

            /** @var DOMElement $heading */
            foreach ($headings ?: [] as $heading) {
                $label = trim($heading->textContent);
                $baseId = Str::slug($label) ?: 'section';
                $id = $baseId;
                $suffix = 2;

                while (isset($usedIds[$id])) {
                    $id = "{$baseId}-{$suffix}";
                    $suffix++;
                }

                $usedIds[$id] = true;
                $heading->setAttribute('id', $id);
                $toc[] = [
                    'id' => $id,
                    'label' => $label,
                    'level' => strtolower($heading->tagName) === 'h2' ? 2 : 3,
                ];
            }

            $html = '';
            foreach ($root->childNodes as $child) {
                $html .= $document->saveHTML($child);
            }
        }

        $wordCount = str_word_count(strip_tags($html));

        return [
            'html' => new HtmlString($html),
            'toc' => $toc,
            'reading_minutes' => max(1, (int) ceil($wordCount / 220)),
        ];
    }
}
