<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\User as UserModel;
use app\admin\model\File as FileModel;
use app\admin\validate\User as UserValite;
use app\admin\model\Room as RoomModel;
 use think\response\Download;
 use think\Request;
class User extends Controller{
 	
 	//验证登录
 	public function check(){
 		$data =input('post.');
    	$user = new UserModel();
    	$ret=$user->where('name',$data['name'])->find();
    	if ($ret) {
    		if ($ret['password'] === md5($data['password'])) {
    			session('name',$data['name']);
    		}else{
    			$this->error('密码错误');
    			exit;
    		}
    	}else{
    		$this->error('用户不存在!!');
    		exit;
    	}
    		if(captcha_check($data['code'])){
 			$this->success('登陆成功','admin/User/list');	
			}else{
				$this->error('验证码错误');
				exit;
			}
 	}
 	
	public function list(){
		$name = session('name');
		//var_dump($name);
		$this->assign('name',$name);
		return $this->fetch();
	} 


 	public function add(){
 		return $this->fetch();
 	}

 	//用户注册
 	public function insert(){

 		$data=input('post.');
 		//验证码检验
 	
	if( !captcha_check($data['code']))
	{
		$this->error('验证码错误');
		exit;
	}

			$val = new UserValite();
		  if (!$val->check($data)) {
         $this->error($val->getError());
         exit;
      }
      $user = new UserModel($data);
      $ret = $user->allowField(true)->save();

      if ($ret) {
      	 $this->success('用户创建成功','login');
      }else{
      	$this->error('用户创失败');
      	exit;
      }

	}

//	登录页
	public function login(){
 		return $this->fetch();
 	}


	//课室页
	public function room(){
		if(session('name') == null){
 			$this->error('未登录,请先登录','login');
 		}
 		$name = session('name');
 		$list = RoomModel::paginate(10);
			// 获取分页显示
		$page = $list->render();
			// 模板变量赋值
		$this->assign('list', $list);
		$this->assign('page', $page);
 		$this->assign('name',$name);
		return $this->fetch();
	}


	//申请页
	public function shenqing(){
		if(session('name') == null){
 			$this->error('未登录,请先登录','login');
 		}
 			$name = session('name');
 			$id =input('get.id');
 			$this->assign('name',$name);
 			$this->assign('id',$id);
			return $this->fetch();
	}


	//申请后的操作页
	public function shen(){
		$name = session('name');
		$data = input('post.');
		 $room = new RoomModel();
		 $ret=$room->save([
     'name'  => $name ,
     'date' => $data['date'],
     'reason'=> $data['reason'],
     'status'=>"申请中",
      ],['id' => $data['id']]);

		if ($ret) {
			$this->success('申请成功，请等待管理员审核','admin/User/room');
		}else{
			$this->error('提交失败');
		}



	}









 	//下载页
 	public function down(){
 		if(session('name') == null){
 			$this->error('未登录,请先登录','login');
 			exit;
 		}
 				 $name=session('name');
 				$a = FileModel::paginate(13);
 				$page = $a->render();
 				$this->assign('name',$name);
 				$this->assign('page',$page);
 				$this->assign('file',$a);



 				return $this->fetch();
 	}

	public function download(){
	$a = input('get.id');
	$data = FileModel::get($a);
	//var_dump($data);exit;
	$file = new Download($_SERVER['DOCUMENT_ROOT'].'by/public/static/downfile/'. $data['filenum']);
	 return $file->name($data['filename']);
	// return download( $_SERVER['DOCUMENT_ROOT'].'by/public/static/downfile/one.jpg', 'my.jpg');
	}

 	//退出登录
  public function logout(){
     	session(null);
     	$this->success('退出登录成功','login');
     }
}