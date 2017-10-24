jQuery(document).ready(function($) {
    $('.expand-switch').toggle(function(){
       var post_id = $(this).attr('data-post-id');
        $(this).find('i').removeClass('fa-expand').addClass('fa-compress');
       $('article.' + post_id).addClass('open').removeClass('col-xs-12').removeClass('col-sm-6').removeClass('col-md-4');
    },function(){
        var post_id = $(this).attr('data-post-id');
        $(this).find('i').addClass('fa-expand').removeClass('fa-compress');
        $('article.' + post_id).removeClass('open').addClass('col-xs-12').addClass('col-sm-6').addClass('col-md-4');
    });
});