<?php

declare(strict_types=1);

namespace Artemeon\Support;

use Illuminate\Support\Collection;

final class FullText
{
    private Collection $parts;

    public static function make(float | int | string | null ...$parts): self
    {
        return new self(...$parts);
    }

    private function __construct(float | int | string | null ...$parts)
    {
        $this->parts = $this->tokenize(...$parts);
    }

    public function search(?string $query): float
    {
        if (empty($query)) {
            return 1.0;
        }

        $relevance = 0.0;
        $tokens = $this->tokenize($query);
        $maxMultiplier = 2 ** (count($tokens) - 1);
        $multiplier = $maxMultiplier;
        foreach ($tokens as $token) {
            foreach ($this->parts as $part) {
                if ($token === $part) {
                    $relevance += 10000 * $multiplier;
                }
                if (str_starts_with($part, $token)) {
                    $relevance += 5000 * $multiplier;
                }
                if (str_contains($part, $token)) {
                    $relevance += 1000 * $multiplier;
                }
                similar_text($token, $part, $percentage);
                if ($percentage >= 80) {
                    $relevance += $percentage * $multiplier;
                }
            }

            $multiplier /= 2;
        }

        return $relevance;
    }

    private function tokenize(float | int | string | null ...$parts): Collection
    {
        $fullText = trim(implode(' ', Collection::make($parts)->map(static fn (mixed $part) => (string) $part)->toArray()));

        return Collection::make(explode(' ', $fullText))
            ->filter(static fn (string $token) => preg_match('/[A-Z0-9]/ui', $token))
            ->map(static fn (string $token) => mb_strtolower($token, 'UTF-8'));
    }
}
