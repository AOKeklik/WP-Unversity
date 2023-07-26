<?php
    get_header();
    while (have_posts()):
        the_post();
        pageBanner();
        $mapInfo = get_field("map_location")
?>
<div class="container container--narrow page-section">
    <div class="metabox metabox--position-up metabox--with-home-link">
        <p>
            <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link("campus")?>">
                <i class="fa fa-home" aria-hidden="true"></i> 
                All Campuses
            </a> 
            <span class="metabox__main"><?php the_title()?></span>
        </p>
    </div>

    <div class="generic-content"><?php the_content()?></div>

    <div class="acf-map" id="map">
        <div class="marker" data-lat="<?php echo $mapInfo["lat"]?>" data-lng="<?php echo $mapInfo["lng"]?>" data-zoom="<?php echo $mapInfo["zoom"]?>">
            <h3><?php the_title()?></h3>
            <p><?php echo $mapInfo["address"]?></p>
        </div>
    </div>
    <?php
        $relatedPrograms = new WP_Query(array (
            "posts_per_page" => -1,
            "post_type" => "program",
            "orderby" => "title",
            "order" => "asc",
            "meta_query" => array(
                array (
                    "key" => "related_campus",
                    "compare" => "like",
                    "value" => get_the_ID()
                )
            )
        ));
        if ($relatedPrograms->have_posts()):
    ?>
        <hr class="section-break">
        <h2 class="headline headline--medium">Programs Available At This Campus</h2>
        <ul class="min-list link-list">
    <?php
        while ($relatedPrograms->have_posts()):
            $relatedPrograms->the_post();
    ?>
            <li>
                <a href=""><?php the_title()?></a>
            </li>
    <?php endwhile?>
        </ul>
    <?php endif?>
    <?php wp_reset_postdata()?>
</div>
<?php 
    endwhile;
    get_footer();
?>