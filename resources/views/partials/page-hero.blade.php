<section class="page-hero" aria-labelledby="page-title">
    <div class="wrap">
        <p class="eyebrow" data-hero-fade>{{ $eyebrow }}</p>
        <h1 class="display hero__title" id="page-title">
            <span class="line"><span class="line__inner">{{ page_copy($copyPage.'.hero_title') }}</span></span>
            <span class="line"><span class="line__inner accent">{{ page_copy($copyPage.'.hero_accent') }}</span></span>
        </h1>
        <div class="page-hero__bottom" data-hero-fade>
            <p class="lead">{{ page_copy($copyPage.'.hero_intro', page_copy($copyPage.'.intro')) }}</p>
            @isset($pill)
                <span class="pill">{{ $pill }}</span>
            @endisset
        </div>
    </div>
</section>
