# Filament UI Switcher Plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/andreia/filament-ui-switcher.svg?style=flat-square)](https://packagist.org/packages/andreia/filament-ui-switcher)
[![Total Downloads](https://img.shields.io/packagist/dt/andreia/filament-ui-switcher.svg?style=flat-square)](https://packagist.org/packages/andreia/filament-ui-switcher)

Switch between the available Filament layouts with a customizable UI settings slideover modal accessible from a ⚙️ icon in the topbar.

[![Filament UI Switcher Demo Video](https://raw.githubusercontent.com/andreia/filament-ui-switcher/main/docs/demo_video.jpg)](https://www.youtube.com/watch?v=aEBp4mYrej8)

## Features

**Customization Options:**
- **Primary Color** - Choose from the predefined colors or add your custom colors
- **Font Family** - Select from 6 popular Google Fonts (Inter, Poppins, Roboto, DM Sans, Nunito Sans, Public Sans)
- **Font Size** - Adjust base font size (12-20px)
- **Layout** - Switch between Sidebar, Compact Sidebar, and Topbar navigation
- **Mode (optional)** - Display Filament's mode switcher (dark, light, and system)
- **Reset All** - Quickly reset all customizations to default values with a single click

**Storage Options:**
- **Session Storage** (default) - Preferences stored in user session
- **Database Storage** - Persist preferences per user in database

## Requirements

- PHP 8.2 or higher
- Laravel 11.x or 12.x
- Filament 4.1+ or 5.x
- Livewire 3.x or 4.x

> **Note:** While the plugin technically works with Filament 4.0, the "No Topbar" layout option requires Filament 4.1+. Other layout options work on all Filament 4.x versions.

## Installation

### 1. Install via Composer

```bash
composer require andreia/filament-ui-switcher
```

The package will auto-register via Laravel's package discovery.

### 2. Publish Assets

This package uses Filament's asset management system. After installing, you need to publish the assets:

```bash
php artisan filament:assets
```

This will publish the CSS assets to `public/css/andreia/filament-ui-switcher/` where they will be automatically loaded by Filament and cached by your browser for better performance.

> **Note:** If you update the package, run `php artisan filament:assets` again to get the latest assets.

### 3. Publish Config

You can customize the colors, fonts, and more with the config file:

```bash
# Publish config
php artisan vendor:publish --tag=filament-ui-switcher-config

# Publish translations
php artisan vendor:publish --tag=filament-ui-switcher-translations
```

This will create a `config/ui-switcher.php` file where you can customize:

**Default Preferences:**
```php
'defaults' => [
    'font' => 'Inter',           // Default font family
    'color' => '#6366f1',      // Default primary color
    'layout' => 'sidebar',       // Default layout
    'font_size' => 16,           // Default font size in pixels
    'density' => 'default',      // Default UI density
],
```

**Available Fonts:**
Add or remove fonts from the font picker:
```php
'fonts' => [
    'Inter',
    'Poppins',
    'Public Sans',
    'DM Sans',
    'Nunito Sans',
    'Roboto',
    // Add any font supported by your configured Filament font provider:
    // 'Montserrat',
    // 'Open Sans',
],
```

Use self-hosted fonts with Filament's local font provider:
```php
'defaults' => [
    'font' => 'Inter',
    // ...
],

'font_provider' => \Filament\FontProviders\LocalFontProvider::class,
'font_url' => '/css/fonts.css',

'fonts' => [
    'Inter',
    'Poppins',
],
```

You can also configure the provider or URL per font:
```php
'fonts' => [
    'inter' => [
        'label' => 'Inter',
        'family' => 'Inter',
        'provider' => \Filament\FontProviders\LocalFontProvider::class,
        'url' => '/css/fonts.css',
    ],
],
```

**Custom Colors:**
Customize the color palette shown in the color picker:
```php
'custom_colors' => [
    '#6366f1', '#3b82f6', '#0ea5e9', '#10b981',
    '#22c55e', '#84cc16', '#eab308', '#f59e0b',
    '#f97316', '#ef4444', '#ec4899', '#8b5cf6',
    // Add your brand colors:
    // '#yourBrandColor',
],
```

**Custom UI icon:**
Customize the UI icon modal trigger:
```php
'icon' => 'heroicon-o-cog-6-tooth',
```

**Font Size Range:**
Set the min and max values for the font size slider:
```php
'font_size_range' => [
    'min' => 12,  // Minimum font size in pixels
    'max' => 20,  // Maximum font size in pixels
],
```

**Available Layouts:**
Control which layout options are available to users:
```php
'layouts' => [
    'sidebar',               // Full sidebar with icons and labels
    'sidebar-collapsed',     // Collapsed sidebar (icons only)
    'sidebar-no-topbar',     // Sidebar without topbar (Filament v4.1+)
    'topbar',                // Top navigation bar
],
```

### 3. Publish View (Optional)

If you want to customize the view:

```bash
# Publish view (optional)
php artisan vendor:publish --tag=filament-ui-switcher-views
```

### 4. Register the Plugin

Add the plugin to your Filament Panel Provider (e.g., `app/Providers/Filament/AdminPanelProvider.php`):

```php
use Andreia\FilamentUiSwitcher\FilamentUiSwitcherPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->id('admin')
        ->path('admin')
        // ... other config
        ->plugin(FilamentUiSwitcherPlugin::make());
}
```

#### Customize Icon Position

By default, the settings icon appears before the user menu (`USER_MENU_BEFORE`) on topbar. You can customize where the icon appears using any Filament render hook:

```php
use Andreia\FilamentUiSwitcher\FilamentUiSwitcherPlugin;
use Filament\View\PanelsRenderHook;

->plugin(
    FilamentUiSwitcherPlugin::make()
        ->iconRenderHook(PanelsRenderHook::TOPBAR_END)
)
```

**Available render hooks for the icon:**
- `PanelsRenderHook::USER_MENU_BEFORE` (default) - Before the user menu
- `PanelsRenderHook::USER_MENU_AFTER` - After the user menu  
- `PanelsRenderHook::TOPBAR_START` - Start of topbar
- `PanelsRenderHook::TOPBAR_END` - End of topbar
- `PanelsRenderHook::GLOBAL_SEARCH_BEFORE` - Before global search
- `PanelsRenderHook::GLOBAL_SEARCH_AFTER` - After global search
- Or any custom render hook you've defined

#### Enable Mode Switcher (Optional)

By default, the mode switcher is hidden. If you want to include Filament's native mode switcher (to switch between light, dark, and system) in the settings modal, enable it using the `->withModeSwitcher()` method:

```php
->plugin(
    FilamentUiSwitcherPlugin::make()
        ->withModeSwitcher()
)
```

#### Hide Settings Sections

The font family, font size, color, and layout controls are visible by default. You can hide any of them from the settings modal using the plugin configuration:

```php
->plugin(
    FilamentUiSwitcherPlugin::make()
        ->displayFontFamily(false)
        ->displayFontSize(false)
        ->displayColor(false)
        ->displayLayout(false)
)
```

#### Configure Options in the Panel Provider

The published config file remains the default source of options. You can override any of those values per panel using fluent plugin methods:

```php
use Filament\FontProviders\LocalFontProvider;

->plugin(
    FilamentUiSwitcherPlugin::make()
        ->defaults([
            'font' => 'Inter',
            'color' => '#6366f1',
            'layout' => 'sidebar',
            'font_size' => 16,
            'density' => 'default',
        ])
        ->fonts(
            fonts: [
                'Inter',
                'Poppins',
            ],
            url: asset('css/fonts.css'),
            provider: LocalFontProvider::class,
        )
        ->fontSizeRange(min: 12, max: 20)
        ->layouts([
            'sidebar',
            'topbar',
        ])
        ->customColors([
            '#6366f1',
            '#10b981',
        ])
)
```

These methods may also receive closures, which are evaluated when the panel renders:

```php
->plugin(
    FilamentUiSwitcherPlugin::make()
        ->fonts(fn (): array => tenant()->fonts ?? ['Inter'])
        ->customColors(fn (): array => tenant()->brand_colors ?? ['#6366f1'])
        ->defaults(fn (): array => [
            'font' => tenant()->font_family ?? 'Inter',
            'color' => tenant()->primary_color ?? '#6366f1',
        ])
)
```

### 5. Add the UI switcher path in your theme file

Filament recommends developers create a custom theme to better support plugin's additional TailwindCSS classes. After you have created your [custom theme](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme), add the UI swicher vendor directory to your theme's `theme.css` file usually located in `resources/css/filament/admin/theme.css`:

```css
/* ... */

@source '../../../../vendor/andreia/filament-ui-switcher';
```

and execute:

```bash
npm run build
```

That's it! A ⚙️ settings icon will now appear in your topbar.

## Appearance

![Filament UI Switcher Screenshot](https://raw.githubusercontent.com/andreia/filament-ui-switcher/main/docs/screenshot.png)

![Filament UI Switcher Modal](https://raw.githubusercontent.com/andreia/filament-ui-switcher/main/docs/ui_switcher_modal.jpg)

## Usage

### Resetting Preferences

The settings modal includes a reset button in the header that allows users to quickly restore all customizations to their default values. When clicked, it will reset:

- Primary color
- Font family
- Font size  
- Layout
- Mode (dark/light/system)

All preferences will be restored to the values defined in your `config/ui-switcher.php` file under the `defaults` key.

## Configuration

The config file (`config/ui-switcher.php`) allows you to choose storage driver:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    | Where to store UI preferences.
    | Options: 'session' (default), 'database'
    */
    'driver' => 'session',

    /*
    |--------------------------------------------------------------------------
    | Database Column
    |--------------------------------------------------------------------------
    | Only used if driver = 'database'.
    | The column on the users table where preferences are stored as JSON.
    */
    'database_column' => 'ui_preferences',
];
```

## Database Storage (Optional)

If you want to persist preferences per user across sessions and devices:

### Step 1: Publish and Run Migration

```bash
php artisan vendor:publish --tag=filament-ui-switcher-migrations
php artisan migrate
```

This adds a `ui_preferences` JSON column to your `users` table.

### Step 2: Add HasUiPreferences Trait to User Model

Update your `User` model adding the `HasUiPreferences` trait and a `'ui_preferences' => 'array'` cast, like so:

```php
use Andreia\FilamentUiSwitcher\Models\Traits\HasUiPreferences;

class User extends Authenticatable
{
    use HasUiPreferences;

    protected function casts(): array
    {
        return [
            // ...
            'ui_preferences' => 'array',
        ];
    }
}
```

### Step 3: Update Config

Set the driver to database in `config/ui-switcher.php`:

```php
'driver' => 'database',
```

Now preferences are saved per-user and persist across logins!

## Programmatic Access

You can also access and modify preferences programmatically:

```php
use Andreia\FilamentUiSwitcher\Support\UiPreferenceManager;

// Get a preference
$font = UiPreferenceManager::get('ui.font', 'Inter');

// Set a preference
UiPreferenceManager::set('ui.color', '#10b981');
```

If using database storage with the `HasUiPreferences` trait:

```php
// Get user's preference
$font = auth()->user()->getUiPreference('ui.font', 'Inter');

// Set user's preference
auth()->user()->setUiPreference('ui.color', '#10b981');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Security

If you discover any security related issues, please email andreiabohner@gmail.com instead of using the issue tracker.

## Credits

- [Andréia Bohner](https://github.com/andreia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

Made with ❤️ for the Filament community
