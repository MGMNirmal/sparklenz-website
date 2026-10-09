{{-- The screening room: filled by resources/js/screening-room.js. All
     project text is inserted as text nodes; the YouTube iframe is created
     by the official IFrame Player API inside .yt-host only. --}}
<dialog class="screening on-dark" id="screening-room" aria-labelledby="screening-title" aria-describedby="screening-summary">
    <div class="screening__panel">
        <div class="screening__bar">
            <span class="frame-no" data-screening-counter></span>
            <button class="icon-btn" type="button" data-screening-close>
                <x-icon name="close" />
                <span class="sr-only">Close screening room</span>
            </button>
        </div>

        <div class="screening__stage" data-screening-stage></div>

        <p class="screening__status" data-screening-status role="status" aria-live="polite"></p>

        <div class="screening__thumbs" data-screening-thumbs hidden></div>

        <div class="screening__info">
            <div>
                <div class="meta"><span data-screening-client></span><span data-screening-category></span><span class="badge" data-screening-demo hidden>Concept project</span></div>
                <h2 id="screening-title" data-screening-title></h2>
                <p id="screening-summary" data-screening-summary></p>
                <p data-screening-description></p>
            </div>
            <div class="screening__actions">
                <a class="btn btn--light" href="{{ route('contact') }}" data-screening-enquire>Start a project like this <x-icon name="arrow" /></a>
                <div class="screening__nav">
                    <button class="icon-btn" type="button" data-screening-prev><x-icon name="left" /><span class="sr-only">Previous project</span></button>
                    <button class="icon-btn" type="button" data-screening-next><x-icon name="right" /><span class="sr-only">Next project</span></button>
                </div>
            </div>
        </div>
    </div>
</dialog>
