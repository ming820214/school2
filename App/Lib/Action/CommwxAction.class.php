<?php
// 本类由系统自动生成，仅供测试用途
class CommwxAction extends Action {

    Public function _initialize(){
    	//判断是否登陆状态
    	if (!session('?wxid')){
    		//获取用户openid
	    	if (isset($_GET['code'])&&$_GET['code']!=''){
				//==获取code
				$code=$_GET['code'];
				$appid="wx0fa3e4e59355ad6d";
				$secret="1c6e7cc02f4bebe694eb59d4d5c07266";


				//====通过code换取网页授权access_token
			    $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";  
				$ch = curl_init($url);  
			    curl_setopt($ch, CURLOPT_HEADER, 0);  
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			    curl_setopt($ch, CURLOPT_POST, 0);  
			    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
		        $output = curl_exec($ch);  
		        curl_close($ch);  
				$output = stripslashes($output);
				// var_dump(json_decode($output, true));
				$info=json_decode($output, true);
				// echo "获取到的id为：".$info['openid'];

				//=====拉取用户信息
				$access_token=$info['access_token'];
				$openid=$info['openid'];
				$ur2="https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
				$ch = curl_init($ur2);  
			    curl_setopt($ch, CURLOPT_HEADER, 0);  
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
			    curl_setopt($ch, CURLOPT_POST, 0);  
			    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
		        $output = curl_exec($ch);  
		        curl_close($ch);  
				$output = stripslashes($output);
				// var_dump(json_decode($output, true));
				$info=json_decode($output, true);
				// echo "获取到的用户昵称为：".$info['nickname'];
				

				session('openid',$openid);
				//数据库查询判断用户是否合法
				$where['wxid']=$openid;
				$m=M('student')->where($where)->find();
				if($m){
					session('school',$m['school']);
		    		session('id',$m['id']);
		    		session('namee',$m['name']);
		    		session('wxid',$openid);
				}else{
					$this->redirect('Weixinlogin/index');
				}

		    }
		}

    }

}
?>