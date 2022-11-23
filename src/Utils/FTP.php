<?php

function wpse_74180_upload_to_ftp( $args ) {

	$upload_dir = wp_upload_dir();
	$upload_url = get_option('upload_url_path');
	$upload_yrm = get_option('uploads_use_yearmonth_folders');


	/**
	 * Change this to match your server
	 * You only need to change the those with (*)
	 * If marked with (-) its optional 
	 */

	$settings = array(
		'host'	  =>	'ip or hostname',  			// * the ftp-server hostname
		'port'    =>    21,                                 // * the ftp-server port (of type int)
		'user'	  =>	'username', 				// * ftp-user
		'pass'	  =>	'password',	 				// * ftp-password
		'cdn'     =>    'cdn.example.com',			// * This have to be a pointed domain or subdomain to the root of the uploads
		'path'	  =>	'/',	 					// - ftp-path, default is root (/). Change here and add the dir on the ftp-server,
		'base'	  =>    $upload_dir['basedir']  	// Basedir on local 
	);


	/**
	 * Change the upload url to the ftp-server
	 */

	if( empty( $upload_url ) ) {
		update_option( 'upload_url_path', esc_url( $settings['cdn'] ) );
	}


	/**
	 * Host-connection
	 * Read about it here: http://php.net/manual/en/function.ftp-connect.php
	 */
	
	$connection = ftp_connect( $settings['host'], $settings['port'] );


	/**
	 * Login to ftp
	 * Read about it here: http://php.net/manual/en/function.ftp-login.php
	 */

	$login = ftp_login( $connection, $settings['user'], $settings['pass'] );

	
	/**
	 * Check ftp-connection
	 */

	if ( !$connection || !$login ) {
	    die('Connection attempt failed, Check your settings');
	}


	function ftp_putAll($conn_id, $src_dir, $dst_dir, $created) {
            $d = dir($src_dir);
	    while($file = $d->read()) { // do this for each file in the directory
	        if ($file != "." && $file != "..") { // to prevent an infinite loop
	            if (is_dir($src_dir."/".$file)) { // do the following if it is a directory
	                if (!@ftp_chdir($conn_id, $dst_dir."/".$file)) {
	                    ftp_mkdir($conn_id, $dst_dir."/".$file); // create directories that do not yet exist
	                }
	                $created  = ftp_putAll($conn_id, $src_dir."/".$file, $dst_dir."/".$file, $created); // recursive part
	            } else {
	                $upload = ftp_put($conn_id, $dst_dir."/".$file, $src_dir."/".$file, FTP_BINARY); // put the files
	                if($upload)
	                	$created[] = $src_dir."/".$file;
	            }
	        }
	    }
	    $d->close();
	    return $created;
	}

	/**
	 * If we ftp-upload successfully, mark it for deletion
	 * http://php.net/manual/en/function.ftp-put.php
	 */
	$delete = ftp_putAll($connection, $settings['base'], $settings['path'], array());
	


	// Delete all successfully-copied files
	foreach ( $delete as $file ) {
		unlink( $file );
	}
	
	return $args;
}