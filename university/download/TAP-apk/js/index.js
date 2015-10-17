$(document).ready(function(){
  var perc;
  
  $("#loaderText").on('click',function(){
  
     TweenMax.to("#loader",7,  {width:'100%',ease:Power0.easeOut,onUpdate:function(){
       var mainWidth = $("#container").width()+30;
        perc = ($("#loader").width()/mainWidth)*100; 
        console.log(Math.floor(perc),$("#container").width());
        checkPercent(Math.floor(perc));
      }, onStart:function(){
        $("#loaderText").css({'visibility':'hidden'});
        $("#loaderText2").css({'visibility':'visible', 'display':'block'});
      }});
  
  });
  
  
 
  
});


function checkPercent(perc){
  perc = String(perc).replace("px","");
 
  if(perc == 12){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(0);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 22){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(1);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 32){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(2);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 40){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(3);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 47){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(4);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 51){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(5);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 61){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(6);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 66){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(7);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 74){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(8);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 77){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(9);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }
  else if(perc == 85){
     console.log(perc);
    //var obj2Go = $("#loaderText>span").get(0);
   // $(obj2Go).css({'visibility':'hidden'});
    var obj2Show = $("#loaderText2>span").get(10);
    TweenMax.to($(obj2Show),1,{'background-position':'-480px 0%'});
  }   
  
    else if( perc == 100){
      TweenMax.to("#loaderText2",1,{autoAlpha:0});
      TweenMax.to("#finalText",1.2,{autoAlpha:1});
    }
  
}