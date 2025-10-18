<?php
// file: `example-me/app/models/frontend/class-print-hallo-shortcode.php`

namespace Instagram_Posts\App\Models\Admin;
use Instagram_Posts\App\Models\Base_Model;


if ( ! class_exists( __NAMESPACE__ . '\\' . 'Instagram_Json' ) ) {


	/**
	 * Class to handle data related operations of `example_me_print_posts` shortcode
	 *
	 * @since      1.0.0
	 * @package    Instagram_Json
	 * @subpackage Instagram_Posts/App/Models/Admin
	 */
	class Instagram_Json extends Base_Model {
	
		/**
		 * Fetches posts from database
		 *
		 * @param string $shortcode Shortcode for which posts should be fetched
		 * @param array $atts Arguments passed to shortcode
		 * @return \WP_Query WP_Query Object
		 */
		public function get_json() {
			
			//$access_token = "EAAV43NF7V6wBOZBo5h22O3WCKh6SYAhBbHdCwZCh1mC5W6xDFkNYh5xLNLSNEJq6Q7jsfZBApOHsukQiJGXBNWkyKQCaczFScOkv2hZAITjwwCNHuyHMfBrskvjAfEETmf0LMxZC70cxZAqTaUKnK7sZAyQbHNgHR621psPa5urjoF9Te4GeKsNFPdKjeZAqYQrMkMZBijHvMAfZAa0nvR9kJfr1Tf6X4T6tcmyGOk9w4uZAOcZBCVmJYQZDZD";
			$access_token = "IGAAPp8vdZAZC1dBZAE1MeWdKMUFYUzI2Y004SVQ1V3ZAjSmVtdF9JRUJVQ29oOFdtNGlZAMVV0THVDMi1rcTFhRFFYMHpuZAjBGRlFWQms2MjNZAaDk2d2lYY25ucHVUQ00tWGl4M2dHaGk5dkZA1VndWaU41eWd3";
			$user_id = "29597602019827055";
			$limit = 10;


			$fields = "caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username,children{id,media_type,media_url,permalink,thumbnail_url,timestamp,username}";
			
			$url = "https://graph.instagram.com/v23.0/{$user_id}/media?fields={$fields}&limit={$limit}&access_token=".$access_token;

			$options = array(
				CURLOPT_RETURNTRANSFER => true,   // return web page
				CURLOPT_HEADER         => false,  // don't return headers
				CURLOPT_FOLLOWLOCATION => true,   // follow redirects
				CURLOPT_ENCODING       => "",     // handle compressed
				CURLOPT_USERAGENT      => "test", // name of client
				CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
				CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
				CURLOPT_TIMEOUT        => 120,    // time-out on response
			);

			$ch = curl_init($url);
			curl_setopt_array($ch, $options);

			$content  = curl_exec($ch);
			
			curl_close($ch);

			if(!empty($content)){
				$content = json_decode($content);

				if(isset($content->data) && !empty($content->data)){
					echo '<pre>'; print_r($content); echo '</pre>';

					return $content;
				}
			}else{
				
			}


			$array = array();
			
			return $array;
		}

	

		
	}
}