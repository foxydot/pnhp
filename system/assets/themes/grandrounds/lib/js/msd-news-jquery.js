jQuery(document).ready(function($) {
    $('.news_category_aggregate article:nth-child(2)').removeClass('col-sm-6').addClass('col-sm-12');
    $('.news_category_aggregate article').click(function(){
        $('.content_modal .modal-body').html($(this).html());
        $('.content_modal').modal('show');
    });
});