<?php
// define('AUTH_KEY',         '');
// define('SECURE_AUTH_KEY',  '');
// define('LOGGED_IN_KEY',    '');
// define('NONCE_KEY',        '');
// define('AUTH_SALT',        '');
// define('SECURE_AUTH_SALT', '');
// define('LOGGED_IN_SALT',   '');
// define('NONCE_SALT',       '');
?>

<?php
/* inc */
require get_theme_file_path("./inc/like-route.php");
require get_theme_file_path("./inc/search-route.php");
/* template */
function pageBanner ($args = NULL) {
    if (!isset($args['title'])) $args['title'] = get_the_title(); 
    if (!isset($args['subtitle'])) $args['subtitle'] = get_field("banner_title");
    if (!isset($args['img'])):
        if (get_field("banner_image") and !is_archive() and !is_home()) $args['img'] = get_field("banner_image")["sizes"]["pageBanner"];
        else $args['img'] = get_theme_file_uri("/images/ocean.jpg");
    endif;

    ?>
        <div class="page-banner">
            <div class="page-banner__bg-image" style="background-image: url('<?php echo $args['img']?>');"></div>
            <div class="page-banner__content container container--narrow">
                <h1 class="page-banner__title"><?php echo $args['title']?></h1>
                <div class="page-banner__intro">
                    <p><?php echo $args['subtitle']?></p></p>
                </div>
            </div>  
        </div>
    <?php
}
/* template */
add_action("rest_api_init", "university_custom_rest");
function university_custom_rest () {
    register_rest_field("post", "author_name", array (
        "get_callback" => function () {
            return get_the_author();
        }
    ));
    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() {return count_user_posts(get_current_user_id(), 'note');}
      ));
}

add_action("wp_enqueue_scripts", "university_files");
function university_files () {
    wp_enqueue_script("main-university-js", get_theme_file_uri("/build/index.js"), array("jquery"), "1.0", true);
    wp_enqueue_style("custom-google-fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
    wp_enqueue_style("font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
    wp_enqueue_style("university_main_styles", get_theme_file_uri("/build/style-index.css"));
    wp_enqueue_style("university_extra_styles", get_theme_file_uri("/build/index.css"));

    wp_localize_script("main-university-js", "universityData", array (
        "root_url" => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}

add_action("after_setup_theme", "university_features");
function university_features () {
    add_theme_support("title-tag");
    add_theme_support("post-thumbnails");
    add_image_size("professorLandscape", 400, 260, true);
    add_image_size("professorPortrait", 480, 650, true);
    add_image_size("pageBanner", 1500, 350, true);
}

add_action("pre_get_posts", "university_adjust_queries");
function university_adjust_queries ($query) {
    // campus
    if (!is_admin() and is_post_type_archive("campus") and $query->is_main_query()):
        $query->set("orderby", "title");
        $query->set("order", "asc");
        $query->set("posts_per_page", -1);
    endif;
    // program
    if (!is_admin() and is_post_type_archive("program") and $query->is_main_query()):
        $query->set("orderby", "title");
        $query->set("order", "asc");
        $query->set("posts_per_page", -1);
    endif;
    // event
    if (!is_admin() && is_post_type_archive("event") && $query->is_main_query()):
        $today = date("Ymd");
        $query->set("meta_key", "event_date");
        $query->set("orderby", "meta_value_num");
        $query->set("order", "DESC");
        $query->set("meta_query", array (
            "key" => "event_date",
            "compare" => ">=",
            "value" => $today,
            "type" => "numeric"
        ));
    endif;
}

// user login
add_action("admin_init", "redirectSubsToFrontend");
function redirectSubsToFrontend () {
    $currentUser = wp_get_current_user();
    if (count($currentUser->roles) == 1 && $currentUser->roles[0] == "subscriber"):
        wp_redirect(site_url("/"));
        exit;
    endif;
}
add_action("wp_loaded", "noSubsAdminBar");
function noSubsAdminBar () {
    $currentUser = wp_get_current_user();
    if (count($currentUser->roles) == 1 && $currentUser->roles[0] == "subscriber"):
        show_admin_bar(false);
    endif;
}
add_action("login_headerurl", "ourHeaderUrl");
function ourHeaderUrl () {
    return esc_url(site_url("/"));
}
add_action("login_enqueue_scripts", "ourLoginCSS");
function ourLoginCSS () {
    wp_enqueue_style("custom-google-fonts", "//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i");
    wp_enqueue_style("font-awesome", "//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css");
    wp_enqueue_style("university_main_styles", get_theme_file_uri("/build/style-index.css"));
    wp_enqueue_style("university_extra_styles", get_theme_file_uri("/build/index.css"));
}
add_action("login_headertitle", "ourLoginTitle");
function ourLoginTitle () {
    return get_bloginfo("name");
}
add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2);
function makeNotePrivate($data, $postarr) {
  if ($data['post_type'] == 'note') {
    if(count_user_posts(get_current_user_id(), 'note') > 4 AND !$postarr['ID']) {
      die("You have reached your note limit.");
    }

    $data['post_content'] = sanitize_textarea_field($data['post_content']);
    $data['post_title'] = sanitize_text_field($data['post_title']);
  }

  if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
    $data['post_status'] = "private"; 
  }
  
  return $data;
}



// ignore files
add_filter("ai1wm_exclude_themes_from_export", "ignoreCurtainFiles");
function ignoreCurtainFiles ($exclude_filters) {
    $exclude_filters[] = "fictionalUniversity-theme/node_modules";
    return $exclude_filters;
}