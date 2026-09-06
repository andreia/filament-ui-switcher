<?php

declare(strict_types=1);

namespace Andreia\FilamentUiSwitcher\Support;

final class FontManager
{
    public static function resolve(
        ?string $font,
        ?array $fonts = null,
        ?string $defaultProvider = null,
        ?string $defaultUrl = null,
        mixed $defaultFont = null,
    ): array {
        $fonts = self::available($fonts, $defaultProvider, $defaultUrl);

        foreach ($fonts as $fontOption) {
            if (in_array($font, [$fontOption['value'], $fontOption['family'], $fontOption['label']], true)) {
                return $fontOption;
            }
        }

        return self::normalize($defaultFont ?? config('ui-switcher.defaults.font', 'Inter'), null, $defaultProvider, $defaultUrl);
    }

    public static function available(
        ?array $fonts = null,
        ?string $defaultProvider = null,
        ?string $defaultUrl = null,
    ): array {
        $fonts ??= config('ui-switcher.fonts', ['Inter', 'Poppins', 'Roboto']);
        $defaultProvider ??= config('ui-switcher.font_provider');
        $defaultUrl ??= config('ui-switcher.font_url');

        return collect($fonts)
            ->map(fn (mixed $font, int|string $key): array => self::normalize($font, $key, $defaultProvider, $defaultUrl))
            ->filter(fn (array $font): bool => filled($font['family']))
            ->values()
            ->all();
    }

    private static function normalize(
        mixed $font,
        int|string|null $key = null,
        ?string $defaultProvider = null,
        ?string $defaultUrl = null,
    ): array {
        $defaultProvider ??= config('ui-switcher.font_provider');
        $defaultUrl ??= config('ui-switcher.font_url');

        if (is_array($font)) {
            $family = $font['family'] ?? $font['name'] ?? (is_string($key) ? $key : null);
            $label = $font['label'] ?? $family;

            return [
                'value' => (string) ($font['value'] ?? (is_string($key) ? $key : $family)),
                'label' => (string) $label,
                'family' => (string) $family,
                'provider' => $font['provider'] ?? $defaultProvider,
                'url' => $font['url'] ?? $defaultUrl,
                'fallback' => $font['fallback'] ?? 'sans-serif',
            ];
        }

        return [
            'value' => (string) $font,
            'label' => (string) $font,
            'family' => (string) $font,
            'provider' => $defaultProvider,
            'url' => $defaultUrl,
            'fallback' => 'sans-serif',
        ];
    }
}
