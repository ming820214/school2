<?php
	class CourseModel extends RelationModel{

		protected $_link=array(
			//站在course表的角度去观察student表数据，mapping是匹配、映射的意思
			'Student'=> array(  
					'mapping_type'=>BELONGS_TO,
					'foreign_key'=>'stuid',//本表中的字段
					'mapping_name'=>'student',//对应表中的字段
					// 'mapping_fields'=>'name', 
					// 'as_fields'=>'name',
			),
			
		
		);


	}
?>