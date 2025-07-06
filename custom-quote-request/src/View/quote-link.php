<div class="quote-link">

  <a class="<?php 
    echo ( $has_quote ) ? "quote-page" : "no-quote"; 
  ?>" href="<?php 
    echo ( $has_quote ) ? home_url( '/quote-review' ) : "#"; 
  ?>">

    <img src="<?php echo CQR_URL . '/assets/img/quote-icon.svg' ?>" alt="<?php _e( 'Quote Icon', 'custom-quote-request' ) ?>">
    <span><?php	
      echo ( $has_quote ) ? __( 'Quote review', 'custom-quote-request' ) : __( 'No products in your quote', 'custom-quote-request' ); 
    ?></span>

  </a>

</div>