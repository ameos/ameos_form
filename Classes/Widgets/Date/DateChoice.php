<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Date;

use Ameos\AmeosForm\Widgets\WidgetAbstract;
use Ameos\AmeosForm\Widgets\WidgetInterface;

class DateChoice extends WidgetAbstract implements WidgetInterface
{
    /**
     * @param array $dataFromElement
     * @return array
     */
    public function getRenderingInformation(array $dataFromElement): array
    {
        $data = $dataFromElement;
        $data['year']  = $this->renderYear();
        $data['month'] = $this->renderMonth();
        $data['day']   = $this->renderDay();
        return $data;
    }

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml(): string
    {
        $formatDisplay = $this->element->getConfigurationItem('format-display') ?? 'ymd';
        $output = '';
        switch (substr($formatDisplay, 0, 1)) {
            case 'd':
                $output .= $this->renderDay();
                break;
            case 'm':
                $output .= $this->renderMonth();
                break;
            case 'y':
                $output .= $this->renderYear();
                break;
        }

        switch (substr($formatDisplay, 1, 1)) {
            case 'd':
                $output .= $this->renderDay();
                break;
            case 'm':
                $output .= $this->renderMonth();
                break;
            case 'y':
                $output .= $this->renderYear();
                break;
        }

        switch (substr($formatDisplay, 2, 1)) {
            case 'd':
                $output .= $this->renderDay();
                break;
            case 'm':
                $output .= $this->renderMonth();
                break;
            case 'y':
                $output .= $this->renderYear();
                break;
        }

        return $output;
    }

    /**
     * return year html selector
     *
     * @return  string
     */
    protected function renderYear()
    {
        $value = $this->element->getValue();
        if ($value === '') {
            $value = new \DateTime();
        } elseif (is_numeric($value)) {
            $value = new \DateTime('@' . $value);
        }

        return $this->renderItem($this->getYearsItems(), $value->format('Y'), 'year');
    }

    /**
     * return month html selector
     *
     * @return  string
     */
    protected function renderMonth()
    {
        $value = $this->element->getValue();
        if ($value === '') {
            $value = new \DateTime();
        } elseif (is_numeric($value)) {
            $value = new \DateTime('@' . $value);
        }

        return $this->renderItem($this->getMonthsItems(), $value->format('n'), 'month');
    }

    /**
     * return day html selector
     *
     * @return  string
     */
    protected function renderDay()
    {
        $value = $this->element->getValue();
        if ($value === '') {
            $value = new \DateTime();
        } elseif (is_numeric($value)) {
            $value = new \DateTime('@' . $value);
        }

        return $this->renderItem($this->getDaysItems(), $value->format('j'), 'day');
    }

    private function renderItem($items, $currentValue, $type)
    {
        $output = '<select id="' . $this->element->getHtmlId() . '-' . $type . '" '
            . 'name="' . $this->element->getAbsoluteName() . '[' . $type . ']"'
            . $this->element->getClassAttribute()  . '>';
        foreach ($items as $item) {
            $selected = $currentValue == $item ? ' selected="selected"' : '';
            $output .= '<option value="' . $item . '"' . $selected . '>' . $item . '</option>';
        }
        $output .= '</select>';
        return $output;
    }

    /**
     * return available years value
     */
    protected function getYearsItems()
    {
        for ($year = 1900; $year <= date('Y') + 20; $year++) {
            yield $year;
        }
    }

    /**
     * return available months value
     */
    protected function getMonthsItems()
    {
        for ($month = 1; $month <= 12; $month++) {
            yield $month;
        }
    }

    /**
     * return available days value
     */
    protected function getDaysItems()
    {
        for ($day = 1; $day <= 31; $day++) {
            yield $day;
        }
    }
}
