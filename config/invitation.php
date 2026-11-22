<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Birthday Invitation
    |--------------------------------------------------------------------------
    |
    | Event details shown on the invitation ticket. Date, time and place are
    | placeholders until the host confirms them. The guest chat link is read
    | from the environment so it can be changed without touching the code.
    |
    */

    'host' => 'Евгений',

    'date' => '12.12.2026',

    'time' => '19:00',

    'place' => 'В чате гостей',

    'chat_url' => env('INVITATION_CHAT_URL', 'https://t.me/'),

    /*
    |--------------------------------------------------------------------------
    | Guest List
    |--------------------------------------------------------------------------
    |
    | The password the host enters at /host/login to see who is coming.
    | Leave it empty to keep the guest list closed. Reply times on the list
    | are shown in the given timezone.
    |
    */

    'host_password' => env('INVITATION_HOST_PASSWORD'),

    'timezone' => env('INVITATION_TIMEZONE', 'Europe/Moscow'),

];
