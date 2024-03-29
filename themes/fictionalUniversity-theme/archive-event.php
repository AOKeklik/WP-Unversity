<?php 
  get_header();
  /* banner */
  pageBanner(array (
    "title" => get_the_archive_title(),
    "subtitle" => get_the_archive_description(),
  ));
?>

<div class="container container--narrow page-section">
    <?php
      while (have_posts()) {
        the_post();
        get_template_part("template-parts/content", get_post_type());
      }?>
    <?php echo paginate_links()?>
    <hr class="section-break">
    <p>Looking for a recap of past events? <a href="<?php echo site_url("/past-events")?>">Check out our past events archive</a>.</p>
</div>
<?php get_footer()?>