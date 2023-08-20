<?php

/* 
    Plugin Name: Are You Paying Attention Quiz
    Description: Give your readers a multiple choice question.
    Version: 1.0
    Author: Onur
    Author URI: https://www.google.com
*/

if (!defined("ABSPATH")) exit;
class AreYouPayingAttention {
    function __construct () {
        add_action("init", array ($this, "adminAssets"));
    }

    function adminAssets () {
        wp_register_script("quizeditjs", plugin_dir_url(__FILE__) . "build/index.js", array ("wp-blocks", "wp-element", "wp-editor"));
        wp_register_style("quizeditcss", plugin_dir_url(__FILE__) . "build/index.css");
        register_block_type("ourplugin/are-you-paying-attention", array (
            "editor_script" => "quizeditjs",
            "editor_style" => "quizeditcss",
            "render_callback" => array ($this, "theHTML")
        ));
    }

    function theHTML ($attributes) {
        if (!is_admin()):
            wp_enqueue_script("quizfrontendjs", plugin_dir_url(__FILE__) . "build/frontend.js", array("wp-element"), "1.0", true);
            wp_enqueue_style("quizfrontendcss", plugin_dir_url(__FILE__) . "build/frontend.css");
        endif;
        ob_start(); ?>
            <div class="paying-attention-update-me">
                <pre style="display: none;"><?php echo wp_json_encode($attributes)?></pre>
            </div>
        <?php return ob_get_clean();
    }
}

$areYouPayingAttention = new AreYouPayingAttention();