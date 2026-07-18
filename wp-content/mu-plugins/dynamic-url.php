<?php
/**
 * Make WordPress URLs follow the incoming Host (works behind any domain / tunnel).
 * Loaded as a must-use plugin so it runs before the theme and overrides siteurl/home.
 */
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$proto = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' )
    || ( $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '' ) === 'https'
    ? 'https' : 'http';
$url = $proto . '://' . $host;

add_filter( 'pre_option_home',    function () use ( $url ) { return $url; } );
add_filter( 'pre_option_siteurl', function () use ( $url ) { return $url; } );

// Rewrite uploaded-media (attachment) URLs so self-hosted files resolve behind the tunnel too.
add_filter( 'wp_get_attachment_url', function ( $src ) use ( $url ) {
    $home = home_url();
    if ( $home === $url || ! $src ) { return $src; }
    return str_replace( $home, $url, $src );
} );
add_filter( 'upload_dir', function ( $dirs ) use ( $url ) {
    $home = home_url();
    if ( $home !== $url ) {
        $dirs['baseurl'] = str_replace( $home, $url, $dirs['baseurl'] );
        $dirs['url']     = str_replace( $home, $url, $dirs['url'] );
    }
    return $dirs;
} );
