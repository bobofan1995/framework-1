<?php
/**
 * http://localhost?yc=user/login
 * if rewrite:
 * http://localhost/login
 */
class UserController extends Controller{
	public function actionLogin(){
		$this->title = '管理系统登录';
		return $this->view('login');
	}
}
