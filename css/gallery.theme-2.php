<?php
  
  $absolute_path = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
  $wp_load = $absolute_path[0] . 'wp-load.php';
  require_once($wp_load);

  $css_options = get_option('widget_vertusdl_testimonials_widget');
  $widget_height = $css_options[2]['widget_height'];

  header('Content-type: text/css');
  header('Cache-control: must-revalidate');

?>

/* line 8, ../sass/_variables.scss */
.gallery .control-button {
  color: #ccc;
  color: rgba(255, 255, 255, 0.4); }

/* line 9, ../sass/_variables.scss */
.gallery .control-button:hover {
  color: white;
  color: rgba(255, 255, 255, 0.8); }

/*
  Theme controls how everything looks in Gallery CSS.
*/
/* line 7, ../sass/gallery.theme.scss */
.gallery {
  position: relative; }
  /* line 10, ../sass/gallery.theme.scss */
  .gallery .item {
    height: 400px;
    overflow: hidden;
    text-align: center;
    background: #4d87e2; }
  /* line 11, ../sass/gallery.theme.scss */
  .gallery .controls {
    position: absolute;
    bottom: 0;
    width: 100%;
    text-align: center; }
  /* line 13, ../sass/gallery.theme.scss */
  .gallery .control-button {
    display: inline-block;
    margin: 0 .02em;
    font-size: 3em;
    text-align: center;
    text-decoration: none;
    -webkit-transition: color .1s;
    transition: color .1s; }
