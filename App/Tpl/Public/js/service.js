
  $(document).ready(function()  {  
        
   //隐藏显示窗口
 $(".helper-button").click(function () { 
$(".helper-wrap").toggle("fast",function(){ $(".show").css({right:"53px",top:"70px"});}); 
})  
//关闭窗口
 $(".helper-box-close").click(function () { 
$(".helper-wrap").hide("fast",function(){ $(".show").css({right:"53px",top:"70px"});}); 

}) 

//广告商网站主显示
$("#advertiser").mousemove(
  function () {
    $(this).siblings().children("a").removeClass("on");
   $(this).children("a").addClass("on");
  $("#affili").fadeOut(10,function(){$("#adver").fadeIn(10);});
  }
)


$("#affiliate").mousemove(
  function () {
   $(this).siblings().children("a").removeClass("on");
   $(this).children("a").addClass("on");
    $("#adver").fadeOut(10,function(){	$("#affili").fadeIn(10);});
  }
)
//广告商网站主常见问题显示
$(".con li a").click(function () {
     
     $(this).parent().siblings().children(".helper-box-list-con").slideUp(300);
     $(this).parent().children(".helper-box-list-con").slideToggle(300);
  }
);

//查询功能

$("#sbt").click(function(){
var t=$("#sdate").val();
var tc=$("#steacher").val();
$("#search").empty();

      $.ajax({
         url: "../Api/search_class",
         data: "t="+t+"&tc="+tc,
         type: "POST",
         async:false,
         dataType:"json",

         success: function(msgg){
            if(msgg.s==1){

            $("#search").append("<li>①&nbsp;&nbsp;"+msgg.e1+"</li><li>②"+msgg.e2+"</li><li>③"+msgg.e3+"</li><li>④"+msgg.e4+"</li><li>⑤"+msgg.e5+"</li><li>⑥"+msgg.e6+"</li><li>⑦"+msgg.e7+"</li><li>⑧"+msgg.e8+"</li>");
            }else{
              $("#search").append("<li>没有查询到数据</li>");
            }
          }

       

      });






});





    })  
     
