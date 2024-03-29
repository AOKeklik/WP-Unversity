<?php $eventDate = new DateTime(get_field("event_date"))?>
<div class="event-summary">
        <a class="event-summary__date t-center" href="#">
        <span class="event-summary__month"><? echo $eventDate->format("M")?></span>
        <span class="event-summary__day"><? echo $eventDate->format("d")?></span>
    </a>
    <div class="event-summary__content">
        <h5 class="event-summary__title headline headline--tiny"><a href="<? the_permalink()?>"><? the_title()?></a></h5>
        <p>
            <? if(has_excerpt()) the_excerpt(); else echo wp_trim_words(get_the_content(), "10", "...")?> 
            <a href="<? the_permalink()?>" class="nu gray">Learn more</a>
        </p>
    </div>
</div>