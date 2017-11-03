<?php

include( 'wp-load.php' );
global $wpdb;
 $refno = $_REQUEST['refno'];
 $merchantid = $_REQUEST['merchantid'];
 $total = $_REQUEST['total'];
 $customeremail = $_REQUEST['customeremail'];
 $ref = 'wc-completed';


$wpdb->update(
    'wp_posts',
    array(
         'post_status'   => $ref   
    ),
    array( 'ID' => $refno )
); 

?>