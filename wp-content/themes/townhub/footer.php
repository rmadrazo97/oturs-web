<?php
/* banner-php */
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 */

                do_action( 'townhub_footer_before');
?>
                </div>
                <!-- Content end -->

            


            </div>
            <!-- wrapper end -->

            <?php 
            $head_carts = townhub_get_header_cart_link();
            if (  !empty($head_carts) ) {
            ?>
            <!--cart  --> 
            <div class="show-cart color2-bg"><i class="far fa-shopping-cart"></i><span class="cart-count"><?php echo esc_html($head_carts['count'] );?></span></div>
            <div class="cart-overlay"></div>
            <div class="cart-modal">
                <div class="cart-modal-wrap fl-wrap">
                    <span class="close-cart color2-bg"><?php esc_html_e( 'Close ', 'townhub' ); ?><i class="fal fa-times"></i> </span>
                    <h3><?php esc_html_e( 'Your cart', 'townhub' ); ?></h3>
                    <div class="widget_shopping_cart_content"></div>
                    
                </div>
            </div>
            <!--cart end-->  
            <?php 
            }
            ?>
            
            <?php do_action( 'townhub_footer' ); ?>
            
            <?php if( false == townhub_get_option('hide_totop') ): ?>
            <a class="to-top"><i class="fas fa-caret-up"></i></a>
            <?php endif; ?>

            
        </div>
        <!-- Main end -->
        <?php wp_footer(); ?>

<script>
jQuery(document).ready(function($) {
  $('a[data-lantext="En"]').click(function(e){
	  console.log('en');
	 doGTranslate('es|en');
	 e.preventDefault();
	  
  });
	
  $('a[data-lantext="Es"]').click(function(e){
	  console.log('es');
	 doGTranslate('es|es'); 
	 e.preventDefault();
  });
	
	$('.menu-item-gtranslate').remove();
})
</script>
    </body>
</html>
