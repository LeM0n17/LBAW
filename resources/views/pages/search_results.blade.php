<section id="featured" class="homesection">
    <h2 class="title">Searched Events</h2>
    <div class="sidescroller">
        @each('partials.eventcard', $events, 'event')
    </div>
</section>