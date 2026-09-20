<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Company Profile
    |--------------------------------------------------------------------------
    |
    | Company-wide content rendered on the proposal "About Us" and "Colofon"
    | pages. Editable per-quote fields (highlights, agent intro letter) live
    | on the quotation_versions table; static content lives here so a single
    | edit propagates to every future PDF.
    |
    */

    'tagline' => env(
        'COMPANY_TAGLINE',
        'Crafting unforgettable Tanzania safaris with heart, detail, and local expertise.'
    ),

    'mission' => env(
        'COMPANY_MISSION',
        "To deliver exceptional and authentic travel experiences across Tanzania, showcasing the region's natural beauty, rich culture, and warm hospitality. We aim to create meaningful journeys that inspire, educate, and connect travelers with local communities, while promoting sustainable and responsible tourism."
    ),

    'vision' => env(
        'COMPANY_VISION',
        'To be the leading and most trusted safari company in Tanzania, recognized globally for our creativity, personalized service, and commitment to preserving the wonders of our destination for future generations.'
    ),

    'address' => env('COMPANY_ADDRESS', 'Nungwi Zanzibar'),
    'country' => env('COMPANY_COUNTRY', 'Tanzania'),
    'email' => env('COMPANY_EMAIL', 'info@serenbluesafaris.com'),
    'phone' => env('COMPANY_PHONE', null),

    /*
    |--------------------------------------------------------------------------
    | Vehicles
    |--------------------------------------------------------------------------
    |
    | Vehicles surfaced on the proposal "Vehicles" page. Until a Vehicle DB
    | model exists, this is the source of truth.
    |
    */

    'vehicles' => [
        [
            'name' => '4x4 Safari Land Cruiser',
            'description' => 'Custom-built Toyota Land Cruiser with pop-up roof for game viewing, charging ports, fridge, and binoculars on board.',
            'image' => 'assets/images/vehicles/landcruiser.jpg',
            'capacity' => 'Up to 6 guests',
        ],
        [
            'name' => 'Extended Tour Van',
            'description' => 'Air-conditioned extended van for transfer days and group movements between camps and airstrips.',
            'image' => 'assets/images/vehicles/tour-van.jpg',
            'capacity' => 'Up to 9 guests',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Colofon
    |--------------------------------------------------------------------------
    */

    'colofon' => [
        'quote' => env(
            'COMPANY_COLOFON_QUOTE',
            "One's destination is never a place, but a new way of seeing things"
        ),
        'quote_author' => env('COMPANY_COLOFON_QUOTE_AUTHOR', 'Henry Miller'),
        'copyright_text' => env('COMPANY_COLOFON_COPYRIGHT_TEXT', 'Dream Travel and Tours'),
        'copyright_images' => env(
            'COMPANY_COLOFON_COPYRIGHT_IMAGES',
            'Dream Travel and Tours & partners. View copyright per photographer.'
        ),
    ],

];
