<?php

if( ! defined( 'ABSPATH' ) ){
	exit('restricted access');
}

function nexproperty_autoload( $classname ) {
	$namespace_parts = explode('\\', $classname);

	$namespace_parts[0] = 'includes';

	$directory_parts = array_map( function( $part ) {
		return strtolower( str_replace( '_' , '-', $part) );
	}, $namespace_parts );
	
	$file_path = join( DIRECTORY_SEPARATOR, $directory_parts ) . '.php';
	if( locate_template( $file_path ) ) {
		locate_template( $file_path, true, false );
	} 
}

spl_autoload_register( 'nexproperty_autoload' );

locate_template( 'includes/init.php', true, true );