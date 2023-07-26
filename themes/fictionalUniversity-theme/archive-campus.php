<?php 
    get_header();
    pageBanner(array (
        "title" => "Our Campuses",
        "subtitle" => get_the_archive_description()
    ));
?>
<div class="container container--narrow page-section">
    <div class="acf-map" id="map">
        <?php
            while (have_posts()):
                the_post();
                $mapLocation = get_field("map_location");
        ?>
            <div class="marker" data-lat="<?php echo $mapLocation["lat"]?>" data-lng="<?php echo $mapLocation["lng"]?>" data-zoom="<?php echo $mapLocation["zoom"]?>">
                <h3><a href="<?php the_permalink()?>"><?php the_title()?></a></h3>
                <p><?php echo  $mapLocation["address"]?></p>
            </div>
        <?php endwhile?>
    </div>
</div>
<?php get_footer()?>
