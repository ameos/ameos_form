<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Enum;

use Ameos\AmeosForm\Constraints;
use Ameos\AmeosForm\Elements;
use Ameos\AmeosForm\Enum\Constraint;
use Ameos\AmeosForm\Enum\Element;
use Ameos\AmeosForm\Exception\TypeNotFoundException;
use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use PHPUnit\Framework\Attributes\Test;

final class EnumTest extends AbstractUnitTestCase
{
    #[Test]
    public function everyElementTypeHasAClass(): void
    {
        $constants = (new \ReflectionClass(Element::class))->getConstants(\ReflectionClassConstant::IS_PUBLIC);
        self::assertCount(23, $constants);
        foreach ($constants as $type) {
            self::assertIsString($type);
            self::assertTrue(class_exists(Element::getElementClassName($type)), $type);
        }
        self::assertSame(Elements\Text::class, Element::getElementClassName(Element::TEXT));
        self::assertSame(Elements\Upload::class, Element::getElementClassName('upload'));
    }

    #[Test]
    public function constraintTypesHaveAClass(): void
    {
        self::assertSame(Constraints\Required::class, Constraint::getConstraintClassName(Constraint::REQUIRED));
        self::assertSame(Constraints\Sameas::class, Constraint::getConstraintClassName('sameas'));
        self::assertSame(Constraints\Filemime::class, Constraint::getConstraintClassName('filemime'));
    }

    #[Test]
    public function unknownElementThrowsException(): void
    {
        $this->expectException(TypeNotFoundException::class);
        Element::getElementClassName('unknown');
    }

    #[Test]
    public function unknownConstraintThrowsException(): void
    {
        $this->expectException(TypeNotFoundException::class);
        Constraint::getConstraintClassName(Constraint::FILESIZE);
    }
}
