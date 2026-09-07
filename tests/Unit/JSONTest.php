<?php

declare(strict_types=1);

use Artemeon\Support\JSON;

describe('JSON Encoding', function (): void {
    it('should encode data', function (mixed $input, string $expected): void {
        expect(JSON::encode($input))
            ->toBe($expected);
    })
        ->with([
            [['foo' => 'bar'], '{"foo":"bar"}'],
            [['foo' => 'bar', 'baz' => 'qux'], '{"foo":"bar","baz":"qux"}'],
            [true, 'true'],
            [false, 'false'],
            [null, 'null'],
            [123, '123'],
            [123.456, '123.456'],
            ['foo', '"foo"'],
            ['foo"bar', '"foo\\"bar"'],
        ]);
});

describe('Simple JSON Decoding', function (): void {
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
});

describe('JSON Validation', function (): void {
    it('should validate data', function (): void {
        expect(JSON::validate('{"foo": "bar"}'))
            ->toBeTrue()
            ->and(JSON::validate('lol'))
            ->toBeFalse();
    });
});

$nonArrayAndObjectJsonData = [
    ['true', 'boolean'],
    ['false', 'boolean'],
    ['"foo"', 'string'],
    ['123', 'integer'],
    ['123.456', 'double'],
    ['null', 'NULL'],
];

describe('JSON Array Decoding', function () use ($nonArrayAndObjectJsonData): void {
    it('should decode as an array', function (): void {
        expect(JSON::decodeAsArray('["foo", "bar"]'))
            ->toBe(['foo', 'bar']);
    });

    it('should throw with invalid array', function (string $json, string $type): void {
        expect(fn (): array => JSON::decodeAsArray($json))
            ->toThrow(JsonException::class, sprintf('Input JSON %s is expected to be an array. Got %s instead.', $json, $type));
    })->with([
        ['{"foo": "bar"}', 'object'],
        ...$nonArrayAndObjectJsonData,
    ]);
});

describe('JSON Object Decoding', function () use ($nonArrayAndObjectJsonData): void {
    it('should decode as an object', function (): void {
        expect(JSON::decodeAsObject('{"foo": "bar"}'))
            ->toEqual((object) ['foo' => 'bar']);
    });

    it('should throw with invalid object', function (string $json, string $type): void {
        expect(fn (): object => JSON::decodeAsObject($json))
            ->toThrow(JsonException::class, sprintf('Input JSON %s is expected to be an object. Got %s instead.', $json, $type));
    })->with([
        ['["foo", "bar"]', 'array'],
        ...$nonArrayAndObjectJsonData,
    ]);
});
