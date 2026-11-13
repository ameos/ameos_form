<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Elements\Traits;

/**
 * Shared by elements able to submit the form
 *
 * @phpstan-require-extends \Ameos\AmeosForm\Elements\ElementAbstract
 */
trait Submitter
{
    /**
     * Label, may contain HTML for buttons
     */
    public function getLabel(): string
    {
        return $this->stringify($this->configuration['label'] ?? 'Envoyer');
    }

    public function isClicked(): bool
    {
        return $this->form->isSubmitted() && array_key_exists($this->name, $this->form->getBodyData());
    }
}
