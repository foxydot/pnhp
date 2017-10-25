<?php

function msdlab_news_cleanup()
{
remove_action('genesis_before_loop', 'genesis_do_cpt_archive_title_description');
remove_action('genesis_before_loop', 'genesis_do_date_archive_title');
remove_action('genesis_before_loop', 'genesis_do_blog_template_heading');
remove_action('genesis_before_loop', 'genesis_do_posts_page_heading');
remove_action('genesis_before_loop', 'genesis_do_taxonomy_title_description', 15);
remove_action('genesis_before_loop', 'genesis_do_author_title_description', 15);
remove_action('genesis_before_loop', 'genesis_do_author_box_archive', 15);
}

function msdlab_multimedia_icons(){
global $post;
global $multimedia_info;
$multimedia_info->the_meta($post->ID);
if($multimedia_info->get_the_value('hasvideo')){
print '<i class="fa fa-video-camera"><span class="sr-only">This article includes video</span></i>';
}
if($multimedia_info->get_the_value('hasaudio')){
print '<i class="fa fa-volume-up"><span class="sr-only">This article includes audio</span></i>';
}
}

function msdlab_news_entry_attr($attr){
$attr['class'] .= ' col-xs-12 col-sm-6 col-md-4';
return $attr;
}
function msdlab_equalize_attr($attr){
$attr['class'] .= ' equalize';
return $attr;
}

function msdlab_add_content_modal(){
    print '<div class="content_modal modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->';
}