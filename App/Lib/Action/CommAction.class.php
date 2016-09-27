<?php
// 本类由系统自动生成，仅供测试用途
class CommAction extends Action {

    Public function _initialize(){

    	// 判断是否登陆过
    	if(isset($_SESSION['school']) && $_SESSION['school']!=''){
		}elseif(isset($_SESSION['schooll'])&&session('school')=='集团'){//仅集团账号可以匿名访问
		}else{
			$this->redirect('Login/index');
		}
    }



}


?>