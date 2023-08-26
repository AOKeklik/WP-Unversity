<?php

function relatedPostsHTML ($id) {
    $relatedPosts = new WP_Query([
        "posts_per_page" => -1,
        "post_type" => "post",
        "meta_query" => [
            [
                "key" => "featuredprofessor",
                "compare" => "=",
                "value" => $id
            ]
        ]
    ]);
    ob_start();
    if ($relatedPosts->found_posts):
        ?>
            <p><?php the_title()?> is mentioned in the following posts:</p>
            <ul>
                <?php
                    while ($relatedPosts->have_posts()):
                        $relatedPosts->the_post();
                        ?>
                            <li><a href="<?php the_permalink()?>"><?php the_title()?></a></li>
                        <?php
                    endwhile;
                ?>
            </ul>
        <?php
    endif;
    wp_reset_query();
    return ob_get_clean();
}