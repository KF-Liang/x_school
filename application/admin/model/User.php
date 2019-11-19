<?php
namespace app\admin\model;
	use think\Model;
    use think\Request;
class User extends Model{
	protected $auto = ['password'];
	
	protected function setPasswordAttr($value){
		return md5($value);
	}
}