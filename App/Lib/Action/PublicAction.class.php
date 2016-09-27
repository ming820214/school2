<?php
	class PublicAction extends Action{//登录验证码处理
		public function code(){
			import('ORG.Util.Image');
    		Image::buildImageVerify(4,1,'png',60,25,'code');
		}
	}
?>
