jQuery(function($){
    var $grid = $('.speaker_aggregate>.wrap').isotope({
        itemSelector: 'article',
    });


    $('.speaker-filters select').on( 'change', function() {
        // get filter value from option value
        var filterValue = this.value;
        //$grid.isotope({ filter: filterValue });
        window.location.href = filterValue;
    });

    $(window).scroll(function(){
        $grid.isotope();
    });
});