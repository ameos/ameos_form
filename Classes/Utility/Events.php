<?php

namespace Ameos\AmeosForm\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class Events {

	/**
	 * @var array<Ameos\AmeosForm\Utility\Events>
	 */
	protected static $instances = [];

	/**
	 * @static
	 * @var array $registeredEvents
	 */
	protected $registeredEvents = [];

	/**
	 * @constructor
	 */
	private function __construct() {
		
	}

	/**
	 * get instance by form identifier
	 * @static
	 * @param string $formIdentifier form identifier
	 * @return Ameos\AmeosForm\Utility\Events
	 */
	public static function getInstance($formIdentifier) {
		if(!array_key_exists($formIdentifier, self::$instances)) {
			$newInstance = new Events();
			self::$instances[$formIdentifier] = $newInstance;
		}
		return self::$instances[$formIdentifier];
	}

	/**
	 * register an event
	 * @param string $eventIdenfifier event idenfifier
	 * @param callable $callback call back method
	 * @param array $arguments event arguments
	 * @return Ameos\AmeosForm\Utility\Events
	 */
	public function registerEvent($eventIdenfifier, $callback, $arguments) {
		if(!array_key_exists($eventIdenfifier, $this->registeredEvents)) {
			$this->registeredEvents[$eventIdenfifier] = [];
		}
		$this->registeredEvents[$eventIdenfifier][] = [
			'callback'  => $callback,
			'arguments' => $arguments
		];
		return $this;
	}

	/**
	 * trigger an event
	 * @param string $eventIdenfifier event idenfifier
	 */
	public function trigger($eventIdenfifier) {
		if(array_key_exists($eventIdenfifier, $this->registeredEvents)) {
			foreach($this->registeredEvents[$eventIdenfifier] as $event) {
				call_user_func_array($event['callback'], $event['arguments']);
			}
		}
	}
}
