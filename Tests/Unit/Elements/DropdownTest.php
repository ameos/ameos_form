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

class DropdownTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function elementRenderer() {
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
		$elementConfiguration['items']        = ['value-1' => 'Value 1', 'value-2' => 'Value 2', 'value-3' => 'Value 3'];

		$form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
		$form->add('my-select', 'dropdown', $elementConfiguration);
		$result = $form->get('my-select')->toHtml();

		$this->assertEquals($result, $expectedResult);
	}

	/**
	 * @test
	 */
	public function elementRendereringInformation() {
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
			'isvalid'      => TRUE,
			'hasError'     => FALSE,
			'optionValueField' => 'uid',
		];

		$elementConfiguration = [];
		$elementConfiguration['defaultValue'] = 'value-2';
		$elementConfiguration['class']        = 'my-css-class';
		$elementConfiguration['title']        = 'My text field';
		$elementConfiguration['custom']       = 'customattr="custom-value"';
		$elementConfiguration['placeholder']  = 'Here your value';
		$elementConfiguration['otherdata']    = 'test';
		$elementConfiguration['items']        = ['value-1' => 'Value 1', 'value-2' => 'Value 2', 'value-3' => 'Value 3'];

		$form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');

		$form->add('my-select', 'dropdown', $elementConfiguration);
		$form->get('my-select')->with('style', 'display: block;');
		
		$result = $form->get('my-select')->getRenderingInformation();

		$this->assertEquals($result, $expectedResult);
	}
}
