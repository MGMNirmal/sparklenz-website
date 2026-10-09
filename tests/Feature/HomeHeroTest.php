<?php

namespace Tests\Feature;

use App\Models\PageCopy;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeHeroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SiteContentSeeder::class, RateCardSeeder::class]);
    }

    public function test_hero_has_one_stable_accessible_heading_and_hidden_animated_text(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<span class="sr-only">Your story. Made to move, inspire, connect and stand out.</span>', false)
            ->assertSee('<span class="hero__line" aria-hidden="true" data-hero-fade>Your story.</span>', false)
            ->assertSee('data-words=\'["move","inspire","connect","stand out"]\'', false)
            ->assertSee('Creative studio · Sri Lanka');
    }

    public function test_hero_reserves_space_for_every_changing_word(): void
    {
        $response = $this->get('/');

        foreach (['move', 'inspire', 'connect', 'stand out'] as $word) {
            $response->assertSee('<span class="hero__sizer">'.$word.'.</span>', false);
        }
    }

    public function test_hero_actions_link_to_existing_destinations(): void
    {
        $this->get('/')
            ->assertSee('class="btn btn--gradient" href="'.route('contact').'"', false)
            ->assertSee('class="btn btn--outline" href="'.route('work').'"', false);
    }

    public function test_changing_words_are_editable_and_escaped(): void
    {
        PageCopy::where('page', 'home')->where('key', 'hero_words')->firstOrFail()
            ->update(['content' => "shine\n<b>sparkle</b>"]);

        $this->get('/')
            ->assertSee('Made to shine and &lt;b&gt;sparkle&lt;/b&gt;.', false)
            ->assertDontSee('<b>sparkle</b>', false);
    }
}
