<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class Date extends ElementAbstract
{
    /**
     * @var int $valueYear current year value
     */
    protected $valueYear = 0;

    /**
     * @var int $valueMonth current month value
     */
    protected $valueMonth = 0;

    /**
     * @var int $valueDay current day value
     */
    protected $valueDay = 0;

    /**
     * @var int $yearMinimumLimit
     */
    protected $yearMinimumLimit;

    /**
     * @var int $yearMaximumLimit
     */
    protected $yearMaximumLimit;

    /**
     * @constuctor
     *
     * @param   string  $absolutename absolutename
     * @param   string  $name name
     * @param   array   $configuration configuration
     * @param   Form $form form
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);
        if (!isset($this->configuration['format-output'])) {
            $this->configuration['format-output'] = 'timestamp';
        }
        if (!isset($this->configuration['format-display'])) {
            $this->configuration['format-display'] = 'dmy';
        }

        $this->yearMinimumLimit = isset($this->configuration['year-minimum-limit'])
            ? (int)$this->configuration['year-minimum-limit']
            : 1900;

        $this->yearMaximumLimit = isset($this->configuration['year-maximum-limit'])
            ? (int)$this->configuration['year-maximum-limit']
            : date('Y') + 20;
    }

    /**
     * return rendering information
     *
     * @return  array rendering information
     */
    public function getRenderingInformation(): array
    {
        $data = parent::getRenderingInformation();
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
        $output = '';
        switch (substr($this->configuration['format-display'], 0, 1)) {
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

        switch (substr($this->configuration['format-display'], 1, 1)) {
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

        switch (substr($this->configuration['format-display'], 2, 1)) {
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
    public function renderYear()
    {
        $cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';

        $availableYears = $this->getYearsItems();

        $outputYears = '<select id="' . $this->getHtmlId() . '-year" '
            . 'name="' . $this->absolutename . '[year]"' . $cssclass  . '>';
        foreach ($availableYears as $year) {
            $selected = ($this->valueYear == $year) ? ' selected="selected"' : '';
            $outputYears .= '<option value="' . $year . '"' . $selected . '>' . $year . '</option>';
        }
        $outputYears .= '</select>';
        return $outputYears;
    }

    /**
     * return month html selector
     *
     * @return  string
     */
    public function renderMonth()
    {
        $cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';

        $availableMonths = $this->getMonthsItems();

        $outputMonths =
            '<select'
            . ' id="' . $this->getHtmlId() . '-month"'
            . ' name="' . $this->absolutename . '[month]"' . $cssclass  . '>';
        foreach ($availableMonths as $month) {
            $selected = ($this->valueMonth == $month) ? ' selected="selected"' : '';
            if ($month != '') {
                $outputMonths .= '<option value="' . $month . '"' . $selected . '>'
                    . strftime('%B', mktime(0, 0, 0, (int)$month, 1)) . '</option>';
            } else {
                $outputMonths .= '<option value=""' . $selected . '></option>';
            }
        }
        $outputMonths .= '</select>';
        return $outputMonths;
    }

    /**
     * return day html selector
     *
     * @return  string
     */
    public function renderDay()
    {
        $cssclass = isset($this->configuration['class']) ? ' class="' . $this->configuration['class'] . '"' : '';

        $availableDays = $this->getDaysItems();

        $outputDays = '<select id="' . $this->getHtmlId() . '-day" '
            . 'name="' . $this->absolutename . '[day]"' . $cssclass  . '>';
        foreach ($availableDays as $day) {
            $selected = ($this->valueDay == $day) ? ' selected="selected"' : '';
            $outputDays .= '<option value="' . $day . '"' . $selected . '>' . $day . '</option>';
        }
        $outputDays .= '</select>';
        return $outputDays;
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

    /**
     * set the value
     *
     * @param   mixed $value
     * @return  self this
     */
    public function setValue(mixed $value): self
    {
        if (is_array($value)) {
            $this->valueDay   = (int)$value['day'];
            $this->valueMonth = (int)$value['month'];
            $this->valueYear  = (int)$value['year'];

            if ($this->valueDay === 0 || $this->valueMonth === 0 || $this->valueYear === 0) {
                $value = '';
            } else {
                if (!checkdate($this->valueMonth, $this->valueDay, $this->valueYear)) {
                    $this->systemerror[] = LocalizationUtility::translate('error.date.valid', 'AmeosForm');
                    return $this;
                }
                $date  = new \DateTime($value['year'] . '-' . $value['month'] . '-' . $value['day']);
                $value = $date->getTimestamp();
            }
        } elseif (is_a($value, \DateTime::class)) {
            $value = $value->getTimestamp();

            $this->valueDay   = (int)date('j', (int)$value);
            $this->valueMonth = (int)date('n', (int)$value);
            $this->valueYear  = (int)date('Y', (int)$value);
        } elseif (is_numeric($value)) {
            $this->valueDay   = (int)date('j', (int)$value);
            $this->valueMonth = (int)date('n', (int)$value);
            $this->valueYear  = (int)date('Y', (int)$value);
        }
        parent::setValue($value);
        return $this;
    }

    /**
     * return the value
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        $value = parent::getValue();
        if ($value == '') {
            return '';
        }

        $value = date('Y-m-d', $value);
        $date  = new \Datetime($value);
        if ($this->configuration['format-output'] == 'timestamp') {
            return $date->getTimestamp();
        }

        return $date->format($this->configuration['format-output']);
    }
}
