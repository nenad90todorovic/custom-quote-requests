<?php /* Template Name: Quote Review */ 
  $quotes = get_transient( "quote_" . CURRENT_USER_ID ) ?? null;
?>

<?php	get_header() ?>

<section class="quote-details">

  <h1><?php	_e( 'Quote review', 'custom-quote-request' ) ?></h1>

  <h2><?php	_e( 'Products', 'custom-quote-request' ) ?></h2>

  <form class="quote-form">

    <?php 
      foreach ( $quotes as $quoteItem ) :
      $product = wc_get_product( $quoteItem );
    ?>
      
      <section class="quote-details-item">

        <?php // product image 
          if ( $product->get_image_id() ) : ?>
          
          <img src="<?php 
            echo wp_get_attachment_image_url( $product->get_image_id() ) 
          ?>" alt="<?php _e( 'Product Image', 'custom-quote-request' ) ?>">

        <?php endif; ?>

        <div class="quote-product-meta">

          <h3><?php	echo $product->get_title() ?></h3>

          <label for="<?php echo "quote_num_$quoteItem" ?>"><?php _e( "How many" , "custom-quote-request" ) ?>:</label>
          
          <input type="number" name="<?php echo "quote_num_$quoteItem" ?>" id="<?php echo "quote_num_$quoteItem" ?>" min="1" max="100" value="1">
        
        </div>

        <div class="quote-product-notes">
          <textarea placeholder="Additional notes..." name="<?php 
            echo "quote_notes_$quoteItem" 
          ?>" id="<?php 
            echo "quote_notes_$quoteItem" 
          ?>"></textarea>
        </div>

        <div class="quote-product-close">
          <span>x</span>
        </div>

      </section>
  
    <?php endforeach; ?>

    <aside class="quote-contact">

      <h2><?php	_e( 'Contact', 'custom-quote-request' ) ?></h2>

      <div class="form-field">
        <input required type="text" name="name" id="name" placeholder="<?php _e( 'Full name*', 'custom-quote-request' ) ?>">
      </div>

      <div class="form-field">
        <input required type="text" name="phone" id="phone" placeholder="<?php _e( 'Phone number*', 'custom-quote-request' ) ?>">
      </div>

      <div class="form-field">
        <input required type="email" name="email" id="email" placeholder="<?php _e( 'Email*', 'custom-quote-request' ) ?>">
      </div>

    </aside>

    <div class="quote-cta">
      <a href="<?php echo home_url() . '/shop' ?>"><?php _e( 'Back to shop', 'custom-quote-request' ) ?></a>
      <button type="submit"><?php	_e( 'Submit', 'custom-quote-request' ) ?></button>
    </div>

  </form>

</section>

<?php	get_footer() ?>
