<div class="quote-btn-wrap<?php echo $isAddedProduct ? ' added-product' : null; ?>">
  <button data-quote-item="<?php echo $productId ?>"><?php	
    echo apply_filters( 'quote_btn_txt', $quoteBtnTxt ) 
  ?></button>
</div>