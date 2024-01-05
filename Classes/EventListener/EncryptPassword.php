<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\EventListener;

use Ameos\AmeosForm\Elements\Password;
use Ameos\AmeosForm\Event\ValidFormEvent;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final class EncryptPassword
{
    public function __invoke(ValidFormEvent $event): void
    {
        $hashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('FE');
            
        $elements = $event->getForm()->getElements();
        foreach ($elements as $element) {
            $configuration = $element->getConfiguration();
            if (is_a($element, Password::class) && $configuration['encrypt']) {
                $element->setValue($hashInstance->getHashedPassword((string)$element->getValue()));
            }
        }
    }
}
