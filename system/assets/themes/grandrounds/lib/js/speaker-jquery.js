jQuery(function($){
    var $grid = $('.speaker_aggregate>.wrap').isotope({
        itemSelector: 'article',
    });
    $('.speaker_aggregate').append( '<span class="load-more"></span>' );
    var button = $('.speaker_aggregate .load-more');
    var page = 2;
    var loading = false;
    var scrollHandling = {
        allow: true,
        reallow: function() {
            scrollHandling.allow = true;
        },
        delay: 400 //(milliseconds) adjust to the highest acceptable value
    };

    $('.speaker-filters select').on( 'change', function() {
        // get filter value from option value
        var filterValue = this.value;
        //$grid.isotope({ filter: filterValue });
        window.location.href = filterValue;
    });

    $(window).scroll(function(){
        if( ! loading && scrollHandling.allow ) {
            scrollHandling.allow = false;
            setTimeout(scrollHandling.reallow, scrollHandling.delay);
            var offset = $(button).offset().top - $(window).scrollTop();
            if( 2000 > offset ) {
                loading = true;
                var data = {
                    action: 'be_ajax_load_more',
                    page: page,
                    query: beloadmore.query,
                };
                $.post(beloadmore.url, data, function(res) {
                    if( res.success) {
                        var $content = $( res.data );
                        // add jQuery object
                        $grid.append( $content ).imagesLoaded(function() {
                            $grid.isotope('appended', $content );
                        });
                        $('.speaker_aggregate').append( button );
                        page = page + 1;
                        loading = false;
                    } else {
                        //console.log('pass error'+res);
                    }
                }).fail(function(xhr, textStatus, e) {
                    //console.log(xhr.responseText);
                });

            }
        }
    });
});