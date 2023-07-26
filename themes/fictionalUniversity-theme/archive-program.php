<?php 
    get_header();
    /* banner */
    pageBanner(array (
        "title" => "All Programs",
        "subtitle" => get_the_archive_description(),
    ));
?>

<div class="container container--narrow page-section">
    <?php
        while (have_posts()):
            the_post();
    ?>
        <ul class="link-list min-list">
            <li><a href="<?php the_permalink()?>"><?php the_title()?></a></li>
        </ul>
    <?php endwhile?>
    <?php echo paginate_links()?>
</div>
<?php get_footer()?>