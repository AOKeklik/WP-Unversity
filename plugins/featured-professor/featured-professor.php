<?php
/* 
    Plugin Name: Featured Professor Block Type
    Version: 1.0
    Author: Onur
    Author Uri: https://www.google.com
    Text Domain: featured-professor
    Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . "inc/generateProfessorHTML.php";
require_once plugin_dir_path(__FILE__) . "inc/relatedPostsHTML.php";

class FeaturedProfessor {
    function __construct () {
        add_action("init", [$this, "onInit"]);
        add_action("rest_api_init", [$this, "profHTML"]);
        add_filter("the_content", [$this, "addRelatedPosts"]);
    }

    function onInit () {
        wp_register_script("featuredProfessorScript", plugin_dir_url(__FILE__) . "build/index.js", ["wp-blocks", "wp-editor", "wp-i18n"]);
        wp_register_style("featuredProfessorStyle", plugin_dir_url(__FILE__) . "build/index.css");
        register_block_type("ourplugin/featured-professor", [
            "editor_script" => "featuredProfessorScript",
            "editor_style" => "featuredProfessorStyle",
            "render_callback" => [$this, "renderCallback"]
        ]);
        wp_localize_script("featuredProfessorScript", "featuredProfessorData", [
            "root_url" => get_Site_url()
        ]);
        register_meta("post", "featuredprofessor", [
            "show_in_rest" => true,
            "type" => "number",
            "single" => false
        ]);

        load_plugin_textdomain("featured-professor", false, dirname(plugin_basename(__FILE__)) . "/languages");
        wp_set_script_translations("featuredProfessorScript", "featured-professor", plugin_dir_path(__FILE__) . "/languages");
    }
    function renderCallback ($attributes) {
        if ($attributes["profId"]):
            wp_enqueue_style("featuredProfessorStyle");
            return generateProfessorHTML($attributes["profId"]);
        endif;
            return null;
    }

    function profHTML () {
        register_rest_route("featuredProfessor/v3", "getHTML", [
            "method" => WP_REST_SERVER::READABLE,
            "callback" => [$this, "getProfHTML"]
        ]);
    }
    function getProfHTML ($data) {
        return generateProfessorHTML($data["profId"]);
    }

    function addRelatedPosts ($content) {
        if (is_singular("professor") && in_the_loop() && is_main_query()):
            return $content . relatedPostsHTML(get_the_ID());
        endif;
        return $content;
    }
}

$featuredProfessor = new FeaturedProfessor();