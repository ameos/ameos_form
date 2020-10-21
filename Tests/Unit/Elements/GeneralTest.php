<?php

namespace Ameos\AmeosForm\Tests\Unit\Elements;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class GeneralTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @test
     */
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

        $form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
        $form->add('input-text', 'text', $elementConfiguration);
        $result = $form->get('input-text')->toHtml();

        $this->assertEquals($result, $expectedResult);
    }

    /**
     * @test
     */
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
        ];

        $elementConfiguration = [];
        $elementConfiguration['defaultValue'] = 'my-value';
        $elementConfiguration['class']        = 'my-css-class';
        $elementConfiguration['title']        = 'My text field';
        $elementConfiguration['custom']       = 'customattr="custom-value"';
        $elementConfiguration['placeholder']  = 'Here your value';
        $elementConfiguration['otherdata']    = 'test';

        $form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');

        $form->add('input-text', 'text', $elementConfiguration);
        $form->get('input-text')->with('style', 'display: block;');
        
        $result = $form->get('input-text')->getRenderingInformation();

        $this->assertEquals($result, $expectedResult);
    }
}
