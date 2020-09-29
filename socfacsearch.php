<?php
/**
 * Plugin Name:     SoC Faculty Search
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A plug-in using shortcode [socfacsearch] creates a text search box to search faculty members. Will use Alpine.js
 * Author:
 * Author URI:
 * Text Domain:     socfacsearch
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Socfacsearch
 */

// Your code starts here.

add_shortcode('socfacsearch', function ($atts = []) {


	$f = new SoCFacultySearch();
	$f->doSearch();
	$f->SendData();
	return $f->LoadHTMLPayload();

});


class SoCFacultySearch
{

	public $data;
	public $htmlPayload;

	public function __construct()
	{

	}

	public function doSearch()
	{


		$r = get_posts([
			'posts_per_page' => -1,
			'post_type' => 'faculty',
			"post_status" => "publish",
			'order' => 'asc',
			'orderby' => 'post_title',
		]);

		foreach ($r as $i) {
			
			// parse out the path only to work with multisite
			$parsed = parse_url($i->guid);
			$path = $parsed['path'];
		
			// if root path, skip
			if ($path == '/') {
				// $issue[] = [
				// 	'name' => $i->post_title,
				// 	'url' => $path,
				// 	'guid' => $i->guid,
				// ];
				continue;
			}
		
			$data[] = [
				'name' => $i->post_title,
				'url' => $path,
				'guid' => '?p=' . $i->ID,
			];
		}

		//return data
		$this->data = $data;
	}

	public function SendData()
	{
		$payload = json_encode($this->data);

		$out ="var faculty = $payload;";
		$out .= "console.log('number of faculty in memory:' + faculty.length);";
		$out .= "console.dir(faculty);";

		echo sprintf("<script>%s</script>", $out);

	}


	public function LoadHTMLPayload()
	{
		$formSource = plugin_dir_path(__FILE__) . 'src/form.html';
		return file_get_contents($formSource);
	}

}
