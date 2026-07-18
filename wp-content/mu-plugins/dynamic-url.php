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
