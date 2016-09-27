<?php
class CourseViewModel extends ViewModel {
    public function _initialize() {
        $this->db(1, C('HW001_DSN'));
    }

    public $viewFields = array(
        'OaCourseSbt'     => ['id' => 'sbt_id', 'teacher_id', 'subject_id', '_type' => 'left'],
        'OaCourse'        => ['id', 'state', 'std_id', 'name', 'unitprice', 'hour',
                             'ext_hour', 'factor', 'price', 'std_type','course',
                             'used_hour', 'create_time', 'remark', 'std_type', '_type' => 'left', '_on' => 'OaCourse.id=OaCourseSbt.course_id'],
        // 这里键值加空格原因http://www.thinkphp.cn/topic/31628.html
        'OaUser'          => ['name' => 'teacher', '_as' => 'u1',
                                            '_on' => 'u1.id=OaCourseSbt.teacher_id', '_type' => 'left'],
        'OaFooInfo'       => ['name' => 'subject_name',
                                            '_on' => 'OaFooInfo.id=OaCourseSbt.subject_id', '_type' => 'left'],
        'OaUnitpriceRole' => ['id' => 'plan_id', 'name' => 'plan_name','_on' => 'OaUnitpriceRole.id=OaCourse.unit_plan']
        );

    /**
     * 获取某个学员能排课的列表
     * @param  int   $sid 学员id
     * @return array      课程列表
     */
    public function getStdCourse($sid,$course_id=0) {
        $this->db(1);
        $Student = D('Student');
        $std = $Student->find($sid);
        $map = [
            'OaCourse.std_id' => $std['std_id'],
            'OaCourse.state'  => 200, // 正常状态
            'OaCourse.used_hour'   => ['exp', '< OaCourse.hour+OaCourse.ext_hour'],//有可排剩余时间
        ];

        if($course_id)$map['course_id']=$course_id;
        $courseList = $this->where($map)->select();

        foreach ($courseList as $i => $course) {
            //$courseList[$i]['unconfirmed_hour'] = $this->getUnconfirmedCount($course['id'], $course['std_id']); //注释掉待确认课时数提示
			$courseList[$i]['unconfirmed_hour'] = ''; //显示待确认课时为空
            // 这里应该判断本校区，，就判断方案名了,@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
            if (
                !$this->allowpaike($course['id'])
                || strpos($course['plan_name'], (session('school')=='阜新二部'?'阜新实验校区':session('school'))) === false
                ) {
                unset($courseList[$i]);
            } else {
                // 去掉校区名字
                $courseList[$i]['plan_name'] = str_replace((session('school')=='阜新二部'?'阜新实验校区':session('school')), '', $course['plan_name']);
                $courseList[$i]['xid'] = "{$course['sbt_id']}_{$course['id']}_{$course['teacher_id']}_{$course['subject_id']}_{$course['teacher']}_{$course['subject_name']}";
            }

        }

        //去除重复的
        foreach ($courseList as $k=>$v) {
            if(in_array($v['xid'],$cc))unset($courseList[$k]);
            $cc[]=$v['xid'];
        }

        return $courseList;
    }

    /**
     * 根据 sid 列表获取课程交集
     * @param  array $sidList 学员的 ID 列表，至少2位
     * @return array          交集课程列表
     */
    public function getStdCourseByList($sidList,$gid) {
        $id=M('stu_grade')->where(['gid'=>$gid])->find();
        $course=$this->getStdCourse($id['stuid'],$id['course_id']);

        return $course;
    }

    /**
    * 根据Id获取订单课程详情
    * @param  int   $id 订单中课程的id
    * @return array
    */
    public function getCourseById($id){
        $this->db(1);
        $course = $this->where(['OaCourse.id'=>$id])->find();
        $course['unconfirmed_hour'] = $this->getUnconfirmedCount($course['id'], $course['std_id']);
        return $course;
    }

    /**
    * 获取未确认课程课时
    * @param  int   $courseId 订单中课程的id
    * @param  int   $stdId    学号
    * @return float           未被确认的课时数
    */
    public function getUnconfirmedCount($courseId, $stdId) {
        return (float)(M('class')->where([
                'course_id' => $courseId,
                'std_id' => $stdId,
                'state' => ['in','0,1'],
                'cwqr'  => '', // 待确认的课
                ])->sum('count'));
    }

    /**
    * 判断某节课是否能排
    * @param  array  $course 一条课程的记录
    * @return boolen         是否能排课
    */
    public function allowArrange($course) {

        if ($course['state'] != 200 // 正常状态才能排课；参考 OA 系统/Application/Common/Conf/finance.php
            || $course['unconfirmed_hour'] + $course['used_hour'] >= $course['hour']+$course['ext_hour'] // 判断课时数量
        ) {
            return true;
        }
        return true;

    }

    /*
    *判断订单是否可以继续排课
    * @param  int  $course_id 课程的id
    * @param  float $count 要添加的排课课时数
    * @return boolen         是否能排课
    */

    public function allowpaike($course_id,$count=0){

        $course=$this->getCourseById($course_id);//获取订单信息
        if($course && $course['hour'])
        if( $count <= ($course['hour']+$course['ext_hour'] - $course['unconfirmed_hour'] - $course['used_hour']))return true;
        return false;
    }

    // // 订单借用,如果存在相同类型订单，且借用的课时数允许，则返回订单的id
    // public function borrowpaike($course_id,$count=0){
    //     $this->db(1);
    //     $data=M('course')->find($course_id);
    //     var_dump($data);
    //     return false;
    // }


}
