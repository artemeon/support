<?php

declare(strict_types=1);

use Artemeon\Support\StringUtil;

it('should get index of', function (): void {
    expect(StringUtil::of('foobar barfoo')->indexOf('barfoo'))
        ->toBe(7);
});

it('should get last index of', function (): void {
    expect(StringUtil::of('foobar barfoo barfoo')->lastIndexOf('barfoo'))
        ->toBe(14);
});

it('should get equality', function (): void {
    expect(StringUtil::of('barfoo')->equals('barfoo'))
        ->toBeTrue();
});

it('should get limited text', function (): void {
    expect(StringUtil::of('Lorem ipsum dolor sit amet.')->limit(15)->value())
        ->toBe('Lorem ipsum dol…');
});

it('should get array', function (): void {
    expect(StringUtil::of('1,2,3')->toArray())
        ->toBe(['1', '2', '3']);
});

it('should remove script tags', function (): void {
    expect(StringUtil::of('<script>alert("foo");</script>')->removeScriptTags()->value())
        ->toBe('');
});

it('should parse url string', function (): void {
    expect(StringUtil::of('&foo=bar&baz=foo')->parseUrlString())
        ->toBe(['foo' => 'bar', 'baz' => 'foo']);
});

it('should convert br tags to new lines', function (): void {
    expect(StringUtil::of('<br>')->br2nl()->value())
        ->toBe("\n");
});

it('should check if value is null or empty', function (): void {
    expect(StringUtil::of('')->isNullOrEmpty())
        ->toBeTrue()
        ->and(StringUtil::of(null)->isNullOrEmpty())
        ->toBeTrue();
});

it('should output xml safe string', function (): void {
    expect(StringUtil::of('&<>')->xmlSafeString()->value())
        ->toBe('&amp;&lt;&gt;');
});
