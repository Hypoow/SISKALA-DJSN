<?php

return [

    /*
     * Path to the json file containing the credentials.
     */
    'service_account_credentials_json' => storage_path('app/google-calendar/supple-cubist-481504-k7-08a6bda851f6.json'),

    /*
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),

    /*
     *  The email address of the user account to impersonate.
     */
    'user_to_impersonate' => null,
];
