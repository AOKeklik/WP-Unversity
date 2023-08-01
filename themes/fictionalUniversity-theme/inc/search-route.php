<?php

add_action('rest_api_init', 'universityRegisterSearch');
function universityRegisterSearch() {
  register_rest_route('university/v3', 'search', array(
    'methods' => WP_REST_SERVER::READABLE,
    'callback' => 'universitySearchResults'
  ));
}

function universitySearchResults($data) {
  $mainQuery = new WP_Query(array(
    "posts_per_page" => -1,
    'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
    's' => sanitize_text_field($data['term'])
  ));

  $results = array(
    'generalInfo' => array(),
    'professors' => array(),
    'programs' => array(),
    'events' => array(),
    'campuses' => array()
  );

    while($mainQuery->have_posts()):
        $mainQuery->the_post();

        // post page
        if (get_post_type() == 'post' OR get_post_type() == 'page'):
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            ));
        endif;
        // professor
        if (get_post_type() == 'professor'):
            array_push($results["professors"], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
                "image" => get_the_post_thumbnail_url(0, "professorLandscape")
            ));
        endif;
        // program
        if (get_post_type() == 'program'):
            $relatedCampuses = get_field("related_campus");
            if ($relatedCampuses):
                foreach ($relatedCampuses as $campus):
                    array_push($results["campuses"], array(
                        "title" => get_the_title($campus),
                        "permalink" => get_the_permalink($campus),
                    ));
                endforeach;
            endif;
            array_push($results["programs"], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
                "id" => get_the_ID()
            ));
        endif;
        // campus
        if (get_post_type() == 'campus'):
            array_push($results["campuses"], array(
                "title" => get_the_title(),
                "permalink" => get_the_permalink(),
            ));
        endif;
        // events
        if (get_post_type() == 'event'):
            $eventDate = new DateTime(get_field("event_date"));
            $desc = "";
            if(has_excerpt(0)) $desc =  get_the_excerpt(); else $desc = wp_trim_words(get_the_content(), 10, "...");
            array_push($results["events"], array(
                "title" => get_the_title(),
                "description" => $desc,
                "permalink" => get_the_permalink(),
                "month" => $eventDate->format("M"),
                "day" => $eventDate->format("d"),
            ));
        endif;
    endwhile;
    
    
    if ($results['programs']):
        $programsMetaQuery = array("realtion" => "or");
        foreach ($results["programs"] as $program):
            array_push($programsMetaQuery, array (
                "key" => "related_programs",
                "compare" => "like",
                "value" => $program["id"]
            ));
        endforeach;
        $programRelationshipQuery = new WP_Query(array(
            "post_type" => array("professor", "event", "programs"),
            "meta_query" => $programsMetaQuery
        ));
    
        while ($programRelationshipQuery->have_posts()):
            $programRelationshipQuery->the_post();
            // professors
            if (get_post_type() == 'professor'):
                array_push($results["professors"], array(
                    "title" => get_the_title(),
                    "permalink" => get_the_permalink(),
                    "image" => get_the_post_thumbnail_url(0, "professorLandscape")
                ));
            endif;
            // events
            if (get_post_type() == 'event'):
                $eventDate = new DateTime(get_field("event_date"));
                $desc = "";
                if(has_excerpt(0)) $desc =  get_the_excerpt(); else $desc = wp_trim_words(get_the_content(), 10, "...");
                array_push($results["events"], array(
                    "title" => get_the_title(),
                    "description" => $desc,
                    "permalink" => get_the_permalink(),
                    "month" => $eventDate->format("M"),
                    "day" => $eventDate->format("d"),
                ));
            endif;
            // campus
            if (get_post_type() == 'campus'):
                array_push($results["campuses"], array(
                    "title" => get_the_title(),
                    "permalink" => get_the_permalink(),
                ));
            endif;
        endwhile;
    
        $results["professors"] = array_values(array_unique($results["professors"], SORT_REGULAR));
        $results["events"] = array_values(array_unique($results["events"], SORT_REGULAR));
    endif;

    return $results;
}

?>