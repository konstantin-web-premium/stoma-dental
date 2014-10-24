$(document).ready(function(){
  slideShow(true);
});
 
function slideShow(first) {
    $('#photos a').not('.show').hide();
    var current = $('#photos .show');
    var next = current.next().length ? current.next() : current.siblings().first();

    if (!first){
        current.hide().removeClass('show');
        next.fadeIn().addClass('show');
    }
    setTimeout(slideShow, 5000);
}
