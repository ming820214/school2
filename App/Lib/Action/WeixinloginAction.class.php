<?php
// 本类由系统自动生成，仅供测试用途
class WeixinloginAction extends Action {


//---首页-----
    public function index(){

    	$this->display();

    }

//-----注册提交页面------
	public function dologin(){
		if(isset($_SESSION['openid'])&&$_SESSION['openid']!='')
		if(isset($_POST['bdname'])&&$_POST['bdname']!='')
		if(isset($_POST['password'])&&$_POST['password']!='')

				$username=$this->_post('bdname', 'htmlspecialchars');
				$password=$this->_post('password', 'htmlspecialchars');
				$wxid=$_SESSION['openid'];
				$user=M('student');
				$where['tel']=$username;
				$where['pw']=$password;
				$arr=$user->where($where)->find();
				if($arr){
					$m=M('student');
					$data['wxid']=$wxid;
					$st=$m->where($arr)->save($data); 
					$_SESSION['school']=$arr['school'];
					$_SESSION['wxid']=$arr['wxid'];
					$_SESSION['id']=$arr['id'];
					$_SESSION['namee']=$arr['name'];
					$this->success('登录成功',U('Weixin/index'));
				}else{
					$this->error('密码或用户名错误');
				}


	}


}