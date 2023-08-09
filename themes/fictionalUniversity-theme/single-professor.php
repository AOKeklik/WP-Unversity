<?php get_header()?>
<?php 
    while (have_posts()):
        the_post();
        /* banner */
        pageBanner();
        $statusHeart = "no";
        $countLike = new WP_Query(array(
            "posts_per_page" => -1,
            "post_type" => "like",
            "meta_query" => array(array(
                "key" => "liked_proffessor_id",
                "compare" => "=",
                "value" => get_the_ID()
            ))
        ));
        if (is_user_logged_in()) {
            $existLike = new WP_Query(array(
                "posts_per_page" => -1,
                "post_type" => "like",
                "author" => get_current_user_id(),
                "meta_query" => array(array(
                    "key" => "liked_proffessor_id",
                    "compare" => "=",
                    "value" => get_the_ID()
                ))
            ));
            if ($existLike->found_posts) {
                $statusHeart = "yes";
            }
        }
?>
    <div class="container container--narrow page-section">
        <div class="generic-content">
            <div class="row group">
                <div class="one-third"><?php the_post_thumbnail("professorPortrait")?></div>
                <div class="two-thirds">
                    <span class="like-box" data-like="<?php if (isset($existLike->posts[0]->ID)) echo $existLike->posts[0]->ID?>" data-professor="<?php the_ID()?>" data-exists="<?php echo $statusHeart?>">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span class="like-count"><?php echo esc_html($countLike->found_posts)?></span>
                    </span>
                    <?php the_content()?>
                </div>
            </div>
        </div>
        <?php
            $relatedPrograms = get_field("related_programs");
            if ($relatedPrograms):
        ?>
            <hr class="section-break">
            <h2 class="headline headline--medium">Subject(s) Taught</h2>
            <?php
                foreach ($relatedPrograms as $program):
            ?>
                <ul class="link-list min-list">
                    <li><a href="<?php echo get_the_permalink($program)?>"><?php echo get_the_title($program)?></a></li>
                </ul>
            <?php endforeach?>
        <?php endif?>
    </div>
<?php endwhile?>
<?php get_footer()?>