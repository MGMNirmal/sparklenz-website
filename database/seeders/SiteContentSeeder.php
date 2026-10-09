<?php

namespace Database\Seeders;

use App\Models\PageCopy;
use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds contact details from the rate card plus original draft page copy.
 * Existing values are never overwritten, so admin edits survive re-seeding.
 */
class SiteContentSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->settings() as $order => [$key, $group, $label, $type, $value, $help]) {
            Setting::firstOrCreate(['key' => $key], [
                'group' => $group, 'label' => $label, 'type' => $type, 'value' => $value,
                'help' => $help, 'sort_order' => $order,
            ]);
        }

        foreach ($this->copy() as $order => [$page, $key, $label, $type, $content, $max]) {
            PageCopy::firstOrCreate(['page' => $page, 'key' => $key], [
                'label' => $label, 'type' => $type, 'content' => $content,
                'max_length' => $max, 'sort_order' => $order,
            ]);
        }
    }

    /**
     * @return list<array{0: string, 1: string, 2: string, 3: string, 4: string|null, 5: string|null}>
     */
    private function settings(): array
    {
        return [
            ['email', 'contact', 'Email address', 'email', 'sparklenzstudio@gmail.com', null],
            ['phone_display', 'contact', 'Phone (as displayed)', 'text', '+94 78 931 8635', null],
            ['phone_link', 'contact', 'Phone (international format for links)', 'phone', '+94789318635', 'Digits with a leading +, no spaces.'],
            ['whatsapp_number', 'contact', 'WhatsApp number', 'phone', '94789318635', 'Country code and number, digits only.'],
            ['whatsapp_message', 'contact', 'WhatsApp greeting', 'text', 'Hi Sparklenz, I’d like to discuss a project.', 'Pre-filled message when someone taps WhatsApp.'],
            ['location', 'contact', 'Location line', 'text', 'Sri Lanka', null],
            ['instagram_url', 'social', 'Instagram URL', 'url', 'https://www.instagram.com/sparklenz_official/', 'Rate card handle: @sparklenz_official'],
            ['tiktok_url', 'social', 'TikTok URL', 'url', 'https://www.tiktok.com/@sparklenz.official', 'Rate card handle: @sparklenz.official'],
            ['facebook_url', 'social', 'Facebook URL', 'url', 'https://www.facebook.com/profile.php?id=61579842123921', 'Hidden while empty.'],
            ['youtube_url', 'social', 'YouTube channel URL', 'url', null, 'Hidden while empty.'],
            ['meta_description', 'seo', 'Default meta description', 'textarea', 'Sparklenz is a Sri Lankan creative studio for video editing, video production, drone coverage, graphic design and social media content.', 'Used by search engines when a page has no description of its own.'],
        ];
    }

    /**
     * @return list<array{0: string, 1: string, 2: string, 3: string, 4: string, 5: int}>
     */
    private function copy(): array
    {
        return [
            ['home', 'hero_eyebrow', 'Hero badge', 'text', 'Creative studio · Sri Lanka', 60],
            ['home', 'hero_title', 'Hero heading (line 1)', 'text', 'Your story.', 40],
            ['home', 'hero_accent', 'Hero heading (line 2, before the changing word)', 'text', 'Made to', 30],
            ['home', 'hero_words', 'Changing words (one per line)', 'lines', "move\ninspire\nconnect\nstand out", 120],
            ['home', 'hero_intro', 'Hero introduction', 'textarea', 'From the first spark to the final frame, we turn ideas into video, design and content that people stop to feel.', 240],
            ['home', 'ticker', 'Ticker phrases (one per line)', 'lines', "Video editing\nVideo production\nDrone coverage\nGraphic design\nSocial media content\nBeyond the frame", 400],
            ['home', 'work_title', 'Selected work heading', 'textarea', "Different stories.\nOne creative lens.", 80],
            ['home', 'work_intro', 'Selected work introduction', 'textarea', 'A selection of films, stills and design. Hover to preview, or open a project in the screening room.', 200],
            ['home', 'services_title', 'Services heading', 'textarea', "One studio.\nMore possibilities.", 80],
            ['home', 'services_intro', 'Services introduction', 'textarea', 'From a single edit to a full month of content, find the creative support your next idea needs.', 200],
            ['home', 'wall_title', 'Project wall heading', 'textarea', "Every frame,\nin motion.", 80],
            ['home', 'wall_intro', 'Project wall introduction', 'textarea', 'A moving wall of recent films, stills and design. Pick any frame to open it in the screening room.', 200],
            ['home', 'studio_title', 'Studio introduction heading', 'textarea', "Curious minds.\nCareful craft.\nA shared vision.", 100],
            ['home', 'studio_body', 'Studio introduction text', 'textarea', 'Good content begins with understanding your story. We listen closely, find the idea, then bring the attention and creative energy to tell it well.', 300],
            ['home', 'process_title', 'Process heading', 'textarea', "From “what if”\nto “that’s it.”", 80],
            ['global', 'cta_eyebrow', 'Closing call-to-action eyebrow', 'text', 'The next story could be yours', 60],
            ['global', 'cta_title', 'Closing call-to-action heading', 'textarea', "Let’s make something\nworth watching.", 80],
            ['global', 'footer_tagline', 'Footer tagline', 'textarea', "Ideas. Creativity. Results.\nYour creative partner, beyond the frame.", 160],
            ['process', 'step_1_title', 'Step 1 title', 'text', 'Tell us your idea.', 60],
            ['process', 'step_1_body', 'Step 1 text', 'textarea', 'Share what you want to say, who it is for and where it needs to go.', 200],
            ['process', 'step_2_title', 'Step 2 title', 'text', 'Bring it into focus.', 60],
            ['process', 'step_2_body', 'Step 2 text', 'textarea', 'We agree the direction and the scope, then shoot, design and edit.', 200],
            ['process', 'step_3_title', 'Step 3 title', 'text', 'Make it feel right.', 60],
            ['process', 'step_3_body', 'Step 3 text', 'textarea', 'We review the work together and refine it for the audience and platform.', 200],
            ['about', 'hero_title', 'Hero heading (line 1)', 'text', 'Small details.', 60],
            ['about', 'hero_accent', 'Hero heading (accent line)', 'text', 'Bigger perspectives.', 60],
            ['about', 'hero_intro', 'Hero introduction', 'textarea', 'A creative studio for ideas that deserve to be seen, heard and remembered.', 200],
            ['about', 'story_title', 'Story heading', 'textarea', "We see more than a brief.\nWe see what’s possible.", 100],
            ['about', 'story_body', 'Story text', 'textarea', 'From a quick social edit to a carefully crafted production, Sparklenz brings video, design and content together under one roof. Our approach is simple: listen closely, find the idea and make every frame work for your story.', 600],
            ['about', 'value_1_title', 'Value 1 title', 'text', 'A little more curiosity.', 60],
            ['about', 'value_1_body', 'Value 1 text', 'textarea', 'We ask the right questions before we pick up a camera or open an edit.', 200],
            ['about', 'value_2_title', 'Value 2 title', 'text', 'Care in every frame.', 60],
            ['about', 'value_2_body', 'Value 2 text', 'textarea', 'Rhythm, colour and the small details all shape how a story feels.', 200],
            ['about', 'value_3_title', 'Value 3 title', 'text', 'Made with you.', 60],
            ['about', 'value_3_body', 'Value 3 text', 'textarea', 'A clear conversation and a shared direction, from the first idea to the final result.', 200],
            ['about', 'team_title', 'Team heading', 'textarea', "Good energy.\nBetter together.", 80],
            ['about', 'team_intro', 'Team introduction', 'textarea', 'The people behind the perspective.', 200],
            ['services', 'hero_title', 'Hero heading (line 1)', 'text', 'Bring the idea.', 60],
            ['services', 'hero_accent', 'Hero heading (accent line)', 'text', 'We’ll bring it to life.', 60],
            ['services', 'hero_intro', 'Hero introduction', 'textarea', 'One edit or a month of content. Start with what you need and make it unmistakably yours.', 200],
            ['services', 'packages_title', 'Packages heading', 'textarea', "A little more\nconsistency.", 80],
            ['services', 'packages_intro', 'Packages introduction', 'textarea', 'Four monthly packages for different content needs. Compare the inclusions, then talk to us about the right fit.', 200],
            ['services', 'rate_notes', 'Rate card notes (one per line)', 'lines', "Script writing: LKR 1,200 per script, where applicable.\nTransportation is not included in applicable shooting rates — charged separately according to location.\nDrone shooting is charged separately where applicable.\nAll prices are in Sri Lankan Rupees (LKR) and may vary depending on project scope and requirements.\nPrices shown are starting / reference rates and may vary based on project requirements and complexity. Contact us for a customised quotation.", 1200],
            ['work', 'hero_title', 'Hero heading (line 1)', 'text', 'A little different.', 60],
            ['work', 'hero_accent', 'Hero heading (accent line)', 'text', 'A lot to feel.', 60],
            ['work', 'hero_intro', 'Hero introduction', 'textarea', 'Films, stills and design from the studio. Open any project to watch it in the screening room.', 200],
            ['work', 'empty_state', 'Message when no work is published', 'textarea', 'New work is being prepared for this page. In the meantime, ask us for recent examples.', 200],
            ['contact', 'hero_title', 'Hero heading (line 1)', 'text', 'Your next story', 60],
            ['contact', 'hero_accent', 'Hero heading (accent line)', 'text', 'starts here.', 60],
            ['contact', 'intro', 'Introduction', 'textarea', 'Have a clear brief, a rough idea or a question? We’d love to hear what you’re thinking.', 240],
            ['contact', 'success_message', 'Message after a successful enquiry', 'textarea', 'Thank you — your enquiry has reached the studio. We’ll reply by email or phone soon.', 240],
            ['contact', 'privacy_note', 'Form privacy note', 'textarea', 'Sparklenz uses the details you send only to reply to your enquiry. Films on this site are embedded from YouTube. They load from YouTube only when you preview or play them, and YouTube may then set its own cookies under Google’s privacy policy.', 500],
        ];
    }
}
