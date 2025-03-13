<?php

namespace Ameos\AmeosForm\Tests\Unit\Elements;

use Ameos\AmeosForm\Elements\Text;
use Ameos\AmeosForm\ErrorManager;
use Ameos\AmeosForm\Form\Form;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class GeneralTest extends UnitTestCase
{
    protected bool $resetSingletonInstances = true;

    #[Test]
    public function elementRenderer()
    {
        $expectedResult = '<input type="text" id="tx_ameosform-unittest_input-text" '
            . 'name="tx_ameosform-unittest[input-text]" '
            . 'value="my-value" '
            . 'placeholder="Here your value" '
            . 'style="display: block;" '
            . 'title="My text field" '
            . 'customattr="custom-value" '
            . 'class="my-css-class" />';

        $elementConfiguration = [];
        $elementConfiguration['defaultValue'] = 'my-value';
        $elementConfiguration['class']        = 'my-css-class';
        $elementConfiguration['style']        = 'display: block;';
        $elementConfiguration['title']        = 'My text field';
        $elementConfiguration['custom']       = 'customattr="custom-value"';
        $elementConfiguration['placeholder']  = 'Here your value';

        $form = $this->createMock(Form::class);
        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', $elementConfiguration, $form);
        $result = $element->toHtml();

        $this->assertEquals($result, $expectedResult);
    }

    #[Test]
    public function elementRendereringInformation()
    {
        $expectedHtmlResult = '<input type="text" id="tx_ameosform-unittest_input-text" '
            . 'name="tx_ameosform-unittest[input-text]" '
            . 'value="my-value" '
            . 'placeholder="Here your value" '
            . 'style="display: block;" '
            . 'title="My text field" '
            . 'customattr="custom-value" '
            . 'class="my-css-class" />';

        $expectedResult = [
            'defaultValue' => 'my-value',
            'class'        => 'my-css-class',
            'style'        => 'display: block;',
            'title'        => 'My text field',
            'custom'       => 'customattr="custom-value"',
            'placeholder'  => 'Here your value',
            'otherdata'    => 'test',
            '__compiled'   => $expectedHtmlResult,
            'name'         => 'input-text',
            'value'        => 'my-value',
            'absolutename' => 'tx_ameosform-unittest[input-text]',
            'htmlid'       => 'tx_ameosform-unittest_input-text',
            'errors'       => [],
            'isvalid'      => true,
            'hasError'     => false,
            'required'     => false,
        ];

        $elementConfiguration = [];
        $elementConfiguration['defaultValue'] = 'my-value';
        $elementConfiguration['class']        = 'my-css-class';
        $elementConfiguration['title']        = 'My text field';
        $elementConfiguration['custom']       = 'customattr="custom-value"';
        $elementConfiguration['placeholder']  = 'Here your value';
        $elementConfiguration['otherdata']    = 'test';

        $form = $this->createMock(Form::class);
        $errorManager = new ErrorManager($form);
        $form->method('getErrorManager')->willReturn($errorManager);
        $element = new Text('tx_ameosform-unittest[input-text]', 'input-text', $elementConfiguration, $form);
        $element->with('style', 'display: block;');
        $result = $element->getRenderingInformation();

        $this->assertEquals($result, $expectedResult);
    }
}
