<div x-on:reload-page.window="closeAndReload()"
     x-on:reset-theme-to-default.window="
        // Reset theme to system default
        localStorage.removeItem('theme');
        // Dispatch theme-changed event to update the UI
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: 'system' }));
     ">

    {{-- Slideover Modal --}}
    <x-filament::modal
        id="ui-switcher-modal"
        slide-over
        close-button
        width="md"
        :autofocus="false"
    >
        {{-- Cog icon in topbar/sidebar --}}
        <x-slot name="trigger">
            <button
                class="flex items-center gap-2 {{ $layout === 'sidebar-no-topbar' ? 'justify-start w-full px-3 py-2' : 'justify-center w-9 h-9' }} rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                type="button"
                aria-label="{{ __('filament-ui-switcher::filament-ui-switcher.button.aria_label') }}"
            >
                <x-filament::icon
                    icon="{{ $this->icon }}"
                    class="h-5 w-5 {{ $layout === 'sidebar-no-topbar' ? 'shrink-0' : '' }}"
                />
                @if($layout === 'sidebar-no-topbar')
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ __('filament-ui-switcher::filament-ui-switcher.modal.heading') }}
                    </span>
                @endif
            </button>
        </x-slot>

        <x-slot name="header">
            {{-- Header content --}}
            <div class="flex items-center gap-2">
                <x-filament::icon
                    icon="{{ $this->icon }}"
                    class="h-6 w-6"
                />
                <h2 class="fi-modal-heading">
                    {{ __('filament-ui-switcher::filament-ui-switcher.modal.heading') }}
                </h2>
            </div>
        </x-slot>

        {{-- Loading Banner --}}
        <div wire:loading.delay.shortest
             wire:target="setFont,setLayout,setColor,setFontSize,setDensity,resetToDefaults"
             class="bg-primary-500 text-white px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium">{{ __('filament-ui-switcher::filament-ui-switcher.loading.message') }}</span>
        </div>

        <div class="space-y-8 py-4">
            {{-- Mode Switcher (Optional) --}}
            @if($hasModeSwitcher)
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <span class="flex items-center gap-2">
                            <x-filament::icon
                                icon="heroicon-o-computer-desktop"
                                class="h-4 w-4"
                            />
                            {{ __('filament-ui-switcher::filament-ui-switcher.mode.heading') }}
                        </span>
                    </h3>
                    <div class="mt-3">
                        <x-filament-panels::theme-switcher />
                    </div>
                </div>
            @endif

            {{-- Layout Selector --}}
            @if($hasLayout && filled($this->availableLayouts))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <span class="flex items-center gap-2">
                            <x-filament::icon
                                icon="heroicon-o-squares-2x2"
                                class="h-4 w-4"
                            />
                            {{ __('filament-ui-switcher::filament-ui-switcher.layout.heading') }}
                        </span>
                    </h3>
                    <div class="grid grid-cols-2 gap-3">
                        @if(in_array('sidebar', $this->availableLayouts, true))
                            <button
                                wire:click="setLayout('sidebar')"
                                wire:loading.class="opacity-50 cursor-wait"
                                wire:target="setLayout"
                                type="button"
                                class="relative flex flex-col items-center justify-center p-4 rounded-lg border-2 transition-all hover:border-primary-400 hover:shadow-md {{ $layout === 'sidebar' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-200 dark:ring-primary-800' : 'border-gray-200 dark:border-gray-700' }}">
                                <div class="flex flex-col gap-0.5 mb-2">
                                    <div class="w-14 h-1 rounded bg-gray-400 dark:bg-gray-500"></div>
                                    <div class="flex gap-0.5">
                                        <div class="w-3.5 h-7 rounded bg-gray-400 dark:bg-gray-500"></div>
                                        <div class="w-10 h-7 rounded bg-gray-300 dark:bg-gray-600"></div>
                                    </div>
                                </div>
                                <span class="text-xs font-medium">{{ __('filament-ui-switcher::filament-ui-switcher.layout.sidebar') }}</span>
                            </button>
                        @endif

                        @if(in_array('sidebar-collapsed', $this->availableLayouts, true))
                            <button
                                wire:click="setLayout('sidebar-collapsed')"
                                type="button"
                                class="relative flex flex-col items-center justify-center p-4 rounded-lg border-2 transition-all hover:border-primary-400 {{ $layout === 'sidebar-collapsed' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                <div class="flex flex-col gap-0.5 mb-2">
                                    <div class="w-14 h-1 rounded bg-gray-400 dark:bg-gray-500"></div>
                                    <div class="flex gap-0.5">
                                        <div class="w-1.5 h-7 rounded bg-gray-400 dark:bg-gray-500"></div>
                                        <div class="w-12 h-7 rounded bg-gray-300 dark:bg-gray-600"></div>
                                    </div>
                                </div>
                                <span class="text-xs font-medium">{{ __('filament-ui-switcher::filament-ui-switcher.layout.compact') }}</span>
                            </button>
                        @endif

                        @if(in_array('sidebar-no-topbar', $this->availableLayouts, true))
                            <button
                                wire:click="setLayout('sidebar-no-topbar')"
                                type="button"
                                class="relative flex flex-col items-center justify-center p-4 rounded-lg border-2 transition-all hover:border-primary-400 {{ $layout === 'sidebar-no-topbar' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                <div class="flex gap-0.5 mb-2">
                                    <div class="w-3.5 h-8 rounded bg-gray-400 dark:bg-gray-500"></div>
                                    <div class="w-10 h-8 rounded bg-gray-300 dark:bg-gray-600"></div>
                                </div>
                                <span class="text-xs font-medium text-center leading-tight">{{ __('filament-ui-switcher::filament-ui-switcher.layout.no_topbar') }}</span>
                            </button>
                        @endif

                        @if(in_array('topbar', $this->availableLayouts, true))
                            <button
                                wire:click="setLayout('topbar')"
                                type="button"
                                class="relative flex flex-col items-center justify-center p-4 rounded-lg border-2 transition-all hover:border-primary-400 {{ $layout === 'topbar' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                <div class="flex flex-col gap-0.5 mb-2">
                                    <div class="w-14 h-1.5 rounded bg-gray-400 dark:bg-gray-500"></div>
                                    <div class="w-14 h-6.5 rounded bg-gray-300 dark:bg-gray-600"></div>
                                </div>
                                <span class="text-xs font-medium">{{ __('filament-ui-switcher::filament-ui-switcher.layout.topbar') }}</span>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Color --}}
            @if($hasColor && filled($this->customColors))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <span class="flex items-center gap-2">
                            <x-filament::icon
                                icon="heroicon-o-swatch"
                                class="h-4 w-4"
                            />
                            {{ __('filament-ui-switcher::filament-ui-switcher.color.heading') }}
                        </span>
                    </h3>

                    <div>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($this->customColors as $color)
                                <button
                                    wire:click="setColor('{{ $color }}')"
                                    wire:loading.class="opacity-50"
                                    wire:target="setColor"
                                    type="button"
                                    class="relative w-10 h-10 rounded-lg transition-all hover:scale-110 {{ $primaryColor === $color ? 'border-gray-900 dark:border-gray-100 shadow-lg ring-2 ring-offset-2 ring-gray-400' : 'border-transparent' }}"
                                    style="background-color: {{ $color }}">
                                    @if($primaryColor === $color)
                                        <svg class="absolute inset-0 m-auto w-6 h-6 text-white drop-shadow-lg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- Font Family --}}
            @if($hasFontFamily && filled($this->availableFonts))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        <span class="flex items-center gap-2">
                            <x-filament::icon
                                icon="heroicon-o-bars-3-bottom-left"
                                class="h-4 w-4"
                            />
                            {{ __('filament-ui-switcher::filament-ui-switcher.font.heading') }}
                        </span>
                    </h3>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">{{ __('filament-ui-switcher::filament-ui-switcher.font.family') }}</p>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($this->availableFonts as $fontOption)
                            @php($isSelectedFont = in_array($font, [$fontOption['value'], $fontOption['family'], $fontOption['label']], true))
                            <button
                                wire:click="setFont(@js($fontOption['value']))"
                                wire:loading.class="opacity-50"
                                wire:target="setFont"
                                type="button"
                                class="relative flex flex-col items-center justify-center p-3 rounded-lg border-2 transition-all hover:border-primary-400 hover:shadow-md {{ $isSelectedFont ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 ring-2 ring-primary-200 dark:ring-primary-800' : 'border-gray-200 dark:border-gray-700' }}"
                                style="font-family: '{{ $fontOption['family'] }}', {{ $fontOption['fallback'] }}">
                                @if($isSelectedFont)
                                    <svg class="absolute top-1 right-1 w-5 h-5 text-primary-600 dark:text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                                <span class="text-2xl font-medium mb-1">Aa</span>
                                <span class="text-xs">{{ $fontOption['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Font Size --}}
            @if($hasFontSize)
                <div x-data="{
                    fontSize: {{ $fontSize }},
                    min: {{ $this->fontSizeRange['min'] }},
                    max: {{ $this->fontSizeRange['max'] }},
                    getGradient() {
                        const percent = ((this.fontSize - this.min) / (this.max - this.min)) * 100;
                        const isDark = document.documentElement.classList.contains('dark');
                        const direction = document.documentElement.getAttribute('dir') === 'rtl' ? 'to left' : 'to right';
                        if (isDark) {
                            return `linear-gradient(${direction}, rgba(167, 139, 250, 0.3) 0%, rgba(139, 92, 246, 0.7) ${percent / 2}%, rgb(139, 92, 246) ${percent}%, #374151 ${percent}%, #374151 100%)`;
                        } else {
                            return `linear-gradient(${direction}, rgba(139, 92, 246, 0.3) 0%, rgba(139, 92, 246, 0.6) ${percent / 2}%, rgb(124, 58, 237) ${percent}%, #e5e7eb ${percent}%, #e5e7eb 100%)`;
                        }
                    }
                }">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('filament-ui-switcher::filament-ui-switcher.font.size') }}</p>
                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 dark:bg-gray-800 rounded" x-text="fontSize + 'px'"></span>
                    </div>
                    <div wire:ignore>
                        <input
                            type="range"
                            :min="min"
                            :max="max"
                            step="1"
                            x-model="fontSize"
                            @change="$wire.setFontSize(fontSize)"
                            :style="{ background: getGradient() }"
                            class="ui-switcher-slider w-full">
                    </div>
                </div>
            @endif
        </div>

        <x-slot name="footerActions">
            {{ $this->resetAction() }}
        </x-slot>
    </x-filament::modal>

    <x-filament-actions::modals />
</div>
