<?php
// phpcs:ignoreFile

$languages_directory = sprintf( '%s/node_modules/highlight.js/src/languages', dirname( __DIR__ ) );
if ( ! file_exists( $languages_directory ) ) {
	echo "Please first npm install\n";
	exit( 1 );
}

$output_file = dirname( __DIR__ ) . '/src/language-names.js';

$language_names = [];

$language_files = glob( $languages_directory . '/*.js' );
sort( $language_files );
foreach ( $language_files as $language_file ) {
	$slug = basename( $language_file, '.js' );

	if ( ! preg_match( '#^\s*/\*\s*Language:\s*(.+?)\n#s', file_get_contents( $language_file ), $matches ) ) {
		echo "Unable to parse Language from $language_file\n";
		exit( 2 );
	}

	$language_names[ $slug ] = $matches[1];
}

$js  =  sprintf( "// File generated by %s. Do not edit directly.\n", basename( __DIR__ ) . '/' . basename( __FILE__ ) );
$js .= "const { __ } = wp.i18n;\n";
$js .= "\n";
$js .= "/* eslint quotes: 0, quote-props: 0 */\n";
$js .= "const LANGUAGE_NAMES = {\n";

foreach ( $language_names as $slug => $name ) {
	$js .= sprintf( "\t%s: __( %s, 'syntax-highlighting-code-block' ),\n", json_encode( $slug ), json_encode( $name, JSON_UNESCAPED_UNICODE ) );
}
$js .= "};\n";
$js .= "\n";
$js .= "export default LANGUAGE_NAMES;";

if ( ! file_put_contents( $output_file, $js ) )  {
	echo "Unable to write to $output_file\n";
	exit( 1 );
}

echo "Done. Wrote to $output_file.\n";
