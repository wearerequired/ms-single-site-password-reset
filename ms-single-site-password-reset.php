<?php
/**
 * Plugin Name: Multisite Single Site Password Reset
 * Plugin URI:  https://github.com/wearerequired/ms-single-site-password-reset/
 * Description: Allows to reset the password on the current site instead of the main site.
 * Version:     1.0.0
 * Author:      required
 * Author URI:  https://required.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Network:     true
 * Text Domain: ms-single-site-password-reset
 * Domain Path: /languages
 *
 * Copyright (c) 2017 required (email: info@required.ch)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @package MsSingleSitePasswordReset
 */

namespace Required\MsSingleSitePasswordReset;

/**
 * Sets the lost password URL for the current site.
 *
 * @since 1.0.0
 *
 * @see wp_lostpassword_url()
 *
 * @param string $lost_password_url The lost password page URL.
 * @param string $redirect         The path to redirect to on login.
 * @return string The lost password page URL for the current site.
 */
function set_lost_password_url( $lost_password_url, $redirect ) {
	$args = [ 'action' => 'lostpassword' ];

	if ( ! empty( $redirect ) ) {
		$args['redirect_to'] = $redirect;
	}

	$lost_password_url = add_query_arg( $args, site_url( 'wp-login.php', 'login' ) );

	return $lost_password_url;
}
add_filter( 'lostpassword_url', __NAMESPACE__ . '\set_lost_password_url', 10, 2 );

/**
 * Filters the lost/reset password URLs in the network site URL.
 *
 * @since 1.0.0
 *
 * @see wp-login.php
 *
 * @param string      $url    The complete network site URL including scheme and path.
 * @param string      $path   Path relative to the network site URL. Blank string if
 *                            no path is specified.
 * @param string|null $scheme Scheme to give the URL context.
 * @return string Site URL link with optional path appended.
 */
function force_lost_password_url_in_network_site_url( $url, $path, $scheme ) {
	if ( 'login' === $scheme || 'login_post' === $scheme ) {
		return site_url( $path, $scheme );
	}

	return $url;
}
add_filter( 'network_site_url', __NAMESPACE__ . '\force_lost_password_url_in_network_site_url', 10, 3 );

/**
 * Filters the subject of the password reset email to use the name
 * of the current site
 *
 * @since 1.0.0
 *
 * @see retrieve_password()
 *
 * @param string $title Default email title.
 * @return string Filtered title.
 */
function replace_blogname_in_retrieve_password_subject( $title ) {
	$network_name = get_network()->site_name;
	$site_name    = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	return str_replace( $network_name, $site_name, $title );
}
add_filter( 'retrieve_password_title', __NAMESPACE__ . '\replace_blogname_in_retrieve_password_subject' );
