<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApplyUserAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only apply to HTML responses
        if ($response->headers->get('content-type') && str_contains($response->headers->get('content-type'), 'text/html')) {
            $user = Auth::user();

            if ($user) {
                $primaryColor = $user->primary_color ?? '#3B82F6';
                $secondaryColor = $user->secondary_color ?? '#1F2937';
                $accentColor = $user->accent_color ?? '#10B981';
                $pageName = $user->page_name ?? config('app.name');

                // Add custom CSS variables to the response
                $customCSS = "
                <style>
                :root {
                    --primary-color: {$primaryColor};
                    --secondary-color: {$secondaryColor};
                    --accent-color: {$accentColor};
                }
                .bg-primary { background-color: {$primaryColor} !important; }
                .text-primary { color: {$primaryColor} !important; }
                .border-primary { border-color: {$primaryColor} !important; }
                .bg-secondary { background-color: {$secondaryColor} !important; }
                .text-secondary { color: {$secondaryColor} !important; }
                .border-secondary { border-color: {$secondaryColor} !important; }
                .bg-accent { background-color: {$accentColor} !important; }
                .text-accent { color: {$accentColor} !important; }
                .border-accent { border-color: {$accentColor} !important; }
                </style>
                ";

                $content = $response->getContent();
                $content = str_replace('</head>', $customCSS . '</head>', $content);
                $content = str_replace('<title>', '<title>' . $pageName . ' - ', $content);

                $response->setContent($content);
            }
        }

        return $response;
    }
}
