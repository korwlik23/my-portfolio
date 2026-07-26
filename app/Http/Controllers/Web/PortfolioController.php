<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Models\Experience;
use App\Models\Link;
use App\Models\Project;
use App\Models\SeoSetting;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\Skill;
use App\Models\Starter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PortfolioController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::pairs();
        $locale = in_array(app()->getLocale(), ['th', 'en'], true)
            ? app()->getLocale()
            : ($settings['default_language'] ?? 'en');

        $blocks = ContentBlock::query()
            ->whereIn('key', ['hero', 'about', 'deployment'])
            ->get()
            ->keyBy('key');

        $projects = Project::query()
            ->with(['features', 'techStacks'])
            ->public()
            ->where('is_featured', true)
            ->ordered()
            ->limit(6)
            ->get();

        return view('portfolio.home', [
            'settings' => $settings,
            'locale' => $locale,
            'hero' => $blocks->get('hero'),
            'about' => $blocks->get('about'),
            'deployment' => $blocks->get('deployment'),
            'projects' => $projects,
            'starters' => Starter::query()->public()->ordered()->limit(8)->get(),
            'skillsByCategory' => Skill::query()->active()->ordered()->get()->groupBy('category'),
            'services' => Service::query()->active()->ordered()->get(),
            'experiences' => Experience::query()->active()->ordered()->get(),
            'links' => Link::query()->active()->ordered()->get(),
            'seo' => SeoSetting::query()->where('page_key', 'home')->first(),
            'resumeUrls' => $this->resumeUrls($settings),
        ]);
    }

    public function downloadResume(string $locale): StreamedResponse|RedirectResponse
    {
        abort_unless(in_array($locale, ['th', 'en'], true), 404);

        $settings = SiteSetting::pairs();
        $path = $settings["resume_{$locale}_path"] ?? null;

        if ($path && Storage::disk('public')->exists($path)) {
            $extension = pathinfo($path, PATHINFO_EXTENSION) ?: 'pdf';

            return Storage::disk('public')->download($path, "tewarach-resume-{$locale}.{$extension}");
        }

        if (! empty($settings['resume_url'])) {
            return redirect()->away($settings['resume_url']);
        }

        abort(404);
    }

    public function media(string $path): StreamedResponse
    {
        abort_if(
            str_contains($path, '..') || str_contains($path, '\\') || str_starts_with($path, '/') || preg_match('/^[A-Za-z]:/', $path),
            404
        );

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }

    public function robots(): Response
    {
        return response(
            "User-agent: *\nAllow: /\nSitemap: ".route('sitemap')."\n",
            200,
            ['Content-Type' => 'text/plain']
        );
    }

    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => url('/'), 'priority' => '1.0'],
        ]);

        $body = view('portfolio.sitemap', ['urls' => $urls])->render();

        return response($body, 200, ['Content-Type' => 'application/xml']);
    }

    private function resumeUrls(array $settings): array
    {
        $urls = [];

        foreach (['th', 'en'] as $locale) {
            $path = $settings["resume_{$locale}_path"] ?? null;
            if (($path && Storage::disk('public')->exists($path)) || ! empty($settings['resume_url'])) {
                $urls[$locale] = route('resume.download', $locale);
            }
        }

        return $urls;
    }
}
