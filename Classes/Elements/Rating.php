<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;

class Rating extends Radio
{
    /**
     * Options: min (1), max (5), step (1), label (★), or explicit items
     *
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        parent::__construct($absolutename, $name, $configuration, $form);

        $this->assetCollector->addStyleSheet('ameos-form-rating', 'EXT:ameos_form/Resources/Public/Elements/rating.css');

        if (!isset($this->configuration['items'])) {
            $this->configuration['items'] = $this->buildItems();
        }
    }

    public function toHtml(): string
    {
        return '<span class="rating">' . str_replace('<br />', '', parent::toHtml()) . '<span class="clear"></span></span>';
    }

    /**
     * @return array<int, string>
     */
    private function buildItems(): array
    {
        $min = (int) $this->stringify($this->configuration['min'] ?? 1);
        $max = (int) $this->stringify($this->configuration['max'] ?? 5);
        $step = max(1, (int) $this->stringify($this->configuration['step'] ?? 1));
        $label = $this->stringify($this->configuration['label'] ?? '★');

        $items = [];
        for ($i = $max; $i >= $min; $i -= $step) {
            $items[$i] = $label;
        }
        return $items;
    }
}
