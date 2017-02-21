<?php

class WxAction extends CommwxtcAction {

	public function Index(){

	}
	//查询排课
	public function tc_class(){
		
		if($_POST['date']){
			$w['teacher']=$_POST['name'];
			$w['timee']=$_POST['date'];
	        $this->tc_name=$_POST['name'];
	        session('tc_name',$_POST['name']);
		}else{
			$w['teacher']=session('tc_name');
			$w['timee']=array('egt',date('Y-m-d'));
        	$this->tc_name=session('tc_name');			
		}
		$m=D('Class')->relation(true)->where($w)->order('timee asc,time1 asc,teacher asc,state asc')->select();
            foreach ($m as $key => $val) {
            	$c=array('t'=>$val['timee'],'g'=>$val['grade'],'tc'=>$val['teacher'],'t1'=>$val['time1'],'t2'=>$val['time2'],'c'=>$val['class']);
            	if($cc!=$c){
		        	$weekarray=array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");
	            	$week=$weekarray[date('w',strtotime($val['timee']))];
					if(($val['stuid']==88888) || ($val['stuid']==99999) || ($val['stuid']==77777) || ($val['stuid']==66666)){
	            		$student=$val['other'];
	            	}elseif($val['grade']){
	            		$student=$val['grade']['name'];
	            	}else{
	            		$student=$val['student']['name'];
	            	}
	            	//待讲师确认数据获取当下往后30天的排课确认
	            	$ls=strtotime($val['timee'])-time();
	            	if($val['tqr']==0 && $ls < 30*24*3600){
	            		$tqr[$val['timee'].'&nbsp;'.$week][$val['id']]='<td align=\'center\'>'.$val['time1'].'--'.$val['time2'].'&nbsp;&nbsp;&nbsp;'.$val['class'].'</td><td>'.$student.'</td>';
	            	}
	            	//循环重复排除
	            	$cc=array('t'=>$val['timee'],'g'=>$val['grade'],'tc'=>$val['teacher'],'t1'=>$val['time1'],'t2'=>$val['time2'],'c'=>$val['class']);
	            	//排课查询结果数据
	            	$v[$val['timee'].'&nbsp;'.$week][]='<tr><td align=\'center\'>'.$val['time1'].'--'.$val['time2'].'&nbsp;&nbsp;&nbsp;'.$val['class'].'</td><td>'.$student.'</td><td>'.$val['student']['xueguan'].'</td></tr><tr><td colspan=\'3\' align=\'center\'>上课校区：'.$val['school'].'</td></tr>';
            	}
            }
        $this->c=$v;
        $this->t=$tqr;
        $this->tite='->讲师课表查询';
        $this->display();
        // var_dump($_POST);

	}

	//确认排课
	public function tqr(){
		$da['tqr']=1;
		$d=date('Y-m-d');
		$w['timee']=array('egt',$d);
		$w['teacher']=session('tc_name');
		if($_GET['id']=='a'){
			$m=M('class')->where($w)->save($da);
			if($m){
		        //通知加课人
				$n=M('class')->where($w)->getField('teacher,add');
				foreach ($n as $val) {//此处有bug以后完善一下
					$adduser[$val['teacher']]=$val['add'];
				}
//		        R('Send/text',array(4,$adduser,'小文提示:[拥抱]\n------------\n您刚新增的排课已被讲师确认……'));
				R('Send/text',array(5,$adduser,'小文提示:[拥抱]\n------------\n您刚新增的排课已被讲师确认……'));
		        //返回
				$this->redirect('Wx/tc_class');
			}
		}else{
			$w['id']=(int)$_GET['id'];
			$m=M('class')->where($w)->save($da);
			if($m){
		        //通知加课人
				$n=M('class')->where($w)->find();
//		        R('Send/text',array(4,$n['teacher'],'小文提示:[拥抱]\n------------\n您刚新增的排课已被讲师确认……'));
				R('Send/text',array(5,$n['teacher'],'小文提示:[拥抱]\n------------\n您刚新增的排课已被讲师确认……'));
				$this->redirect('Wx/tc_class');
			}
		}


	}


}