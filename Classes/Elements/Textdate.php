<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements;

use Ameos\AmeosForm\Form\Form;

/**
 * Date element rendered as a HTML5 date input
 */
class Textdate extends Date
{
    /**
     * @param array<string, mixed>|null $configuration
     */
    public function __construct(string $absolutename, string $name, ?array $configuration, Form $form)
    {
        $configuration ??= [];
        $configuration['widget'] = 'text';
        parent::__construct($absolutename, $name, $configuration, $form);
    }
}
