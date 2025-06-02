<?php

declare(strict_types=1);

use Artemeon\Support\StringUtil;

it('should get index of', function () {
    expect(StringUtil::of('foobar barfoo')->indexOf('barfoo'))
        ->toBe(7);
});

it('should get last index of', function () {
    expect(StringUtil::of('foobar barfoo barfoo')->lastIndexOf('barfoo'))
        ->toBe(14);
});

it('should get equality', function () {
    expect(StringUtil::of('barfoo')->equals('barfoo'))
        ->toBeTrue();
});

it('should get limited text', function () {
    expect(StringUtil::of('Lorem ipsum dolor sit amet.')->limit(15)->value())
        ->toBe('Lorem ipsum dol…');
});

it('should get array', function () {
    expect(StringUtil::of('1,2,3')->toArray())
        ->toBe(['1', '2', '3']);
});

it('should remove script tags', function () {
    expect(StringUtil::of('<script>alert("foo");</script>')->removeScriptTags()->value())
        ->toBe('');
});

it('should parse url string', function () {
    expect(StringUtil::of('&foo=bar&baz=foo')->parseUrlString())
        ->toBe(['foo' => 'bar', 'baz' => 'foo']);
});

it('should convert br tags to new lines', function () {
    expect(StringUtil::of('<br>')->br2nl()->value())
        ->toBe("\n");
});

it('should check if value is null or empty', function () {
    expect(StringUtil::of('')->isNullOrEmpty())
        ->toBeTrue()
        ->and(StringUtil::of(null)->isNullOrEmpty())
        ->toBeTrue();
});

it('should output xml safe string', function () {
    expect(StringUtil::of('&<>')->xmlSafeString()->value())
        ->toBe('&amp;&lt;&gt;');
});
