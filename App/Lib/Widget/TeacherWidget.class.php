<?php
class TeacherWidget extends Widget {
	public function render($data) {
        //获取校区各科老师信息
        $where['school']=$_SESSION['school'];
        $t=M('teacher')->where($where)->select();
            foreach ($t as $key => $value) {
            	$data[$value['class']][]=$value;
            }
		$content = $this->renderFile('footer');
		$this->data=$data;
		return $content;
		// var_dump($tc);
	}
}
