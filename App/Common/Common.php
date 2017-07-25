<?php

//二维数组排序
function array_sort($arr,$keys,$type='asc'){ //保持键值不变
    $keysvalue = $new_array = array();
    foreach ($arr as $k=>$v){
            $keysvalue[$k] = $v[$keys];
    }
    if($type == 'asc'){
            asort($keysvalue);
    }else{
            arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k=>$v){
            $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

//提交的排课数据内部矛盾查询
function maodun_old($list){
    // 取出每个与全部对比找矛盾
    foreach ($list as $k => $v) {
        foreach ($list as $k2 => $v2) {
            if($v2['t2']<=$v2['t1'])return true;
            if($v['date']==$v2['date']&&$k!=$k2){
                if(!($v['t1']>=$v2['t2']||$v['t2']<=$v2['t1']))return true;
            }
        }
    }
    return false;
}

//zhangxm 更新改进该功能，增加24点的排课；
function maodun($list){
    // 取出每个与全部对比找矛盾
    foreach ($list as $k => $v) {
        foreach ($list as $k2 => $v2) {
            if($v2['t2']<=$v2['t1']){
            	return TRUE;
			}
			if(substr($v2['t1'],0,2) == "00" && substr($v2['t2'],0,2) == "24"){
				return TRUE;	
			}
            if($v['date']==$v2['date']&&$k!=$k2){
                if(!($v['t1']>=$v2['t2']||$v['t2']<=$v2['t1']))return TRUE;
            }
        }
    }
    return false;
}

//判断学员id和老师,查找到矛盾的数据记录,返回记录详情,遇id忽略（调课使用）
function maodun2($t1,$t2,$date,$sid,$teacher,$id=0,$other){
    if(($sid==88888) || ($sid==99999) || ($sid==77777) || ($sid==66666)){
      $map['other']=$other;//试听课查询冲突
      // return false;
    }else{
      $map['stuid']=$sid;
    }
    $map['teacher']=$teacher;
    $map['_logic']='or';
    $w['timee']=$date;
    $w['_complex']= $map;
    if($id)$w['id']=['neq',$id];
//     $m=M('class')->where($w)->select(); 优化性能

   /*  $m=M('class')->where($w)->field('timee,teacher,time1,time2')->select();
    foreach ($m as $v) {
        if(!($v['time1']>=$t2||$v['time2']<=$t1))return $v;
    } //将此处的程序代码转为数据库SQL语句从而进一步提高系统的性能； 
    */
    
    $w['_string'] = " (NOT (time1>='" . $t2 . "' OR time2<='" . $t1 . "')) ";
    $m = M('class')->where($w)->field('timee,teacher,time1,time2')->select();
    
    if($m && count($m)>0){
       return $m[0];
    }
}

//添加排课。一次添加一条
function add_one($t1,$t2,$date,$kemu,$teacher,$tid,$stuid,$gid,$other,$course_id){
        $info=M('student')->where(['state'=>1])->find($stuid);
        if($info || $stuid==88888 || $stuid==99999 || $stuid==77777 || $stuid==66666){//检查学员状态
          $data['school']=session('school');
          $data['stuid']=$stuid;
          $data['std_id']=$info?$info['std_id']:0;
          $data['course_id']=$course_id?$course_id:0;
          if($gid){
            $data['course_id']=M('stu_grade')->where(['gid'=>$gid,'stuid'=>$stuid])->getField('course_id');
          }
          if($date && ($date != '0000-00-00' && $date != '0000-00-00 00:00:00')){
            $data['timee']=$date;
          }else{
           return ['课程日期 0000-00-00 出错' . $date];
          }
//           $data['timee']=$date;
          $data['time1']=$t1;
          $data['time2']=$t2;
          $data['class']=$kemu;
          $data['tid']=$tid;
          $data['teacher']=$teacher;
          $data['other']=$other?$other:'';
          $data['grade']=(int)$gid;
          $data['add']=session('user');
		  
          if($stuid==88888 || $stuid==99999 || $stuid==77777 || $stuid==66666){
            $data['tid'] = D('TeacherView')->getTid_byname($teacher);
          }else{
            if((int)$data['course_id']<=0)return ['关联课程出错'];
          }
          //判断时间差计算课时数
          $today1 = strtotime(date('Y-m-d ').$t1.':00');
          $today2 = strtotime(date('Y-m-d ').$t2.':00');
          if($today2>$today1){
            $data['count']=($today2-$today1)/3600;
			
			if($data['count']<=0){
				return ['时间差出现问题!']; //修改偶尔出现的负课时的数据； edit by zhangxm at 2016-03-29 16:57
			}
			
			if($stuid==88888 && $data['count']>0.5){
				return ['试听课排课不允许超出0.5课时！'];
			}
			
            if(!D('CourseView')->allowpaike($data['course_id'],$data['count'])&&$stuid!=88888 && $stuid!=99999 && $stuid!=77777 && $stuid!=66666)return ['订单剩余可用课时不足'];
            /* if(strtotime($date)>time()-24*3600) 针对一对一订单学员排课冲突的问题解决，此处涉及到class表的触发器操作；
            return ['ok',M("class")->add($data)]; */
            if(strtotime($date)>time()-24*3600){
                 $id = M("class")->add($data);
                  if($id){
                   return ['ok',$id];
                  }else{
                   return ['该时间段有冲突！','数据添加失败！'];
                  }
            }
             
          }
        }else{
          return ['学员状态非正常'];
        }
          return ['未知的数据错误'];//遇到错误返回
}

//判断老师是否存在禁排规则
function get_ban($t1,$t2,$date,$teacher){
    $m=M('ban_rules')->where(['name'=>$teacher])->order('type desc')->select();
    foreach ($m as $v) {
        switch ($v['type']) {
            case '3':
                if($date==$v['date'])
                if(!($v['time1']>=$t2||$v['time2']<=$t1))return [$v['name'].','.$v['date'].'时间：'.$v['time1'].'--'.$v['time2'].'禁止排课'];
                break;
            case '2':
                if(date('w',strtotime($date))==$v['week'])
                if(!($v['time1']>=$t2||$v['time2']<=$t1))return [$v['name'].','.'周'.week($v['week']).'时间：'.$v['time1'].'--'.$v['time2'].'禁止排课'];
                break;
            case '1':
                if(!($v['time1']>=$t2||$v['time2']<=$t1))return [$v['name'].','.'时间：'.$v['time1'].'--'.$v['time2'].'禁止排课'];
                break;
        }
    }
    return false;
}

function week($i){
    $w=['日','一','二','三','四','五','六'];
    return $w[$i];
}

function fill_option($list, $data = null) {
	$html = "";
	if (is_array($list)) {
		foreach ($list as $key => $val) {

			if (is_array($val)) {
				$id = $val['id'];
				$name = $val['name'];
				if (empty($data)) {
					$selected = "";
				} else {
					$selected = "selected";
				}
				$html = $html . "<option value='{$id}' $selected>{$name}</option>";
			} else {
				if ($key == $data) {
					$selected = "selected";
				} else {
					$selected = "";
				}
				$html = $html . "<option value='{$key}' $selected>{$val}</option>";
			}
		}
	}

	echo $html;
}

?>
