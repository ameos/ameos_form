<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

class Rating extends Radio
{
    /**
     * @constuctor
     *
     * @param   string  $absolutename absolutename
     * @param   string  $name name
     * @param   array   $configuration configuration
     * @param   \Ameos\AmeosForm\Form $form form
     */
    public function __construct($absolutename, $name, $configuration, $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        $GLOBALS['TSFE']->getPageRenderer()->addCssFile('/typo3conf/ext/ameos_form/Resources/Public/Elements/rating.css');

        $min   = isset($this->configuration['min'])   ? (int)$this->configuration['min']  : 1;
        $max   = isset($this->configuration['max'])   ? (int)$this->configuration['max']  : 5;
        $step  = isset($this->configuration['step'])  ? (int)$this->configuration['step'] : 1;
        $label = isset($this->configuration['label']) ? $this->configuration['label']     : '★';

        if (!isset($this->configuration['items'])) {
            $this->configuration['items'] = [];
            for ($i = $max; $i >= $min; $i = $i - $step) {
                $this->configuration['items'][$i] = $label;
            }
        }
    }

    /**
     * form to html
     *
     * @return  string the html
     */
    public function toHtml()
    {
        $output = parent::toHtml();
        return '<span class="rating">' . str_replace('<br />', '', $output) . '<span class="clear"></span></span>';
    }
}
