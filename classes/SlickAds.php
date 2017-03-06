<?php
class SlickAds {

	public function __construct() {
		add_action('admin_menu', array( $this, 'add_slickads_admin_options' ), 0);
		add_action('wp_footer', array( $this, 'slick_footer_scripts' ));
		add_shortcode('slickads_main', array( $this, 'view_slickads_main' ));
		add_shortcode('slickads_sidebar', array( $this, 'view_slickads_sb' ));
	}

	public function add_slickads_admin_options() {
		global $shortname;
		add_menu_page("Advertising", "Advertising", 'edit_posts', $shortname, array( $this, 'slick_ad_options' ), 0);
	}

	public function slickAds_activate() {
		global $wpdb, $table_name;
		$charset_collate = $wpdb->get_charset_collate();
		$sql = $wpdb->query('
			CREATE TABLE IF NOT EXISTS ' . $wpdb->base_prefix . $table_name . '
			(
				ad_id INT AUTO_INCREMENT,
				ad_url TEXT,
				ad_image TEXT,
				ad_banner TEXT,
				PRIMARY KEY (ad_id)
			);'
		);
	}

	public function slickAds_deactivate() {
		global $wpdb, $table_name;
		$query = $wpdb->query('DROP TABLE ' . $wpdb->base_prefix . $table_name . ';');
	}

	public function view_slickads_main() {
		global $wpdb, $plugin_url, $table_name;
		$results = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . $table_name . ";");
		$ret = '<div style="margin:0 auto;" id="advertisements_main" data-cycle-pause-on-hover="true">';
		$ads = $results;
		foreach($ads as $ad) {
			if($ad->ad_banner != '' && $ad->ad_url != ''){

			$ret .= '<div><a href="'.$ad->ad_url.'" target="_blank"><img src="'.$plugin_url.'/timthumb.php?src='.$ad->ad_banner.'&w=909&h=310&zc=1&q=100&a=c"></a></div>';
			}
		}
		$ret .= '</div><span class="clear"></span>';
		return $ret;
	}

	public function view_slickads_sb() {
		global $wpdb, $plugin_url, $table_name;
		$results = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . $table_name . ";");
		$ret = '<div style="margin:0 auto;" id="advertisements_sb" data-cycle-pause-on-hover="true">';
		$ads = $results;
		foreach($ads as $ad) {
			if($ad->ad_image != '' && $ad->ad_url != ''){

			$ret .= '<div><a href="'.$ad->ad_url.'" target="_blank"><img src="'.$plugin_url.'/timthumb.php?src='.$ad->ad_image.'&w=250&h=400&zc=1&q=100&a=c"></a></div>';
			}
		}
		$ret .= '</div><span class="clear"></span>';
		return $ret;
	}

	public function slick_header_scripts() {
		global $plugin_url;
		$ret = '
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
			<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
			';
		echo $ret;
	}

	public function slick_footer_scripts() {
		global $plugin_url;
		$ret = '
		<script type="text/javascript" src="'.$plugin_url.'/js/jquery-1.10.2.min.js"></script>
		<script type="text/javascript" src="'.$plugin_url.'/js/jquery.cycle2.min.js"></script>
		<script>
		$=jQuery;
			$(document).ready(function(){
				$(\'#advertisements_main\').cycle({
					\'slides\': \'> div\',
					\'pause-on-hover\': true,
					\'random\': true,
					\'log\': false
				});
				$(\'#advertisements_sb\').cycle({
					\'slides\': \'> div\',
					\'pause-on-hover\': true,
					\'random\': true,
					\'log\': false
				});
			});
		</script>';
		echo $ret;
	}

	public function slick_ad_options() {
		global $plugin_url, $wpdb, $table_name;
		wp_enqueue_style('slick-ads', $plugin_url . "/css/style.css", null, '1.0', false);
		wp_enqueue_media();
		if(isset($_POST['submit'])){
			$allIDs = '';
			$ctrl = 0;
			if(isset($_POST['ad_id'])){
				foreach($_POST['ad_id'] as $ad_id){
					if($ctrl > 0){
						$allIDs .= ', ';
					}
					$allIDs .= $ad_id;
					$ctrl++;
				}
				$delete = 'DELETE FROM ' . $wpdb->base_prefix . $table_name . ' WHERE ad_id NOT IN (' . $allIDs . ')';
			} else {
				$delete = 'TRUNCATE TABLE ' . $wpdb->base_prefix . $table_name . ';';
			}
			$query = $wpdb->query($delete);
			if($_POST['ad_url']):
				foreach($_POST['ad_url'] as $i => $url){
					if($_POST['ad_id'][$i] == ''){
						$sql = "INSERT INTO " . $wpdb->base_prefix . $table_name . " VALUES (NULL, '" . $url . "', '" . $_POST['ad_image'][$i] ."', '" . $_POST['ad_banner'][$i] . "');";
					} else {
						$sql = "UPDATE " . $wpdb->base_prefix . $table_name . " SET ad_url = '" . $url . "', ad_image = '" . $_POST['ad_image'][$i] . "', ad_banner = '" . $_POST['ad_banner'][$i] . "' WHERE ad_id = '" . $_POST['ad_id'][$i] . "'";
					}
					$query = $wpdb->query($sql);
				}
			endif;
		}
	?>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<h2 class="sa-header">SLICK ADS</h2>
	<div class="wrap">
		<h2>Advertising</h2>
		<?php
			if(isset($_POST['submit'])){
		?>
		<div id="message" class="updated below-h2"><p>Advertisements updated.</p></div>
		<?php
			}
		?>
		<div id="slick-ads">
			<form method="post">
				<div id="form">
					<div class="heading">
						<div class="row_url">URL</div>
						<div class="row_image">Sidebar Ad (250x400)</div>
						<div class="row_image">Home Page Ad (909x310)</div>
						<div class="row_button">Action</div>
						<span class="clear"></span>
					</div>
					<?php
						$query = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . $table_name . ";");
						$ctrl = 0;
						if(count($query) > 0){
							foreach($query as $rows) {
								$ctrl++;
					?>
					<div id="row_<?php echo $ctrl; ?>">
						<input type="hidden" name="ad_id[]" value="<?php echo $rows->ad_id; ?>">
						<input type="hidden" name="ad_location[]" value="home">
						<div class="row_url"><input type="text" class="field_url" name="ad_url[]" value="<?php echo $rows->ad_url; ?>"></div>
						<div class="row_image">
							<input type="text" class="field_image_thumb" name="ad_image[]" value="<?php echo $rows->ad_image; ?>" readonly="readonly">
							<span class="button image_upload_button" data-href="#row_<?php echo $ctrl; ?>" data-type="thumb">Upload Image</span>
						</div>
						<div class="row_image">
							<input type="text" class="field_image_banner" name="ad_banner[]" value="<?php echo $rows->ad_banner; ?>" readonly="readonly">
							<span class="button image_upload_button" data-href="#row_<?php echo $ctrl; ?>" data-type="banner">Upload Image</span>
						</div>
						<div class="row_button"><button type="button" class="delete_button" onclick="removeThis('#row_<?php echo $ctrl; ?>');"></button></div>
						<span class="clear"></span>
					</div>
					<?php
							}
						} else {
							$ctrl++;
					?>
					<div id="row_<?php echo $ctrl; ?>">
						<input type="hidden" name="ad_id[]" value="">
						<input type="hidden" name="ad_location[]" value="home">
						<div class="row_url"><input type="text" class="field_url" name="ad_url[]" value=""></div>
						<div class="row_image">
							<input type="text" class="field_image_thumb" name="ad_image[]" value="" readonly="readonly">
							<span class="button image_upload_button" data-href="#row_<?php echo $ctrl; ?>" data-type="thumb">Upload Image</span>
						</div>
						<div class="row_image">
							<input type="text" class="field_image_banner" name="ad_banner[]" value="" readonly="readonly">
							<span class="button image_upload_button" data-href="#row_<?php echo $ctrl; ?>" data-type="banner">Upload Image</span>
						</div>
						<div class="row_button"><button type="button" class="delete_button" onclick="removeThis('#row_<?php echo $ctrl; ?>');"></button></div>
						<span class="clear"></span>
					</div>
					<?php
						}
					?>
				</div>
				<div><button class="button add_button" type="button">Add More</button></div>
				<div><button class="button button-primary button-large" type="submit" name="submit">Update</button></div>
			</form>
		</div>
	</div>
	<script src="<?php echo $plugin_url; ?>/js/jquery-1.10.2.min.js"></script>
	<script>
	var curr_ads = <?php echo $ctrl; ?>;
	var max_ad = 10;
	var index = <?php echo $ctrl; ?>;
	$(document).ready(function($){
		$('.add_button').click(function(event){
			if(curr_ads < max_ad){
				curr_ads++;
				index++;
				$('<div id="row_' + index + '">' +
					'<input type="hidden" name="ad_id[]" value=""><input type="hidden" name="ad_location[]" value="home">' +
					'<div class="row_url"><input type="text" class="field_url" name="ad_url[]"></div>' +
					'<div class="row_image"><input type="text" class="field_image_thumb" name="ad_image[]" readonly="readonly"><br /><span class="button image_upload_button" data-href="#row_' + index + '" data-type="thumb">Upload Image</span></div>' +
					'<div class="row_image"><input type="text" class="field_image_banner" name="ad_banner[]" readonly="readonly"><br /><span class="button image_upload_button" data-href="#row_' + index + '" data-type="banner">Upload Image</span></div>' +
					'<div class="row_button"><button type="button" class="delete_button" onclick="removeThis(\'#row_' + index +'\');"></button></div>' +
					'<span class="clear"></span>' +
				'</div>').appendTo('#form');
			} else {
				alert("You've reached the maximum number of ads! Please delete older once to replace new a new one.");
			}
		});
		$('#form').on('click', '.image_upload_button', function(){
			var custom_uploader;
			row = $(this).attr("data-href");
			type = $(this).attr("data-type");
			// If the uploader object has already been created, reopen the dialog
			if (custom_uploader) {
				custom_uploader.open();
				return;
			}
			// Extend the wp.media object
			custom_uploader = wp.media.frames.file_frame = wp.media({
				title: 'Choose Image',
				button: {
					text: 'Choose Image'
				},
				multiple: false
			});
			// When a file is selected, grab the URL and set it as the text field's value
			custom_uploader.on('select', function() {
				attachment = custom_uploader.state().get('selection').first().toJSON();
				$(row).find('.row_image .field_image_' + type).val(attachment.url);
			});
			// Open the uploader dialog
			custom_uploader.open();
		})
	});
	function removeThis(str){
		$(str).remove();
		curr_ads--;
	}
	</script>
	<?php
	}

}
?>
