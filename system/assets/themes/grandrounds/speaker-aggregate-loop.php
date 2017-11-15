<?php

do_action( 'genesis_before_entry' );

genesis_markup( array(
    'open'    => '<article %s>',
    'context' => 'entry',
) );

do_action( 'genesis_entry_header' );

do_action( 'genesis_before_entry_content' );


do_action( 'genesis_after_entry_content' );

do_action( 'genesis_entry_footer' );

genesis_markup( array(
    'close'   => '</article>',
    'context' => 'entry',
) );

do_action( 'genesis_after_entry' );