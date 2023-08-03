<?php

add_action("init", "university_post_types");
function university_post_types () {

    // campus
    register_post_type("campus", array (
        "capability_type" => "campus",
        "map_meta_cap" => true,
        "show_in_rest" => true,
        "supports" => array ("title", "editor", "excerpt"),
        "rewrite" => array("slug" => "campuses"),
        "has_archive" => true,
        "public" => true,
        "description" => "We have several conveniently located campuses.",
        "labels" => array (
            "name" => "Campuses",
            "add_new_item" => "Add New Campus",
            "all_items" => "Edit Campus",
            "singular_name" => "Campus"
        ),
        "menu_icon" => "dashicons-location-alt"
    ));
    // event
    register_post_type("event", array(
        "capability_type" => "event",
        "map_meta_cap" => true,
        "show_in_rest" => true,
        "supports" => array("title", "editor", "excerpt", "thumbnail"),
        "rewrite" => array("slug" => "events"),
        "has_archive" => true,
        "public" => true,
        "description" => "See what is going on in our world.",
        "labels" => array(
            "name" => "Event",
            "add_new_item" => "Edit Event",
            "all_items" => "All Events",
            "singular_name" => "Event"
        ),
        "menu_icon" => "dashicons-calendar"
    ));

    // program
    register_post_type("program", array (
        "show_in_rest" => true,
        "supports" => array("title"),
        "rewrite" => array("slug" => "programs"),
        "has_archive" => true,
        "public" => true,
        "description" => "There is something for everyone. Have a look around.",
        "labels" => array (
            "name" => "Programs",
            "add_new_item" => "Add New Program",
            "edit_item" => "Edit Program",
            "all_items" => "All Programs",
            "singular_name" => "Program"
        ),
        "menu_icon" => "dashicons-awards"
    ));

    // professor
    register_post_type("professor", array (
        "show_in_rest" => true,
        "supports" => array ("title", "editor", "thumbnail"),
        "rewrite" => array("slug" => "professor"),
        "has_archive" => false,
        "public" => true,
        "labels" => array (
            "name" => "Profesors",
            "add_new_item" => "Add New Professor",
            "edit_item" => "Edit Professor",
            "all_items" => "All Professors",
            "singular_name" => "Professor"
        ),
        "menu_icon" => "dashicons-welcome-learn-more"
    ));
}