<?php
class ComponentLoader{
	private $ParamFilter;

	public function __get($property){
		$property = ucfirst($property);
		if (empty($this->property)) {
			$this->property = new $property;
		}
		return $this->property;
	}
}
