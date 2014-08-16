<?php
/**
 * @package Cornholio
 * @version 0.1
 */
/*
Plugin Name: Cornholio
Plugin URI: http://wordpress.org/extend/plugins/cornholio
Description: This is not just a plugin, it symbolizes the hope and enthusiasm of an entire generation summed up in two words sung most famously by Louis Armstrong: Hello, Dolly. When activated you will randomly see a lyric from <cite>Hello, Dolly</cite> in the upper right of your admin screen on every page.
Author: Dimage Sapelkin
Version: 0.1
Author URI: http://dimage.sharkrazor.net
License: GPL2
*/

/*  Copyright 2014  Dimage Sapelkin  (email : dsapelkin@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// defined('ABSPATH') or die("No script kiddies please!");

//define('CORNHOLIO_TYPES_ARRAY','cornholio_types');
define('CORNHOLIO','Cornholio');
define('CORNHOLIO_OPTIONS_TYPE_PREFIX','cornholio_fields_');
define('CORNHOLIO_POST_TYPE_PREFIX','cornholio_type_');
define('CORNHOLIO_TYPE_NAME', 'typename');
define('CORNHOLIO_TYPE_LABEL', 'label');
define('CORNHOLIO_TYPE_DESC', 'description');
define('CORNHOLIO_OPTION_PAGE_ID', 'corn-options');
define('CORNHOLIO_OPTION_TITLE', 'Cornholio options');
define('CORNHOLIO_OPTION_NEW_SECTION', 'cornholio_add_new');
define('CORNHOLIO_NEW_SECTION_NAME', 'cornholio_new_section_name');
define('CORNHOLIO_OPTION_NEW_NAME', 'cornholio_option_new_name');

function cornup_network_admin() {
  add_options_page( CORNHOLIO_OPTION_TITLE, CORNHOLIO_OPTION_TITLE, 'manage_options', CORNHOLIO_OPTION_PAGE_ID, 'show_corn_options' );
}

function show_corn_options() {
  $AddNew = __("Add New");
  echo "<div class='wrap'>\n" .
       "   <h2>Cornholio settings</h2>\n" .
       "   <form method='post' action='options.php'>\n";
               /* 'option_group' must match 'option_group' from register_setting call */
               settings_fields( CORNHOLIO_OPTION_NEW_SECTION );
               do_settings_sections( CORNHOLIO_OPTION_PAGE_ID );
  echo "        <p class='submit'>\n" .
       "             <input name='submit' type='submit' id='submit' class='button-primary' value='" . __("Add New") . "' />\n" .
       "        </p>\n" .
       "   </form>\n" .
       "</div>\n";
}

function cornholio_page() {
  echo "<h3>Cornholio</h3>";
  $alloptions = wp_load_alloptions();
  $numTypes = 0;
  foreach( $alloptions as $n => $v ) {
    if (0 === stripos($n, CORNHOLIO_OPTIONS_TYPE_PREFIX)) { // find every cornholio custom post type
      $uv = $v;
      if (is_serialized($v)) $uv = maybe_unserialize($v);
      $numTypes += 1;
      $post_type = getOptField($uv, CORNHOLIO_TYPE_NAME);    // take the type name
      $args = array(
        'label' => getOptField($uv, CORNHOLIO_TYPE_LABEL),
        'description' => getOptField($uv, CORNHOLIO_TYPE_DESC),

      );
      echo "<p>" . $args['label'] . " &mdash; " . $args['description'] . " &mdash; " . $post_type . "</p>";
    }
  }
  if (0 == $numTypes) echo "<p>No custom post types so far</p>";
  // display links to every post type
}

function corn_field_shortcode( $atts ) {

}

function corn_cob_shortcode( $atts, $content = null ) {

}

function cornup_admin_button() {
  add_options_page( CORNHOLIO_OPTION_TITLE, CORNHOLIO, 'manage_options', CORNHOLIO_OPTION_PAGE_ID, 'show_corn_options');
  add_menu_page( CORNHOLIO, CORNHOLIO, 'edit_posts', 'cornholio', 'cornholio_page', plugin_dir_url( __FILE__ ) . '/CornIcon.png', 21);
  // add submenus to Cornholio menu
}

function getOptField($option, $fieldname) {
  if ($option[$fieldname] !== null) return $option[$fieldname];
  else return false; //TODO: add debug log error here
}


function cornup_post_types() {
  $newType = get_option( CORNHOLIO_OPTION_NEW_NAME, null );
//  echo CORNHOLIO_OPTION_NEW_NAME . " = " . strval($newType) . "<br>";
  if ($newType != null) {
//    echo CORNHOLIO_OPTION_NEW_NAME . " = " . $newType . "<br>";
    $opt_name = CORNHOLIO_OPTIONS_TYPE_PREFIX . strtolower($newType);
    $opt_value = array(
      CORNHOLIO_TYPE_NAME => CORNHOLIO_POST_TYPE_PREFIX . strtolower($newType),
      CORNHOLIO_TYPE_LABEL => $newType,
      CORNHOLIO_TYPE_DESC => ''
    );
    add_option( $opt_name, $opt_value, '', 'yes' );
    delete_option( CORNHOLIO_NEW_SECTION_NAME );
  }

  $alloptions = wp_load_alloptions();
  foreach( $alloptions as $n => $v ) {
    if (0 === stripos($n, CORNHOLIO_OPTIONS_TYPE_PREFIX)) { // find every cornholio custom post type
      $uv = $v;
      if (is_serialized($v)) $uv = maybe_unserialize($v);
//      echo strval($n) . " = " . var_dump($uv) . "<br>";
      $post_type = $uv[CORNHOLIO_TYPE_NAME];    // take the type name
      $args = array(
        'label' => $uv[CORNHOLIO_TYPE_LABEL],
        'description' => $uv[CORNHOLIO_TYPE_DESC],
        'public' => true,
        'show_in_menu' => false,

      );
      register_post_type( $post_type, $args );
    }
  }
}

function cornholio_show_new_section() {
  echo "<p>Add new custom type</p>";
}

function cornholio_show_new_name() {
  echo "<input name='".CORNHOLIO_OPTION_NEW_NAME."' id='".CORNHOLIO_OPTION_NEW_NAME."' type='text' value='Name' class='code'/>";
}

function cornup_settings_page() {
  add_settings_section( CORNHOLIO_OPTION_NEW_SECTION, __("Add new custom type"), 'cornholio_show_new_section', CORNHOLIO_OPTION_PAGE_ID );
  add_settings_field( CORNHOLIO_NEW_SECTION_NAME, __("Name"), 'cornholio_show_new_name', CORNHOLIO_OPTION_PAGE_ID, CORNHOLIO_OPTION_NEW_SECTION );
  register_setting( CORNHOLIO_OPTION_NEW_SECTION, CORNHOLIO_OPTION_NEW_NAME, 'strval' );
}

add_action( 'init', 'cornup_post_types' );
add_action( 'admin_init', 'cornup_settings_page' );
add_action( 'network_admin_menu', 'cornup_network_admin' );
add_action( 'admin_menu', 'cornup_admin_button' );
add_shortcode( 'cornfield', 'corn_field_shortcode' );
add_shortcode( 'corncob', 'corn_cob_shortcode' );

?>
