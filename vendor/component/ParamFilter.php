<?php
class ParamFilter{
	public function isValid($param, $type){
		switch ($type) {
			case null:
				return true;
			case '\d+':
				return $this->isPosInt($param);
			case '\d':
				return $this->isInt($param);
			default:
				return false;
		}
	}
	public function isPosInt($param){
		if (!is_numeric($param) || $param < 0 || (int)$param != $param) {
			return false;
		}
		return true;
	}

	public function isInt($param){
		if (!is_numeric($param) || (int)$param != $param) {
			return false;
		}
		return true;
	}

}
