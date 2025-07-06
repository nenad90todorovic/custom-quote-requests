<?php

namespace CQR\Controller;

use CQR\Model\Quote;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

class QuoteFormController {

  public function __construct() {
    add_action( 'wp_ajax_submit_quote_data', array( new Quote, 'submitQuoteData' ) );
  }
  
}
