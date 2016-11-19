<?php

// 本类由系统自动生成，仅供测试用途
class WeixinShareAction extends JssdkAction {

	public function __construct(){
		parent::__construct("wx0fa3e4e59355ad6d", "1c6e7cc02f4bebe694eb59d4d5c07266");
	}
	
	/**
	 *返回微信签名信息
	 */
	public function getSignatureInfo(){
		
		$tk=M('access_share')->where('id=1')->find();
		if(!$tk || $tk['expire_time']<time()){
			$data = $this->getSignPackage();
		}else{
			$data= $tk;				
		}
		
		
		$this->ajaxReturn($data,'JSON');
	}
	
	public function hongwenshare(){
		
// 		$data = $this->getSignPackage();
		
		$this->display();
	}
	
//---首页-----
    public function index(){

			$m = M('school');
			$condation['school']=$_SESSION['school'];
			$st=$m->where($condation)->find();
			$this->assign(sc,$st);
			$this->display();

    }
    	

//----讲师点评查询
	public function comment(){
		$m=M('student');
		$where['id']=$_SESSION['id'];
		$mm=$m->where($where)->find();
		$this->assign(com,$mm);
		$this->display();

	}

//----课表查询
	public function classs(){


    	$arr['id']=$_SESSION['id'];
		$st=M('student')->where($arr)->find(); 
        $this->assign('vo',$st);	

        $ar['stuid']=$_SESSION['id'];
		$ar['timee']=array('egt',date('Y-m-d'));

        $stt=M('class')->where($ar)->order('timee asc')->select();
            foreach ($stt as $key => $val) {
	        	$weekarray=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
            	$week=$weekarray[date('w',strtotime($val['timee']))];
            	$v[$val['timee'].'&nbsp;'.$week][]=$val['time1'].'--'.$val['time2'].'&nbsp;&nbsp;&nbsp;'.$val['class'];
            }
        $this->assign('c',$v); 
        $this->display();


	}





}