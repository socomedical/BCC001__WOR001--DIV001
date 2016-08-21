<?php 

function elegant_enqueue_css() { wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); }

add_action( 'wp_enqueue_scripts', 'elegant_enqueue_css' );

include('editor/footer-editor.php');

include('editor/login-editor.php');

include('components/settings__migrate.php');

?>