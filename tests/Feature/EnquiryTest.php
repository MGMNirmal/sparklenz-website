<?php

namespace Tests\Feature;

use App\Mail\EnquiryReceived;
use App\Models\Enquiry;
use Database\Seeders\RateCardSeeder;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;
use Tests\TestCase;

class EnquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([SiteContentSeeder::class, RateCardSeeder::class]);
        RateLimiter::clear('enquiries');
    }

    /**
     * @return array<string, string>
     */
    private function validEnquiry(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Nimal Perera',
            'email' => 'nimal@example.com',
            'phone' => '+94 77 123 4567',
            'service_interest' => 'Spark Social',
            'message' => 'We need four reels a month for our café.',
        ], $overrides);
    }

    public function test_a_valid_enquiry_is_saved_before_success_is_shown(): void
    {
        $this->post('/contact', $this->validEnquiry())
            ->assertRedirect(route('contact').'#enquiry')
            ->assertSessionHas('enquiry_sent', true);

        $this->assertDatabaseHas('enquiries', ['email' => 'nimal@example.com', 'status' => 'new', 'service_interest' => 'Spark Social']);

        $this->followingRedirects()->post('/contact', $this->validEnquiry(['email' => 'second@example.com']))
            ->assertSee('Enquiry received.');
    }

    public function test_invalid_enquiries_are_rejected_with_messages(): void
    {
        $this->from('/contact')->post('/contact', ['name' => '', 'email' => 'not-an-email', 'message' => 'short'])
            ->assertRedirect('/contact')
            ->assertSessionHasErrors(['name', 'email', 'message'], null, 'enquiry');

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_honeypot_submissions_are_not_saved_and_not_shown_as_success(): void
    {
        $this->from('/contact')->post('/contact', $this->validEnquiry(['website' => 'http://spam.example']))
            ->assertSessionHasErrors('website', null, 'enquiry')
            ->assertSessionMissing('enquiry_sent');

        $this->assertDatabaseCount('enquiries', 0);
    }

    public function test_enquiries_are_rate_limited(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->post('/contact', $this->validEnquiry(['email' => "person{$i}@example.com"]))->assertRedirect();
        }

        $this->post('/contact', $this->validEnquiry())->assertTooManyRequests();
        $this->assertDatabaseCount('enquiries', 3);
    }

    public function test_notification_is_sent_when_configured(): void
    {
        Mail::fake();
        config(['sparklenz.enquiry_notify_to' => 'studio@example.com']);

        $this->post('/contact', $this->validEnquiry())->assertSessionHas('enquiry_sent');

        Mail::assertSent(EnquiryReceived::class, fn (EnquiryReceived $mail) => $mail->hasTo('studio@example.com'));
        $this->assertNotNull(Enquiry::first()->notified_at);
    }

    public function test_enquiry_is_kept_when_the_notification_fails(): void
    {
        config(['sparklenz.enquiry_notify_to' => 'studio@example.com']);
        Mail::shouldReceive('to')->andThrow(new RuntimeException('SMTP down'));

        $this->post('/contact', $this->validEnquiry())->assertSessionHas('enquiry_sent', true);

        $this->assertDatabaseHas('enquiries', ['email' => 'nimal@example.com', 'notified_at' => null]);
    }

    public function test_contact_page_preselects_a_service_from_the_query_string(): void
    {
        $this->get('/contact?service=drone-coverage')->assertSee('<option value="Drone coverage" selected', false);
    }
}
