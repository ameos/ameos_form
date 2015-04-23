<?php
namespace Ameos\AmeosForm\Tests\Unit\Validators;

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

class CustomTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function customIsValid() {
		$form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
		$form->add('input-text', 'text');
		$form->validator('input-text', 'custom', 'custom error', ['method' => function($value, $form) {
			return $value == 'test';
		}]);

		$_POST['tx_ameosform-unittest']['issubmitted'] = 1; // simulate post form
		
		$form->bindRequest(array('input-text' => 'test'));
		$result = $form->get('input-text')->isValid();

		$this->assertTrue($result);
	}

	/**
	 * @test
	 */
	public function customIsNotValid() {
		$form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
		$form->add('input-text', 'text');
		$form->validator('input-text', 'custom', 'custom error', ['method' => function($value, $form) {
			return $value == 'test';
		}]);

		$_POST['tx_ameosform-unittest']['issubmitted'] = 1; // simulate post form
		
		$form->bindRequest(array('input-text' => 'othervalue'));
		$result = $form->get('input-text')->isValid();

		$this->assertFalse($result);
	}
}
