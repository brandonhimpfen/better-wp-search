<?php
/*                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              /*
 * Plugin Name:       Better WP Search
 * Plugin URI:        http://www.himpfen.com/better-wp-search/
 * Description:       Better WP Search improves the default WordPress search functionality.
 * Version:           1.0.0
 * Author:            Brandon Himpfen
 * Author URI:        http://www.himpfen.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If there is only one search result, redirect to that post
function bws_redirect_single_search() {
  if (is_search() && is_main_query()) {
      global $wp_query;
      if ($wp_query->post_count == 1 && $wp_query->max_num_pages == 1) {
          wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
          exit;
      }
  }
}
add_action('template_redirect', 'bws_redirect_single_search' );

// Nice Search
// Replaes /?s=query searches to /search/query and converts %20 to +
// Originally created by Mark Jaquith
// Plugin URI: https://wordpress.org/plugins/nice-search/
function bws_nice_search() {
	global $wp_rewrite;
	if ( !isset( $wp_rewrite ) || !is_object( $wp_rewrite ) || !$wp_rewrite->using_permalinks() )
		return;

	$search_base = $wp_rewrite->search_base;
	if ( is_search() && !is_admin() && strpos( $_SERVER['REQUEST_URI'], "/{$search_base}/" ) === false ) {
		wp_redirect( home_url( "/{$search_base}/" . urlencode( get_query_var( 's' ) ) ) );
		exit();
	}
}

add_action( 'template_redirect', 'bws_nice_search' );

// Hotfix for http://core.trac.wordpress.org/ticket/13961 for WP versions less than 3.5
if ( version_compare( $wp_version, '3.5', '<=' ) ) {
	function bws_nice_search_urldecode_hotfix( $q ) {
		if ( $q->get( 's' ) && empty( $_GET['s'] ) && is_main_query() )
			$q->set( 's', urldecode( $q->get( 's' ) ) );
	}
	add_action( 'pre_get_posts', 'bws_nice_search_urldecode_hotfix' );
}
