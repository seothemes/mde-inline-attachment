<?php

require($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

$upload_dir = wp_upload_dir();

$post_id = $_POST['post_id'];

$uploadFolder = $upload_dir['path'];
$onlinePath = $upload_dir['url'];
$response = array();
if (isset($_FILES['file'])) {
	$file = $_FILES['file'];
	$filename = '/' . uniqid() . '.' . (pathinfo($file['name'], PATHINFO_EXTENSION) ? : 'png');
	move_uploaded_file($file['tmp_name'], $uploadFolder . $filename);
	$response['filename'] = $onlinePath . $filename;

	$wp_filetype = wp_check_filetype( $filename);
	$attachment = array(
		'guid' => $uploadFolder . $filename,
		'post_mime_type' => $wp_filetype['type'],
		'post_title' => preg_replace('/\.[^.]+$/', '', $filename),
		'post_content' => '',
		'post_status' => 'inherit'
	);
	$attachment_id = wp_insert_attachment($attachment, $uploadFolder . $filename, $post_id);

	require_once(ABSPATH . 'wp-admin/includes/image.php');

	$attachment_data = wp_generate_attachment_metadata( $attachment_id, $uploadFolder . $filename );
	wp_update_attachment_metadata( $attachment_id, $attachment_data );

} else {
	$response['error'] = 'Error while uploading file';
}
echo json_encode($response);

?>
