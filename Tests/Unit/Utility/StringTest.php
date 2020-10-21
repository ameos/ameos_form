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

class StringTest extends \TYPO3\TestingFramework\Core\Unit\UnitTestCase
{

    /**
     * @test
     */
    public function camelCase()
    {
        $expectedResult = [
            'foo_bar'     => 'FooBar',
            'foobar'      => 'Foobar',
            'foo_barTest' => 'FooBarTest',
        ];

        $result = [
            'foo_bar'     => \Ameos\AmeosForm\Utility\String::camelCase('foo_bar'),
            'foobar'      => \Ameos\AmeosForm\Utility\String::camelCase('foobar'),
            'foo_barTest' => \Ameos\AmeosForm\Utility\String::camelCase('foo_barTest'),
        ];
        
        $this->assertEquals($result, $expectedResult);
    }
}
