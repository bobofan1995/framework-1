<?php
class DBException extends Exception implements ExceptionInterface{
	public function errorMessage(){
		//error message
		$errorMsg = '<b>'.__CLASS__ . '</b> : Error on line '.$this->getLine().' in '.$this->getFile(). ' ' . $this->getMessage();
		return $errorMsg;
	}
}