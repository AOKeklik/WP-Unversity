<?php
/* 
    Plugin Name: Word Count And Time Plugin
    Description: Word count and time plugin.
    Version: 1.0
    Author: Onur
    Author URI: https://www.google.com
    Text Domain: wcpdomain
    Domain Path: /languages
*/

class WordCountAndTimePlugin {
    function __construct () {
        add_action("admin_menu", array ($this, "adminPage"));
        add_action("admin_init", array ($this, "settings"));
        add_filter("the_content", array ($this, "ifWrap"));
        add_action("init", array ($this, "languages"));
    }
    function languages () {
        load_plugin_textdomain("wcpdomain", false, dirname(plugin_basename(__FILE__)) . "/languages");
    }
    function ifWrap ($content) {
        if (is_main_query() && is_single() && (
            get_option("wcp_wordcount", "1") || 
            get_option("wcp_charactercount", "1") ||
            get_option("wcp_readtime", "1")
        )):
            return $this->createHTML($content);
        endif;
        return $content;
    }
    function createHTML ($content) {
        $con = strip_tags($content);
        $html = "<h3>" . esc_html(get_option("wcp_headline", 'Post Statistics')) . "</h3><p>";

        if (get_option("wcp_wordcount", "1") || get_option("wcp_readtime", 1)):
            $wordCound = str_word_count($con);
            $readTime = round($wordCound / 225);
        endif;

        if (get_option("wcp_wordcount", "1")):
            $html .= esc_html__("This post has", "wcpdomain") . " <strong>" . $wordCound . "</strong> " . esc_html__("words", "wcpdomain") . "<br />";
        endif;

        if (get_option("wcp_charactercount", "1")):
            $html .= esc_html__("This post has", "wcpdomain") . " <strong>" . strlen($con) . "</strong> " . esc_html__("characters", "wcpdomain") . "<br />";
        endif;

        if (get_option("wcp_readtime", "1")):
            $isLong = ($readTime > 1) ? "minutes" : "minute";
            $html .= esc_html__("This post will take about", "wcpdomain") . " <strong>" . $readTime . "</strong> " . $isLong . " " . esc_html__("to read.", "wcpdomain");
        endif;

        $html .= "</p>";

        if (get_option("wcp_location", "0") == "1"):
            return $content . $html;
        endif;

        return $html . $content;
    }
    function adminPage () {
        add_options_page("Word Count Settings", "Word Count", "manage_options", "word-count-settings-page", array ($this, "ourHTML"));
    }
    function settings () {
        add_settings_section("wcp_first_section", null, null, "word-count-settings-page"); 
        // 1 lacation of plugin
        add_settings_field("wcp_location", "Display Location", array ($this, "locationHTML"), "word-count-settings-page", "wcp_first_section");
        register_setting("wordcountplugin", "wcp_location", array ("sanitize_callback" => array ($this, "sanitizeLocation"), "default" => 0));
        // 2 headline of plugin
        add_settings_field("wcp_headline", "Headline Text", array ($this, "headlineHTML"), "word-count-settings-page", "wcp_first_section");
        register_setting("wordcountplugin", "wcp_headline", array ("sanitize_callback" => "sanitize_text_field", "default" => "Post Statistics"));
        // 3 checkboxes
        add_settings_field("wcp_wordcount", "Word Count", array ($this, "checkboxHTML"), "word-count-settings-page", "wcp_first_section", array ("theName" => "wcp_wordcount"));
        register_setting("wordcountplugin", "wcp_wordcount", array ("sanitize_callback" => "sanitize_text_field", "default" => 1));
        add_settings_field("wcp_charactercount", "Character Count", array ($this, "checkboxHTML"), "word-count-settings-page", "wcp_first_section", array ("theName" => "wcp_charactercount"));
        register_setting("wordcountplugin", "wcp_charactercount", array ("sanitize_callback" => "sanitize_text_field", "default" => 1));
        add_settings_field("wcp_readtime", "Read Time", array ($this, "checkboxHTML"), "word-count-settings-page", "wcp_first_section", array ("theName" => "wcp_readtime"));
        register_setting("wordcountplugin", "wcp_readtime", array ("sanitize_callback" => "sanitize_text_field", "default" => 1));
    }
    // 1 lacation of plugin
    function locationHTML () {?>
        <select name="wcp_location">
            <option value="0" <?php selected(get_option("wcp_location"), "0") ?>>Beginning of post</option>
            <option value="1" <?php selected(get_option("wcp_location"), "1")?>>End of post</option>
        </select>
    <?php }
    // 2 headline of plugin
    function headlineHTML () {?>
        <input type="text" name="wcp_headline" value="<?php echo esc_attr(get_option("wcp_headline"))?>" />
    <?php }
    // 3 checkboxes
    function checkboxHTML ($args) {?>
        <input type="checkbox" name="<?php echo $args["theName"]?>" value="1" <?php checked(get_option($args["theName"]), "1")?> />
    <?php }
    function sanitizeLocation ($input) {
        if ($input != "0" and $input != "1"):
            add_settings_error("wcp_location", "wcp_location_error", "Display location must be either beginning or end.");
            return get_option("wcp_location");
        endif;
        return $input;
    }
    function ourHTML () {?>
        <div class="wrap">
            <h1>Word Count Settings</h1>
            <form action="options.php" method="post">
                <?php
                    settings_fields("wordcountplugin");
                    do_settings_sections("word-count-settings-page");
                    submit_button();
                ?>
            </form>
        </div>
    <?php }
}

new WordCountAndTimePlugin();