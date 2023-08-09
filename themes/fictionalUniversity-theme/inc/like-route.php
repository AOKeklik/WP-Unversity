<?php

add_action('rest_api_init', 'universityLikeRoutes');
function universityLikeRoutes() {
  register_rest_route('university/v3', 'manageLike', array(
    'methods' => 'POST',
    'callback' => 'createLike'
  ));

  register_rest_route('university/v3', 'manageLike', array(
    'methods' => 'DELETE',
    'callback' => 'deleteLike'
  ));
}

function createLike ($data) {
    if (is_user_logged_in()):
        $professorId = sanitize_text_field($data["professorId"]);
        $existLike = new WP_Query(array (
            "posts_per_page" => -1,
            "author" => get_current_user_id(),
            "post_type" => "like",
            "meta_query" => array(array(
                "key" => "liked_proffessor_id",
                "compare" => "=",
                "value" => $professorId
            ))
        ));
        if( $existLike->found_posts == 0 && get_post_type($professorId) == "professor"):
            return wp_insert_post(array (
                "post_type" => "like",
                "post_status" => "publish",
                "post_title" => "2nd PhP Test",
                "meta_input" => array (
                    "liked_proffessor_id" => $professorId
                )
            ));
        else:
            die("Invalid professor id!!");
        endif;
    else:
        die("Only logged in users can create a like.");
    endif;
}
function deleteLike ($data) {
    $likedId = sanitize_text_field($data["likedId"]);
    if (get_current_user_id() == get_post_field("post_author", $likedId) && get_post_type($likedId) == "like"):
        wp_delete_post($likedId, true);
        return "Value Deleted Successfuly!";
    else:
        die("You do not have permission to delete that.");
    endif;
}

?>