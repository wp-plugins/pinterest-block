<?php

//If uninstall/delete not called from WordPress then exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//Remove option records from options table
delete_option( 'pblock_settings' );
delete_option( 'pblock_hide_pointer' );

//Remove custom post meta fields
$posts = get_posts( array( 'numberposts' => -1 ) );

foreach( $posts as $post ) {
    delete_post_meta( $post->ID, 'pblock_pinning_blocked' );
}
