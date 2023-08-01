<?php get_header()?>
<?php
    while (have_posts()):
        the_post();
        /* banner */
        pageBanner();
?>
<!-- content program -->
<div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
            <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link("program")?>"><i class="fa fa-home" aria-hidden="true"></i> All Programs</a> 
            <span class="metabox__main"><?php the_title()?></span>
        </p>
    </div>
    <div class="generic-content"><?php echo get_field("main_body_content")?></div>
    <?php 
        $relatedProfessors = new WP_Query(array (
            "posts_per_page" => -1,
            "post_type" => "professor",
            "orderby" => "title",
            "order" => "asc",
            "meta_key" => "related_programs",
            "meta_query" => array (
                "key" => "related_programs",
                "compare" => "like",
                "value" => get_the_ID()
            )
        ));
        if ($relatedProfessors->have_posts()):
    ?>
        <hr class="section-break">
        <h2 class="headline headline--medium"><?php the_title()?> Professors</h2>
        <ul class="professor-cards">
        <?php 
            while ($relatedProfessors->have_posts()):
                $relatedProfessors->the_post();
        ?>
            <li class="professor-card__list-item">
                <a class="professor-card" href="<?php the_permalink()?>">
                    <img class="professor-card__image" src="<?php the_post_thumbnail_url("professorLandscape")?>">
                    <span class="professor-card__name"><?php the_title()?></span>
                </a>
            </li>
        <?php endwhile?>
        </ul>
    <?php endif?>
    <?php wp_reset_postdata()?>
    <?php
        $today = date("Ymd");
        $relatedUpcomingEvents = new WP_Query(array (
            "posts_per_page" => -1,
            "post_type" => "event",
            "meta_key" => "event_date",
            "orderby" => "meta_value_num",
            "order" => "asc",
            "meta_query" => array (
                array (
                    "key" => "event_date",
                    "compare" => ">=",
                    "value" => $today,
                    "type" => "numeric",
                ),
                array (
                    "key" => "related_programs",
                    "compare" => "like",
                    "value" => '"' . get_the_ID() . '"'
                )
            )
        ));
        if ($relatedUpcomingEvents->have_posts()):
    ?>
        <hr class="section-break">
        <h2 class="headline headline--medium">Upcoming <?php the_title()?> Events</h2>
        <?php
            while ($relatedUpcomingEvents->have_posts()):
                $relatedUpcomingEvents->the_post();
                get_template_part("template-parts/content-event");
            endwhile?>
    <?php endif?>
    <?php wp_reset_postdata()?>
    <?php
        $relatedCampus = get_field("related_campus");
        if ($relatedCampus):
    ?>
        <hr class="section-break">
        <h2 class="headline headline--medium">Title is Available At These Campuses:</h2>
        <ul class="min-list link-list">
        <?php foreach ($relatedCampus as $campus):?>
            <li>
                <a href="<?php echo get_the_permalink($campus)?>"><?php echo get_the_title($campus)?></a>
            </li>
        <?php endforeach?>
        </ul>
    <?php endif?>
</div>
<?php endwhile?>
<?php get_footer()?>