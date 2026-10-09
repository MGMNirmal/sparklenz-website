<x-layouts.site title="Contact" page="contact" description="Start a project with Sparklenz. Send an enquiry, call, email or chat on WhatsApp.">
    @include('partials.page-hero', ['eyebrow' => 'Let’s start with hello', 'copyPage' => 'contact'])

    @php
        $formErrors = $errors->getBag('enquiry');
        $socials = array_filter([
            'Instagram' => site_setting('instagram_url'),
            'TikTok' => site_setting('tiktok_url'),
            'Facebook' => site_setting('facebook_url'),
            'YouTube' => site_setting('youtube_url'),
        ]);
        $interest = old('service_interest', $selectedInterest);
        $prefill = $selectedProject ? 'I’d like to talk about a project similar to “'.$selectedProject->title.'”.' : '';
    @endphp

    <section class="section" style="padding-top: 0" aria-label="Contact details and enquiry form">
        <div class="wrap contact-layout">
            <div class="contact-info" data-reveal>
                @if ($email = site_setting('email'))
                    <div class="contact-item">
                        <span class="label muted">Email us</span>
                        <a href="mailto:{{ $email }}">{{ $email }} <x-icon name="arrow" /></a>
                    </div>
                @endif
                @if ($phone = site_setting('phone_display'))
                    <div class="contact-item">
                        <span class="label muted">Call us</span>
                        <a href="tel:{{ site_setting('phone_link') }}">{{ $phone }} <x-icon name="arrow" /></a>
                    </div>
                @endif
                <div class="btn-row">
                    @if ($whatsapp = whatsapp_url())
                        <a class="btn btn--outline" href="{{ $whatsapp }}" target="_blank" rel="noopener">Chat on WhatsApp <x-icon name="whatsapp" /><span class="sr-only">(opens in a new tab)</span></a>
                    @endif
                </div>
                @if ($socials)
                    <div>
                        <p class="label muted" style="margin-bottom: 12px">Follow the studio</p>
                        <ul class="social-links">
                            @foreach ($socials as $label => $url)
                                <li><a class="btn btn--sm btn--outline" href="{{ $url }}" target="_blank" rel="noopener me">{{ $label }} ↗<span class="sr-only">(opens in a new tab)</span></a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <p class="muted">Big idea or small beginning — both are welcome. We’ll discuss your needs and confirm the scope and pricing together.</p>
            </div>

            <div class="form-card" id="enquiry">
                @if (session('enquiry_sent'))
                    <div class="form-success" role="status" data-focus-on-load>
                        <div class="form-success__icon"><x-icon name="check" /></div>
                        <h2>Enquiry received.</h2>
                        <p class="lead">{{ page_copy('contact.success_message') }}</p>
                        <div class="btn-row">
                            <a class="btn btn--berry" href="{{ route('work') }}">Explore our work <x-icon name="arrow" /></a>
                            <a class="btn btn--outline" href="{{ route('contact') }}#enquiry">Send another enquiry</a>
                        </div>
                    </div>
                @else
                    <h2>Tell us a little more.</h2>
                    <p class="muted">Fields marked * are required.</p>

                    @if ($formErrors->any())
                        <div class="form-alert form-alert--error" role="alert" tabindex="-1" data-focus-on-load>
                            <strong>Your enquiry hasn’t been sent yet.</strong> Please check:
                            <ul>
                                @foreach ($formErrors->all() as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('enquiries.store') }}" novalidate data-enquiry-form>
                        @csrf
                        <div class="form-grid">
                            <div class="field">
                                <label for="name">Your name *</label>
                                <input id="name" name="name" required maxlength="100" autocomplete="name" value="{{ old('name') }}"
                                       @if ($formErrors->has('name')) aria-invalid="true" aria-describedby="name-error" @endif>
                                @error('name', 'enquiry') <p class="field-error" id="name-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="field">
                                <label for="email">Email address *</label>
                                <input id="email" name="email" type="email" required maxlength="200" autocomplete="email" value="{{ old('email') }}"
                                       @if ($formErrors->has('email')) aria-invalid="true" aria-describedby="email-error" @endif>
                                @error('email', 'enquiry') <p class="field-error" id="email-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="field">
                                <label for="phone">Phone <span>(optional)</span></label>
                                <input id="phone" name="phone" type="tel" maxlength="40" autocomplete="tel" value="{{ old('phone') }}"
                                       @if ($formErrors->has('phone')) aria-invalid="true" aria-describedby="phone-error" @endif>
                                @error('phone', 'enquiry') <p class="field-error" id="phone-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="field">
                                <label for="service_interest">Interested in <span>(optional)</span></label>
                                <select id="service_interest" name="service_interest">
                                    <option value="">Not sure yet</option>
                                    @foreach ($interestOptions as $option)
                                        <option value="{{ $option }}" @selected($interest === $option)>{{ $option }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field full">
                                <label for="message">What do you have in mind? *</label>
                                <textarea id="message" name="message" required minlength="10" maxlength="3000"
                                          @if ($formErrors->has('message')) aria-invalid="true" aria-describedby="message-error" @endif>{{ old('message', $prefill) }}</textarea>
                                @error('message', 'enquiry') <p class="field-error" id="message-error">{{ $message }}</p> @enderror
                            </div>
                            <div class="hp-field" aria-hidden="true">
                                <label for="website">Leave this field empty</label>
                                <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                            </div>
                        </div>
                        <div class="btn-row" style="margin-top: 24px">
                            <button class="btn btn--berry" type="submit" data-submit>Send enquiry <x-icon name="arrow" /></button>
                        </div>
                        <p class="form-privacy">{{ page_copy('contact.privacy_note') }}</p>
                    </form>
                @endif
            </div>
        </div>
    </section>
</x-layouts.site>
