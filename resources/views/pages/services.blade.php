<x-layouts.site title="Services & Packages" page="services" description="Sparklenz rates for video editing, video production, drone coverage, graphic design and monthly content packages. All prices in LKR.">
    @include('partials.page-hero', ['eyebrow' => 'Services & packages', 'copyPage' => 'services', 'pill' => 'All prices in LKR'])

    <section class="section" style="padding-top: 0" aria-label="Services and rates">
        <div class="wrap">
            <div class="service-acc">
                @foreach ($services as $service)
                    <div class="service-acc__item" id="{{ $service->slug }}"
                         x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }"
                         x-init="if (location.hash === '#{{ $service->slug }}') open = true"
                         @hashchange.window="if (location.hash === '#{{ $service->slug }}') open = true">
                        <h2 class="service-acc__heading">
                            <button class="service-acc__head" type="button" :aria-expanded="open.toString()" aria-expanded="true" aria-controls="panel-{{ $service->slug }}" @click="open = !open">
                                <span class="frame-no">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span>
                                    <span class="service-acc__name">{{ $service->name }}</span>
                                    <span class="service-acc__summary">{{ $service->summary }}</span>
                                </span>
                                <span class="service-acc__price">
                                    @if ($service->starting_price !== null)
                                        {{ $service->price_prefix }} LKR {{ lkr($service->starting_price) }}
                                        <small>{{ $service->starting_unit }}</small>
                                    @else
                                        {{ $service->starting_label }}
                                    @endif
                                </span>
                                <span class="icon-btn" aria-hidden="true"><x-icon name="plus" /></span>
                            </button>
                        </h2>
                        <div id="panel-{{ $service->slug }}" x-show="open" x-collapse.duration.400ms>
                            <div class="service-acc__body">
                                <div>
                                    @if ($service->intro)
                                        <p class="lead">{{ $service->intro }}</p>
                                    @endif
                                    @if ($service->includes)
                                        <div class="includes">
                                            @foreach ($service->includes as $group)
                                                <div>
                                                    @if ($group['heading'] ?? '')
                                                        <h3>{{ $group['heading'] }}</h3>
                                                    @endif
                                                    <ul>
                                                        @foreach ($group['items'] as $item)
                                                            <li>{{ $item }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    <a class="btn btn--berry" href="{{ route('contact', ['service' => $service->slug]) }}#enquiry">Enquire about {{ strtolower($service->name) }} <x-icon name="arrow" /></a>
                                </div>
                                <div>
                                    <div class="rate-tables">
                                        @foreach ($service->rateGroups() as $groupLabel => $rates)
                                            <table class="rate-table">
                                                @if ($groupLabel !== '')
                                                    <caption>{{ $groupLabel }}</caption>
                                                @endif
                                                <thead>
                                                    <tr><th scope="col">Service</th><th scope="col" class="num">Price (LKR)</th></tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($rates as $rate)
                                                        <tr>
                                                            <th scope="row">{{ $rate->label }}@if ($rate->detail)<small>{{ $rate->detail }}</small>@endif</th>
                                                            <td class="num">
                                                                @if ($rate->previous_price)
                                                                    <s><span class="sr-only">Previously LKR </span>{{ lkr($rate->previous_price) }}</s><span class="sr-only">, now</span>
                                                                @endif
                                                                {{ $rate->price === null ? ($rate->detail ? 'Custom quote' : 'On request') : $rate->formattedPrice() }}
                                                                @if ($rate->unit)
                                                                    <span class="unit">{{ $rate->unit }}</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @endforeach
                                    </div>
                                    @if ($service->notes)
                                        <ul class="notes" style="margin-top: 20px">
                                            @foreach ($service->notes as $note)
                                                <li>{{ $note }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section packages on-dark" id="packages" aria-labelledby="packages-title">
        <div class="wrap">
            <div class="section-head" data-reveal>
                <div>
                    <p class="eyebrow">Monthly packages</p>
                    <h2 class="h-xl" id="packages-title">{{ page_copy_html('services.packages_title') }}</h2>
                </div>
                <p>{{ page_copy('services.packages_intro') }}</p>
            </div>

            <div class="package-grid">
                @foreach ($packages as $package)
                    <article class="package-card {{ $package->is_featured ? 'package-card--featured' : '' }}" id="{{ $package->slug }}" data-reveal aria-labelledby="pkg-{{ $package->slug }}">
                        @if ($package->is_featured)
                            <span class="badge package-card__flag">Most complete</span>
                        @endif
                        <div class="package-card__top"><span>{{ $package->type_label }}</span><span class="frame-no">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span></div>
                        <div>
                            <h3 id="pkg-{{ $package->slug }}">{{ $package->name }}</h3>
                            @if ($package->tagline)
                                <p class="package-card__tagline">{{ $package->tagline }}</p>
                            @endif
                        </div>
                        @if ($package->description)
                            <p>{{ $package->description }}</p>
                        @endif
                        @if ($start = $package->startingPrice())
                            <p class="package-card__price"><small>From LKR</small><strong>{{ lkr($start) }}</strong><small>/ {{ $package->billing_unit }}</small></p>
                        @endif
                        <div class="table-scroll">
                            <table class="rate-table">
                                <caption class="sr-only">{{ $package->name }} monthly prices in LKR</caption>
                                <thead>
                                    <tr>
                                        <th scope="col">{{ $package->tier_column_label }}</th>
                                        <th scope="col" class="num">{{ $package->price_column_label }}</th>
                                        @if ($package->alt_price_column_label)
                                            <th scope="col" class="num">{{ $package->alt_price_column_label }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($package->tiers as $tier)
                                        <tr>
                                            <th scope="row">{{ $tier->label }}@if ($tier->inclusions)<small>{{ $tier->inclusions }}</small>@endif</th>
                                            <td class="num">
                                                @if ($tier->previous_price)
                                                    <s><span class="sr-only">Previously LKR </span>{{ lkr($tier->previous_price) }}</s><span class="sr-only">, now LKR </span>
                                                @endif
                                                {{ lkr($tier->price) }}
                                            </td>
                                            @if ($package->alt_price_column_label)
                                                <td class="num">{{ lkr($tier->alt_price) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($package->includes)
                            <div class="includes"><ul>
                                @foreach ($package->includes as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul></div>
                        @endif
                        @if ($package->additional_item_price || $package->notes)
                            <ul class="notes">
                                @if ($package->additional_item_price)
                                    <li>Additional video: LKR {{ lkr($package->additional_item_price) }} {{ str_replace('per additional video', 'per video', $package->additional_item_unit) }}</li>
                                @endif
                                @foreach ($package->notes ?? [] as $note)
                                    <li>{{ $note }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <a class="btn {{ $package->is_featured ? 'btn--berry' : 'btn--light' }}" href="{{ route('contact', ['service' => $package->slug]) }}#enquiry">Enquire about {{ $package->name }} <x-icon name="arrow" /></a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section" aria-labelledby="notes-title">
        <div class="wrap rate-notes">
            <div><p class="eyebrow">Good to know</p><h2 class="h-md" id="notes-title">Rate card notes</h2></div>
            <ul class="notes" style="font-size: 16px; gap: 10px">
                @foreach (page_copy_lines('services.rate_notes') as $note)
                    <li>{{ $note }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    @include('partials.cta')
</x-layouts.site>
