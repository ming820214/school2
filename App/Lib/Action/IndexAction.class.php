<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends CommAction {

    public function index(){
        $this->display();
    }

    public function header(){
        $this->display();
    }

    public function ext(){
        if(!session('?user')){
            $_SESSION['school']='';
            $this->success('匿名离开',U('Login/index'));
        }else{
           $where['user']=session('user');
           $arr=M('user')->where($where)->find();
           $data['log']='<br/>**退出：'.date('Y-m-d H:i:s').$arr['log'];
           $where['id']=session('id');
                $_SESSION=array();
                    if(isset($_COOKIE[session_name()])){
                        setcookie(session_name(),'',time()-1,'/');
                    }
                session_destroy();
           if(M('user')->where($where)->save($data)){
            $this->success('您已经成功退出',U('Login/index'));
           }
        }
    }

    public function large(){
        if(!session('?user'))die;
        $id=$this->_post('id');
        $why=$this->_post('why');
            foreach ($id as $v) {
                R('Class/delt',array($v,$why,0));
            }
        $teacher=M('class')->where(['id'=>['in',implode(',',$id)]])->getField('teacher',true);
        R('Send/text',array(5,$teacher,'小文提示:[拥抱]\n------------\n亲,[流泪]您的课表有变动请及时查看。\n-----------\n【仇人：'.session('user').',[傲慢]如果不服，可拨打电话：\n[敲打]'.session('tel').'】\n备注：'.$why));
            $this->success('批量删除成功！');
    }
//-----------------学员信息-----------------------

    public function st(){
        if(!session('?user'))die;
        if(session('?school_id'))die("<h1>请退出先oa系统……<h1>");
        $m = M("Student");
        $condition['school'] = session('school')=='阜新二部'?'阜新实验校区':session('school');
        $condition['state'] = 1;
        // 把查询条件传入查询方法
        $arr=$m->where($condition)->order('id asc')->select();
        // var_dump($arr);
        $this->assign('st',$arr);
        $this->display('Aaa:student');
    }

    //学员搜索
    public function st_search(){
        if (isset($_POST['name'])&&$_POST['name']!='') {
            $key=$_POST['name'];
            $m=M("Student");
            $condition['school']=$_SESSION['school'];
            $condition['state']=1;
            $condition['name']=array('like',"%$key%");
            $st=$m->where($condition)->select();
            if ($st) {
                $this->assign('st',$st);
                $this->display('aaa:student');
            }else{
            $this->success('没有查询到相关信息，请重新输入',U('Index/st'));
            }

        } else {
            $this->success('没有查询到相关信息，请重新输入',U('Index/st'));
        }
    }

    public function sc_ad(){
        if(!session('?user'))die;

        if (isset($_POST['ad'])&&$_POST['ad']!=''){
        $m=M('school');
        $condition['school']=$_SESSION['school'];
        $date['ad']=$_POST['ad'];
        $st=$m->where($condition)->save($date);
            if($st){
            //==============操作记录开始
                $info='更新校区公告，'.$_POST['ad'];
                $this->record($info);
            //==============操作记录结束
            $this->success('公告更新成功');
            }else{
            $this->success('公告更新失败');
            }
        }else{
        $m=M('school');
        $condition['school']=$_SESSION['school'];
        $st=$m->where($condition)->find();
        $this->assign(st,$st);
        $this->display('aaa:gonggao');
        }
    }
    public function sc_set(){
        if(!session('?user'))die;

        if (isset($_POST['pw'])&&$_POST['pw']!=''){
        $condition['user']=session('user');
        $date['pw']=md5($_POST['pw']);
        $st=M('user')->where($condition)->save($date);
            if($st){
            //==============操作记录开始
                $info='修改密码，'.$_POST['pw'];
                $this->record($info);
            //==============操作记录结束
            $this->success('密码更新成功');
            }else{
            $this->success('密码更新失败');
            }
        }else{
                $this->display('aaa:pw');
        }
    }

    public function sc_teacher(){
        $data['school']=$_SESSION['school'];
        if (isset($_POST['add'])&&$_POST['add']!=''){
            // var_dump($_POST);die;
        $data['name']=$this->_post('name');
        $data['class']=$this->_post('class');
        //查询系统存在讲师
        if(M('hw003.person_all',null)->where(['name'=>$_POST['name']])->find())
            $st=M('teacher')->add($data);
            if($st){
            //==============操作记录开始
                $info='新增讲师，'.$_POST['name'].$_POST['class'];
                $this->record($info);
            //==============操作记录结束
            $this->success('新增成功');
            }else{
            $this->success('新增失败');
            }
        }else{

                $data['class']='语文';
                $this->t1=M('teacher')->where($data)->select();
                $data['class']='数学';
                $this->t2=M('teacher')->where($data)->select();
                $data['class']='英语';
                $this->t3=M('teacher')->where($data)->select();
                $data['class']='物理';
                $this->t4=M('teacher')->where($data)->select();
                $data['class']='化学';
                $this->t5=M('teacher')->where($data)->select();
                $data['class']='生物';
                $this->t6=M('teacher')->where($data)->select();
                $data['class']='文综';
                $this->t7=M('teacher')->where($data)->select();
                $this->display('aaa:teacher');
        }

    }

    public function sc_delt($id){
        if(!session('?user'))die;

        $condition['id']=$id;
        $condition['school']=$_SESSION['school'];
        $conn=M('teacher')->where($condition)->find();

        if ($conn) {
            //==============操作记录开始
                $info='删除讲师，'.$conn['name'].$conn['class'];
                $this->record($info);
            //==============操作记录结束
            if ($n=M('teacher')->where($condition)->delete()) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }


    // public function sc_class(){
    //     die;
    //     if($_POST['add'])$this->sc_class_add();
    //     if($_POST['change'])$this->sc_class_change();

    //         //输出班级信息
    //         $where['school']=$_SESSION['school'];
    //         $m=D('School_grade')->relation(true)->where($where)->select();
    //         $this->grade=$m;
    //         //输出学员列表
    //         $s=M('student')->where($where)->order('p')->select();
    //         $this->vo=$s;
    //         $this->display('aaa:grade');

    // }

    // public function sc_class_change(){
    //     if(!session('?user'))die;

    //     if(isset($_POST['id'])&&$_POST['id']!=''&&$_POST['id']!=0){
    //         if(count($_POST['box'])<2)die('班级成员至少两人以上！');
    //         M('school_grade')->where(['id'=>$_POST['id']])->save(['name'=>$_POST['name'],'other'=>$_POST['other']]);

    //         //==============操作记录开始
    //             $info='变更班级成员，'.$_POST['id'];
    //             $this->record($info);
    //         //==============操作记录结束

    //         //获取班级中某一学员所有未确认的排课以及学员信息
    //         $c['grade']=(int)$_POST['id'];
    //         $c['state']=0;
    //         $m=M('class')->where($c)->find();
    //         $xue['stuid']=$m['stuid'];
    //         $xue['state']=0;
    //         $xue['grade']=$this->_post('gid');
    //         $classd=M('class')->where($xue)->select();
    //         //删除班级全体成员所有未确认的排课
    //         $cc=M('class')->where($c)->delete();//删除所有未确认的排课
    //             // 为成员重新排课
    //             if($classd){
    //                 foreach ($classd as $key1 => $value1) {//循环为该班更新排课
    //                     foreach ($_POST['box'] as $key2 => $value2) {//循环学员
    //                         $f=R('Api/addclass',array($value2,$value1['school'],$value1['timee'],$value1['time1'],$value1['time2'],$value1['class'],$value1['teacher'],$value1['other'],$value1['grade']));
    //                     }
    //                 }
    //             }
    //             $s['gid']=(int)$_POST['id'];
    //             $ss=M('stu_grade')->where($s)->delete();//删除所有班级成员记录
    //             foreach ($_POST['box'] as $key3 => $value3) {//班级成员重新录入
    //                 $where['id']=(int)$value3;
    //                 $my=M('student')->where($where)->find();//调取学员资料
    //                 $data['name']=$my['name'];
    //                 $data['stuid']=(int)$value3;
    //                 $data['school']=session('school');
    //                 $data['gid']=(int)$_POST['id'];
    //                 $mm=M('stu_grade')->data($data)->add();//重新将学员录入一次
    //             }
    //             if($mm)$this->success('成功更新');
    //     }
    // }

    // //创建班级
    // public function sc_class_add(){
    //     die;

    //     if($_POST['name']!=''){
    //         if(count($_POST['box'])<2)die('班级成员至少两人以上！');
    //     $dat['name']=$this->_post('name');
    //     $dat['school']=session('school');
    //     $dat['other']=$this->_post('other');
    //     $xx=M('school_grade')->data($dat)->add();
    //     }
    //     if($xx){
    //         foreach ($_POST['box'] as $key => $value) {
    //             $where['id']=(int)$value;
    //             $m=M('student')->where($where)->find();
    //             $data['name']=$m['name'];
    //             $data['stuid']=(int)$value;
    //             $data['school']=session('school');
    //             $data['gid']=$xx;
    //             $mm=M('stu_grade')->data($data)->add();
    //         }
    //             if($mm){
    //                             //==============操作记录开始
    //                                 $info='新增班级，'.$xx;
    //                                 $this->record($info);
    //                             //==============操作记录结束
    //                 $this->success('添加成功！',U('index/sc_class'));
    //             }else{
    //                 $this->redirect('Index/sc_class');
    //             }

    //     }

    // }

    public function sc_class_del($gid){
        if(!session('?user'))die;

        $where['id']=(int)$gid;
        $where['school']=session('school');
        $m=M('school_grade')->where($where)->find();
        if($m){
            $gg=M('school_grade')->where($where)->delete();//删除班级
            $c['grade']=(int)$gid;
            $c['state']=0;
            $cc=M('class')->where($c)->delete();//删除所有未确认的排课
            $s['gid']=(int)$gid;
            $ss=M('stu_grade')->where($s)->delete();//删除所有班级成员记录
            if($gg && $ss){
                //==============操作记录开始
                    $info='删除班级，'.$gid;
                    $this->record($info);
                //==============操作记录结束
                $this->success('班级删除成功！');
            }else{
                $this->error('删除失败');
            }
        }
    }

    //禁止排课功能设置
    public function ban_rules(){
    	
        if($_POST['add']){
            // var_dump($_POST);die;
            $m=M('ban_rules');
            $m->create();
            $m->school=session('school');
            $m->add=session('user');
            $m->add();
        }
        /*if($_GET['delt']){
            $m=M('ban_rules');
            $m->delete($_GET['delt']);
        }*/
        if($_GET['delt']){
            $m=M('ban_rules');
			$obj = $m->where(['id'=>$_GET['delt']])->find();
			if(($obj['school'] == session('school')) && ($obj['add'] == session('user'))){
				$m->delete($_GET['delt']);	
				$this->ajaxReturn(['state'=>'success','info'=>'操作成功！']);
			}else{
				$this->ajaxReturn(['state'=>'error','info'=>"该规则需要由" . session('school') . " " . session('user') . "删除，其他人无权删除该规则！"]);
			}
            
        }
		
		$this->tlst = M('teacher')->where(['school'=>session('school')])->getField('name as id,name');;
        $this->list=M('ban_rules')->where(['school'=>session('school')])->select();
        $this->display('aaa:ban_rules');
    }

	//排查规则是否有冲突；
	public function searchBans(){
			
		if($_POST['name']){
			$w['name'] = $_POST['name'];	
		}else{
			$this->ajaxReturn(['state'=>'error','info'=>'警告：请选择具体的教师！']);
		}
		
		if($_POST['type']){
			if($_POST['type'] ==1){
			
				if($_POST['time1'] && $_POST['time2']){
					$w['_string'] = "(str_to_date(time1,'%H:%i')<= str_to_date('" . $_POST['time1'] . "','%H:%i') and str_to_date(time2,'%H:%i') > str_to_date('" . $_POST['time1'] . "','%H:%i') ) or (str_to_date(time1,'%H:%i')<= str_to_date('" . $_POST['time2'] . "','%H:%i') and str_to_date(time2,'%H:%i') > str_to_date('" . $_POST['time2'] . "','%H:%i') )";
				}else{
					$this->ajaxReturn(['state'=>'error','info'=>'警告：请填全禁排时间段！']);
				}
			
				$ban = M('ban_rules')->where($w)->select();
				
				if($ban){
					$this->ajaxReturn(['state'=>'error','info'=>'警告：有冲突规则！']);
				}else{
					$this->ajaxReturn(['state'=>'success']);
				}
		
			}else{
				
				$w['type'] = $_POST['type']; 
				
				if($_POST['date']){
					$w['date'] = $_POST['date']; 
				}
				
				if($_POST['week']){
					$w['week'] = $_POST['week']; 	
				}	
				
				
				if($_POST['time1'] && $_POST['time2']){
					$w['_string'] = "(str_to_date(time1,'%H:%i')<= str_to_date('" . $_POST['time1'] . "','%H:%i') and str_to_date(time2,'%H:%i') > str_to_date('" . $_POST['time1'] . "','%H:%i') ) or (str_to_date(time1,'%H:%i')<= str_to_date('" . $_POST['time2'] . "','%H:%i') and str_to_date(time2,'%H:%i') > str_to_date('" . $_POST['time2'] . "','%H:%i') )";
				}else{
					$this->ajaxReturn(['state'=>'error','info'=>'警告：请填全禁排时间段！']);
				}
			
				$ban = M('ban_rules')->where($w)->select();
				
				if($ban){
					$this->ajaxReturn(['state'=>'error','info'=>'警告：有冲突规则！']);
				}else{
					
					$condition['name'] = $_POST['name'];
					$ban = M('ban_rules')->where($condition)->select();
					
					foreach($ban as $obj){
						if($obj['type'] == 1){
							if(($_POST['time2']<=$obj['time2'] && $_POST['time2']>$obj['time1']) || ($_POST['time1']<$obj['time2'] && $_POST['time1']>=$obj['time1'])){
								$this->ajaxReturn(['state'=>'error','info'=>'警告：与该教师的每天禁排规则类型有冲突！']);
							}
						}else if($obj['type'] == 2){
							if($_POST['date']){
								$week = date('w',strtotime($_POST['date']));
								
								if($week = $obj['week']){
									if(($_POST['time2']<=$obj['time2'] && $_POST['time2']>$obj['time1']) || ($_POST['time1']<$obj['time2'] && $_POST['time1']>=$obj['time1'])){
										$this->ajaxReturn(['state'=>'error','info'=>'警告：与该教师的每周禁排规则类型有冲突！']);
									}	
								}
							}
						}else if($obj['type'] == 3){
							if($_POST['week']){
//								$week = week(date('w',strtotime($obj['date'])));
								$week = date('w',strtotime($obj['date']));
								
								if($week = $_POST['week']){
									if(($_POST['time2']<=$obj['time2'] && $_POST['time2']>$obj['time1']) || ($_POST['time1']<$obj['time2'] && $_POST['time1']>=$obj['time1'])){
										$this->ajaxReturn(['state'=>'error','info'=>'警告：与该教师的指定日期禁排规则类型有冲突！']);
									}	
								}
							}
						}
					}
					
					$this->ajaxReturn(['state'=>'success']);
				}
			}
		}
	}
//=================内部调用添加课程方法======================================

    public function record($info){
        $where['id']=session('id');
        $m=M('user')->where($where)->find();
        $data['record']=date('Y-m-d H:i:s').$info.'<br/>'.$m['record'];
        M('user')->where($m)->save($data);
    }



}
