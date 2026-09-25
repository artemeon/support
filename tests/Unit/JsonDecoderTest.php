<?php

declare(strict_types=1);

use Artemeon\Support\Exception\InvalidJsonFormatException;
use Artemeon\Support\JsonDecoder;

dataset('valid json', ['{}', '{"name":"Foo"}']);

it('should decode into assoc array with valid json', function (string $json): void {
    expect(JsonDecoder::decode($json))->toBeArray();
})->with('valid json');

it('should decode into object with valid json', function (string $json): void {
    expect(JsonDecoder::decode($json, false))->toBeObject();
})->with('valid json');

dataset('invalid json', ['foo', '{"foo":', '{"bar:"baz}']);

it('should throw exception with invalid json', function (string $json): void {
    JsonDecoder::decode($json);
})
    ->with('invalid json')
    ->throws(InvalidJsonFormatException::class);

it('should decode into null for invalid json', function (string $json): void {
    expect(JsonDecoder::decodeSilently($json))->toBeNull();
})
    ->with('invalid json');

it('should validate with valid json', function (string $json): void {
    expect(JsonDecoder::validate($json))->toBeTrue();
})
    ->with('valid json');

it('should validate with invalid json', function (string $json): void {
    expect(JsonDecoder::validate($json))->toBeFalse();
})
    ->with('invalid json');

it('should decode up to a nesting depth of 511', function (): void {
    expect(JsonDecoder::decode(str_repeat('[', 511) . str_repeat(']', 511)))->toBeArray()
        ->and(JsonDecoder::validate(str_repeat('[', 511) . str_repeat(']', 511)))->toBeTrue();
});

it('should reject a nesting depth of 512', function (): void {
    expect(JsonDecoder::decodeSilently(str_repeat('[', 512) . str_repeat(']', 512)))->toBeNull()
        ->and(JsonDecoder::validate(str_repeat('[', 512) . str_repeat(']', 512)))->toBeFalse();
});

it('should wrap the JSON exception with code 0', function (): void {
    $previous = new JsonException('Syntax error', 4);
    $exception = InvalidJsonFormatException::wrappingException($previous);

    expect($exception->getCode())->toBe(0)
        ->and($exception->getPrevious())->toBe($previous);
});
