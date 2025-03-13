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

class RequiredTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @test
     */
    public function requiredIsValid()
    {
        $form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
        $form
            ->disableCsrftoken()
            ->add('input-text', 'text')
            ->addConstraint('input-text', 'required', 'field mandatory');

        $_POST['tx_ameosform-unittest']['issubmitted'] = 1; // simulate post form

        $result = $form->get('input-text')->isValid();

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function requiredIsNotValid()
    {
        $form = \Ameos\AmeosForm\Form\Factory::make('tx_ameosform-unittest');
        $form
            ->disableCsrftoken()
            ->add('input-text', 'text')
            ->addConstraint('input-text', 'required', 'field mandatory');

        $_POST['tx_ameosform-unittest']['issubmitted'] = 1; // simulate post form

        $result = $form->get('input-text')->isValid();

        $this->assertFalse($result);
    }
}
