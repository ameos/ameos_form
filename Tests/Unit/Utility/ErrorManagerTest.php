<?php

namespace Ameos\AmeosForm\Tests\Unit\Utility;

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

class ErrorManagerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @test
     */
    public function addErrors()
    {
        $form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
        $form->disableCsrftoken()->add('input-text-required', 'text')->addConstraint('input-text-required', 'required', 'field mandatory');
        $form->disableCsrftoken()->add('input-text-email', 'text')->addConstraint('input-text-email', 'email', 'email not valid');
        $form->disableCsrftoken()->add('input-text-required-2', 'text')->addConstraint('input-text-required-2', 'required', 'field mandatory');

        $_POST['tx_ameosform-unittest']['issubmitted'] = 1; // simulate post form
        
        $form->bindRequest(['input-text-required' => '', 'input-text-email' => 'mail-not-valid', 'input-text-required-2' => 'test']);

        $expectedResult = [
            'is_valid' => false,
            'errors_merged'     => ['field mandatory', 'email not valid'],
            'errors_by_element' => ['input-text-required' => ['field mandatory'], 'input-text-email' => ['email not valid']],
            'errors_for_text'   => ['field mandatory'],
            'errors_for_mail'   => ['email not valid'],
        ];
        
        $result = [
            'is_valid' => $form->isValid(),
            'errors_merged'     => $form->getErrors(),
            'errors_by_element' => $form->getErrorsByElement(),
            'errors_for_text'   => $form->getErrorsFormElement('input-text-required'),
            'errors_for_mail'   => $form->getErrorsFormElement('input-text-email'),
        ];

        $this->assertEquals($result, $expectedResult);
    }
}
