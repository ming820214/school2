
(function($) {

/*---------------------------
 Defaults for Reveal
----------------------------*/

/*---------------------------
 Listener for data-reveal-id attributes
----------------------------*/

	$('a[data-reveal-id]').live('click', function(e) {
		e.preventDefault();
		var modalLocation = $(this).attr('data-reveal-id');
		$('#'+modalLocation).reveal($(this).data());
	});

/*---------------------------
 Extend and Execute
----------------------------*/

    $.fn.reveal = function(options) {


        var defaults = {
	    	animation: 'fadeAndPop', //fade, fadeAndPop, none
		    animationspeed: 300, //how fast animtions are
		    closeonbackgroundclick: true, //if you click background will modal close?
		    dismissmodalclass: 'close-reveal-modal' //the class of a button or element that will close an open modal
    	};

        //Extend dem' options
        var options = $.extend({}, defaults, options);

        return this.each(function() {

/*---------------------------
 Global Variables
----------------------------*/
        	var modal = $(this),
        		topMeasure  = parseInt(modal.css('top')),
				topOffset = modal.height() + topMeasure,
          		locked = false,
				modalBG = $('.reveal-modal-bg');

/*---------------------------
 Create Modal BG
----------------------------*/
			if(modalBG.length == 0) {
				modalBG = $('<div class="reveal-modal-bg" />').insertAfter(modal);
			}

/*---------------------------
 Open & Close Animations
----------------------------*/
			//Entrance Animations
			modal.bind('reveal:open', function () {
			  modalBG.unbind('click.modalEvent');
				$('.' + options.dismissmodalclass).unbind('click.modalEvent');
				if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modal.css({/*'top': $(document).scrollTop()-topOffset,*/ 'opacity' : 0, 'visibility' : 'visible'});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							/*"top": $(document).scrollTop()+topMeasure + 'px',*/
							"opacity" : 1
						}, options.animationspeed,unlockModal());
					}
					if(options.animation == "fade") {
						modal.css({'opacity' : 0, 'visibility' : 'visible'/*, 'top': $(document).scrollTop()+topMeasure*/});
						modalBG.fadeIn(options.animationspeed/2);
						modal.delay(options.animationspeed/2).animate({
							"opacity" : 1
						}, options.animationspeed,unlockModal());
					}
					if(options.animation == "none") {
						modal.css({'visibility' : 'visible'/*, 'top':$(document).scrollTop()+topMeasure*/});
						modalBG.css({"display":"block"});
						unlockModal()
					}
				}
				modal.unbind('reveal:open');
			});

			//Closing Animation
			modal.bind('reveal:close', function () {
			  if(!locked) {
					lockModal();
					if(options.animation == "fadeAndPop") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"top":  $(document).scrollTop()-topOffset + 'px',
							"opacity" : 0
						}, options.animationspeed/2, function() {
							modal.css({'top':topMeasure, 'opacity' : 1, 'visibility' : 'hidden'});
							unlockModal();
						});
					}
					if(options.animation == "fade") {
						modalBG.delay(options.animationspeed).fadeOut(options.animationspeed);
						modal.animate({
							"opacity" : 0
						}, options.animationspeed, function() {
							modal.css({'opacity' : 1, 'visibility' : 'hidden', 'top' : topMeasure});
							unlockModal();
						});
					}
					if(options.animation == "none") {
						modal.css({'visibility' : 'hidden', 'top' : topMeasure});
						modalBG.css({'display' : 'none'});
					}
				}
				modal.unbind('reveal:close');
			});

/*---------------------------
 Open and add Closing Listeners
----------------------------*/
        	//Open Modal Immediately
    	modal.trigger('reveal:open')

			//Close Modal Listeners
			var closeButton = $('.' + options.dismissmodalclass).bind('click.modalEvent', function () {
			  modal.trigger('reveal:close')
			});

			// if(options.closeonbackgroundclick) {
			// 	modalBG.css({"cursor":"pointer"})
			// 	modalBG.bind('click.modalEvent', function () {
			// 	  modal.trigger('reveal:close')
			// 	});
			// }
			// $('body').keyup(function(e) {
   //      		if(e.which===27){ modal.trigger('reveal:close'); } // 27 is the keycode for the Escape key
			// });

/*---------------------------
 Animations Locks
----------------------------*/
			function unlockModal() {
				locked = false;
			}
			function lockModal() {
				locked = true;
			}

        });//each call
    }//orbit plugin call
})(jQuery);

//内部调用，用于时间戳转换的
	function add0(m){return m<10?'0'+m:m }
	function format(shijianchuo)
	{
	//shijianchuo是整数，否则要parseInt转换
	var time = new Date(shijianchuo);
	var y = time.getFullYear();
	var m = time.getMonth()+1;
	var d = time.getDate()+1;
	// var h = time.getHours()+1;
	// var mm = time.getMinutes()+1;
	// var s = time.getSeconds()+1;
	return y+'-'+add0(m)+'-'+add0(d);
	}

// 根据排课类型输出定位项
	$("#xuan").live('change', function(e) {
		$('#showBtn').hide();
		// alert($(this).val());
		var xuan=$(this).val();
		$(this).nextAll().remove();
		if(xuan==0){
			$(this).parent().append("<select class='xuan' id='p'><option></option><option>※</option><option>A</option><option>B</option><option>C</option><option>D</option><option>E</option><option>F</option><option>G</option><option>H</option><option>J</option><option>K</option><option>L</option><option>M</option><option>N</option><option>O</option><option>P</option><option>Q</option><option>R</option><option>S</option><option>T</option><option>W</option><option>X</option><option>Y</option><option>Z</option></select><select class='xuan' id='_stuid' name='id' aa='student'></select>");
		}else if(xuan==1){
			$(this).parent().append("<select class='xuan' name='id' id='_gid' aa='grade'></select>");
			$(this).parent().append("&nbsp;&nbsp;筛选：<input type='text' name='filter_t' id='filter_t'/>");
			var g=$(this).next();
			      $.ajax({
			         url: url+"/Api/grade",
			         data: "g",
			         type: "POST",
			         async:true,
			         dataType:"json",

			         success: function(msg){
			         	if(msg!=null)
					    for(var i=0; i < msg.length;i++)
					    {
					            mg=msg[i];
					            g.append('<option value='+mg.id+'>'+mg.name+'</option>');
					     }
						$("#_gid").change();
			         }

			      });
		}else if(xuan==2){
			$(this).parent().append("<input name='student' type='text' placeholder='试听学员信息' class='in'/><input type='hidden' name='id' aa='student'>");
			$('._bowarpper table').remove();
			$('._bowarpper').prepend(htmlC);
			
	        if($('._bowarpper input[name="date"]')){
	        	$('#showBtn').show();
	        }
		}else if(xuan == 5){
			$(this).parent().append("<input name='student' type='text' placeholder='培训信息' class='in'/><input type='hidden' name='id' aa='student'>");
			$('._bowarpper table').remove();
			$('._bowarpper').prepend(htmlC);
			
	        if($('._bowarpper input[name="date"]')){
	        	$('#showBtn').show();
	        }
		}else if(xuan == 6){
			$(this).parent().append("<input name='student' type='text' placeholder='考核信息' class='in'/><input type='hidden' name='id' aa='student'>");
			$('._bowarpper table').remove();
			$('._bowarpper').prepend(htmlC);
			
	        if($('._bowarpper input[name="date"]')){
	        	$('#showBtn').show();
	        }
		}
	});

$('#filter_t').live('keyup',function(e){
	$('#_gid option').show();
	$('#_gid option').each(function(){
		if($(this).text().indexOf($('#filter_t').val()) == -1){
			$(this).attr('dq','1'); //该属性，无任何意义，就是为了实现某种显示效果
			$(this).hide();
		}else{
			$(this).attr('dq','2');
		}
	});
	$('#_gid option[dq=2]:first').prop('selected',true)
	$("#_gid").change();
});


//根据姓名首字母输出选项
	$("#p").live('change',function(){
		$('#showBtn').hide();
		var p=$(this).val();
	    var next=$(this).next();
	    $(this).next().empty();
	    $.ajax({
	         url: url+"/Api/name",
	         data: "p="+p,
	         type: "POST",
	         async:true,
	         dataType:"json",
	         success: function(msg){
	         	if(msg!=null){
				    for(var i=0; i < msg.length;i++)
				    {
				            mg=msg[i];
				            next.append('<option value='+mg.id+'>'+mg.name+'</option>');
				     }

	         	}
			     $('#_stuid').change();
	         }

	      });
	});

	//学员课程信息
	$(document).on('change', '#_stuid', function() {
		$('#showBtn').hide();
		$('#tishi').text('');
		$('._bowarpper table').remove();
		if($('#_stuid').val()!=null){
		$.ajax({
				url: url+"/Api/course",
				data: {stuid: $('#_stuid').val()},
				type: "GET",
				dataType: "JSON",
				async:false,			
		        success: function(msg){
		        	if(msg != null){
		        		if(msg.length){
							$('._bowarpper table').remove();
				         	init(msg);
			         	}
		        	}else{
			         	$('#tishi').text('该学员没有可用订单！');
			        }
		         	
		        }
		    });
		}
	});

	// 班级课程信息
	$(document).on('change', '#_gid', function() {
		$('#showBtn').hide();
		$('#tishi').text('');
		$('._bowarpper table').remove();
		if($('#_gid').val()!=null){
	        $.ajax({
		         url: url+"/Api/course_gid",
		         data: {gid: $('#_gid').val()},
		         type: "GET",
		         dataType: "JSON",
		         async:false,
		         success: function(msg){
		         	if(msg != null){
		        		if(msg.length){
							$('._bowarpper table').remove();
				         	init(msg);
			         	}else{
			         		$('#tishi').text('该学员没有可用订单！');
			         	}
		        	}else{
			         		$('#tishi').text('该学员没有可用订单！');
			         	}
		        }

		    });
		}

	});

// 添加时间
	$("#timeadd").live('click', function(e) {

	$(this).parent().children("div").append("<div class='row2'><input  name='date'  class='Wdate' type='text' onfocus=\"WdatePicker({minDate:'%y-%M-{%d}'})\" style='width: 100px;margin: 5px;' readonly='readonly'/></div>");

	});
// 删除时间
	$("#timedelt").live('click', function(e) {
	$(this).parent().children("div").children("div").last().remove();
	});

//添加周时间
	$("#week").live('click',function(){
		var d=$(this).parent().children("div").children("div").last().find("input[name$='date']").val();
		if(d){
		var date = new Date(d);
		// alert(date.getTime()/1000);
		var week=date.getTime()+6*24*3600*1000;
		// alert(week);
		week=format(week);
		$(this).parent().children("div").append("<div class='row2'><input  name='date'  class='Wdate' type='text' onfocus=\"WdatePicker({minDate:'%y-%M-{%d}'})\" style='width: 100px;margin: 5px;' readonly='readonly' value='"+week+"'/></div>");
		}else{
			alert("请选择循环星期的起点，先选择第一天！");
		}
	});

// 添加排课
	$("#classadd").live('click', function(e) {
		var box = $(this).parent().children(".over");
		if($("#xuan").val()==2 || $("#xuan").val()==5 || $("#xuan").val()==6){
			box.append(htmlD);
		}else{
			box.append(htmlB);
		}
	});

// 删除排课
	$("#classdelt").live('click', function(e) {

	$(this).parent().children("div").children("div").last().remove();

	});

//判断科目并输出讲师
$("select[name$='kemu']").live('change', function(e) {
	   $(this).next("select").empty();
	   var t=$(this).val();
	   var tt=$(this).next("select[name$='teacher']");
	   // alert(t);
	  $.ajax({
	     url: url+"/Api/teacher",
	     data: "class="+t,
	     type: "POST",
	     async:true,
	     dataType:"json",

	     success: function(msg){
	     	if(msg!=null)
		     for(var i=0; i < msg.length;i++)
		     {
		            mg=msg[i];
		            tt.append('<option>'+mg.name+'</option>');
		     }
	     }

	  });
});

//查询课时冲突
var returnArray = function( a ){
	var cccc = new Array();
	$.each( a ,function(i , n){
      		cccc=(cccc.concat($(this).val()));
      });
	return cccc ;
}

// 增加或减少box里的table,1增，2减
function table_change(type){
	if(type==1){
		$("#tishi").empty();
		if($("#xuan").val()==2 || $("#xuan").val()==5 || $("#xuan").val()==6){
			$("._bowarpper").append(htmlC);
		}else{
			$("._bowarpper").append(htmlA);
		}
	}else if(type==2){
		$("._bowarpper table").last().remove();
	}
}

//批量脚本的查询
$("#search").live('click',function(){
	  var ta = $("input[name$='timea']").val();
	  var tb = $("input[name$='timeb']").val();
	  var tc = $("input[name$='t1']").val();
	  var td = $("input[name$='t2']").val();
		  if(ta!=''&&tb!=''&&tc!=''&&td!=''){
		      var s  = $("input[name$='stuid']").val();
		      var g  = $("input[name$='g']").val();
		      var st = $("input[name$='student']").val();
		      var teach = $("input[name$='teach']").val();
		      if(s==undefined){
		      	s=$("select[name$='stuid']").live().val();
		      }
		      if(g==undefined){
		      	g=$("select[name$='g']").live().val();
		      }
		      if(teach==undefined){
		      	teach=$("input[name$='teach']").live().val();
		      }
		  	//数据查询
				    $.ajax({
					        url: url+"/Api/class_search",
					        data: "ta="+ta+"&tb="+tb+"&tc="+tc+"&td="+td+"&s="+s+"&g="+g+"&st="+st+"&teacher="+teach,
					        type: "POST",
					        async:true,
					        dataType:"json",

					        success: function(msg){

				            	$("#tishi2").empty();
				            		for (var i = 0; i < msg.length; i++) {
							            $("#tishi2").prepend("<div><label><input type='checkbox' name='id[]' value='"+msg[i].id+"'>"+msg[i].date+"&nbsp;&nbsp;时间："+msg[i].t1+"—"+msg[i].t2+"&nbsp;&nbsp;&nbsp;&nbsp;学员："+msg[i].nm+"&nbsp;&nbsp;&nbsp;&nbsp;科目："+msg[i].class+"&nbsp;讲师："+msg[i].teacher+"</label></div>");
				            		};

						            if(msg.a==0){
						            	$("#tishi2").prepend("没有查询到可以批量操作的数据……");
						            }

					        }

				    });

		  }else{
		  	alert('时间不能为空！');
		  }
});

//批量操作上的全选或全取消
$("#quanA").live('click',function(){
	$("#myModal2").find(":checkbox").each(function(){
		$(this).attr("checked", true);
	})
});
$("#quanB").live('click',function(){
	$("#myModal2").find(":checkbox").each(function(){
		$(this).attr("checked", false);
	})
});

function cDayFunc(){
  var  ct = $dp.cal;
  var date = ct.newdate['y'];
  date+='-'+ct.newdate['M'];
  date+='-'+ct.newdate['d'];
  var cid = $("#myModal3").find("input[name$='cid']").val();
    $.ajax({
        url: url+"/Api/ts",
        data: "date="+date+"&cid="+cid,
        type: "POST",
        async:true,
        dataType:"json",

        success: function(msg){

        	$("#tishi3").empty();
        	$("#tishi3").append("<div style='text-align:center;'><h3>学员或讲师当天的排课</h3></div>");
        		for (var i = 0; i < msg.length; i++) {
		            $("#tishi3").append("<div style='padding:5px;'>&nbsp;&nbsp;&nbsp;&nbsp;时间："+msg[i].t1+"—"+msg[i].t2+"&nbsp;&nbsp;&nbsp;&nbsp;科目："+msg[i].class+"&nbsp;&nbsp;&nbsp;&nbsp;讲师："+msg[i].teacher+"</div>");
        		};
        }

    });
}


function Msg( msg , sec ){
var browserWidth	 = window.innerWidth,
ID	 = "HiddenMessageDialog" + new Date().getTime();
var dialogBoxWidth	 = (function(){
var len = msg.length , w = 0;
for (var i = 0; i < len; i++) {
var code = msg.charCodeAt(i);
if( code <= 255){
w += 9.6;
} else {
w += 19.2;
}
}
if( w + 40 >= browserWidth){
return browserWidth;
} else {
return w + 40;
}
})();
var dialogbox = document.createElement("div");
dialogbox.style.position	= "fixed";
dialogbox.style.background	= "rgba(44,50,50,0.8)";
dialogbox.style.width	 = dialogBoxWidth + "px";
dialogbox.style.height	 = "50px";
dialogbox.style.left	 = ( browserWidth - dialogBoxWidth )/2 + "px";
dialogbox.style.bottom	 = "50px";
dialogbox.style.textAlign	= "center";
dialogbox.style.lineHeight	= "50px";
dialogbox.style.fontSize	= "1.2em";
dialogbox.style.color	 = "#DDDDDD";
dialogbox.style.borderRadius= "25px";
dialogbox.style.webkitBorderRadius = "25px";
dialogbox.style.mozBorderRadius = "25px";
dialogbox.style.zIndex	 = "999999999";
dialogbox.setAttribute( "id" , ID );
var textNode = document.createTextNode( msg );
dialogbox.appendChild( textNode );
document.body.appendChild( dialogbox );
setTimeout(function(){
var timer = 100;
var tt = setInterval(function(){
dialogbox.style.opacity = timer / 100 + "";
dialogbox.style.webkitOpacity = timer / 100 + "";
dialogbox.style.mozOpacity = timer / 100 + "";
timer -= 20;
if(timer<0){
document.body.removeChild( document.getElementById( ID ) );
clearInterval(tt);
}
},100);
}, sec * 1000 || 4500 );
}
