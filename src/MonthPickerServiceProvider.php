<?php
namespace Aliloubm;

use Filament\PluginServiceProvider;
 
class MonthPickerServiceProvider extends PluginServiceProvider
{
    public static string $name = 'month-picker';

    protected function getScripts(): array
    {
        return [
            'my-package-scripts' => __DIR__ . '/../resources/js/index.js',
        ];
    }
}
