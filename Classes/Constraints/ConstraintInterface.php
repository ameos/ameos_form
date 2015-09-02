<?php
namespace Ameos\AmeosForm\Constraints;

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

interface ConstraintInterface 
{

	/**
	 * return true if the element is valide
	 *
	 * @param	string $value value to test
	 * @return	bool true if the element is valide
	 */
	public function isValid($value);

	/**
	 * return the message
	 * 
	 * @return	string the message
	 */
	public function getMessage();
}
