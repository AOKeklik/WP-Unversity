<?php get_header();?>
<?php while(have_posts()) {
    the_post(); 
    /* banner */
    pageBanner();
?>
    <!-- page-section -->
    <div class="container container--narrow page-section">   
        <!-- metabox -->
        <?php 
            $parentPageId = wp_get_post_parent_id(get_the_ID());
            if($parentPageId): 
        ?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_permalink($parentPageId);?>">
                        <i class="fa fa-home" aria-hidden="true"></i> 
                        Back to <?php echo get_the_title($parentPageId)?>
                    </a> 
                    <span class="metabox__main"><?php the_title()?></span>
                </p>
            </div>
        <?php endif;?>
        <!-- metabox -->

        <!-- subpages -->
        <?php
            $testPages = get_pages(array (
                "child_of" => get_the_ID()
            ));
            if ($parentPageId or $testPages):
        ?>
            <div class="page-links">
                <h2 class="page-links__title"><a href="<?php echo get_permalink($parentPageId);?>"><?php echo get_the_title($parentPageId)?></a></h2>
                <ul class="min-list">
                    <?php
                        if ($parentPageId):
                            $findChildrenOf = $parentPageId;
                        else:
                            $findChildrenOf = get_the_ID();
                        endif;
                        wp_list_pages(array (
                            "title_li" => null,
                            "child_of" => $findChildrenOf,
                            "short_column" => "menu_order"
                        ))
                    ?>
                </ul>
            </div>
        <?php endif;?>
        <!-- subpages -->

        <!-- content -->
        <div class="generic-content">
            <?php the_content();?>
        </div>
        <!-- content -->

    </div>
    <!-- page-section -->
<?php } ?>

<?php get_footer();?>