<?php 
  get_header();
  /* banner */
  pageBanner(array(
    'title' => get_the_archive_title(),
    'subtitle' => get_the_archive_description()
  ));
?>
<!-- section content -->
<div class="container container--narrow page-section">
    <?php
        while (have_posts()):
        the_post();
    ?>
        <div class="post-item">
            <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink()?>"><?php the_title()?></a></h2>
                
            <div class="metabox">
                <p>Posted by <?php the_author_posts_link()?> on <?php the_time("d/m/y")?> in <?php the_category(", ")?></p>
            </div>

            <div class="generic-content">
                <? the_excerpt()?>
                <p><a class="btn btn--blue" href="<? the_permalink()?>">Continue reading &raquo;</a></p>
            </div>

        </div>
    <?php endwhile?>
    <?php echo paginate_links()?>
    
</div>
<!-- section content -->
<?php get_footer()?>