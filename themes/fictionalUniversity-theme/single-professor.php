<?php get_header()?>
<?php 
    while (have_posts()):
        the_post();
        /* banner */
        pageBanner();
?>
    <div class="container container--narrow page-section">
        <div class="generic-content">
            <div class="row group">
            <div class="one-third"><?php the_post_thumbnail("professorPortrait")?></div>
            <div class="two-thirds"><?php the_content()?></div>
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