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

class SameasTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @test
	 */
	public function sameasIsValid() {
		$form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
		$form->disableCsrftoken();
		$form->add('input-text-1', 'text');
		$form->add('input-text-2', 'text');
		$form->addConstraint('input-text-2', 'sameas', 'must be the same', ['sameas' => 'input-text-1']);

		$_POST['tx_ameosform-unittest']['issubmitted'] = 1; // simulate post form
		
		$form->bindRequest(array('input-text-1' => 'test', 'input-text-2' => 'test'));
		$result = $form->get('input-text-2')->isValid();

		$this->assertTrue($result);
	}

	/**
	 * @test
	 */
	public function sameasIsNotValid() {
		$form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
		$form->disableCsrftoken();
		$form->add('input-text-1', 'text');
		$form->add('input-text-2', 'text');
		$form->addConstraint('input-text-2', 'sameas', 'must be the same', ['sameas' => 'input-text-1']);

		$_POST['tx_ameosform-unittest']['issubmitted'] = 1; // simulate post form
		
		$form->bindRequest(array('input-text-1' => 'test', 'input-text-2' => 'othervalue'));
		$result = $form->get('input-text-2')->isValid();

		$this->assertFalse($result);
	}
}
