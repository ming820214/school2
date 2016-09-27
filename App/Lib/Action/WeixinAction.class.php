<?php
// 本类由系统自动生成，仅供测试用途
class WeixinAction extends CommwxAction {


//---首页-----
    public function index(){

			$m = M('school');
			$condation['school']=$_SESSION['school'];
			$st=$m->where($condation)->find();
			$this->assign(sc,$st);
			$this->display();

    }
    	


//----课时查询
	public function course(){

		$m=M('course');
		$where['stuid']=$_SESSION['id'];
		$where['state']=0;
		$mm=$m->where($where)->find();
		$name=$_SESSION['namee'];
		// var_dump($mm);
		// var_dump($_SESSION);
		// session('[destroy]'); 
		$this->assign(name,$name);
		$this->assign(cou,$mm);
		//========课时说明目录
		$a=$mm['a'];
		if($a==0){$a='';}
		$b=$mm['b'];
		if($b==0){$b='';}
		$c=$mm['c'];
		if($c==0){$c='';}
		$d=$mm['d'];
		if($d==0){$d='';}
		$e=$mm['e'];
		if($e==0){$e='';}
		$f=$mm['f'];
		if($f==0){$f='';}
		$g=$mm['g'];
		if($g==0){$g='';}
		$h=$mm['h'];
		if($h==0){$h='';}
		$i=$mm['i'];
		if($i==0){$i='';}
		//====================
		$this->assign(a,$a);
		$this->assign(c,$c);
		$this->assign(b,$b);
		$this->assign(d,$d);
		$this->assign(e,$e);
		$this->assign(f,$f);
		$this->assign(g,$g);
		$this->assign(h,$h);
		$this->assign(i,$i);
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