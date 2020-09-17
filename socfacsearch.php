<?php
/**
 * Plugin Name:     SoC Faculty Search
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     A plug-in using shortcodes creates a text search box to search faculty members. Will use Alpine.js
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

		// query faculty
		$query = new WP_Query([
			'post_type' => 'faculty',
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC',
		]);

		// Check that we have query results.
		if ($query->have_posts()) {

			$data = [];

			// Start looping over the query results.
			while ($query->have_posts()) {

				$query->the_post();

				$data[] = [
					'name' => get_the_title(),
					'url' => get_post_permalink(),
				];

				// Contents of the queried post results go here.

			}

		}

		// Restore original post data.
		wp_reset_postdata();

		//return data
		$this->data = $data;
	}

	public function SendData()
	{
		$payload = json_encode($this->data);

		$out ="var faculty = $payload;";

		echo sprintf("<script>%s</script>", $out);

	}


	public function LoadHTMLPayload()
	{
		$formSource = plugin_dir_path(__FILE__) . 'src/form.html';
		return file_get_contents($formSource);
	}

}
