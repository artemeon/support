<?php

declare(strict_types=1);

use Artemeon\Support\JSON;

it('should encode data', function (): void {
    expect(JSON::encode(['foo' => 'bar']))
        ->toBe('{"foo":"bar"}');
});

it('should decode data', function (): void {
    expect(JSON::decode('{"foo": "bar"}', true))
        ->toBe(['foo' => 'bar']);
});

it('should throw with invalid data', function (): void {
    JSON::decode('lol');
})->throws(JsonException::class);

it('should return null and not throw with invalid data', function (): void {
    expect(JSON::decodeSilently('lol'))
        ->toBeNull();
});

it('should validate data', function (): void {
    expect(JSON::validate('{"foo": "bar"}'))
        ->toBeTrue()
        ->and(JSON::validate('lol'))
        ->toBeFalse();
});
