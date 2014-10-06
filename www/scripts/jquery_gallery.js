$(function(){
$('.gallery1 img:gt(0)').hide();
setInterval(function()
{$('.gallery1 :first-child').fadeOut(1000).next('img').fadeIn(1000)
.end().appendTo('.gallery1');}, 5000);
});
