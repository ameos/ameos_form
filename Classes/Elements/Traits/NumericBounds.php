<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements\Traits;

/**
 * min / max / step handling shared by number and range elements
 *
 * @phpstan-require-extends \Ameos\AmeosForm\Elements\ElementAbstract
 */
trait NumericBounds
{
    private const array BOUNDS = ['min', 'max', 'step'];

    protected function getBoundsAttributes(): string
    {
        return implode('', array_map(fn(string $key): string => $this->getAttribute($key), self::BOUNDS));
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    protected function addBoundsRenderingInformation(array $data): array
    {
        foreach (self::BOUNDS as $key) {
            if (isset($this->configuration[$key])) {
                $data[$key] = $this->configuration[$key];
            }
        }
        return $data;
    }
}
