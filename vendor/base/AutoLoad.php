<?php
/**
 * 类的自动加载
 * @param classMap array 把某些类加入索引文件中
 * @param classFolder array 把某些目录的路径加入索引目录中
 */
class AutoLoad{
	public static $classMap;

	public static $classFolder;

	public static function autoLoader($className){

		if (isset(static::$classMap[$className])){
			if (substr(static::$classMap[$className], -4) != '.php')
				include(VENDOR_PATH . static::$classMap[$className] . '/' . $className . '.php');
			else
				include(static::$classMap[$className]);

			return;
		}

		foreach (static::$classFolder as $folder) {
			$file = $folder . '/' . $className . '.php';
			if (file_exists($file)) {
				include($file);
				return;
			}
		}
	}
}
