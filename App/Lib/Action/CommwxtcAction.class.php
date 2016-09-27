<?php
// 本类由系统自动生成，仅供测试用途
class CommwxtcAction extends Action {

    Public function _initialize(){
    		// if(!session('?tc_name'))
    		//获取员工id
		    	if (isset($_GET['code'])&&$_GET['code']!=''){
					//==获取code
					$code=$_GET['code'];
						//获取并判断access_tokon是否过期
						$tk=M('access')->where('id=1')->find();
						if((time()-$tk['timestamp'])>7000){
//								$CorpID='wx965351f4462ae3ba';
//						 		$Secret='8fYYV_V5kfCCggQuBaoc4pLkJw2d_G7KtEuPlzSMk2YEy7FHoZvImEAuBu1vrwGn';
								$CorpID='wx48efe07c32d6e8fa';
						 		$Secret='JrQ85DM3IQetnZDXTrifzYiDuu1lMYlSE4bSx2SSy3Y0ouh6ltDQRwliUTHRfm0Q';
								    $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$CorpID&corpsecret=$Secret";  
									$ch = curl_init($url);
								    curl_setopt($ch, CURLOPT_HEADER, 0);  
								    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
								    curl_setopt($ch, CURLOPT_POST, 0);  
								    curl_setopt($ch, CURLOPT_SSLVERSION,CURLOPT_SSLVERSION_TLSv1);
								    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
							        $output = curl_exec($ch);  
							        curl_close($ch);  
									$output = stripslashes($output);//获取到json格式的token和时间限制数据
									// echo($output);
									$access_token =json_decode($output, true);//转成数组
					//获取access_tokon
									$access_token = $access_token['access_token'];//获取到值
							$date['tokon']=$access_token;
							$date['timestamp']=time();
							M('access')->where('id=1')->save($date);
						}else{
							$access_token=$tk['tokon'];
						}
					// echo($access_token);
					//调用的应用id
//					$agentid=4;//讲师助手
					$agentid=5;//讲师助手

					//====通过code换取获取员工id信息
				    $url = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=$access_token&code=$code&agentid=$agentid";
					$ch = curl_init($url); 
				    curl_setopt($ch, CURLOPT_HEADER, 0);  
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
				    curl_setopt($ch, CURLOPT_POST, 0);  
				    curl_setopt($ch, CURLOPT_SSLVERSION,CURLOPT_SSLVERSION_TLSv1);
				    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
			        $output = curl_exec($ch);  
			        curl_close($ch);
					$output = stripslashes($output);
					// var_dump(json_decode($output, true));
					$info=json_decode($output, true);
					// var_dump($info);
					$user_id=$info['UserId'];

					//====通过id换取获取员工资料信息$user_info
				    $url = "https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token=$access_token&userid=$user_id";
					$ch = curl_init($url);  
				    curl_setopt($ch, CURLOPT_HEADER, 0);  
				    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
				    curl_setopt($ch, CURLOPT_POST, 0);
				    curl_setopt($ch, CURLOPT_SSLVERSION,CURLOPT_SSLVERSION_TLSv1);
				    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);  
			        $output = curl_exec($ch);  
			        curl_close($ch);
					$output = stripslashes($output);
					// var_dump($output);
					$user_info=json_decode($output, true);
					// 将获取到的值存储到seccion
					session('tc_name',$user_info['name']);
					// session('tc_pic',$user_info['avatar']);

			    }

    }

}


?>