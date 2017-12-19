<?php
// 本类负责页面相关查询调用
class ApiAction extends CommAction {

/**
批量查询脚本
 */
    public function class_search(){
      $t1=$this->_post('ta');
      if($t1<date('Y-m-d'))$t1=date('Y-m-d');
      $t2=$this->_post('tb');
      $where['timee']=array('between',"$t1,$t2");
      if($_POST['s']!= undefined)$where['stuid']=$this->_post('s');
      if($_POST['g']!= undefined)$where['grade']=$this->_post('g');
      if($_POST['st']!= undefined)$where['other']=$this->_post('st');
      if($_POST['teacher']!= undefined)$where['teacher']=$this->_post('teacher');
      $where['school']=session('school');
      $where['state']=0;
      // print_r($where);
      $shuchu=D('Class')->relation(true)->where($where)->order('timee asc,time1 asc,teacher asc,state asc')->select();
      // print_r($shuchu);
      //====================
      foreach ($shuchu as $v) {
        $s=array('t'=>$v['timee'],'t1'=>$v['time1'],'t2'=>$v['time2'],'tc'=>$v['teacher']);
        if($ss!=$s){//去除重复
            //组装数据
            if($v['grade']){
                $data[]=array('id'=>$v['id'],'nm'=>$v['student']['name'],'t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'date'=>$v['timee']);
            }else{
                if($v['stuid']==88888){
                    $data[]=array('id'=>$v['id'],'nm'=>'试听课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'date'=>$v['timee']);
                }else if($v['stuid']==99999){
                    $data[]=array('id'=>$v['id'],'nm'=>'培训课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'date'=>$v['timee']);
                }else if($v['stuid']==77777){
                    $data[]=array('id'=>$v['id'],'nm'=>'考核课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'date'=>$v['timee']);
                }else if($v['stuid']==66666){
                    $data[]=array('id'=>$v['id'],'nm'=>'会议课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'date'=>$v['timee']);
                }else{
                    $data[]=array('id'=>$v['id'],'nm'=>$v['student']['name'],'t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'date'=>$v['timee']);
                }
            }
        }
        $ss=$s;
      }
        //==============

      if($data){
          print(json_encode($data));
      }else{
          $d['a']=0;
          print(json_encode($d));
      }
    }
/**
执行脚本冲突、排课、跳转的所有问题
 */
    public function class_add(){
        // if(!session('?user'))die;
         if(!session('?user')){
         	$this->ajaxReturn(['state'=>'1','info'=>'匿名用户无法排课，请先登录之后再排课！']);
         }
		$remark = $_POST['remark'];
        // 把post过来的数据变成二维数组
        foreach ($_POST['box'] as $onebox) {
          foreach ($onebox[1] as $day) {
              foreach ($onebox[0] as $vv) {
                if($vv[2]){
                    $temp = explode('_', $vv[2]);
                    $vv[3] = $temp[5];
                    $vv[4] = $temp[4];
                    $vv[2] = $temp[1];
                    $vv[5] = $temp[2];
                }
                if($vv[0]&&$vv[1]&&$day)
                if($vv[3]&&$vv[4])
                $list[]=['t1'=>$vv[0],'t2'=>$vv[1],'course_id'=>$vv[2],'kemu'=>$vv[3],'teacher'=>$vv[4],'tid'=>$vv[5],'date'=>$day,'other'=>$_POST['other']];
              }
          }
        }

        if(!$list || maodun($list))$this->ajaxReturn(['state'=>'1','info'=>'提交的数据有矛盾或错误！']);

        //查找数据库的撞课情况
        $stuids[]=$_POST['id'];
        $gid=0;
        if($_POST['type']=='grade'){
            $gid=$_POST['id'];
            //$stuids=M('stu_grade')->where(['gid'=>$_POST['id']])->getField('stuid',true);
            $stuids=M('stu_grade')->where(['gid'=>$_POST['id']])->getField('stuid,course_id');
            
            foreach ($stuids as $stuid=>$courseOfId) {
                if($courseOfId){
                    foreach ($list as $v) {
                        $ban=get_ban($v['t1'],$v['t2'],$v['date'],$v['teacher']);
                        if($ban)$this->ajaxReturn(['state'=>4,'info'=>$ban[0]]);
                        $aa=maodun2($v['t1'],$v['t2'],$v['date'],$stuid,$v['teacher'],0,$v['other']);
                        if($aa)$this->ajaxReturn(['state'=>'2','info'=>'有撞课信息','data'=>$aa]);
                    }
                }else{
                    $this->ajaxReturn(['state'=>'1','info'=>'该小组有成员未绑定订单，请纠正后再排课！！']);
                    break;
                }
                
            }
            
            //添加排课
            foreach ($stuids as $stuid=>$courseOfId) {
                foreach ($list as $v) {
                    $add=add_one($v['t1'],$v['t2'],$v['date'],$v['kemu'],$v['teacher'],$v['tid'],$stuid,$gid,$v['other'],$v['course_id']);
                    if($add[0]!='ok'){
                        M('class')->where(['id'=>['in',$class_ids]])->delete();//删除操作已近添加的id
                        $this->ajaxReturn(['state'=>'3','info'=>$add[0]]);
                    }
                    $teacher[]=$v['teacher'];
                    $class_ids[]=$add[1];//临时存储添加成功的id
                }
            }
            
        }else{
            foreach ($stuids as $stuid) {
                foreach ($list as $v) {
                    $ban=get_ban($v['t1'],$v['t2'],$v['date'],$v['teacher']);
                    if($ban)$this->ajaxReturn(['state'=>4,'info'=>$ban[0]]);
                    $aa=maodun2($v['t1'],$v['t2'],$v['date'],$stuid,$v['teacher'],0,$v['other']);
                    if($aa)$this->ajaxReturn(['state'=>'2','info'=>'有撞课信息','data'=>$aa]);
                }
            }
            
            //添加排课
            foreach ($stuids as $stuid) {
                foreach ($list as $v) {
                    $add=add_one($v['t1'],$v['t2'],$v['date'],$v['kemu'],$v['teacher'],$v['tid'],$stuid,$gid,$v['other'],$v['course_id']);
                    if($add[0]!='ok'){
                        M('class')->where(['id'=>['in',$class_ids]])->delete();//删除操作已近添加的id
                        $this->ajaxReturn(['state'=>'3','info'=>$add[0]]);
                    }
                    $teacher[]=$v['teacher'];
                    $class_ids[]=$add[1];//临时存储添加成功的id
                }
            }
        }
		
		if($remark){
			$requires = '\n具体排课要求如下：' . $remark ;	
		}else{
			$requires = '';
		}
		if($_SESSION['user'] == '李明帅'){
			R('Send/text',array(5,$_SESSION['user'],'小文提示:[拥抱]\n------------\n亲,[握手]刚才有人用你的账户排课了！' . $requires . '\n-----------\n【好人：'.session('user').',[嘘]如需报答，可拨打电话：\n[礼物]'.session('tel').'】'));	
		}
        //通知讲师
//      R('Send/text',array(4,$teacher,'小文提示:[拥抱]\n------------\n亲,[握手]刚才有人给你新排课了，记得要查看课表并及时确认哦！\n-----------\n【好人：'.session('user').',[嘘]如需报答，可拨打电话：\n[礼物]'.session('tel').'】'));
        R('Send/text',array(5,$teacher,'小文提示:[拥抱]\n------------\n亲,[握手]刚才有人给你新排课了，记得要查看课表并及时确认哦！' . $requires . '\n-----------\n【好人：'.session('user').',[嘘]如需报答，可拨打电话：\n[礼物]'.session('tel').'】'));

        $this->ajaxReturn(['state'=>'ok','info'=>'数据保存成功！']);

    }

    //获取对应科目讲师信息
    public function teacher(){

      if(isset($_POST['class'])&&$_POST['class']!=''){
          if($_POST['class']=='历史' or $_POST['class']=='地理' or $_POST['class']=='政治'){
          $where['class']='文综';
          }else{
          $where['class']=$_POST['class'];
          }
        $where['school']=session('school');
        $shuchu=M('teacher')->where($where)->select();
        print(json_encode($shuchu));//将信息发送给浏览器
      }

    }

    //给课时追加旷课原因备注
    public function class_why(){

      if(isset($_POST['why'])&&$_POST['why']!=''){
        $where['id']=(int)$_POST['id'];
        $data['why']=$this->_post('why');
        if(M('class')->where($where)->save($data)){
          $shuchu=1;
          print(json_encode($shuchu));//将有更新结果发送给前台
        }else{
          $shuchu=0;
          print(json_encode($shuchu));//将有更新结果发送给前台
        }
      }
    }

    //输出校区所有班级
    public function grade(){
      $where['school']=session('school')=='阜新二部'?'阜新实验校区':session('school');//@@@@@@@@@@@@@@@@@@@@@@@@@
      $where['is_del']=0;
      $shuchu=M('school_grade')->where($where)->select();
      print(json_encode($shuchu));
    }

    //根据首字母输出学员选项
    public function name(){
        $p['p']=$this->_post('p');
        $p['school']=session('school')=='阜新二部'?'阜新实验校区':session('school');//@@@@@@@@@@@@@@@@@@@@
        $p['state']= 1;
        $name=M('student')->where($p)->select();
          for ($i=0; $i <count($name) ; $i++) {
            $shuchu[$i]['id']=$name[$i]['id'];
            $shuchu[$i]['name']=$name[$i]['name'];
          }
        print(json_encode($shuchu));
    }

    public function course($stuid) {
      $list=D('CourseView')->getStdCourse($stuid); 
      //防止使用一对多的订单排一对一
      foreach ($list as $k=>$v) {
        if(!(strpos($v['plan_name'],'一对一')||strpos($v['plan_name'],'1对1')))unset($list[$k]);
      }
      $this->ajaxReturn(array_values($list));
    }
    public function course_gid($gid) {
        $stuid=M('stu_grade')->where(['gid'=>$gid])->getField('stuid',true);
        die(json_encode(D('CourseView')->getStdCourseByList($stuid,$gid)));
    }
    //根据日期返回星期
    public function week(){
      $shuchu=date('W',strtotime($_POST['time']));
      print(json_encode($shuchu));
    }
    //调课提示
    public function ts($date,$cid){
//       $info=M('class')->find($cid);
    	$info=M('class')->where("id=". $cid)->find();
      $w['timee']=$_POST['date'];
      $w['stuid']=$info['stuid'];
      $m1=M('class')->where($w)->select();
      $w2['timee']=$_POST['date'];
      $w2['teacher']=$info['teacher'];
      $m2=M('class')->where($w2)->select();
      if($m1&&$m2){
        $m=array_merge($m1,$m2);
      }else{
        $m=$m1?$m1:$m2;
      }
      foreach ($m as $v) {
        $data[]=array('t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher']);
      }
      print(json_encode($data));
    }

}
?>
