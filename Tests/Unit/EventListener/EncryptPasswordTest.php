<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\EventListener;

use Ameos\AmeosForm\Event\ValidFormEvent;
use Ameos\AmeosForm\EventListener\EncryptPassword;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;

final class EncryptPasswordTest extends AbstractUnitTestCase
{
    #[Test]
    public function passwordsAreHashedUnlessDisabled(): void
    {
        $hash = $this->createStub(PasswordHashInterface::class);
        $hash->method('getHashedPassword')->willReturnCallback(static fn(string $password): string => 'hashed:' . $password);
        $factory = $this->createMock(PasswordHashFactory::class);
        $factory->expects(self::once())->method('getDefaultHashInstance')->with('FE')->willReturn($hash);

        $form = $this->createForm(['pass' => 'secret', 'plain' => 'secret', 'empty' => '', 'name' => 'John']);
        $form->add('pass', 'password')->add('plain', 'password', ['encrypt' => false])->add('empty', 'password')->add('name', 'text');

        (new EncryptPassword($factory))(new ValidFormEvent($form));

        self::assertSame('hashed:secret', $this->element($form, 'pass')->getValue());
        self::assertSame('secret', $this->element($form, 'plain')->getValue());
        self::assertSame('', $this->element($form, 'empty')->getValue());
        self::assertSame('John', $this->element($form, 'name')->getValue());
    }
}
