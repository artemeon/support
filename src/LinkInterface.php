<?php

declare(strict_types=1);

namespace Artemeon\Support;

interface LinkInterface
{
    /**
     * @param array<string, mixed> $parameters
     */
    public function withParameters(array $parameters): static;

    public function withParameter(string $key, mixed $value): static;

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array;

    public function getHref(): string;
}
