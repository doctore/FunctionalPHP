<?php

namespace FunctionalPHP\exception;

/**
 * Thrown to indicate that the requested operation is not supported
 */
class UnsupportedOperationException extends \Exception {

	/**
	 * Extra information about the occurred error
	 */
	private $debugMessage;


	public function __construct ($message, $debugMessage = "", $code = 1) {

		parent::__construct ($message, $code);
		$this->debugMessage = $debugMessage;
	}

	/**
	 * Returns extra information about the occurred error
	 *
	 * @return extra information about the occurred error
	 */
	public function getDebugMessage() {

		return $this->mensajeDebug;
	}


	public function __toString() {

		return __CLASS__.": [{$this->getCode()}]: {$this->getMessage()}\n{$this->debugMessage}\n";
	}


	/**
	 * Returns a complete summary of the occurred error
	 *
	 * @return string con la información del error.
	 */
	public function getCompleteErrorInformation(): string {

		return "An ".__CLASS__." has occurred: \n"
			  ."File: ".$this->getFile()."\n"
			  ."Line: ".$this->getLine()."\n"
			  ."Code: ".$this->getCode()."\n"
			  ."Message: ".$this->getMessage()."\n"
			  ."Debug: ".$this->getMessage()."\n"
			  ."\nMemory: ".memory_get_usage();
	}

}

?>