<?php

declare(strict_types=1);

use ZirkelDesign\CapCaptcha\Settings;

beforeEach(function (): void {
    cap_reset_options();
    cap_reset_filters();
    capResetSettingsSingleton();
});

it('loads the widget and its floating add-on from the plugin by default', function (): void {
    $settings = new Settings;

    expect($settings->isWidgetFromCapServer())->toBeFalse()
        ->and($settings->getWidgetScriptUrl())->toBe(CAP_CAPTCHA_URL.'assets/js/vendor/cap-widget.js')
        ->and($settings->getFloatingScriptUrl())->toBe(CAP_CAPTCHA_URL.'assets/js/vendor/cap-widget.floating.js');
});

it('loads both from the cap server when configured', function (): void {
    update_option(Settings::OPTION_KEY, [
        'endpoint_base' => 'https://cap.example.test',
        'widget_source' => Settings::WIDGET_CAP_SERVER,
    ]);
    $settings = new Settings;

    expect($settings->getWidgetScriptUrl())->toBe('https://cap.example.test/assets/widget.js')
        ->and($settings->getFloatingScriptUrl())->toBe('https://cap.example.test/assets/floating.js');
});

it('keeps the bundled widget while there is no endpoint to load it from', function (): void {
    update_option(Settings::OPTION_KEY, ['widget_source' => Settings::WIDGET_CAP_SERVER]);
    $settings = new Settings;

    expect($settings->isWidgetFromCapServer())->toBeFalse()
        ->and($settings->getWidgetScriptUrl())->toBe(CAP_CAPTCHA_URL.'assets/js/vendor/cap-widget.js');
});

it('saves the bundled widget for a source it does not know', function (): void {
    $settings = Settings::get_instance();

    expect($settings->sanitize(['widget_source' => 'https://evil.example/w.js'])['widget_source'])->toBe(Settings::WIDGET_BUNDLED)
        ->and($settings->sanitize(['widget_source' => Settings::WIDGET_CAP_SERVER])['widget_source'])->toBe(Settings::WIDGET_CAP_SERVER);
});
