<?php
/* add_ons_php */

//$azp_attrs,$azp_content,$azp_element
$azp_mID = $el_id = $el_class = $show_reviews = $hide_widget_on = ''; 

// var_dump($azp_attrs);
extract($azp_attrs);

$classes = array(
	'azp_element',
    'lcomments',
    'azp-element-' . $azp_mID,
    $el_class,
);
// $animation_data = self::buildAnimation($azp_attrs);
// $classes[] = $animation_data['trigger'];
// $classes[] = self::buildTypography($azp_attrs);//will return custom class for the element without dot
// $azplgallerystyle = self::buildStyle($azp_attrs);

$classes = preg_replace( '/\s+/', ' ', implode( ' ', array_filter( $classes ) ) );

if($el_id != ''){
    $el_id = 'id="'.$el_id.'"';
}
if(( $hide_widget_on_check = townhub_addons_is_hide_on_plans($hide_widget_on) ) !== 'true') :
?>
<div class="<?php echo $classes; ?> authplan-hide-<?php echo $hide_widget_on_check;?>" <?php echo $el_id;?>>
    <div class="for-hide-on-author"></div>
    <?php echo do_shortcode( '[cth_lreviews]' ); ?> 
</div>
<?php 
endif;// check hide on plans