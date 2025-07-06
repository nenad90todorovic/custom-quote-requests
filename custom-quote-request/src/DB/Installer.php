<?php

namespace CQR\DB;

if ( !defined( 'ABSPATH' ) ) :
  exit;
endif;

class Installer {

  private static function getWpdbData() {
    
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    global $wpdb;
    $wpdb = $wpdb;

    return array(
      'wpdb'     => $wpdb,
      'collate'  => $wpdb->get_charset_collate(),
      'requests' => $wpdb->prefix . 'quote_requests',
      'items'    => $wpdb->prefix . 'quote_items',
    ); 

  }

  public static function install() {

    // get all wpdb data
    extract( Installer::getWpdbData() );

    // quote requests table
    dbDelta( "CREATE TABLE IF NOT EXISTS $requests (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      user_id BIGINT UNSIGNED NOT NULL,
      customer_name VARCHAR(255),
      phone_number VARCHAR(50),
      email VARCHAR(255),
      status VARCHAR(50) DEFAULT 'pending',
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $collate ENGINE=InnoDB;" );

    // quote items table
    dbDelta( "CREATE TABLE IF NOT EXISTS $items (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      request_id BIGINT UNSIGNED NOT NULL,
      product_id BIGINT UNSIGNED NOT NULL,
      quantity INT UNSIGNED NOT NULL,
      notes TEXT,
      FOREIGN KEY (request_id) REFERENCES $requests(id) ON DELETE CASCADE
    ) $collate ENGINE=InnoDB;" );

    // custom page & page db data for quote display view
    $quotePageId = wp_insert_post( array(
      'post_title'    => __( 'Quote Review', 'custom-quote-request' ),
      'post_content'  => '',
      'post_status'   => 'publish',
      'post_type'     => 'page',
    ) );

    if ( $quotePageId ) :
      update_post_meta( $quotePageId, '_wp_page_template', 'quote-review.php' );
      add_option( 'quote_page_id', $quotePageId );
    endif;

  }

  public static function uninstall() {

    // get all wpdb data
    extract( Installer::getWpdbData() );
    
    // drop items & requests table
    $wpdb->query( "DROP TABLE IF EXISTS $items;" );
    $wpdb->query( "DROP TABLE IF EXISTS $requests;" );

    // remove page & db data for quote display view
    $quotePageId = get_option( 'quote_page_id' );
    wp_delete_post( $quotePageId, true );
    delete_option( 'quote_page_id' );

  }

}