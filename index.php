<?php

// hide exif_imagetype notice
error_reporting(E_ALL & ~E_NOTICE);

// set your default timezone, in my case is Asia/Ho_Chi_Minh
date_default_timezone_set('your timezone');

$dir = 'input directory here';

// step 1: rename files to prevent duplicate
prevent_duplicate_file($dir);

// step 2: start rename using date taken or date modified
start_a_journey($dir);

/**
* Start a journey
*/
function start_a_journey($dir) {
	if (!is_dir($dir))
		return;	

	$items = scandir($dir);
	foreach ($items as $item)
		rename_image($dir, $dir . DIRECTORY_SEPARATOR . $item);		
}

/**
* 
* Rename an image using date taken or date modified
*
* @author quydm dominhquy at gmail dot com
*
* @param string file The image to rename
* @return void
*
*/
function rename_image($dir, $file) {
	if (!is_image($file))
		return;

	$data = exif_read_data($file);
	if (isset($data['DateTimeOriginal'])) {
		$file_name = str_replace(' ', '_', $data['DateTimeOriginal']);
		$file_name = str_replace(':', '-', $file_name);
	} else {
		$file_name = date('Y-m-d_H-i-s', $data['FileDateTime']);
	}

	$file_info = pathinfo($file);
	$file_extension = strtolower($file_info['extension']);

	$new_file = $dir . DIRECTORY_SEPARATOR . $file_name . '.' . $file_extension;
	if (file_exists($new_file))
		$new_file = $dir . DIRECTORY_SEPARATOR . $file_name . '_' . rand(1, 100) . '.' . $file_extension;

	rename($file, $new_file);
}

/**
*
* Rename image files in a directory to prevent duplicate
*
* @author quydm dominhquy at gmail dot com
* 
* @param string dir The directory to scan
* @return void
* 
*/
function prevent_duplicate_file($dir) {
	if (!is_dir($dir))
		return;	

	$items = scandir($dir);
	$count = count($items);
	foreach ($items as $item) {
		$old_file = $dir . DIRECTORY_SEPARATOR . $item;
		if (!is_image($old_file))
			continue;

		$new_file = $dir . DIRECTORY_SEPARATOR . $count-- . $item;
		rename($old_file, $new_file);
	}
}

/**
*
* Check if a file is an image. Only support JPEG and PNG.
*
* @author quydm dominhquy at gmail dot com
* 
* @param string file The file to check
* @return bool Return TRUE if the file is an image, FALSE otherwise
*
*/
function is_image($file) {
	$img_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);

	$type = exif_imagetype($file);
	return in_array($type, $img_types);
}
