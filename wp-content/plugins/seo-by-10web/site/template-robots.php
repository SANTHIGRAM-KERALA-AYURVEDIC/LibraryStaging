<?php
defined('ABSPATH') || die('Access Denied');

function wdseo_robots_file_content() {
	$options = new WD_SEO_Options();
	if (isset($options->robots_file)) {
		return $options->robots_file;
	}
}

function wdseo_robots_file() {
  $wdseo_robots = wdseo_robots_file_content();
  return $wdseo_robots;
}
