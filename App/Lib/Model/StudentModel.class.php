<?php 
class StudentModel extends RelationModel{



    protected $_link = array(

			//站在student表的角度去观察course表数据，mapping是匹配、映射的意思
			'course'=> array(  
					'mapping_type'=>HAS_ONE,
					'foreign_key'=>'stuid',//本表中的字段
					'mapping_name'=>'id',//对应表中的字段
					// 'relation_foreign_key'=>'upid', 
			),



    );



}


 ?>