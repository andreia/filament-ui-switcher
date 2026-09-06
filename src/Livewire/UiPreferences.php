<?php

declare(strict_types=1);

namespace Andreia\FilamentUiSwitcher\Livewire;

use Andreia\FilamentUiSwitcher\Support\FontManager;
use Andreia\FilamentUiSwitcher\Support\UiPreferenceManager;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;

final class UiPreferences extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public string $font = 'Inter';

    public string $layout = 'sidebar';

    public string $primaryColor = '#6366f1';

    public int $fontSize = 16;

    public string $density = 'default';

    public bool $hasModeSwitcher = false;

    public bool $hasFontFamily = true;

    public bool $hasFontSize = true;

    public bool $hasColor = true;

    public bool $hasLayout = true;

    public array $defaults = [];

    public ?array $configuredFonts = null;

    public ?string $fontProvider = null;

    public ?string $fontUrl = null;

    public ?array $configuredFontSizeRange = null;

    public ?array $configuredLayouts = null;

    public ?array $configuredCustomColors = null;

    public function mount(): void
    {
        $defaults = $this->getDefaults();

        // Load saved preferences
        $this->font = UiPreferenceManager::get('ui.font', $defaults['font'] ?? 'Inter');
        $this->layout = UiPreferenceManager::get('ui.layout', $defaults['layout'] ?? 'sidebar');
        $this->primaryColor = UiPreferenceManager::get('ui.color', $defaults['color'] ?? '#6366f1');
        $this->fontSize = UiPreferenceManager::get('ui.font_size', $defaults['font_size'] ?? 16);
        $this->density = UiPreferenceManager::get('ui.density', $defaults['density'] ?? 'default');
    }

    public function setFont(string $font): void
    {
        if (! collect($this->getAvailableFontsProperty())->contains(fn (array $fontOption): bool => in_array($font, [$fontOption['value'], $fontOption['family'], $fontOption['label']], true))) {
            return;
        }

        $this->font = $font;
        UiPreferenceManager::set('ui.font', $font);

        $this->dispatch('reload-page');
    }

    public function setLayout(string $layout): void
    {
        if (! in_array($layout, $this->getAvailableLayoutsProperty(), true)) {
            return;
        }

        $this->layout = $layout;
        UiPreferenceManager::set('ui.layout', $layout);

        $this->dispatch('reload-page');
    }

    public function setColor(string $color): void
    {
        if (! in_array($color, $this->getCustomColorsProperty(), true)) {
            return;
        }

        $this->primaryColor = $color;
        UiPreferenceManager::set('ui.color', $color);

        $this->dispatch('reload-page');
    }

    public function setFontSize($size): void
    {
        $range = $this->getFontSizeRangeProperty();
        $size = min(
            max((int) $size, $range['min']),
            $range['max'],
        );

        $this->fontSize = $size;
        UiPreferenceManager::set('ui.font_size', $size);

        $this->dispatch('reload-page');
    }

    public function setDensity(string $density): void
    {
        $this->density = $density;
        UiPreferenceManager::set('ui.density', $density);

        $this->dispatch('reload-page');
    }

    /**
     * Reset all preferences to default values from config
     */
    public function resetToDefaults(): void
    {
        $defaults = $this->getDefaults();

        // Reset to config defaults
        $this->font = $defaults['font'] ?? 'Inter';
        $this->layout = $defaults['layout'] ?? 'sidebar';
        $this->primaryColor = $defaults['color'] ?? '#6366f1';
        $this->fontSize = $defaults['font_size'] ?? 16;
        $this->density = $defaults['density'] ?? 'default';

        // Save all preferences
        UiPreferenceManager::set('ui.font', $this->font);
        UiPreferenceManager::set('ui.layout', $this->layout);
        UiPreferenceManager::set('ui.color', $this->primaryColor);
        UiPreferenceManager::set('ui.font_size', $this->fontSize);
        UiPreferenceManager::set('ui.density', $this->density);

        // Dispatch event to reset theme/mode in localStorage (handled by JavaScript)
        $this->dispatch('reset-theme-to-default');

        $this->dispatch('reload-page');
    }

    /**
     * Get available fonts from config
     */
    public function getAvailableFontsProperty(): array
    {
        return FontManager::available($this->configuredFonts, $this->fontProvider, $this->fontUrl);
    }

    /**
     * Get available layouts from plugin configuration or config
     */
    public function getAvailableLayoutsProperty(): array
    {
        return $this->configuredLayouts ?? config('ui-switcher.layouts', ['sidebar', 'sidebar-collapsed', 'sidebar-no-topbar', 'topbar']);
    }

    /**
     * Get UI icon from config
     */
    public function getIconProperty(): string
    {
        return config('ui-switcher.icon', 'heroicon-o-cog-6-tooth');
    }

    /**
     * Get custom colors from config
     */
    public function getCustomColorsProperty(): array
    {
        return $this->configuredCustomColors ?? config('ui-switcher.custom_colors', []);
    }

    /**
     * Get font size range from config
     */
    public function getFontSizeRangeProperty(): array
    {
        return $this->configuredFontSizeRange ?? config('ui-switcher.font_size_range', ['min' => 12, 'max' => 20]);
    }

    public function resetAction(): Action
    {
        return Action::make('reset')
            ->color('warning')
            ->link()
            ->label(__('filament-ui-switcher::filament-ui-switcher.reset.button'))
            ->extraAttributes([
                'aria-label' => __('filament-ui-switcher::filament-ui-switcher.reset.aria_label'),
            ])
            ->icon('heroicon-o-arrow-path')
            ->requiresConfirmation()
            ->action(fn () => $this->resetToDefaults());
    }

    public function render()
    {
        return view('filament-ui-switcher::livewire.ui-switcher');
    }

    protected function getDefaults(): array
    {
        return array_replace(config('ui-switcher.defaults', []), $this->defaults);
    }
}
