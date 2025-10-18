<?php
// file: `example-me/app/models/frontend/class-print-hallo-shortcode.php`

namespace Instagram_Posts\App\Models\Frontend;
use Instagram_Posts\App\Models\Base_Model;


if ( ! class_exists( __NAMESPACE__ . '\\' . 'Instagram_Posts' ) ) {


	/**
	 * Class to handle data related operations of `example_me_print_posts` shortcode
	 *
	 * @since      1.0.0
	 * @package    Instagram_Posts
	 * @subpackage Instagram_Posts/App/Models/Admin
	 */
	class Instagram_Posts extends Base_Model {
		
		/**
		 * Saves a new event
		 */
		public function createNew($post, $post_id){
			global $wpdb;

			$wpdb->insert(
				$wpdb->base_prefix.'instagram_posts', 
				[
					'instagram_id'	=> $post->id, 
					'caption'		=> $post->caption, 
					'media_type'	=> $post->media_type,
					'media_url'		=> $post->media_url, 
					'permalink' 	=> $post->permalink,
					'thumbnail_url'	=> $post->thumbnail_url, 
					'ig_timestamp'	=> $post->timestamp, 
					'username'		=> $post->username,  
					'children' 		=> json_encode($post->children),
					'post_id' 		=> $post_id,
				], ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d']
			);

			return true;
		}
	
		private function post_synced($id){
			global $wpdb;

			$query = "SELECT * FROM ".$wpdb->base_prefix."instagram_posts WHERE instagram_id = '$id'";
			$query_results = $wpdb->get_results($query);
			if(count($query_results) == 0) {
				return false;
			}
			return true;
		}

		private function create_wp_post($post_title, $post_content, $post_thumbnail, $post_date){

			$categories = array();
			#array_push($categories,get_cat_ID( 'Alle' ));
			$countCat = 0;
			if(preg_match("/(#streethockey|#streethockeygals)/i", $post_content)){
				array_push($categories,get_cat_ID( 'Streethockey' ));
				$countCat++;
			}
			if(preg_match("/(#theater|#theatergals)/i", $post_content)){
				array_push($categories,get_cat_ID( 'Theater' ));
				$countCat++;
			}
			if(preg_match("/(#beachvolley|#beachvolleygals)/i", $post_content)){
				array_push($categories,get_cat_ID( 'Beach-Volley' ));
				$countCat++;
			}
			if($countCat == 0){
				array_push($categories,get_cat_ID( 'Allgemein' ));
			}
			
			$wordpress_post = array(
				'post_title' => $post_title,
				'post_content' => $post_content,
				'post_date' => $post_date,
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type' => 'post',
				'post_category' => $categories,
				'_thumbnail_id' => $post_thumbnail,
				);
				 
			$post_id = wp_insert_post( $wordpress_post );
			return $post_id;
		}

		/**
		 * A function to truncate a text
		 *
		 *
		 * @param string $text The text to truncate
		 * @param int $length Optional. The length of the new text.
		 * @return string a new truncated text.
		 */
		private function truncate(string $text, int $length = 40): string {
			if (strlen($text) <= $length) {
				return $text;
			}
			$text = substr($text, 0, $length);
			$text = substr($text, 0, strrpos($text, " "));
			$text .= "...";
			return $text;
		}

		private function getTitle($caption){
			$lines=explode("\n", $caption);
			return $this->truncate($lines[0]);
		}

		private function getContent($caption){
			$lines=explode("\n", $caption);
			if(count($lines) > 0){
				if(strlen($lines[0]) > 40){
					return $caption;
				}else{
					return substr($caption, strpos($caption, "\n", 0));
				}
				
			}else{
				if(strlen($lines[0]) > 40){
					return $lines[0];
				}
			}
			return "";
		}

		private function addMediaToLibrary($image_url){
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents( $image_url );
			$filename = substr(basename( $image_url ), 0, strpos(basename( $image_url ), "?", 0));
			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename;
			}else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}
			file_put_contents( $file, $image_data );
			$wp_filetype = wp_check_filetype( basename($filename), null );
			$attachment = array(
				'guid'           => $upload_dir['url'] . '/' . basename($filename),
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name( $filename ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			$attach_id = wp_insert_attachment( $attachment, $file );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			return $attach_id;
		}

		private function getThumbnail($post){
			if($post->thumbnail_url){
				return $this->addMediaToLibrary($post->thumbnail_url);
			}else{
				return $this->addMediaToLibrary($post->media_url);
			}
		}
		

		private function process_posts($posts){
			$countPosts = 0;
			$countMedias = 0;
			foreach($posts as $post){
				if(!$this->post_synced($post->id)){
					
					$post_title = $this->getTitle($post->caption);
					$post_content = $this->getContent($post->caption);		

					
					$post_content = '[vc_row][vc_column][vc_column_text]'.$post_content.'[/vc_column_text][/vc_column][/vc_row]';

					if($post->children->data){
						$medias = "";
						$count = 0;
						foreach($post->children->data as $child){
							if($count == 0){
								$post_thumbnail = $this->getThumbnail($child);
								$medias .= $this->addMediaToLibrary($child->media_url).",";
							}else{
								$medias .= $this->addMediaToLibrary($child->media_url).",";
							}
							$count++;
						}
						$medias = rtrim($medias,",");
						$post_content .= '[vc_row row_height_percent="0" override_padding="yes" h_padding="2" top_padding="0" bottom_padding="5" overlay_alpha="100" gutter_size="100" column_width_percent="100" shift_y="0" z_index="0" style="inherited"]';
						$post_content .= '[vc_column column_width_percent="100" align_horizontal="align_center" gutter_size="3" overlay_alpha="50" shift_x="0" shift_y="0" medium_width="0" zoom_width="0" zoom_height="0" width="1/1"]';
						$post_content .= '[vc_gallery el_id="gallery-12" type="css_grid" medias="'.$medias.'" grid_items="2" screen_lg_items="2" screen_lg_breakpoint="1000" screen_md_items="2" screen_md_breakpoint="600" screen_sm_items="1" screen_sm_breakpoint="480" gutter_size="2" media_items="media|lightbox|original" single_overlay_opacity="50" single_overlay_anim="no" single_text_anim="no" single_image_anim="no" single_h_align="center" single_padding="2" single_icon="fa fa-search3" single_border="yes" lbox_caption="yes" no_double_tap="yes" carousel_rtl="" single_title_uppercase="" single_title_bold="" single_title_serif="" onclick="link_image" custom_links_target="_self" items="" single_half_padding="" single_no_background="" uncode_shortcode_id="109311" gallery_back_color_type="uncode-palette" gallery_back_color_solid="#ff0000"][/vc_column][/vc_row]';
					
					}else{
						$post_thumbnail = $this->getThumbnail($post);
						if($post->media_type == "VIDEO"){
							$video = $this->addMediaToLibrary($post->media_url);
							$post_content .= '[vc_row][vc_column width="1/1"][vc_single_image media="'.$video.'" media_width_percent="100"][/vc_column][/vc_row]';
						}else{
							$post_content .= '[vc_row][vc_column width="1/1"][vc_single_image media="'.$post_thumbnail.'" media_width_percent="100"][/vc_column][/vc_row]';	
						}		
					}
					
					
					$post_id = $this->create_wp_post($post_title, $post_content, $post_thumbnail, $post->timestamp);
					$this->createNew($post, $post_id);
				}
			}
		}
		

		/**
		 * Fetches posts from database
		 *
		 * @param string $shortcode Shortcode for which posts should be fetched
		 * @param array $atts Arguments passed to shortcode
		 * @return \WP_Query WP_Query Object
		 */
		public function load_posts() {
			
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
					$this->process_posts($content->data);

					return $content;
				}
			}


			$array = array();
			
			return $array;
		}

	

		
	}
}