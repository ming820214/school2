<?php
	class LoginAction extends Action{
		public function index(){
			// $this->tongji();//触发每日数据统计
			// M('tongji')->where(['timestamp'=>['like','2015-09-06%']])->delete();
			$this->display();
		}

		public function doLogin(){
			//接受值
			//判断用户在数据库中是否存在
			//存在 允许登录
			//不存在 显示错误信息
			$username=$this->_POST('username');
			$password=$this->_POST('password');
			$code=$_POST['code'];
			if(md5($code)!=$_SESSION['code']){
				$this->error('验证码不正确');
			}
			$user=M('user');
			$where['user']=$username;
			$where['pw']=md5($password);
			$arr=$user->where($where)->find();
			if($arr){
				$_SESSION['school']=$arr['school'];
				$_SESSION['id']=$arr['id'];
				$_SESSION['user']=$arr['user'];
				$_SESSION['tel']=$arr['tel'];
				$_SESSION['position']=$arr['position'];
				$data['log']='<br/>#登录'.date('Y-m-d H:i:s').'IP：'.($this->ip()).$arr['log'];
//              R('Send/text',array(0,$arr['user'],date('Y-m-d H:i:s').'你的排课系统账号在电脑端登陆，如非本人操作请及时修改密码'));
				R('Send/text',array(2,$arr['user'],date('Y-m-d H:i:s').'你的排课系统账号在电脑端登陆，如非本人操作请及时修改密码'));
				if(M('user')->where($arr)->save($data)){
					$password=='ok'?$this->success('安全考虑请及时修改密码，否则将停用该账户……',U('Index/index'),10):$this->redirect('Index/index');
				}else{
					$this->error('登录出错');
					session('[destroy]');
				}
			}else{
					$this->error('密码或用户名错误');
			}
		}



		//获取用户IP
		public function ip(){

			$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
			$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"];
			return $user_IP;
		}


	public function tongji(){
		// $w['date']=date('Y-m-d',time()-24*3600);
		$d=1409500800;
		for ($i=0; $i < 100; $i++) {
			$w['date']=date('Y-m-d',$d);

			if(!M('tongji')->where($w)->find()){
				$date=date('Y-m',$d);
				$w2['timee']=array('like',$date."%");
				$w2['state']=array('in','0,1');
				$m=M('class')->where($w2)->order('school,timee,teacher,time1,grade')->getField('id,school,timee,class,grade,time1,teacher,count',true);
				foreach ($m as $val) {
		            unset($val['id']);
		            if($val!=$cc){
						$t[$val['school']][$val['class']]+=$val['count'];
					}
		            $cc=$val;
				}
				foreach ($t as $key => $val2) {
					$dd[]=array('school'=>$key,'date'=>$w['date'],'a'=>$val2['数学'],'b'=>$val2['语文'],'c'=>$val2['英语'],'d'=>$val2['物理'],'e'=>$val2['化学'],'f'=>$val2['生物'],'g'=>$val2['政治'],'h'=>$val2['历史'],'i'=>$val2['地理']);
				}
				// var_dump($dd);die;
				M('tongji')->addall($dd);
				unset($dd,$t);
			$d+=24*3600;
			}
		}

	}


	public function wx(){


			/**
			 * Curl版本
			 * 使用方法：
			 * $post_string = "app=request&version=beta";
			 * request_by_curl('http://facebook.cn/restServer.php',$post_string);
			 */
			function request_by_curl($remote_server, $post_string)
			{
			    $ch = curl_init();
			    curl_setopt($ch, CURLOPT_URL, $remote_server);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, 'mypost=' . $post_string);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    // curl_setopt($ch, CURLOPT_USERAGENT, "Jimmy's CURL Example beta");
			    $data = curl_exec($ch);
			    curl_close($ch);
			    return $data;
			}

			$msg['touser']='WWW';
			$msg['msgtype']='text';
			$content['content']='5555';
			$msg['agentid']=5;
			$msg['text']=json_encode($content);
			$post_string = json_encode($msg);
			// print($post_string);
			// die;
			request_by_curl('https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=cV7QJ6ET_5N_Z2VUPARQUWXSiRZdJ0WlM7HoStv1cAKmZCFoZWTMVJtuvRsOLA8Z',$post_string);







	}
}

?>
