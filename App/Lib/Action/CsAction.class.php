<?php
	class CsAction extends Action{
		
		public function index(){
			if ($_GET['school'] && (session('?school_id')||session('?schooll'))) {
				if($_GET['t']){
					$t=$_GET['t'];
					$_SESSION['school']='随机校区';
					$this->success('讲师的课表查询中……',U("Class/all?teacher=$t"));
				}else{
					$_SESSION['school']=$_GET['school'];
					$this->success('校区匿名进入中……',U('Index/index'));
				}
			}

		}

	}

?>