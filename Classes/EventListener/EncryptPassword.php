<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\EventListener;

use Ameos\AmeosForm\Elements\Password;
use Ameos\AmeosForm\Event\ValidFormEvent;
use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;

/**
 * Hash the password elements (option "encrypt", enabled by default) once the form is valid
 */
#[AsEventListener(identifier: 'encryptPassword')]
final readonly class EncryptPassword
{
    public function __construct(private PasswordHashFactory $passwordHashFactory) {}

    public function __invoke(ValidFormEvent $event): void
    {
        $hashInstance = null;
        foreach ($event->getForm()->getElements() as $element) {
            if (!$element instanceof Password || $element->getConfigurationItem('encrypt') !== true) {
                continue;
            }
            $value = $element->getValue();
            if (!is_string($value) || $value === '') {
                continue;
            }
            $hashInstance ??= $this->passwordHashFactory->getDefaultHashInstance('FE');
            $element->setValue($hashInstance->getHashedPassword($value));
        }
    }
}
