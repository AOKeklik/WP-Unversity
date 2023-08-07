<?php
    if (!is_user_logged_in()):
        wp_redirect(esc_url(site_url("/")));
        exit;
    endif
?>
<?php get_header()?>
<?php
    while (have_posts()):
        the_post();
        pageBanner();
?>

    <div class="container container--narrow page-section">
        <div class="create-note">
            <h2 class="headline headline--medium">Create New Note</h2>
            <input class="new-note-title" placeholder="Title">
            <textarea class="new-note-body" placeholder="Your note here..."></textarea>
            <span class="submit-note">Create Note</span>
            <span class="note-limit-message"></span>
        </div>
        <ul class="min-list link-list" id="my-notes">
            <?php
                $notesProperties = array (
                    "posts_per_page" => -1,
                    "post_type" => "note"
                );
                $notes = new WP_Query($notesProperties);
                if ($notes->have_posts()):
                    while ($notes->have_posts()):
                        $notes->the_post();
            ?>
                <li data-id="<?php the_ID()?>">
                    <input readonly class="note-title-field" value="<?php echo str_replace("Private: ", "", esc_attr(get_the_title()))?>">
                    <span class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</span>
                    <span class="delete-note"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</span>
                    <textarea readonly class="note-body-field"><?php echo esc_textarea(wp_strip_all_tags(get_the_content()))?></textarea>
                    <span class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</span>
                </li>
            <?php endwhile?>
            <?php endif?>
        </ul>
    </div>

<?php endwhile?>
<?php get_footer()?>