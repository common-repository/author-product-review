<?php
/**
 * @package Author_Product_Review
 * @version 1.1
 */
/*
* Plugin Name: Author Product Review
* Plugin URI: http://www.techlila.com/wordpress-plugins/author-product-review/
* Description: This plugin adds product review schema.org markup to posts. This plugin helps to add <strong>rating, author name, date and price of the product</strong>. It'll help you to get more click through rate and better visibility in Google search engine.
* Author: Rajesh Namase
* Version: 1.1
* Author URI: http://www.techlila.com/
* License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define('AUTHOR_PRODUCT_REVIEW_VERSION', '1.1');

include plugin_dir_path(__FILE__).'options.php';

function add_author_product_review($content) {
	global $wp_query;
	
	if( get_post_type() != 'post' || !is_single())
		return $content;
		
	$max_rate = get_option('review-max-rating', 5);
	$min_rate = get_option('review-interval-rating', 0.5);
	$show_rate = get_option('review-show-rating', 1);
	$fill_style = get_option('review-fill-style', 'horizontal');
	
	$postID = $wp_query->post->ID;
	
	$name = get_post_meta($postID, 'schema_product_name', true);
	$price = get_post_meta($postID, 'schema_product_price', true);
    $price_currency = get_post_meta($postID, 'schema_price_currency', true);
	$rating = (float) get_post_meta($postID, 'schema_rating', true);
	
	
	if($rating < $min_rate)
		return str_replace('{rating}', '', $content);
	
	if(!$show_rate){
		$style = ' style="display: none;"';
	} else {
		$style = '';
	}
	
	$customPlacement = strpos($content, '{rating}') !== false;
	
	if($customPlacement){
		$custom = '<div class="inline-rating"><span class="review-rating">' . $rating . '</span> / <span class="best-rating">' . $max_rate . '</span> stars ';
		$stars = $rating;
		for($i = 1; $i <= $max_rate; $i++){
			$custom .= '<span class="review-star-empty">';
			if($stars > 1){
				$custom .= '<span class="review-star full">&nbsp;</span>';
			} elseif($stars > 0) {
				$size = $stars * 16;
				if($fill_style == 'vertical'){
					$css = 'height: ' . $size . 'px;width:16px;background-position:0 -' . (16 - $size) . 'px;vertical-align:-' . (16 - $size) . 'px;';
				} else {
					$css = 'width: ' . $size . 'px;';
				}
				$custom .= '<span class="review-star" style="' . $css . '">&nbsp;</span>';	
			} else {
				$custom .= '&nbsp;';
			}
			$stars--;
			$custom .= '</span>';
		}
		$custom .= '</div>';
		$content = str_replace('{rating}', $custom, $content);
		$style = ' style="display: none;"';
	}
	$return = '<div itemscope itemtype="http://schema.org/Review"><div itemprop="reviewBody">' . "\n" . $content . "\n</div>";
	$return .= "\n\n<!-- Author Product Review -->\n";
	$return .= '<meta itemprop="name" content="' . get_the_title() . '" />';
        $return .= '<meta itemprop="author" content="' . get_the_author() . '" />';   
        $return .= '<meta itemprop="datePublished" content="' . get_the_date('c')  . '" />' . "\n";      
         
        if($name) {
                    $return .= '<div itemprop="itemReviewed" itemscope itemtype="http://schema.org/Thing">' . "\n"; 
		    $return .= '<meta itemprop="name" content="' . $name . '" />' . "\n";
                    $return .= "</div>\n";
        }

        $return .= '<div class="review-data" itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"' . $style . '>';	
	$return .= '<meta itemprop="worstRating" content="' . $min_rate . '" />';
    	$return .= '<span class="star-rating"><span itemprop="ratingValue" class="review-rating">' . $rating . '</span> / <span itemprop="bestRating" class="best-rating">' . $max_rate . '</span> stars</span>';

	$stars = $rating;
	for($i = 1; $i <= $max_rate; $i++){
		$return .= '<span class="review-star-empty">';
		if($stars > 1){
			$return .= '<span class="review-star full">&nbsp;</span>';
		} else {
			$size = $stars * 16;
			if($fill_style == 'vertical'){
				$css = 'height: ' . $size . 'px;width:16px;background-position:0 -' . (16 - $size) . 'px;vertical-align:middle;';
			} else {
				$css = 'width: ' . $size . 'px;';
			}
			$return .= '<span class="review-star" style="' . $css . '">&nbsp;</span>';	
		}
		$stars--;
		$return .= '</span>';
	}
	/*$return .= '<span class="review-blank">';
	$return .= '<span class="review-stars" style="width: ' . ($rating / $max_rate) * 84 . 'px;">&nbsp;</span>';
	$reutnr .= '</span>';*/
	$return .= '</div>';	
        $return .= '</div>';
 
	if($name){
		$return .= '<div itemscope itemtype="http://schema.org/Product">' . "\n";
		$return .= '<meta itemprop="name" content="' . $name . '" />' . "\n";

            if($price){
            	if($price_currency) 
	                $return .= '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer"><meta itemprop="price" content="' . $price . '"><meta itemprop="priceCurrency" content="' . $price_currency . '" /></div>';
                else
                	$return .= '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer"><meta itemprop="price" content="' . $price . '"><meta itemprop="priceCurrency" content="USD" /></div>';
	        }
                 
		$return .= "</div>\n";
	}
	
	return $return;
}

function add_author_product_review_box() {
	add_meta_box('author_product_review', 'Product Review Info', 'author_product_review_box', 'post', 'side', 'high');
}

function author_product_review_box($post) {
	// Use nonce for verification
	wp_nonce_field( plugin_basename( __FILE__ ), 'review_nonce' );
	
	$max_rate = get_option('review-max-rating', 5);
	$min_rate = get_option('review-interval-rating', 0.5);
	$min_rate = ($min_rate > 0)?$min_rate:0.5;
    $currencies =  array("EUR","USD","GBP","INR","AUD","CAD","AED","CHF","CNY","MYR","THB","NZD","JPY","SGD","PHP","SAR","HKD","MXN","SEK","HUF");
	// The actual fields for data entry
	echo '<label for="schema_product_name">Name of Product Reviewed</label><br>';
	echo '<input type="text" id="schema_product_name" name="schema_product_name" placeholder="Ex: WordPress" value="' . get_post_meta($post->ID, 'schema_product_name', true) . '" size="25" /><br>';
	
	echo '<label for="schema_product_price">Price of Product</label><br>';
	echo '<input type="text" id="schema_product_price" name="schema_product_price" placeholder="Ex: 00.00" value="' . get_post_meta($post->ID, 'schema_product_price', true) . '" size="25" /><br>';

        echo '<label for="schema_price_currency">Price Currency <a title="Currency Information" href="http://en.wikipedia.org/wiki/ISO_4217" target="_blank">Click here</a> for more info.</label><br>';   
        echo '<select id="schema_price_currency" name="schema_price_currency" style="width:75px;">';
        
           for($i=0;$i<20;$i++){
               echo '<option ' ;

               if($currencies[$i] == get_post_meta($post->ID, 'schema_price_currency', true)){
                   echo 'selected="selected"';
               } 
               echo  'value="'. $currencies[$i] . '"> '. $currencies[$i] . '</option>';
           }
        echo '</select>';	

	echo '<br><label for="schema_rating">Product Rating</label><br>';
	echo '<select id="schema_rating" name="schema_rating" style="width:75px;">';
	
	$rating =  floatval(get_post_meta($post->ID, 'schema_rating', true));
	if(empty($rating) || $rating == 0)
		echo '<option value="0" selected>None</option>';
	else
		echo '<option value="0">None</option>';
				
	for($i = $min_rate; $i <= $max_rate; $i += $min_rate){
		$select = (number_format($rating,2,'.','') == number_format($i,2,'.',''))?" selected":"";
		echo '<option value="' . $i . '"'. $select . '>' . number_format($i, 1) . '</option>';
	}
	echo '</select>';
}

function author_review_save_postdata( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
	  return;
	
	if ( !wp_verify_nonce( $_POST['review_nonce'], plugin_basename( __FILE__ ) ) )
	  return;
	
	
	// Check permissions
	if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	
	
	$productName = filter_var($_POST['schema_product_name'], FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW);
	$productPrice = filter_var($_POST['schema_product_price'], FILTER_SANITIZE_STRING);
        $priceCurrency = filter_var($_POST['schema_price_currency'], FILTER_SANITIZE_STRING);
	$rating = $_POST['schema_rating'];
	
	if(!update_post_meta($post_id, 'schema_product_name', $productName))
		add_post_meta($post_id, 'schema_product_name', $productName);
	
	if(!update_post_meta($post_id, 'schema_product_price', $productPrice))
		add_post_meta($post_id, 'schema_product_price', $productPrice);	
 
        if(!update_post_meta($post_id, 'schema_price_currency', $priceCurrency))
		add_post_meta($post_id, 'schema_price_currency', $priceCurrency);	
		
	if(!update_post_meta($post_id, 'schema_rating', $rating))
		add_post_meta($post_id, 'schema_rating', $rating);
}

function add_author_review_header(){
	echo '<link type="text/css" rel="stylesheet" href="' . plugins_url( 'author-product-review.css' , __FILE__ ) . '" />' . "\n";
}

add_filter('the_content', 'add_author_product_review');

add_action('add_meta_boxes', 'add_author_product_review_box');
add_action('save_post', 'author_review_save_postdata');
add_action('wp_head', 'add_author_review_header');

if (is_admin()){
  add_action('admin_menu', 'add_author_product_review_options_page');
  add_action('admin_init', 'register_author_product_review_settings');
}

// Add settings link on plugin page
function author_product_review_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=author-product-review/options.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_$plugin", 'author_product_review_settings_link' );

?>