<?php

declare(strict_types=1);

namespace Andreia\FilamentUiSwitcher\Http\Middleware;

use Andreia\FilamentUiSwitcher\FilamentUiSwitcherPlugin;
use Andreia\FilamentUiSwitcher\Support\FontManager;
use Andreia\FilamentUiSwitcher\Support\UiPreferenceManager;
use Closure;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;

final class ApplyUiPreferences
{
    public function handle(Request $request, Closure $next)
    {
        // dd(session()->all());
        // At this point, session middleware has run, so we can access preferences
        if (Filament::isServing()) {
            $panel = Filament::getCurrentPanel();

            if ($panel) {
                $plugin = FilamentUiSwitcherPlugin::getForPanel($panel);
                $defaults = $plugin?->getDefaults() ?? config('ui-switcher.defaults', []);
                $layouts = $plugin?->getLayouts() ?? config('ui-switcher.layouts', []);

                // Load preferences (session is now available)
                $font = FontManager::resolve(
                    UiPreferenceManager::get('ui.font', $defaults['font'] ?? 'Inter'),
                    $plugin?->getFonts(),
                    $plugin?->getFontProvider(),
                    $plugin?->getFontUrl(),
                    $defaults['font'] ?? 'Inter',
                );
                $color = UiPreferenceManager::get('ui.color', $defaults['color'] ?? '#6366f1');
                $layout = UiPreferenceManager::get('ui.layout', $defaults['layout'] ?? 'sidebar');

                if (filled($layouts) && ! in_array($layout, $layouts, true)) {
                    $layout = $defaults['layout'] ?? 'sidebar';
                }

                // Register color GLOBALLY using FilamentColor
                // This generates a proper Filament color palette and must run early
                FilamentColor::register([
                    'primary' => $color,
                ]);

                // $panel->colors([
                //     'primary' => $color,
                // ]);

                $panel->font($font['family'], url: $font['url'], provider: $font['provider']);

                match ($layout) {
                    'topbar' => $panel->topNavigation(),
                    'sidebar-collapsed' => $panel->sidebarCollapsibleOnDesktop(),
                    'sidebar-no-topbar' => $panel->topbar(false),
                    default => $panel->sidebarFullyCollapsibleOnDesktop(false),
                };
            }
        }

        return $next($request);
    }
}
