<?php
class JssdkAction extends Action {
  private $appId;
  private $appSecret;
  private $access_token;
  
  public function __construct($appId, $appSecret) {
    $this->appId = $appId;
    $this->appSecret = $appSecret;
  }

  public function getSignPackage() {
  	
  	$data=M('access_share')->where('id=1')->find();
  	
    $jsapiTicket = $this->getJsApiTicket($data);

    // 注意 URL 一定要动态获取，不能 hardcode.
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//     $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";

    $timestamp = time();
    $nonceStr = $this->createNonceStr();

    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

    $signature = sha1($string);

    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    
//     $data=M('access_share')->where('id=1')->find();
    
    $data['appId'] = $this->appId;
    $data['nonceStr'] = $nonceStr;
    
    $data['signature'] = $signature;
    $data['jsapi_ticket'] = $jsapiTicket;
    $data['access_token'] = $this->access_token;
    
    if($data == null){
    	M('access_share')->add($data);
    }else if($data['timestamp'] < time()){
    	M('access_share')->save($data);
    }
    return $signPackage; 
  }

  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }

  private function getJsApiTicket(&$data) {
    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
  	
  	if($data){
  		if ($data['timestamp'] < time()) {
  			$accessToken = $this->getAccessToken($data);
  			// 如果是企业号用以下 URL 获取 ticket
  			// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
  			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
  			$res = json_decode($this->httpGet($url));
  			$ticket = $res->ticket;
  			if ($ticket) {
//   				$data['timestamp'] = time() + 7000;
  				$data['jsapi_ticket'] = $ticket;
  				//         $this->set_php_file("jsapi_ticket.php", json_encode($data));
  				M('access_share')->where('id=1')->save($data);
  			}
  		} else {
  			$ticket = $data['jsapi_ticket'];
  		}
  	}else{
  		$accessToken = $this->getAccessToken();
  		// 如果是企业号用以下 URL 获取 ticket
  		// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
  		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
  		$res = json_decode($this->httpGet($url));
  		$ticket = $res->ticket;
  	}

    return $ticket;
  }

  private function getAccessToken(&$data) {
    // access_token 应该全局存储与更新，以下代码以写入到文件中做示例

  	if($data){
  		if ($data['timestamp'] < time()) {
  			// 如果是企业号用以下URL获取access_token
  			// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
  			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
  			$res = json_decode($this->httpGet($url));
  			$access_token = $res->access_token;
  			if ($access_token) {
  				$data['timestamp'] = time() + 7000;
  				$data['access_token'] = $access_token;
  				M('access_share')->where('id=1')->save($data);
  			}
  		} else {
  			$access_token = $data['access_token'];
  		}
  	}else{
  		// 如果是企业号用以下URL获取access_token
  		// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
  		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
  		$res = json_decode($this->httpGet($url));
      	$access_token = $res->access_token;
  		
  	}
    
  	$this->access_token = $access_token; 
    
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
    // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }

  private function get_php_file($filename) {
    return trim(substr(file_get_contents($filename), 15));
  }
  private function set_php_file($filename, $content) {
    $fp = fopen($filename, "w");
    fwrite($fp, "<?php exit();?>" . $content);
    fclose($fp);
  }
}

