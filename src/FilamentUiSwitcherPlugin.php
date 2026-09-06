<?php

namespace Andreia\FilamentUiSwitcher;

use Andreia\FilamentUiSwitcher\Http\Middleware\ApplyUiPreferences;
use Andreia\FilamentUiSwitcher\Support\UiPreferenceManager;
use Closure;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class FilamentUiSwitcherPlugin implements Plugin
{
    /**
     * @var array<string, static>
     */
    protected static array $instances = [];

    protected string $iconRenderHook = PanelsRenderHook::USER_MENU_BEFORE;

    protected bool $hasModeSwitcher = false;

    protected bool $hasFontFamily = true;

    protected bool $hasFontSize = true;

    protected bool $hasColor = true;

    protected bool $hasLayout = true;

    protected array|Closure|null $defaults = null;

    protected array|Closure|null $fonts = null;

    protected string|Closure|null $fontProvider = null;

    protected string|Closure|null $fontUrl = null;

    protected array|Closure|null $fontSizeRange = null;

    protected array|Closure|null $layouts = null;

    protected array|Closure|null $customColors = null;

    public static function make(): static
    {
        return new static;
    }

    public function getId(): string
    {
        return 'filament-ui-switcher';
    }

    public static function getForPanel(Panel $panel): ?static
    {
        return static::$instances[$panel->getId()] ?? null;
    }

    public function iconRenderHook(string $hook): static
    {
        $this->iconRenderHook = $hook;

        return $this;
    }

    public function withModeSwitcher(bool $condition = true): static
    {
        $this->hasModeSwitcher = $condition;

        return $this;
    }

    public function displayFontFamily(bool $condition = true): static
    {
        $this->hasFontFamily = $condition;

        return $this;
    }

    public function displayFontSize(bool $condition = true): static
    {
        $this->hasFontSize = $condition;

        return $this;
    }

    public function displayColor(bool $condition = true): static
    {
        $this->hasColor = $condition;

        return $this;
    }

    public function displayLayout(bool $condition = true): static
    {
        $this->hasLayout = $condition;

        return $this;
    }

    public function defaults(array|Closure $defaults): static
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function fonts(
        array|Closure $fonts,
        string|Closure|null $url = null,
        string|Closure|null $provider = null,
    ): static {
        $this->fonts = $fonts;

        if ($url !== null) {
            $this->fontUrl = $url;
        }

        if ($provider !== null) {
            $this->fontProvider = $provider;
        }

        return $this;
    }

    public function fontProvider(string|Closure|null $provider): static
    {
        $this->fontProvider = $provider;

        return $this;
    }

    public function fontUrl(string|Closure|null $url): static
    {
        $this->fontUrl = $url;

        return $this;
    }

    public function fontSizeRange(int|Closure|null $min = null, int|Closure|null $max = null): static
    {
        $this->fontSizeRange = [
            'min' => $min,
            'max' => $max,
        ];

        return $this;
    }

    public function layouts(array|Closure $layouts): static
    {
        $this->layouts = $layouts;

        return $this;
    }

    public function customColors(array|Closure $colors): static
    {
        $this->customColors = $colors;

        return $this;
    }

    public function getDefaults(): array
    {
        return array_replace(
            config('ui-switcher.defaults', []),
            $this->evaluate($this->defaults) ?? [],
        );
    }

    public function getFonts(): array
    {
        return $this->evaluate($this->fonts) ?? config('ui-switcher.fonts', []);
    }

    public function getFontProvider(): ?string
    {
        return $this->evaluate($this->fontProvider) ?? config('ui-switcher.font_provider');
    }

    public function getFontUrl(): ?string
    {
        return $this->evaluate($this->fontUrl) ?? config('ui-switcher.font_url');
    }

    public function getFontSizeRange(): array
    {
        $configuredRange = config('ui-switcher.font_size_range', []);
        $range = $this->evaluate($this->fontSizeRange) ?? $configuredRange;

        return [
            'min' => (int) ($this->evaluate($range['min'] ?? null) ?? $configuredRange['min'] ?? 12),
            'max' => (int) ($this->evaluate($range['max'] ?? null) ?? $configuredRange['max'] ?? 20),
        ];
    }

    public function getLayouts(): array
    {
        return $this->evaluate($this->layouts) ?? config('ui-switcher.layouts', []);
    }

    public function getCustomColors(): array
    {
        return $this->evaluate($this->customColors) ?? config('ui-switcher.custom_colors', []);
    }

    public function register(Panel $panel): void
    {
        static::$instances[$panel->getId()] = $this;

        // Register custom middleware to apply preferences after session is available
        $panel->authMiddleware([
            ApplyUiPreferences::class,
        ], isPersistent: true);

        // Add cog icon to configured render hook (default: USER_MENU_BEFORE)
        // Livewire component is registered in ServiceProvider, so it's available here
        // Pass the settings section configuration to the component
        $panel->renderHook(
            $this->iconRenderHook,
            fn (): string => Blade::render(
                <<<'BLADE'
                @livewire('filament-ui-switcher', [
                    'hasModeSwitcher' => $hasModeSwitcher,
                    'hasFontFamily' => $hasFontFamily,
                    'hasFontSize' => $hasFontSize,
                    'hasColor' => $hasColor,
                    'hasLayout' => $hasLayout,
                    'defaults' => $defaults,
                    'configuredFonts' => $fonts,
                    'fontProvider' => $fontProvider,
                    'fontUrl' => $fontUrl,
                    'configuredFontSizeRange' => $fontSizeRange,
                    'configuredLayouts' => $layouts,
                    'configuredCustomColors' => $customColors,
                ])
                BLADE,
                [
                    'hasModeSwitcher' => $this->hasModeSwitcher,
                    'hasFontFamily' => $this->hasFontFamily,
                    'hasFontSize' => $this->hasFontSize,
                    'hasColor' => $this->hasColor,
                    'hasLayout' => $this->hasLayout,
                    'defaults' => $this->getDefaults(),
                    'fonts' => $this->getFonts(),
                    'fontProvider' => $this->getFontProvider(),
                    'fontUrl' => $this->getFontUrl(),
                    'fontSizeRange' => $this->getFontSizeRange(),
                    'layouts' => $this->getLayouts(),
                    'customColors' => $this->getCustomColors(),
                ],
            ),
        );

        // Inject font size CSS
        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            function (): string {
                $defaults = $this->getDefaults();
                $fontSize = UiPreferenceManager::get('ui.font_size', $defaults['font_size'] ?? 16);

                return <<<HTML
                <style>
                    :root {
                        --font-size-base: {$fontSize}px;
                    }
                    html {
                        font-size: {$fontSize}px !important;
                    }
                </style>
                HTML;
            }
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    protected function evaluate(mixed $value): mixed
    {
        return $value instanceof Closure ? $value() : $value;
    }
}
