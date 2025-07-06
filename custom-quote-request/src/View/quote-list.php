<div class="wrap quote-list">

  <h1><?php	_e( 'Quote List', 'custom-quote-request' ) ?></h1>

  <?php // requests found 
    if ( $requests ) : ?>
    
    <form class="request-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>">
      
      <?php	wp_nonce_field( 'quote_form_action' , 'quote_form_nonce' ) ?>

      <input type="hidden" name="action" value="save_quotes" />

      <?php // quote requests
        foreach ( $requests as $request ) :

        $userId     = $request['user_id'];
        $requestId  = $request['id'];
        $quoteItems = $this->getQuoteItems( $requestId );
      ?>

        <div class="customer-quote-wrap">

          <div class="customer-details">
  
            <h2><?php	_e( 'Customer details', 'custom-quote-request' ) ?>:</h2>
            
            <p><?php _e( 'Name', 'custom-quote-request' ) ?>: <?php	echo $request['customer_name'] ?></p>
  
            <p><?php _e( 'Username', 'custom-quote-request' ) ?>: <a href="<?php
                echo esc_url( admin_url( "user-edit.php?user_id=$userId" ) )
              ?>"><?php	echo ( get_user_by( 'id', $userId ) )->user_login ?>
            </a></p>
  
            <p><?php _e( 'Phone number', 'custom-quote-request' ) ?>: <?php	echo $request['phone_number'] ?></p>
  
            <p><?php _e( 'Email', 'custom-quote-request' ) ?>: <?php echo $request['email'] ?></p>
  
            <p><?php _e( 'Quote status', 'custom-quote-request' ) ?>: 
  
              <select name="<?php echo "status_$requestId" ?>" id="<?php echo "status_$requestId" ?>">
                <?php foreach ( $statusList as $key => $status ) : ?>
                  <option value="<?php echo $key ?>" <?php 
                    selected( $key, $request['status'] ) 
                  ?>><?php echo $status ?></option>
                <?php endforeach; ?>
              </select>
              
            </p>
  
            <p><?php _e( 'Created', 'custom-quote-request' ) ?>: <?php 
              echo ( new DateTime( $request['created_at'] ) )->format( 'd.m.Y.' )
            ?></p>
  
          </div>

          <?php // quote details 
            if ( $quoteItems ) : 
          ?>
  
            <div class="quote-details">
  
              <h2><?php	_e( 'Quote products', 'custom-quote-request' ) ?>:</h2>
  
              <?php 
                foreach ( $quoteItems as $quoteItem ) : 
                $product_id = $quoteItem['product_id'];
                $product    = wc_get_product( $product_id );
              ?>
    
                <div class="quote-details-item">
    
                  <?php // product image 
                    if ( $product->get_image_id() ) : ?>
                    
                    <img src="<?php 
                      echo wp_get_attachment_image_url( $product->get_image_id() ) 
                    ?>" alt="<?php _e( 'Product Image', 'custom-quote-request' ) ?>">
    
                  <?php endif; ?>
    
                  <div class="quote-product-meta">
    
                    <h3><?php	echo $product->get_title() ?></h3>
    
                    <label><?php _e( "How many" , "custom-quote-request" ) ?>:</label>
                    <input type="number" name="<?php echo "quote_num_$product_id" ?>" 
                     value="<?php echo $quoteItem['quantity'] ?>" readonly>
                  
                  </div>
    
                  <div class="quote-product-notes">

                    <label><?php	
                      _e( 'Additional Notes', 'custom-quote-requests' )
                    ?>:</label>
                    <textarea placeholder="Nothing here..." name="<?php 
                      echo "quote_notes_$product_id" 
                    ?>" readonly><?php echo stripslashes( $quoteItem['notes'] ) ?></textarea>

                  </div>
    
                </div>
    
              <?php endforeach; ?>
  
            </div>
  
          <?php endif; ?>

        </div>

      <?php endforeach; ?>

      <?php submit_button(); ?>

    </form>

  <?php // no requests found	
    else : ?>
    <p><?php _e( 'Customers did not create any quotes so far', 'custom-quote-request' ) ?>!</p>
  <?php endif; ?>

</div>