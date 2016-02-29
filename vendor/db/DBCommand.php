<?php
/**
* For MySQL
*/
class DBCommand{

	public static function select($fullTableName, $select){
		if ($select === '*') 
			return [$select];
		elseif($select == null)
			return null;
		else{
			$selects = explode(',', $select);
			foreach ($selects as $key => $value) {
				$selects[$key] = $fullTableName . '.' . $value;
			}
			return $selects;
		}
	}
	/**
	 * @param fullTableName string
	 * @param where array 查询条件
	 * 判断value是否为数学或字符串，若都不是则是数组
	 * 如果是数学或字符串，则为 =
	 * 如果为数组，判断为in或逻辑等式
	 */
	public static function where($fullTableName, array $where){
		$condition = [];
		$param = [];
		$paramPre = ':' . $fullTableName . '_';
		foreach ($where as $key => $value) {
			$paramKey = $paramPre . $key;
			if (is_numeric($value) || is_string($value)) {
				array_push($condition, $fullTableName . '.' . $key . '=' . $paramKey);
				$param[$paramKey] = $value;
			}
			elseif (in_array($value[0], ['>','<','<=','>=','='])) {
				$len = count($value);
				if ($len % 2 !== 0) 
					throw new DBException('The number of values in the array must be an even', 1);
				$n = $len/2;
				for ($i=0; $i < $n; ++$i) { 
					$tempKey = $paramKey . $i;
					array_push($condition, $fullTableName . '.' . $key . $value[$i*2] . $tempKey);
					$param[$tempKey] = $value[1+$i*2];
				}
			}
			else {
				$tempCdt = $paramKey.'0';
				$param[$tempCdt] = $value[0];
				$len = count($value);
				for ($i=1; $i < $len; ++$i) { 
					$tempParamKey = $paramPre.$key.$i;
					$tempCdt .= ','.$tempParamKey;
					$param[$tempParamKey] = $value[$i];
				}
				array_push($condition, $fullTableName.'.'.$key . ' in (' . $tempCdt . ')');
			}
		}
		
		return [implode(' and ', $condition), $param];
	}

	/**
	 * for update delete insert
	 */
	public static function condition(array $condition){
		$where = [];
		foreach ($condition as $key => $value) {
			if (is_string($value) || is_numeric($value)) {
				array_push($where, "`$key` = '$value'");
			}elseif (is_array($value)) {
				if (in_array($value[0], ['>','>=', '<', '<=', '=']) && is_numeric($value[1])) {
					array_push($where, "`$key`".$value[0].$value[1]);
				}else{
					array_push($where, "`$key`".' in ("'.implode('","', $value).'")');
				}
			}
		}
		return implode(',', $where);
	}
}
