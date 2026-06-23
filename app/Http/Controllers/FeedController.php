<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Project;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class FeedController extends Controller
{
    public function articles(): Response
    {
        $articles = Article::where('is_published', true)
            ->latest()
            ->limit(20)
            ->get();

        $xml = $this->generateRss(
            title: config('app.name').' — Articles',
            description: 'Derniers articles du blog',
            items: $articles->map(fn (Article $article) => [
                'title' => $article->title,
                'link' => route('blog.show', $article->slug),
                'description' => $article->excerpt ?? str($article->content)->limit(300),
                'pubDate' => $article->created_at->toRssString(),
                'guid' => URL::temporarySignedRoute('blog.show', now()->addYear(), $article->slug),
            ]),
        );

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=utf-8']);
    }

    public function projects(): Response
    {
        $projects = Project::latest()->limit(20)->get();

        $xml = $this->generateRss(
            title: config('app.name').' — Projets',
            description: 'Derniers projets du portfolio',
            items: $projects->map(fn (Project $project) => [
                'title' => $project->title,
                'link' => route('projects.show', $project),
                'description' => $project->description,
                'pubDate' => $project->created_at->toRssString(),
                'guid' => URL::temporarySignedRoute('projects.show', now()->addYear(), $project),
            ]),
        );

        return response($xml, 200, ['Content-Type' => 'application/rss+xml; charset=utf-8']);
    }

    private function generateRss(string $title, string $description, iterable $items): string
    {
        $appName = config('app.name');
        $appUrl = config('app.url');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">';
        $xml .= '<channel>';
        $xml .= '<title>'.e($title).'</title>';
        $xml .= '<link>'.e($appUrl).'</link>';
        $xml .= '<description>'.e($description).'</description>';
        $xml .= '<language>fr</language>';
        $xml .= '<atom:link href="'.e(url()->current()).'" rel="self" type="application/rss+xml"/>';

        foreach ($items as $item) {
            $xml .= '<item>';
            $xml .= '<title>'.e($item['title']).'</title>';
            $xml .= '<link>'.e($item['link']).'</link>';
            $xml .= '<description><![CDATA['.$item['description'].']]></description>';
            $xml .= '<pubDate>'.$item['pubDate'].'</pubDate>';
            $xml .= '<guid isPermaLink="false">'.e($item['guid']).'</guid>';
            $xml .= '</item>';
        }

        $xml .= '</channel>';
        $xml .= '</rss>';

        return $xml;
    }
}
