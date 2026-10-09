<section class="cta section on-dark" aria-labelledby="cta-title">
    <div class="wrap cta__inner" data-reveal>
        <div>
            <p class="eyebrow">{{ page_copy('global.cta_eyebrow') }}</p>
            <h2 id="cta-title">{{ page_copy_html('global.cta_title') }}</h2>
        </div>
        <a class="cta__circle" href="{{ route('contact') }}">
            <x-icon name="arrow" />
            <span class="sr-only">Start a project with Sparklenz</span>
        </a>
    </div>
</section>
