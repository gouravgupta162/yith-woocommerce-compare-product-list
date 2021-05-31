<?php 

/*
Plugin Name: WSS YITH WC Compare Product List Add-On
Plugin URI:https://www.wondersoftsolutions.com/
Description: The <code><strong>WSS YITH WC Compare Product List</strong></code> add-on lets your customers allow to quick view there compare product list based on category wise. It will working only with <strong>YITH Woocommerce Compare</strong> plugin. use <code><strong>[yith_woocompare_model_list_button]</strong></code> Shortcode
Version: 1.0
Author: Gourav Gupta
Author URI: https://www.upwork.com/o/profiles/users/~01d2123e60c93d83a1/
License: GPLv2 or later
Text Domain: PFG_TXTDM
Domain Path: /languages
WC requires at least: 4.5.0
WC tested up to: 5.3.0
*/ 

function yith_woocompare_model_list_button()
{
	$dir = plugins_url('yith-woocommerce-compare-product-list');
	wp_enqueue_style('bootstrapcss', "$dir/css/slide-out-panel.css");
	wp_enqueue_script('jquery-slideout', "$dir/js/slide-out-panel-jquery-2.0.1.js" );
	wp_enqueue_script('jquery-bootstrapjs', "$dir/js/bootstrap.min.js" );
	wp_enqueue_script('jquery-slidepanel', "$dir/js/slide-out-panel.js" );
	
	wp_enqueue_style('scrolling-tabs', "$dir/css/jquery.scrolling-tabs.css");
	wp_enqueue_script('jquery-scrolling', "$dir/js/jquery.scrolling-tabs.js" ); 
	?>
	<script>
	 
		function tabinit()
		{
			$('.nav-tabs').scrollingTabs().on('ready.scrtabs', function() {
				$('.tab-content').show();
			});
		}
		jQuery('body').on('click', '.btn', () => {
			var ajaxurl = "<?php echo admin_url('admin-ajax.php') ; ?>";
			  
			if (typeof Cookies('yith_woocompare_list_wordpress-comparesmart') === 'undefined'){
				// no cookie
			}
			else {
				// have cookie
				prd_ids = Cookies.get("yith_woocompare_list_wordpress-comparesmart") ;
				var obj = jQuery.parseJSON(prd_ids);
				
				jQuery('#yith_compare_prd_count').html(obj.length);
				const slideOutPanel = jQuery('#slide-out-panel').SlideOutPanel({
				});
				jQuery.ajax({
					url :   ajaxurl,
					type : 'POST', 
					data:{
						action : 'yith_woocompare_model_list_ajax',
						productIds : prd_ids
					},
					success : function( response ) {
						jQuery('#compare-section').html(response);
						//jQuery('#tab-content').html('<a href="?action=yith-woocompare-remove-product&id=all" >Clear all</a><ul class="compare_prd_list">'+response+'</ul>');
						tabinit();
						slideOutPanel.open();
					}
				});
			}
		}); 
	</script>
	
	<style>
	ul.compare_prd_list {
		display: flex;
		flex-direction: column;
		color: #fff;
		font-family: arial;
		list-style: none;
		padding: 0;
		 
		border-radius: 5px;
		 
	}
	ul.compare_prd_list li {
		background: #285b97;
		border-radius: 3px;
		display: flex;
		flex-direction: row-reverse;
		justify-content: space-between;
		padding: 4px;
		margin: 2px;
	}
	ul.compare_prd_list li span {
		display: flex;
		padding: 3px 6px;
		justify-content: flex-start;
		color: #ffffff;
		font-size: 13px;
		align-self: flex-start;
		border-radius: 2px;
		max-width: 240px;
	}
	ul.compare_prd_list li img {
		float: left;
		width: 50px !important;
	}
	ul.compare_prd_list li .remove {
		float: left;
		width: 18px !important;
		color: white;
		background: black;
		padding: 13px 5px;
	}  
	
	.float-button {
		position: fixed;
		left: 0px;
		transition: all 0.2s ease-in 0s;
		z-index: 9999;
		cursor: pointer;
		float: right;
		width: auto;
	} 
		  
	</style> 
	
	<div class="float-button">
		<button id="vert" class="btn btn-primary"><i class="ec ec-compare"></i> Compare</button>
	</div>
	
	<?php
}

add_shortcode( 'yith_woocompare_model_list_button', 'yith_woocompare_model_list_button' );

function yith_woocompare_model_list()
{	 
	?>  
	
	<div id="slide-out-panel" class="slide-out-panel">
		<header class="slide-out-header">
			<div class="yith-woocompare-counter" data-type="text" >
				<a class="yith-woocompare-open" href="?action=yith-woocompare-view-table&iframe=yes">
					<span class="yith-woocompare-counter">
						<span class="yith-woocompare-icon">
							<i class="ec ec-compare"></i>
						</span>
						<span class="yith-woocompare-count"><span id="yith_compare_prd_count"></span> product in compare</span>
					</span>
				</a>
			</div>
		</header> 
		<section id="compare-section"> 
		  
		</section>
	</div>   
<?php
}

add_action('wp_footer', 'yith_woocompare_model_list', 100 );
 

function yith_woocompare_model_list_ajax() {
	$dir = plugins_url('yith-woocommerce-compare-product-list');
	$products_list = json_decode($_POST['productIds']);
	 
	$htmlTabBody = "";
	$multiArray = get_product_categories( $products_list );
	$all_cat = $multiArray['all_cat'];
	$cat_Prd = $multiArray['cat_Prd'];
	 
	$htmlTabHeader = "<ul class='nav nav-tabs' role='tablist'>";
	$x = 0;
	foreach ( $all_cat as $key=>$value ) {
		if($x == 0)
		{
			$htmlTabHeader .=  "<li role='presentation' class='active'><a data-toggle='tab' href='#$key'>$value</a></li>";
			$x++;
		}
		else{
			$htmlTabHeader .=  "<li role='presentation'><a data-toggle='tab' href='#$key'>$value</a></li>";
		} 
	}
	$htmlTabHeader .= "</ul>";
	  
	$x = 0;
	foreach ( $cat_Prd as $key=>$value ) { 
		$products_list = explode(",",$value); 
		 
		if($x == 0)
		{
			$htmlTabBody =  "<div id='$key' role='tabpanel' class='tab-pane   active'><ul class='compare_prd_list'>";
			$x++;
		}else{
			$htmlTabBody .=  "<div id='$key' role='tabpanel' class='tab-pane  '><ul class='compare_prd_list'>";
		}			
		
		foreach ( $products_list as $product_id ) {
			$product = wc_get_product( $product_id );
			if ( ! $product ) {
				continue;
			}
			$removelink = esc_url("?action=yith-woocompare-remove-product&id=$product_id");
			$permalinklink = esc_url( get_permalink( $product_id ) );
			$shop_thumbnail = wp_kses_post( $product->get_image( 'shop_thumbnail' ) );
			$get_title = esc_html( $product->get_title() ); 
			
			$htmlTabBody .= "<li>
								<a href='$removelink' data-product_id='$product_id' class='remove' title='Remove'>x</a>
								<a href='$permalinklink' class='product-info'>$shop_thumbnail<span>$get_title</span></a>
							</li>";
				  
				
		}
		$htmlTabBody .=  "</ul></div>"; 
		 
	 
	}
	echo $htmlTabHeader ."<div class='tab-content' id='tab-content'>". $htmlTabBody . "</div>";
	die();
}

add_action('wp_ajax_yith_woocompare_model_list_ajax', 'yith_woocompare_model_list_ajax');
add_action('wp_ajax_nopriv_yith_woocompare_model_list_ajax', 'yith_woocompare_model_list_ajax'); // Allow front-end submission



function get_product_categories( $product_id ) { 
	$cat = $categories = array();
	$catPrd = array();
	
	if( ! is_array( $product_id ) ) {
		$categories = get_the_terms( $product_id, 'product_cat' );
	}
	else {
		foreach( $product_id as $id ) {

			$single_cat = get_the_terms( $id, 'product_cat' );

			if( empty( $single_cat ) ) {
				continue;
			}
			// get values
			$single_values = array_values( $single_cat );
			  
			foreach( $single_values as $single_value ) {
				if( ! $single_value ) {
					continue;
				}
				
				if($catPrd[$single_value->term_id])
				{
					$catPrd[$single_value->term_id] = $catPrd[$single_value->term_id].','.$id;
				}else{
				$catPrd[$single_value->term_id] = $id;
				}
			}
				
			 
			$categories = array_merge( $categories, $single_values );
		}
	}

	if( empty( $categories ) ) {
		return $cat;
	}

	foreach( $categories as $category ) {
		if( ! $category ) {
			continue;
		}
		$cat[$category->term_id] = $category->name;
	}
	$multiArray['all_cat'] = $cat;
	$multiArray['cat_Prd'] = $catPrd;
	return $multiArray;
}