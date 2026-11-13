<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Date;

/**
 * Current element value as date, timestamps (in the default timezone) and date objects are supported
 *
 * @phpstan-require-extends \Ameos\AmeosForm\Widgets\WidgetAbstract
 */
trait DateValue
{
    /**
     * Current date, today when the element is empty
     */
    protected function getDateValue(): \DateTimeInterface
    {
        return $this->getDateValueOrNull() ?? new \DateTime();
    }

    protected function getDateValueOrNull(): ?\DateTimeInterface
    {
        $value = $this->element->getValue();
        return match (true) {
            $value instanceof \DateTimeInterface => $value,
            is_int($value), is_string($value) && is_numeric($value) => (new \DateTime('@' . $value))
                ->setTimezone(new \DateTimeZone(date_default_timezone_get())),
            default => null,
        };
    }
}
