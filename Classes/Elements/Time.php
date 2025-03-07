<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;

class Time extends ElementAbstract
{
    /**
     * @var string $valueHour current hour value
     */
    protected $valueHour = '';

    /**
     * @var string $valueMinute current minute value
     */
    protected $valueMinute = '';

    /**
     * @constuctor
     *
     * @param string $absolutename absolutename
     * @param string $name name
     * @param array $configuration configuration
     * @param Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        $this->configuration['minutestep'] = isset($this->configuration['minutestep'])
            ? $this->configuration['minutestep'] : 1;
    }

    /**
     * return rendering information
     *
     * @return array
     */
    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
        $data['hour']   = $this->renderHour();
        $data['minute'] = $this->renderMinute();
        return $data;
    }

    /**
     * form to html
     *
     * @return string
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
        $output = '<input type="text" id="' . $this->getHtmlId() . '-hour" list="' . $this->getHtmlId() . '-hour-datalist" size="2" name="' . $this->absolutename . '[hour]" value="' . $this->valueHour . '"' . $this->getAttributes() . ' placeholder="hh" />';
        $output .= '<datalist id="' . $this->getHtmlId() . '-hour-datalist">';
        for ($hour = 0; $hour < 24; $hour++) {
            $output .= '<option value="' . str_pad((string)$hour, 2, '0', STR_PAD_LEFT) . '">' . str_pad((string)$hour, 2, '0', STR_PAD_LEFT) . '</option>';
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
        $output = '<input type="text" id="' . $this->getHtmlId() . '-minute" list="' . $this->getHtmlId() . '-minute-datalist" size="2" name="' . $this->absolutename . '[minute]" value="' . $this->valueMinute . '"' . $this->getAttributes() . ' placeholder="mm" />';
        $output .= '<datalist id="' . $this->getHtmlId() . '-minute-datalist">';
        for ($minute = 0; $minute < 60; $minute += $this->configuration['minutestep']) {
            $output .= '<option value="' . str_pad((string)$minute, 2, '0', STR_PAD_LEFT) . '">' . str_pad((string)$minute, 2, '0', STR_PAD_LEFT) . '</option>';
        }
        $output .= '</datalist>';
        return $output;
    }

    /**
     * set the value
     *
     * @param mixed $value value
     * @return self
     */
    public function setValue(mixed $value): self
    {
        if (is_array($value)) {
            $this->valueHour   = $value['hour'];
            $this->valueMinute = $value['minute'];

            $value = $this->valueMinute . $this->valueHour;
        }
        parent::setValue($value);
        return $this;
    }
}
