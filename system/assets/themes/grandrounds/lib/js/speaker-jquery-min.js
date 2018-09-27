jQuery(function(e){var o=e(".speaker_aggregate>.wrap").isotope({itemSelector:"article"});e(".speaker-filters select").on("change",function(){
// get filter value from option value
var e=this.value;
//$grid.isotope({ filter: filterValue });
window.location.href=e}),e(window).scroll(function(){o.isotope()})});