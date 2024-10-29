<?php
/**
 * @package Author_Product_Review
 * @version 1.1
 */
function register_author_product_review_settings(){
	register_setting( 'review_options_group', 'review-max-rating', 'floatval' );
	register_setting( 'review_options_group', 'review-interval-rating', 'floatval' );
	register_setting( 'review_options_group', 'review-show-rating' );
	register_setting( 'review_options_group', 'review-fill-style' );
}

function add_author_product_review_options_page(){
    add_options_page('Author Product Review Settings', 'Author Product Review', 'manage_options', __FILE__, 'author_product_review_options_page');  
}

function author_product_review_options_page(){ 
?>
<div class="wrap">
<h2>Author Product Review Options <div class="g-plusone" data-size="medium" data-href="http://www.techlila.com"></div></h2>

<p><iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Ftechlila%2F&amp;layout=standard&amp;show_faces=true&amp;width=725&amp;action=like&amp;font=lucida+grande&amp;colorscheme=light&amp;height=62" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:725px; height:62px;" allowTransparency="true"></iframe></p>

<p>After filling out the Reviewed Product information on the edit post page, the rating will be shown at the very bottom of the post by default. <br />You can place the rating anywhere within the post by using the following tag: <code>{rating}</code>.</p>
<form method="post" action="options.php"> 
<?php 
	settings_fields( 'review_options_group' );
	do_settings_fields( 'review_options_group', 'review_settings' );
?>
	<table class="form-table">
        <tr valign="top">
        <th scope="row">Max Rating / Number of Stars</th>
        <td><input type="text" name="review-max-rating" value="<?php echo get_option('review-max-rating', 5); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Min Rating / Interval (Cannot be 0)</th>
        <td><input type="text" name="review-interval-rating" value="<?php echo get_option('review-interval-rating', 0.5); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">Show Rating</th>
        <td><input type="checkbox" name="review-show-rating" value="1" <?php checked( 1, get_option('review-show-rating', 1), true ); ?> /></td>
        </tr>

	<tr valign="top">
        <th scope="row">Star Fill Style</th>
	<?php $fill_style = get_option('review-fill-style', 'horizontal'); ?>
        <td><select name="review-fill-style" size="1">
		<option value="horizontal"<?php echo ($fill_style == 'horizontal')?' selected="selected"':''; ?>>Horizontal</option>
		<option value="vertical"<?php echo ($fill_style == 'vertical')?' selected="selected"':''; ?>>Vertical</option>
	</select> <p class="description">Horizontal fills from left to right<br>Vertical fills from bottom to top</p></td>
        </tr>

    </table>
<p class="submit">
<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
<p><strong>Author's Blog:</strong> <a title="Tech Tips and Tricks" href="http://www.techlila.com">Techlila</a>.<br />
<strong>Did this helped you out? :</strong> <a title="Donnate some ammout" href="https://www.paypal.com/cgi-bin/webscr&business=bsanjay@gmail.com&cmd=_donations&item_name=Friends+of+Rajesh+Namase">Do you like to donate an amount?</a></p>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
</div>

<?php
}
?>
