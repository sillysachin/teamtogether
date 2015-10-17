$(function(){    

//Accordion function
$(".Kjfaq dt").wrapInner('<span class="KjCOntent" />');
$('<span class="plus" />').appendTo(".Kjfaq dt");
$('.Kjfaq dd').hide();

$('.Kjfaq dt').click(function(){
    $(this).find('span:nth-child(2)')
           .toggleClass('plus minus')
           .end()
           .next()
           .slideToggle(140);
    });
    
//Show or hide all answers
$("div.toggleAnswers").click(function(){
    $(".Kjfaq dt")
           .find('span:nth-child(2)')
           .toggleClass('plus minus');  
    $("dd").slideToggle(140);
 });

});