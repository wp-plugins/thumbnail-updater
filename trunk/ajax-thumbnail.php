<?php
/**
 * @package Portfolio
 * @subpackage Portfolio_Ajax_Add_Thumbnail
 * @since version 1.0
 *
 * Handles the ajax call for adding thumbnails on the Add Project page.
 */

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/* Vars */

global $wpdb;

$nonce = false;

$attachment_id = false;

$thumbnail_updated = false;

/* Check permissions */

if( !current_user_can( 'edit_post' ) )
	wp_die( 'Sorry, you do not have the propper permissions to access this resource.' );



/* Nonce does not match */

$nonce = ( isset( $_POST['_ajax_nonce'] ) ) ? $_POST['_ajax_nonce'] : false;

if( !wp_verify_nonce( $nonce, $this->locale ) ) {

	$thumbnail_updated = false;
        
        $message = 'Nonce not valid!';

}

/* Check value of $_POST['attachment_id'] */

$attachment_id = ( isset( $_POST['postid'] ) && !empty( $_POST['postid'] ) && ( $_POST['postid'] !== 'undefined' ) )

	? (int) $_POST['postid']

	: false;

/* Query for $attachment_id */
$attachement_exists = get_post( $attachment_id ) ;



/* Attachment does not exist - do not proceed */
if( !(bool)$attachement_exists ) {

    $thumbnail_updated = false;

    $message = 'No attachment specified';
}

if( (bool)$attachment_id ) {

        $wp_upload_dir = wp_upload_dir( date('Y-m', strtotime($attachement_exists->post_date))  );

        $img_path = $wp_upload_dir['basedir'] . '/' .
                    get_post_meta( $attachment_id, '_wp_attached_file', true );

        $meta = wp_generate_attachment_metadata( $attachment_id, $img_path );

        if ((bool)$meta) {
            wp_update_attachment_metadata( $attachment_id, $meta );
            $thumbnail_updated = true;
        } else {
            $thumbnail_updated = false;
            $message = "Thumbnail generation failed. Please check your permissions.";
        }
    
} else {

    $thumbnail_updated = false;

    $message = "Attachment not valid.";
    
}


/* Output */

header( 'Content-type: application/jsonrequest' );


$json = json_encode( array(

	'attachment_id'     => $attachment_id,

        'thumbnail_updated' => $thumbnail_updated,

        'message'           => $message,

        'debug'             => $meta,
    
	) );

print $json;

exit();

?>