<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Time;

use Ameos\AmeosForm\Widgets\WidgetAbstract;

/**
 * Hour and minute text inputs with datalists, minutes listed with the "minutestep" option
 */
final class TimeChoice extends WidgetAbstract
{
    public function getRenderingInformation(array $dataFromElement): array
    {
        $dataFromElement['hour'] = $this->renderHour();
        $dataFromElement['minute'] = $this->renderMinute();
        return $dataFromElement;
    }

    public function toHtml(): string
    {
        return $this->renderHour() . ':' . $this->renderMinute();
    }

    public function renderHour(): string
    {
        return $this->renderPart('hour', 'hh', $this->getTimeParts()[0], range(0, 23));
    }

    public function renderMinute(): string
    {
        $step = $this->element->getConfigurationItem('minutestep');
        $step = max(1, is_numeric($step) ? (int) $step : 1);
        return $this->renderPart('minute', 'mm', $this->getTimeParts()[1], range(0, 59, $step));
    }

    /**
     * @param list<int> $options
     */
    private function renderPart(string $part, string $placeholder, string $value, array $options): string
    {
        $id = $this->element->getHtmlId() . '-' . $part;
        $output = '<input type="text" '
            . 'id="' . $id . '" '
            . 'list="' . $id . '-datalist" '
            . 'size="2" '
            . 'name="' . $this->element->getAbsoluteName() . '[' . $part . ']" '
            . 'value="' . $this->escape($value) . '"'
            . $this->element->getAttributes() . ' '
            . 'placeholder="' . $placeholder . '" />';
        $output .= '<datalist id="' . $id . '-datalist">';
        foreach ($options as $option) {
            $option = str_pad((string) $option, 2, '0', STR_PAD_LEFT);
            $output .= '<option value="' . $option . '">' . $option . '</option>';
        }
        return $output . '</datalist>';
    }

    /**
     * @return array{0: string, 1: string} hour and minute
     */
    private function getTimeParts(): array
    {
        $value = $this->element->getValue();
        $parts = explode(':', is_string($value) ? $value : '');
        return [$parts[0], $parts[1] ?? ''];
    }
}
