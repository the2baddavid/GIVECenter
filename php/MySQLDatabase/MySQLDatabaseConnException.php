<?php

include_once(dirname(__FILE__).'/MySQLException.php');

//Thrown when a MySQLDatabaseConn object fails to connect to a
//database, either because of invalid username or password
//or because a remote connection could not be established.
class MySQLDatabaseConnException extends MySQLException
{
	/**
	 * MySQLDatabaseConnException constructor, occurs when a MySQL database
	 * fails to establish a connection.
	 * @param string $message - the name of the file that caused the exception
	 * @param int $code - custom error code, defaults to zero
	 * @param Exception $previous - previous exception, defaults to null
	 */
	public function __construct($message, $code = 0, Exception $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
	
	/**
	 * @see Exception::__toString()
	 */
	public function __toString()
	{
		return __CLASS__ . ": [{$this->code}]: [{$this->message}]";
	}
}

?>