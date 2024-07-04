<?php

declare(strict_types=1);

use Artemeon\Support\Exception\InvalidJsonFormatException;
use Artemeon\Support\JsonDecoder;

dataset('valid json', ['{}', '{"name":"Foo"}']);

it('should decode into assoc array with valid json', function (string $json) {
    expect(JsonDecoder::decode($json))->toBeArray();
})->with('valid json');

it('should decode into object with valid json', function (string $json) {
    expect(JsonDecoder::decode($json, false))->toBeObject();
})->with('valid json');

dataset('invalid json', ['foo', '{"foo":', '{"bar:"baz}']);

it('should throw exception with invalid json', function (string $json) {
    JsonDecoder::decode($json);
})
    ->with('invalid json')
    ->throws(InvalidJsonFormatException::class);
