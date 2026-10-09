<section class="process section on-dark" aria-labelledby="process-title">
    <div class="wrap">
        <div class="section-head" data-reveal>
            <div>
                <p class="eyebrow">A simple creative process</p>
                <h2 class="h-lg" id="process-title">{{ page_copy_html('home.process_title') }}</h2>
            </div>
        </div>
        <ol class="process-grid">
            @foreach ([['01', 'Connect'], ['02', 'Create'], ['03', 'Refine']] as [$number, $stage])
                <li class="process-step" data-reveal>
                    <span class="frame-no">{{ $number }} / {{ strtoupper($stage) }}</span>
                    <h3>{{ page_copy('process.step_'.$loop->iteration.'_title') }}</h3>
                    <p>{{ page_copy('process.step_'.$loop->iteration.'_body') }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
