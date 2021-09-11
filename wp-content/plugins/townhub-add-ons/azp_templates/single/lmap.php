<?php
/* add_ons_php */

//$azp_attrs,$azp_content,$azp_element
$azp_mID = $el_id = $el_class = $title = $hide_widget_on = '';
// var_dump($azp_attrs);
extract($azp_attrs);

$classes = array(
	'azp_element',
    'lmap-single',
    'azp-element-' . $azp_mID,  
    $el_class,
);
// $animation_data = self::buildAnimation($azp_attrs);
// $classes[] = $animation_data['trigger'];
// $classes[] = self::buildTypography($azp_attrs);//will return custom class for the element without dot
// $azplgallerystyle = self::buildStyle($azp_attrs);

$classes = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $classes ) ) ); 

if($el_id!=''){
    $el_id = 'id="'.$el_id.'"';
}
if(( $hide_widget_on_check = townhub_addons_is_hide_on_plans($hide_widget_on) ) !== 'true') :
$address = get_post_meta( get_the_ID(), ESB_META_PREFIX.'address', true );
$latitude = get_post_meta( get_the_ID(), ESB_META_PREFIX.'latitude', true );
$longitude = get_post_meta( get_the_ID(), ESB_META_PREFIX.'longitude', true );
if( !empty($latitude) && !empty($longitude) ) : 
?>
<div class="<?php echo $classes; ?> authplan-hide-<?php echo $hide_widget_on_check;?>" <?php echo $el_id;?>> 
    <div class="for-hide-on-author"></div>
    <!-- lsingle-block-box --> 
    <div class="lsingle-block-box lmap-box">
        <?php if($title != ''): ?>
        <div class="lsingle-block-title">
            <h3><?php echo $title; ?></h3>
        </div>
        <?php endif; ?>
        <div class="lsingle-block-content">
            <div class="map-container">

                <div id="<?php echo uniqid('singleMap'); ?>" 
                    class="singleMap singleMap-<?php echo esc_attr( townhub_addons_get_option('map_provider') );?>" 
                    data-lat="<?php echo esc_attr( $latitude );?>" 
                    data-lng="<?php echo esc_attr( $longitude );?>" 
                    data-loc="<?php echo esc_attr( $address );?>" 
                    data-zoom="<?php echo townhub_addons_get_option('gmap_single_zoom');?>"  
                    data-marker="<?php echo esc_url( townhub_addons_get_attachment_thumb_link( townhub_addons_get_listing_marker( get_the_ID() ) ) ); ?>"></div>


                
            </div>

        </div><!-- lsingle-block-content end -->  
    </div><!-- lsingle-block-box end -->  
</div>
<?php endif; 

endif;// check hide on plans