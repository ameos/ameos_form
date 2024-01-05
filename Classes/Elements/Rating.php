<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;

class Rating extends Radio
{
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

        $this->assetCollector->addStyleSheet('ameos-form-rating', 'EXT:ameos_form/Resources/Public/Elements/rating.css');

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
     * @return string
     */
    public function toHtml(): string
    {
        $output = parent::toHtml();
        return '<span class="rating">' . str_replace('<br />', '', $output) . '<span class="clear"></span></span>';
    }
}
