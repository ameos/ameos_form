<?php

declare(strict_types=1);

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements\Dropdown;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Form\Form;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DropdownTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    #[Test]
    public function elementRenderer()
    {
        $expectedResult = '<select id="tx_ameosform-unittest_my-select" name="tx_ameosform-unittest[my-select]" '
            . 'placeholder="Here your value" '
            . 'style="display: block;" '
            . 'title="My text field" '
            . 'customattr="custom-value" '
            . 'class="my-css-class">'
                . '<option value="">Here your value</option>'
                . '<option value="value-1">Value 1</option>'
                . '<option value="value-2" selected="selected">Value 2</option>'
                . '<option value="value-3">Value 3</option>'
            . '</select>';

        $elementConfiguration = [];
        $elementConfiguration['defaultValue'] = 'value-2';
        $elementConfiguration['class']        = 'my-css-class';
        $elementConfiguration['style']        = 'display: block;';
        $elementConfiguration['title']        = 'My text field';
        $elementConfiguration['custom']       = 'customattr="custom-value"';
        $elementConfiguration['placeholder']  = 'Here your value';
        $elementConfiguration['items']        = [
            'value-1' => 'Value 1',
            'value-2' => 'Value 2',
            'value-3' => 'Value 3'
        ];

        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $form->method('getErrorManager')->willReturn($errorManager);
        $element = new Dropdown('tx_ameosform-unittest[my-select]', 'my-select', $elementConfiguration, $form);
        $element->with('style', 'display: block;');
        $result = $element->toHtml();

        $this->assertEquals($result, $expectedResult);
    }

    #[Test]
    public function elementRendereringInformation()
    {
        $expectedHtmlResult = '<select id="tx_ameosform-unittest_my-select" name="tx_ameosform-unittest[my-select]" '
            . 'placeholder="Here your value" '
            . 'style="display: block;" '
            . 'title="My text field" '
            . 'customattr="custom-value" '
            . 'class="my-css-class">'
                . '<option value="">Here your value</option>'
                . '<option value="value-1">Value 1</option>'
                . '<option value="value-2" selected="selected">Value 2</option>'
                . '<option value="value-3">Value 3</option>'
            . '</select>';

        $expectedResult = [
            'defaultValue' => 'value-2',
            'class'        => 'my-css-class',
            'style'        => 'display: block;',
            'title'        => 'My text field',
            'custom'       => 'customattr="custom-value"',
            'placeholder'  => 'Here your value',
            'otherdata'    => 'test',
            '__compiled'   => $expectedHtmlResult,
            'name'         => 'my-select',
            'value'        => 'value-2',
            'absolutename' => 'tx_ameosform-unittest[my-select]',
            'htmlid'       => 'tx_ameosform-unittest_my-select',
            'items'        => ['value-1' => 'Value 1', 'value-2' => 'Value 2', 'value-3' => 'Value 3'],
            'errors'       => [],
            'isvalid'      => true,
            'hasError'     => false,
            'required'     => false,
            'optionValueField' => 'uid',
        ];

        $elementConfiguration = [];
        $elementConfiguration['defaultValue'] = 'value-2';
        $elementConfiguration['class']        = 'my-css-class';
        $elementConfiguration['title']        = 'My text field';
        $elementConfiguration['custom']       = 'customattr="custom-value"';
        $elementConfiguration['placeholder']  = 'Here your value';
        $elementConfiguration['otherdata']    = 'test';
        $elementConfiguration['items']        = [
            'value-1' => 'Value 1',
            'value-2' => 'Value 2',
            'value-3' => 'Value 3'
        ];

        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $form->method('getErrorManager')->willReturn($errorManager);
        $element = new Dropdown('tx_ameosform-unittest[my-select]', 'my-select', $elementConfiguration, $form);
        $element->with('style', 'display: block;');
        $result = $element->getRenderingInformation();

        $this->assertEquals($result, $expectedResult);
    }
}
