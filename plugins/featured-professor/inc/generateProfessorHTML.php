<?php

function generateProfessorHTML ($id) {
    $getProfessor = new WP_Query([
        "post_type" => "professor",
        "p" => $id
    ]);

    while ($getProfessor->have_posts()):
        $getProfessor->the_post();
    ob_start()?>
        <div class="professor-callout">
            <div class="professor-callout__photo" style="background-image: url(<?php the_post_thumbnail_url("professorPortrait")?>)"></div>
            <div class="professor-callout__text">
                <h5><?php the_title()?></h5>
                <p><?php if (has_excerpt()) echo the_excerpt(); else echo wp_trim_words(get_the_content(), 25, "...")?></p>
                <p><strong><a href="<?php the_permalink()?>">Learn more about <?php the_title()?> &raquo;</a></strong></p>
                <?php
                    $relatedPrograms = get_field("related_programs");
                    if ($relatedPrograms):?>
                        <p><?php echo wp_strip_all_tags(get_the_title())?> teaches:
                            <?php foreach ($relatedPrograms as $key => $program):
                                echo get_the_title($program);
                                if (count($relatedPrograms) > 1 && array_key_last($relatedPrograms) != $key):
                                    echo ", ";
                                endif;
                            endforeach?>
                        </p>

                <?php endif?>
            </div>
        </div>
    <?php 
    endwhile;
    return ob_get_clean();
}