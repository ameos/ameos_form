<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Widgets\Time;

use Ameos\AmeosForm\Widgets\WidgetAbstract;
use Ameos\AmeosForm\Widgets\WidgetInterface;

class TimeChoice extends WidgetAbstract implements WidgetInterface
{
    /**
     * @param array $dataFromElement
     * @return array
     */
    public function getRenderingInformation(array $dataFromElement): array
    {
        $data = $dataFromElement;
        $data['hour']   = $this->renderHour();
        $data['minute'] = $this->renderMinute();
        return $data;
    }

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml(): string
    {
        return $this->renderHour() . ':' . $this->renderMinute();
    }

    /**
     * return hour html selector
     *
     * @return string
     */
    public function renderHour(): string
    {
        $values = explode(':', $this->element->getValue());
        $output = '<input type="text" '
            . 'id="' . $this->element->getHtmlId() . '-hour" '
            . 'list="' . $this->element->getHtmlId() . '-hour-datalist" '
            . 'size="2" '
            . 'name="' . $this->element->getAbsoluteName() . '[hour]" '
            . 'value="' . $values[0] . '"'
            . $this->element->getAttributes() . ' '
            . 'placeholder="hh" />';
        $output .= '<datalist id="' . $this->element->getHtmlId() . '-hour-datalist">';
        for ($hour = 0; $hour < 24; $hour++) {
            $output .= '<option value="' . str_pad((string)$hour, 2, '0', STR_PAD_LEFT) . '">'
                . str_pad((string)$hour, 2, '0', STR_PAD_LEFT)
                . '</option>';
        }
        $output .= '</datalist>';
        return $output;
    }

    /**
     * return minute html selector
     *
     * @return string
     */
    public function renderMinute(): string
    {
        $values = explode(':', $this->element->getValue());
        $output = '<input type="text" '
            . 'id="' . $this->element->getHtmlId() . '-minute" '
            . 'list="' . $this->element->getHtmlId() . '-minute-datalist" '
            . 'size="2" '
            . 'name="' . $this->element->getAbsoluteName() . '[minute]" '
            . 'value="' . (isset($values[1]) ? $values[1] : '') . '"'
            . $this->element->getAttributes() . ' '
            . 'placeholder="mm" />';
        $output .= '<datalist id="' . $this->element->getHtmlId() . '-minute-datalist">';
        for ($minute = 0; $minute < 60; $minute += $this->element->getConfigurationItem('minutestep')) {
            $output .= '<option value="' . str_pad((string)$minute, 2, '0', STR_PAD_LEFT) . '">'
                . str_pad((string)$minute, 2, '0', STR_PAD_LEFT)
                . '</option>';
        }
        $output .= '</datalist>';
        return $output;
    }
}
