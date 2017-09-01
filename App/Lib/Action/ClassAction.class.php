<?php
// 本类由系统自动生成，仅供测试用途
class ClassAction extends CommAction {

    public function day($d){//显示指定日期所有课程

        $m=D('Class');
        $time=$d;
        $w['timee']=$time;
        $w['school']=$_SESSION['school'];
        $st=$m->relation(true)->where($w)->order('time1 asc,class asc,teacher asc')->select();
        $qc=array();
        foreach ($st as $v) {
            if($v['time1']!=$qc['t1']or$v['teacher']!=$qc['t'])$count+=$v['count'];
            $qc=array('t1'=>$v['time1'],'t'=>$v['teacher']);
        }
        $this->assign(cls,$st);
        $this->assign(count,$count);
        $this->display();
    }

    public function ext(){
        session('[destroy]');
        $this->success('您已经成功退出',U('Login/index'));
    }

    public function foot(){//获取校区各科老师信息
        /////// Yang add /////////////
        // $Teacher = D('TeacherView');
        // $data = $Teacher->getEffectTeachersBySchoolName(session('school'));
        // $teachers = [];
        // foreach ($data as $value) {
        //     if(!isset($teachers[$value['subject_name']]) || !in_array($value['name'], $teachers[$value['subject_name']])){
        //        $teachers[$value['subject_name']][] = $value['name'];
        //     }
        // }
        // $this->teacher = $teachers;
        /////////////////////////////
        $where['school']=$_SESSION['school'];
        $t=M('teacher')->where($where)->select();
            foreach ($t as $key => $value) {
                $data[$value['class']][]=$value['name'];
            }
        $this->teacher=$data;
    }

//============================================
    public function all($sid='',$gid='',$teacher='',$style=''){

        if(isset($_GET['date'])&&$_GET['date']!=''){
            $datexx=$_GET['date'];
            $ee=$datexx.'-'.'01';
            $c=strtotime($ee);
            $cc=$c+date('t',$c)*24*3600;//获取月末时间戳
        }else{
            $datexx=date('Y-m');
            $ee=$datexx.'-'.'01';
            $c=strtotime(date('Y-m-01'));//获取月初时间戳
            $cc=$c+date('t',$c)*24*3600;//获取月末时间戳
        }
        //oa系统学员匿名查询排课
        if(session('?schooll')&&!session('?school')){
            if(!$sid)$w['cl.school']='无';
        }else{
            $w['cl.school']=$_SESSION['school'];
        }

        if($sid)$w['cl.stuid']=$sid;
        if($gid)$w['cl.grade']=$gid;
        if($teacher){
            $w['cl.teacher']=$teacher;
            unset($w['cl.school']);
			//教师禁排规则
//			$ban = M('hw001.ban_rules')->where(['name'=>$teacher])->select();
        }
        $w['cl.timee']=array('like',"$datexx%");
		
		
//      $m=D('Class')->relation(true)->where($w)->order('timee asc,time1 asc,teacher asc,state asc')->select();
		
		$cl = M('class');
		
		$m = $cl->alias('cl')
		->join(' hw001.student as st on cl.stuid = st.id')
		->join('school_grade as gd on cl.grade = gd.id')
		->field('cl.id ,cl.school, st.name as student,st.xueguan as xueguan,st.jiaoxue as jiaoxue,st.grade as gradeji,gd.id as gid,gd.name as grade,cl.stuid, cl.std_id, cl.course_id, cl.state, cl.tid, cl.teacher, cl.fankui, cl.class, cl.time1, cl.time2, cl.count, cl.timee, cl.other, cl.why, cl.add, cl.qr, cl.tqr,  cl.cwqr, cl.timestamp')
		->order('cl.timee asc,cl.time1 asc,cl.teacher asc,cl.state asc')
		->where($w)
		->select();

		$tj['已排'] = 0;
        $tj['待确认'] = 0;
        $tj['已确认'] = 0;
        $tj['旷课'] = 0;
        $tj['不计时'] = 0;
        $tj['不计确认'] = 0;
        $tj['不计待认'] = 0;
        $tj['不计旷课'] = 0;
		
        $data=array();
        foreach ($m as $v) {
            //确认状态
            if($v['state']==0){
                $data[$v['timee']]['state']=0;
            }
            //日期状态
            $today=date('Y-m-d');
            if($v['timee']<$today){
                $data[$v['timee']]['today']=-1;
            }elseif($v['timee']==$today){
                $data[$v['timee']]['today']=0;
            }else{
                $data[$v['timee']]['today']=1;
            }
            //获取横轴时间分布
            $time[$v['time1']]=$v['time1'];

            $data[$v['timee']]['date']=$v['timee'];
            $s=array('t'=>$v['timee'],'t1'=>$v['time1'],'t2'=>$v['time2'],'tc'=>$v['teacher']);
            if($ss!=$s){//去除重复
                //组装数据
                if($v['grade']){
                    $data[$v['timee']]['data'][]=array('why'=>$v['why'],'school'=>$v['school'],'id'=>$v['id'],'state'=>$v['state'],'gid'=>$v['gid'],'grade'=>$v['grade'],'t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'s'=>$this->s($v['time1']));
                }else{
                    if($v['stuid']==88888){
                        $data[$v['timee']]['data'][]=array('why'=>$v['why'],'school'=>$v['school'],'id'=>$v['id'],'state'=>$v['state'],'sid'=>'88888','student'=>'试听课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'other'=>$v['other'],'s'=>$this->s($v['time1']));
                    }elseif($v['stuid']==99999){
                        $data[$v['timee']]['data'][]=array('why'=>$v['why'],'school'=>$v['school'],'id'=>$v['id'],'state'=>$v['state'],'sid'=>'99999','student'=>'培训课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'other'=>$v['other'],'s'=>$this->s($v['time1']));
                    }elseif($v['stuid']==77777){
                        $data[$v['timee']]['data'][]=array('why'=>$v['why'],'school'=>$v['school'],'id'=>$v['id'],'state'=>$v['state'],'sid'=>'77777','student'=>'考核课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'other'=>$v['other'],'s'=>$this->s($v['time1']));
                    }elseif($v['stuid']==66666){
                        $data[$v['timee']]['data'][]=array('why'=>$v['why'],'school'=>$v['school'],'id'=>$v['id'],'state'=>$v['state'],'sid'=>'66666','student'=>'会议课','t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'other'=>$v['other'],'s'=>$this->s($v['time1']));
                    }else{
                        $data[$v['timee']]['data'][]=array('why'=>$v['why'],'school'=>$v['school'],'id'=>$v['id'],'state'=>$v['state'],'sid'=>$v['stuid'],'student'=>$v['student'],'t1'=>$v['time1'],'t2'=>$v['time2'],'class'=>$v['class'],'teacher'=>$v['teacher'],'nianji'=>$v['gradeji'],'xueguan'=>$v['xueguan'],'jiaoxue'=>$v['jiaoxue'],'s'=>$this->s($v['time1']));
                    }
                }

                
                //统计数据
                if($v['state']==0)$tj['待确认']+=$v['count'];
                if($v['state']==1)$tj['已确认']+=$v['count'];
                if($v['state']==2)$tj['旷课']+=$v['count'];
                if(in_array($v['stuid'],array('66666','77777','88888','99999'))){
                	$tj['不计时'] += $v['count'];
                	if($v['state']==1){
                		$tj['不计确认'] += $v['count'];
                	}
                	if($v['state']==0){
                		$tj['不计待认'] += $v['count'];
                	}
                	if($v['state']==2){
                		$tj['不计旷课'] += $v['count'];
                	}
                }
                $tj['已排']+=$v['count'];
            }
            $ss=$s;
        }
		
        //最后一次组装数据
        for ($i=0; $i < 42 ; $i++) {
            $monday1=$c-((date('w',$c)==0?7:date('w',$c))-1)*24*3600;//获取本月1号所在周一的时间戳
            $day=date('Y-m-d',$monday1+24*3600*$i);
            //将数据循环对应到42组中
            if($monday1+24*3600*$i < $c or $monday1+24*3600*$i >= $cc){
                $data2[$i+1]=false;
            }else{
                if(!$data[$day]){
                    $data[$day]['state']=0;
                    $data[$day]['date']=$day;
                    $data[$day]['today']=date('Y-m-d')==$day?0:-1;
                    $data[$day]['today']=date('Y-m-d')>$day?0:1;
                }
                $data2[$i+1]=$data[$day];
            }
        }
        $name='校区课表';
        if($teacher)$name='讲师：'.$teacher;
        $ww['id']=$sid;
        if($sid)$name='学员：'.(M('student')->where($ww)->getField('name'));
        $ww['id']=$gid;
        if($gid)$name='小组：'.(M('school_grade')->where($ww)->getField('name'));
        $this->name=$name;
        $this->data=$data2;
        $this->foot();
        $this->tj=$tj;
        sort($time,SORT_STRING);


        ///////add Yang////////
        $Course = D('CourseView');
        if($_GET['sid'])$this->assign('oneCourseList', $Course->getStdCourse($sid));
        if($_GET['gid'])$this->assign('oneCourseList', $Course->getStdCourseByList(M('stu_grade')->where(['gid'=>$_GET['gid']])->getField('stuid',true),$_GET['gid']));
        ///////////////////////

        //oa匿名查询学员课表
        if(!session('?school')){
            $this->display('search');
            die;
        }
        if($style==2){
            session('style',null);
            $this->display();
        }elseif($style==1 or session('style')==1){
            session('style',1);
            $this->display('all2');
        }else{
            $this->display();
        }
    }

    //删除某排课
    public function delt($cid,$why,$tz=1){
                if(!session('?user'))die;
//         $g=M('class')->find($cid);
        $g=M('class')->where("id=" . $cid)->find();
        $data=$g;
        if($g['grade']){
            $w0['id']=$g['grade'];
            $data['name']=M('school_grade')->where($w0)->getField('name');
            $w['grade']=$g['grade'];
            $w['timee']=$g['timee'];
            $w['time1']=$g['time1'];
            $w['teacher']=$g['teacher'];
            $w['school']=session('school');
            $w['state']=0;
            if(M('class')->where($w)->delete())print(json_encode(11));
                                //==============操作记录开始
                                    $info='删除小组课,'.implode(',',$g).'删除原因,'.$why;
                                    $this->record($info);
                                //==============操作记录结束
        }else{
            $w0['id']=$g['stuid'];
            $data['name']=M('student')->where($w0)->getField('name');
            if(M('class')->where(array('id'=>$cid,'state'=>0,'school'=>session('school')))->delete())print(json_encode(11));
                                //==============操作记录开始
                                    $info='删除一对一,'.implode(',',$g).'删除原因,'.$why;
                                    $this->record($info);
                                //==============操作记录结束
        }

        if($tz)R('Send/text',array(5,$data['teacher'],'小文提示:[拥抱]\n------------\n亲,[流泪]有人将您：\n[勾引]'.$data['timee'].'日\n[咖啡]'.$data['time1'].'到'.$data['time2'].'\n[瓢虫]\r科目：'.$data['class'].'\n[瓢虫]\r学员：'.$data['name'].'\n的排课删除了。\n-----------\n【仇人：'.session('user').',[傲慢]如果不服，可拨打电话：\n[敲打]'.session('tel').'】\n备注：'.$why));
		
		if($tz && $_SESSION['user'] == '李明帅'){
//			R('Send/text',array(4,$_SESSION['user'],'小文提示:[拥抱]\n------------\n亲,[握手]刚才有人用你的账户排课了！' . $requires . '\n-----------\n【好人：'.session('user').',[嘘]如需报答，可拨打电话：\n[礼物]'.session('tel').'】'));
			R('Send/text',array(5,$_SESSION['user'],'小文提示:[拥抱]\n------------\n亲,[流泪]刚才有人用你的账户将您：\n[勾引]'.$data['timee'].'日\n[咖啡]'.$data['time1'].'到'.$data['time2'].'\n[瓢虫]\r科目：'.$data['class'].'\n[瓢虫]\r学员：'.$data['name'].'\n的排课删除了。\n-----------\n【仇人：'.session('user').',[傲慢]如果不服，可拨打电话：\n[敲打]'.session('tel').'】\n备注：'.$why));	
		}
    }

    //确认某排课
    public function qr($cid,$why=null){
                if(!session('?user'))die;

//         $g=M('class')->find($cid);
        $g=M('class')->where("id=".$cid)->find();
        $d['state']=1;
        $d['qr']=session('user');
        if($why)$d['why']=$why;
        if($g['grade']){
            $w['grade']=$g['grade'];
            $w['timee']=$g['timee'];
            $w['time1']=$g['time1'];
            $w['teacher']=$g['teacher'];
            if(M('class')->where($w)->save($d))print(json_encode(11));
        }else{
            $w['id']=$cid;
            if(M('class')->where($w)->save($d))print(json_encode(11));
        }
    }

    //旷课某排课
    public function kk($cid,$why){
                if(!session('?user'))die;

//         $g=M('class')->find($cid);
        $g=M('class')->where("id=".$cid)->find();
        
        $d['state']=2;
        $d['qr']=session('user');
        $d['why']=$why;
        if($g['grade']){
            $w['grade']=$g['grade'];
            $w['timee']=$g['timee'];
            $w['time1']=$g['time1'];
            $w['teacher']=$g['teacher'];
            if(M('class')->where($w)->save($d))print(json_encode(11));
        }else{
            $w['id']=$cid;
            if(M('class')->where($w)->save($d))print(json_encode(11));
        }
    }

    //个人和班级课表变更
    public function changeClassInfo(){
        if(!session('?user'))die;

//         $info=M('class')->find($_POST['cid']);
        $info=M('class')->where("id=". $_POST['cid'])->find();
        $data=$info;
        $d['timee']=$this->_post('date');
        $d['time1']=$this->_post('time1');
        $d['time2']=$this->_post('time2');
        $d['count']=(strtotime(date('Y-m-d').$d['time2'])-strtotime(date('Y-m-d').$d['time1']))/3600;
		
		if($info['stuid'] == 88888 && $d['count']>0.5){
			$this->error('试听课排课不允许超出0.5课时！');
		}
		
        $why=$this->_post('why');
        $d['why']=$info['why'].'|'.$why;
        if($info['grade']){
            if(maodun2($d['time1'],$d['time2'],$d['timee'],$info['stuid'],$info['teacher'],$info['id'])){
                $this->error('有冲突排课，调课失败,建议删除重新添加……');
            }else{
                $jg=1;
                $w0['id']=$g['grade'];
                $data['name']=M('school_grade')->where($w0)->getField('name');
                $w['timee']=$info['timee'];
                $w['time1']=$info['time1'];
                $w['time2']=$info['time2'];
                $w['teacher']=$info['teacher'];
                $w['grade']=$info['grade'];
                                //==============操作记录开始
                                    $inf='小组课调课,'.implode(',',$info).'调课原因,'.$why.',调到：'.$d['timee'].'['.$d['time1'].'-'.$d['time2'].']';
                                    $this->record($inf);
                                //==============操作记录结束
            }
        }else{
            if(maodun2($d['time1'],$d['time2'],$d['timee'],$info['stuid'],$info['teacher'],$info['id'])){
                $this->error('有冲突排课，调课失败……');
            }else{
                $jg=1;
                $w0['id']=$g['stuid'];
                $data['name']=M('student')->where($w0)->getField('name');
                $w['id']=$this->_post('cid');
                                //==============操作记录开始
                                    $inf='一对一调课,'.implode(',',$info).'调课原因,'.$why.',调到：'.$d['timee'].'['.$d['time1'].'-'.$d['time2'].']';
                                    $this->record($inf);
                                //==============操作记录结束
            }
        }
            if($jg && $d['timee'] && $d['timee'] != '0000-00-00' && $d['timee'] != '0000-00-00 00:00:00'){
                M('class')->where($w)->save($d);
				
				$remark = $_POST['remark'];
				if($remark){
					$requires = '\n具体排课要求如下：' . $remark ;	
				}else{
					$requires = '';
				}
		
//              R('Send/text',array(4,$data['teacher'],'小文提示:[拥抱]\n------------\n亲,[流泪]有人将您：\n[勾引]'.$data['timee'].'日\n[咖啡]'.$data['time1'].'到'.$data['time2'].'\n的排课调到了\n[勾引]'.$d['timee'].'日\n[咖啡]'.$d['time1'].'到'.$d['time2'].'\n[瓢虫]\r科目：'.$d['class'].'\n[瓢虫]\r学员：'.$data['name'].'\n-----------\n【凶手：'.session('user').',[傲慢]如果不服，可拨打电话：\n[敲打]'.session('tel').'】\n备注：'.$why));
                R('Send/text',array(5,$data['teacher'],'小文提示:[拥抱]\n------------\n亲,[流泪]有人将您：\n[勾引]'.$data['timee'].'日\n[咖啡]'.$data['time1'].'到'.$data['time2'].'\n的排课调到了\n[勾引]'.$d['timee'].'日\n[咖啡]'.$d['time1'].'到'.$d['time2'].'\n[瓢虫]\r科目：'.$d['class'].'\n[瓢虫]\r学员：'.$data['name']. '\n-------------\n' . $requires . '\n-----------\n【凶手：'.session('user').',[傲慢]如果不服，可拨打电话：\n[敲打]'.session('tel').'】\n备注：'.$why));
				if($_SESSION['user'] == '李明帅'){
					R('Send/text',array(5,$_SESSION['user'],'小文提示:[拥抱]\n------------\n亲,[流泪]刚才有人用你的账户将您：\n[勾引]'.$data['timee'].'日\n[咖啡]'.$data['time1'].'到'.$data['time2'].'\n的排课调到了\n[勾引]'.$d['timee'].'日\n[咖啡]'.$d['time1'].'到'.$d['time2'].'\n[瓢虫]\r科目：'.$d['class'].'\n[瓢虫]\r学员：'.$data['name']. '\n-------------\n' . $requires . '\n-----------\n【凶手：'.session('user').',[傲慢]如果不服，可拨打电话：\n[敲打]'.session('tel').'】\n备注：'.$why));	
				}
//                 $this->redirect('Class/all');
				$this->success('课程变更成功！');
            }else{
                $this->error('变更失败……');
            }

    }

    //判断传入的课时是否有冲突
    public function pclass($timee,$time1,$time2,$class,$teacher){

           $timee11=$time1;
           $timee12=$time2;
           $where['class']=$class;
           $where['teacher']=$teacher;
           $where['school']=$_SESSION['school'];
           $where['timee']=$timee;
           $m=M('class')->where($where)->select();
           if($m){
                //===遍历数组
                foreach ($m as $value) {
                    $time1=$value['time1'];
                    $time2=$value['time2'];
                    if($time1<$timee11&&$timee11<$time2){
                        $id=$value['id'];
                        return $id;
                    }elseif($time1<$timee12&&$timee12<$time2){
                        $id=$value['id'];
                        return $id;
                    }elseif($timee11<=$time1&&$time2<=$timee12){
                        $id=$value['id'];
                        return $id;
                    }
                }
           }

    }


//================================//临时调用======================================================

    //记录操作
    public function record($info){
        $where['id']=session('id');
        $m=M('user')->where($where)->find();
        $data['record']=date('Y-m-d H:i:s').$info.'|'.$m['record'];
        M('user')->where($m)->save($data);
        $w['school']=session('school');
        $m2=M('school')->where($w)->find();
        $data2['record']=$info.',操作人,'.session('user').',时间,'.time().'#'.$m2['record'];
        $info_record = $info.',操作人,'.session('user').',时间,'.time();
        M('school')->where($w)->save($data2);
		
		$tmp = M('hongwen_oa.dt_record','oa_');
		
		$info_arr = split(",", $info);
		
		$cl = new stdClass();
		
		$cl->cid =     $info_arr[1];
		$cl->school = $info_arr[2];
		$cl->stuid = $info_arr[3];
		$cl->std_id = $info_arr[4];
		$cl->course_id = $info_arr[5];
		$cl->grade = $info_arr[6];
		$cl->state = $info_arr[7];
		$cl->tid = $info_arr[8];
		$cl->teacher = $info_arr[9];
		$cl->fankui = $info_arr[10];
		$cl->class = $info_arr[11];
		$cl->time1 = $info_arr[12];
		$cl->time2 = $info_arr[13];
		$cl->count = $info_arr[14];
		$cl->timee = $info_arr[15];
		$cl->other = $info_arr[16];
		$cl->why = $info_arr[17];
		$cl->add = $info_arr[18];
		$cl->qr = $info_arr[19];
		$cl->tqr = $info_arr[20];
		$cl->cwqr = $info_arr[21];
		$cl->timestamp = $info_arr[22];
		$cl->reason = $info_arr[24];
		$cl->operator = session('user');
		$cl->dtk_time = date('Y-m-d');
		$cl->dtk_type = $info_arr[0];
		$cl->record = $info_record;
		
		$result = $tmp->data($cl)->add();
		
    }

    //计算时间轴离开距离,在时间轴课表视图下计算数据应该存放在哪个区间中
    function s($t){
        $t1=date('Y-m-d');
        $t2=$t1.' '.$t;
        $t3=$t1.' 06:00';//以5点为起点
        $s=(strtotime($t2)-strtotime($t3))/60;
        return $s;
    }

    public function cc(){

    $where['gid']=17;
    $m=M('stu_grade')->where($where)->select();
    foreach ($m as $key => $value) {
                $d['stuid']=$value['stuid'];
                $d['timee']=array('between','2014-07-09,2014-07-17');
                $d['school']='大连校区';
                $data['grade']=17;
                M('class')->where($d)->save($data);
            }

    }

    public function dd(){
        $m=M('stu_grade')->order('gid asc')->select();
        foreach ($m as $v) {
            $grade[$v['gid']][]=$v['stuid'];
        }

        foreach ($grade as $k1 => $val1) {
                $count=count($val1);
                $w['grade']=$k1;
                $w['stuid']=0;
                $ss=M('class')->where($w)->find();
                $w2['timee']=$ss['timee'];
                $w2['time1']=$ss['time1'];
                $w2['time2']=$ss['time2'];
                $w2['teacher']=$ss['teacher'];
                $sd=M('class')->where($w2)->select();
                if(count($sd)==$count){
                    foreach ($val1 as $k2 => $val2) {
                        $d['stuid']=$val2;
                        $w2['stuid']=0;
                        $mb=M('class')->where($w2)->find();
                        $w3['id']=$mb['id'];
                        M('class')->where($w3)->save($d);
                        $countt++;
                    }
                }
        }
        var_dump($countt);
    }

}
