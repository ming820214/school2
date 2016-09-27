<?php
class TeacherViewModel extends ViewModel {
    public function _initialize() {
        $this->db(1, C('HW001_DSN'));
    }

    public $viewFields = array(
        'OaTeachRole' => ['id', 'uid', 'plan_id', 'name' => 'plan_name', 'subject', 'school', 'subject', '_type' => 'left'],
        // // 这里键值加空格原因http://www.thinkphp.cn/topic/31628.html
        'OaUser'      => ['name', '_on' => 'OaUser.id=OaTeachRole.uid', '_type' => 'left'],
        'OaFooInfo'   => ['_as' => 'a1', 'name' => 'subject_name',
                                        '_on' => 'a1.id=OaTeachRole.subject', '_type' => 'left'],
        'OaFooInfo  '  => ['_as' => 'a2', 'name' => 'school_name',
                                            '_on' => 'a2.id=OaTeachRole.school', '_type' => 'left'],
        );

    /**
     * 根据学校名获取授课老师
     * @param  string $schoolName 学校的名称
     * @return array              教师列表
     */
    public function getEffectTeachersBySchoolName($schoolName) {
        $this->db(1);
        return $this->where([
            'a2.name' => $schoolName,
            'OaTeachRole.is_del' => 0,
            ])->select();
    }

    /**
    * 根据姓名，查询老师的id
    */
    public function getTid_byname($name){
        $this->db(1);
        return M('user')->where([
            'name' => $name,
            'is_del' => 0,
            ])->getField('id');
    }
}
