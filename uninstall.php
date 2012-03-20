<?php
//If uninstall/delete not called from WordPress then exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

//Remove option records from options table
delete_option( 'pblock_options' );
delete_option( 'pblock_ignore' );

//Remove all Post/Page meta from postmeta table
	global $wpdb;
    $sql = "SELECT post_id
            FROM $wpdb->postmeta
            WHERE meta_key = 'pblock_pinning_blocked'"; 
            
    $results = $wpdb->get_results( $sql, ARRAY_A );
        
    foreach( $results as $postid ) {
       $postID = $postid['post_id'];
       delete_post_meta( $postID, 'pblock_pinning_blocked' );
    }
?>
