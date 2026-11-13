<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Date;

use Ameos\AmeosForm\Widgets\WidgetAbstract;

/**
 * Three select boxes (day, month, year), ordered with the "format-display" option (default "ymd")
 */
final class DateChoice extends WidgetAbstract
{
    use DateValue;

    private const int FIRST_YEAR = 1900;
    private const int FUTURE_YEARS = 20;

    public function getRenderingInformation(array $dataFromElement): array
    {
        $dataFromElement['year'] = $this->renderYear();
        $dataFromElement['month'] = $this->renderMonth();
        $dataFromElement['day'] = $this->renderDay();
        return $dataFromElement;
    }

    public function toHtml(): string
    {
        $format = $this->element->getConfigurationItem('format-display');
        $format = is_string($format) && $format !== '' ? $format : 'ymd';

        $output = '';
        foreach (str_split(substr($format, 0, 3)) as $part) {
            $output .= match ($part) {
                'd' => $this->renderDay(),
                'm' => $this->renderMonth(),
                'y' => $this->renderYear(),
                default => '',
            };
        }
        return $output;
    }

    protected function renderYear(): string
    {
        $lastYear = (int) date('Y') + self::FUTURE_YEARS;
        return $this->renderItem(range(self::FIRST_YEAR, $lastYear), (int) $this->getDateValue()->format('Y'), 'year');
    }

    protected function renderMonth(): string
    {
        return $this->renderItem(range(1, 12), (int) $this->getDateValue()->format('n'), 'month');
    }

    protected function renderDay(): string
    {
        return $this->renderItem(range(1, 31), (int) $this->getDateValue()->format('j'), 'day');
    }

    /**
     * @param list<int> $items
     */
    private function renderItem(array $items, int $currentValue, string $type): string
    {
        $output = '<select id="' . $this->element->getHtmlId() . '-' . $type . '" '
            . 'name="' . $this->element->getAbsoluteName() . '[' . $type . ']"'
            . $this->element->getClassAttribute() . '>';
        foreach ($items as $item) {
            $selected = $currentValue === $item ? ' selected="selected"' : '';
            $output .= '<option value="' . $item . '"' . $selected . '>' . $item . '</option>';
        }
        return $output . '</select>';
    }
}
