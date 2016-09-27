<?php

class SendAction extends Action {

	public function Index(){
		

		$this->text(0,张晓明,1564646);
		// R('Send/text',array(4,郝振华,1364588888888845));
		// 	$msg['touser']='WWW';
		// 	$msg['msgtype']='text';
		// 	$msg['agentid']=0;
		// 	$msg['text']['content']='16464341';
		// if($this->send($msg))return true;
	}

//发送text消息
	public function text($app,$name,$message){
		if(is_array($name)){
			foreach ($name as $val) {
				$w['name']=$val;
				$w['state']=1;
				$m=M('hw003.person_all',null)->where($w)->find();
				$n[]=$m['userid'];
			}
			$name=implode("|",$n);
			$msg['touser']=$name;
			
		}else{
			$w['name']=$name;
			$w['state']=1;
			$m=M('hw003.person_all',null)->where($w)->find();
			$msg['touser']=$m['userid'];
		}
			$msg['msgtype']='text';
			$msg['agentid']=$app;
			$msg['text']['content']=$message;
		if($this->send($msg))return true;
	}

//发送数据
	public function send($msg){
		//接收到要发送的数据
		$post=$this->ch_json_encode($msg);
		//获取access_tokon
		$tk=M('access')->where('id=2')->find();
		//判断tokon是否过期
		if(($tk['timestamp']+7000)<time() or $tk['tokon']==''){
//					$CorpID='wx965351f4462ae3ba';
//			 		$Secret='8fYYV_V5kfCCggQuBaoc4pLkJw2d_G7KtEuPlzSMk2YEy7FHoZvImEAuBu1vrwGn';
					
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
					$access_token = $access_token['access_token'];//获取到值
			$date['tokon']=$access_token;
			$date['timestamp']=time();
			M('access')->where('id=2')->save($date);
		//发送数据=================
				$urll='https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$access_token;
		}else{
				$urll='https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$tk['tokon'];
		}
				$ch = curl_init($urll);
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			    curl_setopt($ch, CURLOPT_SSLVERSION,CURLOPT_SSLVERSION_TLSv1);
			    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		        $output = curl_exec($ch);
		        curl_close($ch);
				$output = stripslashes($output);
				$out =json_decode($output, true);//转成数组
				if($out['errmsg']=='ok')return true;
	}

//解决json将中文编码编码问题
	function ch_json_encode($data) {
		$ret = $this->ch_urlencode($data);
		$ret =json_encode($ret);
		return urldecode($ret);
	}
	function ch_urlencode($data) {
		if (is_array($data) || is_object($data)) {
			foreach ($data as $k => $v) {
				if (is_scalar($v)) {
					if (is_array($data)) {
						$data[$k] = urlencode($v);
					} elseif (is_object($data)) {
						$data->$k =urlencode($v);
					}
				} elseif (is_array($data)) {
					$data[$k] = $this->ch_urlencode($v);//递归调用该函数
				} elseif (is_object($data)) {
					$data->$k = $this->ch_urlencode($v);
				}
			}
		}
		return $data;
	}


}