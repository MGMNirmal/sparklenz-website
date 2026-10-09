<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo content
    |--------------------------------------------------------------------------
    |
    | Concept projects and placeholder team profiles are flagged "is_demo".
    | They are only shown publicly when this is enabled. It defaults to off
    | in production so fictional records never reach the live site.
    |
    */

    'show_demo_content' => (bool) env('SHOW_DEMO_CONTENT', env('APP_ENV', 'production') !== 'production'),

    /*
    |--------------------------------------------------------------------------
    | Enquiry notifications
    |--------------------------------------------------------------------------
    |
    | When set, each saved enquiry is also emailed to this address using the
    | configured mailer. A failed notification never discards the enquiry.
    |
    */

    'enquiry_notify_to' => env('ENQUIRY_NOTIFY_TO'),

    /*
    |--------------------------------------------------------------------------
    | Upload limits (kilobytes)
    |--------------------------------------------------------------------------
    */

    'max_image_kb' => (int) env('MAX_IMAGE_KB', 8192),

    'max_teaser_kb' => (int) env('MAX_TEASER_KB', 15360),

];
