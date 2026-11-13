<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Widgets\Date\DateChoice;
use Ameos\AmeosForm\Widgets\Date\DateText;
use Ameos\AmeosForm\Widgets\WidgetInterface;

/**
 * Options: widget ("choice" or "text"), input ("datetime" or "timestamp"), format-display (e.g. "dmy")
 */
class Date extends ElementAbstract
{
    protected WidgetInterface $widget;

    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        $this->widget = match ($this->getConfigurationItem('widget') ?? 'choice') {
            'text' => new DateText($this, $form),
            'choice' => new DateChoice($this, $form),
            default => throw new BadConfigurationException('Invalid widget type', 1731340806),
        };
    }

    public function getRenderingInformation(): array
    {
        return $this->widget->getRenderingInformation(parent::getRenderingInformation());
    }

    public function toHtml(): string
    {
        return $this->widget->toHtml();
    }

    /**
     * Accepts a timestamp, a Y-m-d string, an array [year, month, day] or a date object
     */
    public function setValue(mixed $value): self
    {
        $date = $this->toDateTime($value);
        $timestampInput = $date !== null && $this->getConfigurationItem('input') === 'timestamp';
        parent::setValue($timestampInput ? $date->getTimestamp() : $date);
        return $this;
    }

    private function toDateTime(mixed $value): ?\DateTimeInterface
    {
        return match (true) {
            $value instanceof \DateTimeInterface => $value,
            is_numeric($value) => (new \DateTime('@' . (int) $value))->setTimezone(new \DateTimeZone(date_default_timezone_get())),
            is_string($value) => \DateTime::createFromFormat('!Y-m-d', $value) ?: null,
            is_array($value) => $this->fromDateParts($value),
            default => null,
        };
    }

    /**
     * @param array<array-key, mixed> $parts
     */
    private function fromDateParts(array $parts): ?\DateTimeInterface
    {
        $year = (int) $this->stringify($parts['year'] ?? 0);
        $month = (int) $this->stringify($parts['month'] ?? 0);
        $day = (int) $this->stringify($parts['day'] ?? 0);
        return checkdate($month, $day, $year) ? new \DateTime(sprintf('%04d-%02d-%02d', $year, $month, $day)) : null;
    }
}
