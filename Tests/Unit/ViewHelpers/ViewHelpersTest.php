<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\ViewHelpers;

use Ameos\AmeosForm\Tests\Unit\AbstractUnitTestCase;
use Ameos\AmeosForm\ViewHelpers\CompiledFormViewHelper;
use Ameos\AmeosForm\ViewHelpers\ElementViewHelper;
use Ameos\AmeosForm\ViewHelpers\FormViewHelper;
use PHPUnit\Framework\Attributes\Test;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContext;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

final class ViewHelpersTest extends AbstractUnitTestCase
{
    #[Test]
    public function formTagIsRenderedWithElementsAsVariables(): void
    {
        $form = $this->createForm()->add('name', 'text');
        $renderingContext = new RenderingContext();
        $variables = null;
        $viewHelper = $this->initializeViewHelper(new FormViewHelper(), $renderingContext, ['form' => $form, 'class' => 'my-form', 'action' => '/send?a=1&b=2'], static function () use ($renderingContext, &$variables): string {
            $variables = $renderingContext->getVariableProvider()->getAll();
            return '[content]';
        });

        $html = $viewHelper->render();

        self::assertSame(
            '<form method="POST" enctype="multipart/form-data" class="my-form" action="/send?a=1&amp;b=2">[content]'
            . '<input type="hidden" id="tx_test-issubmitted" value="1" name="tx_test[issubmitted]" />'
            . '<input type="hidden" id="tx_test-csrftoken" value="notoken" name="tx_test[csrftoken]" />'
            . '<span style="position:absolute;left:-500000px"><input type="text" id="tx_test-winnie" value="" name="tx_test[winnie]" /></span>'
            . '</form>',
            $html,
        );
        self::assertIsArray($variables);
        self::assertSame($form->getElement('name'), $variables['name']);
        self::assertFalse($renderingContext->getVariableProvider()->exists('name'));
    }

    #[Test]
    public function formTagWithoutCsrfAndHoneypot(): void
    {
        $form = $this->createForm()->disableCsrftoken()->disableHoneypot();
        $viewHelper = $this->initializeViewHelper(new FormViewHelper(), new RenderingContext(), ['form' => $form, 'method' => 'get', 'id' => 'f'], static fn(): string => '');

        self::assertSame(
            '<form method="get" id="f" enctype="multipart/form-data"><input type="hidden" id="tx_test-issubmitted" value="1" name="tx_test[issubmitted]" /></form>',
            $viewHelper->render(),
        );
    }

    #[Test]
    public function formErrorsAreAvailableInsideTheTag(): void
    {
        $form = $this->createForm(['issubmitted' => '1'])->add('name', 'text')->addConstraint('name', 'required', 'Name required');
        $renderingContext = new RenderingContext();
        $errors = null;
        $viewHelper = $this->initializeViewHelper(new FormViewHelper(), $renderingContext, ['form' => $form], static function () use ($renderingContext, &$errors): string {
            $errors = $renderingContext->getVariableProvider()->get('errors');
            return '';
        });

        $viewHelper->render();

        self::assertSame(['Name required'], $errors);
    }

    #[Test]
    public function invalidFormArgumentReturnsMessage(): void
    {
        $viewHelper = $this->initializeViewHelper(new FormViewHelper(), new RenderingContext(), ['form' => null], static fn(): string => '');
        self::assertStringStartsWith('Form is not valid', $viewHelper->render());
    }

    #[Test]
    public function elementViewHelperOverridesOnlyGivenConfiguration(): void
    {
        $form = $this->createForm(['issubmitted' => '1'])->add('name', 'text', ['class' => 'base', 'placeholder' => 'Name'])
            ->addConstraint('name', 'required', 'Name required');
        $viewHelper = $this->initializeViewHelper(
            new ElementViewHelper(),
            new RenderingContext(),
            ['element' => $form->getElement('name'), 'class' => null, 'errorClass' => 'is-invalid', 'placeholder' => '', 'style' => 'color:red'],
            static fn(): string => '',
        );

        self::assertSame(
            '<input type="text" id="tx_test_name" name="tx_test[name]" value="" placeholder="Name" style="color:red" class="base is-invalid" />',
            $viewHelper->render(),
        );
    }

    #[Test]
    public function compiledFormRendersAllElements(): void
    {
        $form = $this->createForm()->add('a', 'hidden');
        $viewHelper = $this->initializeViewHelper(new CompiledFormViewHelper(), new RenderingContext(), ['form' => $form], static fn(): string => '');

        self::assertSame('<input type="hidden" id="tx_test_a" name="tx_test[a]" value="" />', $viewHelper->render());
    }

    /**
     * @template T of AbstractViewHelper
     * @param T $viewHelper
     * @param array<string, mixed> $arguments
     * @return T
     */
    private function initializeViewHelper(AbstractViewHelper $viewHelper, RenderingContext $renderingContext, array $arguments, \Closure $children): AbstractViewHelper
    {
        $viewHelper->setRenderingContext($renderingContext);
        $defaults = [];
        foreach ($viewHelper->prepareArguments() as $definition) {
            $defaults[$definition->getName()] = $definition->getDefaultValue();
        }
        $viewHelper->setArguments(array_merge($defaults, $arguments));
        $viewHelper->setRenderChildrenClosure($children);
        return $viewHelper;
    }
}
