<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Exception\BadConfigurationException;
use Ameos\AmeosForm\Form\Form;
use Ameos\AmeosForm\Widgets\Time\TimeChoice;
use Ameos\AmeosForm\Widgets\Time\TimeText;
use Ameos\AmeosForm\Widgets\WidgetInterface;

/**
 * Options: widget ("choice" or "text"), minutestep (default 1). Value is stored as "hh:mm"
 */
class Time extends ElementAbstract
{
    protected WidgetInterface $widget;

    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        $configuration ??= [];
        $configuration['minutestep'] ??= 1;
        parent::__construct($absolutename, $name, $configuration, $form);
        $this->widget = match ($this->getConfigurationItem('widget') ?? 'choice') {
            'text' => new TimeText($this, $form),
            'choice' => new TimeChoice($this, $form),
            default => throw new BadConfigurationException('Invalid widget type', 1731340807),
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
     * Accepts "hh:mm" or ['hour' => .., 'minute' => ..]. Parts are zero-padded, empty parts give an empty value.
     */
    public function setValue(mixed $value): self
    {
        if (is_array($value)) {
            $parts = array_values($value);
            $hour = trim($this->stringify($value['hour'] ?? $parts[0] ?? ''));
            $minute = trim($this->stringify($value['minute'] ?? $parts[1] ?? ''));
            $value = $hour === '' && $minute === ''
                ? ''
                : str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minute === '' ? '0' : $minute, 2, '0', STR_PAD_LEFT);
        }
        parent::setValue($value);
        return $this;
    }
}
