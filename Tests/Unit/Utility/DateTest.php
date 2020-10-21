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

class DateTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @test
	 */
	public function dateToTimestamp() {
		$expectedResult = [
			'12/01/1985 21:15:30'   => '474408930',
			'2005 12 24'            => '1135378800',
			'10 Jan 2003'           => '1042153200',
		];

		$result = [
			'12/01/1985 21:15:30'   => \Ameos\AmeosForm\Utility\Date::dateToTimestamp('12/01/1985 21:15:30', '%d/%m/%Y %H:%M:%S'),
			'2005 12 24'            => \Ameos\AmeosForm\Utility\Date::dateToTimestamp('2005 12 24', '%Y %m %d'),
			'10 Jan 2003'           => \Ameos\AmeosForm\Utility\Date::dateToTimestamp('10 Jan 2003', '%d %b %Y'),
		];
		
		$this->assertEquals($result, $expectedResult);
	}

	/**
	 * @test
	 */
	public function timestampToDate()
	{
		$expectedResult = [
			'1135582400' => '2005 12 26',
			'1135882400' => '29/12/2005 - 19:53:20',
			'1242156800' => '12/05/2009',
		];

		$result = [
			'1135582400' => \Ameos\AmeosForm\Utility\Date::timestampToDate('1135582400', '%Y %m %d'),
			'1135882400' => \Ameos\AmeosForm\Utility\Date::timestampToDate('1135882400', '%d/%m/%Y - %H:%M:%S'),
			'1242156800' => \Ameos\AmeosForm\Utility\Date::timestampToDate('1242156800', '%d/%m/%Y'),
		];
		
		$this->assertEquals($result, $expectedResult);
	}

}
