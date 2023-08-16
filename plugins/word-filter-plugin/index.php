<?php
/* 
    Plugin Name: Word Filter Plugin
    Description: Our word filter plugin.
    Version: 1.0
    Author: Onur
    Author URI: https://www.google.com
    Text Domain: wcpdomain
    Domain Path: /languages
*/

class WordFilterPlugin {
    function __construct () {
        add_action("admin_menu", array ($this, "ourMenu"));
        add_action("admin_init", array ($this, "ourSettings"));
        if (get_option("plugin_words_to_filter")) add_filter("the_content", array($this, "filterLogic"));
    }
    function ourMenu () {
        $mainPageHook = add_menu_page("Words To Filter", "Word Filter", "manage_options", "ourwordfilter", array($this, "wordFilterPage"), 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAyMCAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGNsaXAtcnVsZT0iZXZlbm9kZCIgZD0iTTEwIDIwQzE1LjUyMjkgMjAgMjAgMTUuNTIyOSAyMCAxMEMyMCA0LjQ3NzE0IDE1LjUyMjkgMCAxMCAwQzQuNDc3MTQgMCAwIDQuNDc3MTQgMCAxMEMwIDE1LjUyMjkgNC40NzcxNCAyMCAxMCAyMFpNMTEuOTkgNy40NDY2NkwxMC4wNzgxIDEuNTYyNUw4LjE2NjI2IDcuNDQ2NjZIMS45NzkyOEw2Ljk4NDY1IDExLjA4MzNMNS4wNzI3NSAxNi45Njc0TDEwLjA3ODEgMTMuMzMwOEwxNS4wODM1IDE2Ljk2NzRMMTMuMTcxNiAxMS4wODMzTDE4LjE3NyA3LjQ0NjY2SDExLjk5WiIgZmlsbD0iI0ZGREY4RCIvPjwvc3ZnPg==', 100);
        add_action("load-{$mainPageHook}", array($this, "mainPageAssets"));

        add_submenu_page("ourwordfilter", "Words To Filter", "Words List", "manage_options", "ourwordfilter", array($this, "wordFilterPage"));
        add_submenu_page("ourwordfilter", "Word Filter Options", "Options", "manage_options", "word-filter-options", array ($this, "optionsSubPage"));
    }
    function mainPageAssets () {
        wp_enqueue_style("filterAdminCss", plugin_dir_url(__FILE__) . "style.css");
    }
    function handleForm () {
        if (
            isset($_POST["ourNonce"]) && 
            wp_verify_nonce($_POST["ourNonce"], "saveFilterWords") && 
            current_user_can("manage_options")
        ):
            update_option("plugin_words_to_filter", sanitize_text_field($_POST["plugin_words_to_filter"]));
        ?>
            <div class="updated">
                <p>Your filtered words were saved.</p>
            </div>
        <?php else:?>
            <div class="error">
                <p>Sorry, you do not have permission to perform that action.</p>
            </div>
    <?php endif;}
    function wordFilterPage () {?>
        <div class="wrap">
            <h1>Word Filter</h1>
            <form method="POST">
                <?php if(isset($_POST["justsubmitted"]) && $_POST["justsubmitted"] == "true") $this->handleForm()?>
                <?php wp_nonce_field('saveFilterWords', 'ourNonce')?>
                <input type="hidden" name="justsubmitted" value="true">
                <label for="plugin_words_to_filter"><p>Enter a <strong>comma-separated</strong> list of words to filter from your site's content.</p></label>
                <div class="word-filter__flex-container">
                <textarea name="plugin_words_to_filter" id="plugin_words_to_filter" placeholder="bad, mean, awful, horrible"><?php echo esc_textarea(get_option("plugin_words_to_filter"))?></textarea>
                </div>
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
            </form>
        </div>
    <?php }
    function filterLogic ($content) {
        $badWords = explode(",", get_option("plugin_words_to_filter"));
        $badWordsTrimmed = array_map('trim', $badWords);
        return str_ireplace($badWordsTrimmed, esc_html(get_option("replacementText", "***")), $content);
    }

    function ourSettings () {
        add_settings_section("replacement-text-section", null, null, "word-filter-options");

        register_setting("replacementFields", "replacementText");
        add_settings_field("replacement-text", "Filtered Text", array ($this, "replacementFieldHTML"), "word-filter-options", "replacement-text-section");
    }
    function replacementFieldHTML () {?>
        <input type="text" name="replacementText" value="<?php echo esc_attr(get_option("replacementText", "***"))?>">
        <p class="description">Leave blank to simply remove the filtered words.</p>
    <?php }
    function optionsSubPage () {?>
        <div class="wrap">
            <h1>Word Filter Options</h1>
            <form action="options.php" method="post">
                <?php settings_errors()?>
                <?php settings_fields("replacementFields")?>
                <?php do_settings_sections("word-filter-options")?>
                <?php submit_button()?>
            </form>
        </div>
    <?php }
}

$WordFilterPlugin = new WordFilterPlugin();