<?php
/**
 * 上传封装
 */
class Upload {
	public $name;//文件名

	public $size;

	public $maxSize;//大小限制，单位KB，默认100MB

	public $type;//上传文件的后缀名

	public $error;

	/**
	 * 后缀名限制
	 * 字符串格式，允许多种则以','隔开
	 */
	public $allowType;

	public $tmp_name;

	public function Upload($file){
		$this->tmp_name = $file['tmp_name'];
		$this->size = $file['size'] / 1024;
		$this->maxSize = 1024*1024*100;
		$this->setFilenameAndSuffix($file['name']);
		$this->error = $file['error'];
	}

	public static function model($file){
		return new Upload($file);
	}

	/**
	 * 设置上传文件的最大体积(KB)
	 */
	public function setMaxSize($size){
		$this->maxSize = $size;
		return $this;
	}

	/**
	 * 设置允许上传格式
	 * 若有多个由','分开
	 */
	public function setAllowType($type){
		$this->allowType = $type;
		return $this;
	}

	/**
	 * 重新设置文件名
	 */
	public function setName($name){
		$this->name = $name;
		return $this;
	}

	/**
	 * 初始化文件名和后缀名
	 */
	private function setFilenameAndSuffix($filename){
		$index = strrpos($filename, '.');
		if ($index != false) {
			$this->name = substr($filename, 0, $index);
			$this->type = substr($filename, $index + 1);
		}else
			$this->name = $filename;
	}

	/**
	 * 保存上传文件
	 * @param path string 保存目录
	 * @param isFullPath boolean 默认为APP_PATH目录下的路径，为true时则是自定义路径
	 * 
	 * 保存目录不存在时会自动创建该目录
	 */
	public function save($path = '', $isFullPath = false){
		if ($this->error > 0 || $this->size > $this->maxSize || !($this->allowType != null && in_array($this->type, explode(',', $this->allowType))) ) {
			return false;
		}

		if ($isFullPath)
			$uploadPath = $path;
		else
			$uploadPath = APP_PATH . '/' . trim($path,'/') . '/';
		if (!file_exists($uploadPath)) {
			mkdir($uploadPath);
		}

		$file = $uploadPath . $this->name .'.'. $this->type;
		return move_uploaded_file($this->tmp_name, $file);
	}
}
