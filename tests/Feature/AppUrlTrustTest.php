<?php

use App\Providers\AppServiceProvider;

function invokeAppUrlTrustHelper(string $method, mixed ...$args): mixed
{
    $provider = new AppServiceProvider(app());
    $reflection = new \ReflectionMethod($provider, $method);
    $reflection->setAccessible(true);

    return $reflection->invoke($provider, ...$args);
}

it('treats only local or private origins as trusted forwarded-host sources', function () {
    expect(invokeAppUrlTrustHelper('isLocalOrPrivateHost', '127.0.0.1'))->toBeTrue()
        ->and(invokeAppUrlTrustHelper('isLocalOrPrivateHost', 'localhost'))->toBeTrue()
        ->and(invokeAppUrlTrustHelper('isLocalOrPrivateHost', '10.0.0.5'))->toBeTrue()
        ->and(invokeAppUrlTrustHelper('isLocalOrPrivateHost', '172.16.4.20'))->toBeTrue()
        ->and(invokeAppUrlTrustHelper('isLocalOrPrivateHost', '192.168.1.10'))->toBeTrue()
        ->and(invokeAppUrlTrustHelper('isLocalOrPrivateHost', 'panel.example.test'))->toBeFalse()
        ->and(invokeAppUrlTrustHelper('isLocalOrPrivateHost', '198.51.100.24'))->toBeFalse();
});

it('sanitizes forwarded hosts before using them as root urls', function () {
    expect(invokeAppUrlTrustHelper('normalizeForwardedHost', 'proxy.example.test'))->toBe('proxy.example.test')
        ->and(invokeAppUrlTrustHelper('normalizeForwardedHost', 'proxy.example.test:8443'))->toBe('proxy.example.test:8443')
        ->and(invokeAppUrlTrustHelper('normalizeForwardedHost', 'https://evil.example.test'))->toBe('')
        ->and(invokeAppUrlTrustHelper('normalizeForwardedHost', 'evil.example.test/path'))->toBe('')
        ->and(invokeAppUrlTrustHelper('normalizeForwardedHost', "evil.example.test\tbad"))->toBe('');
});
