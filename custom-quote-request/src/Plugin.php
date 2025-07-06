<?php

namespace CQR;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

use CQR\Frontend\General as GeneralFront;
use CQR\Admin\General as GeneralAdmin;

class Plugin {
	
	public function __construct( public string $plugin_name ) {

		$textDomain = get_plugin_data( dirname( __FILE__ ) )['TextDomain'] ?? "custom-quote-requests";
		
		// constants
		define( 'CQR_DIR', WP_PLUGIN_DIR . "/$textDomain/"  );
		define( 'CQR_URL', WP_PLUGIN_URL . "/$textDomain/" );
		define( 'CURRENT_USER_ID', get_current_user_id() );		
		
		// custom template(s) on init
		add_filter( 'theme_page_templates', array( $this, 'addQuoteTemplate' ) );
		add_filter( 'page_template', array( $this, 'setCustomTemplate' ) );

		// redirects
		add_action( 'wp', array( $this, 'redirects' ) );

		// link to the Quote Review in wp-admin
    add_filter( 'plugin_action_links_' . $this->plugin_name, array( $this, 'reviewLink' ) );

	}

	public function addQuoteTemplate( $templates ) {
    $templates['quote-review.php'] = 'Quote Review';
    return $templates;
	}

	public function setCustomTemplate( $template )	{
		if ( is_page_template( 'quote-review.php' ) ) :
    	$template = CQR_DIR . 'src/Templates/quote-review.php';			
		endif;

		return $template;
	}

	public function reviewLink( $links ) {

		ob_start();

		?>
			<a href="<?php echo esc_url( get_admin_url( null, 'admin.php?page=quote-list' ) ) ?>"><?php	
				_e( 'Quote List', 'custom-quote-request' ) ?></a>
		<?php

		$links[] = ob_get_clean();
		return $links;

	}

	public function redirects() {

		// if not quote review page, pass
		if ( !is_page_template( 'quote-review.php' ) ) :
			return;
		endif;

		// if no quotes or not logged in, go to shop
		if ( !is_user_logged_in() || !get_transient( "quote_" . CURRENT_USER_ID ) ) :
			wp_safe_redirect( home_url() . '/shop' );
			die;
		endif;

	}

	public function init() {

		// trigger the general logic
		( new GeneralAdmin )->init();
		( new GeneralFront )->init();
		
	}
	
}
