<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Report comments
    |--------------------------------------------------------------------------
    |
    | Comments are switched off for the pilot. Nothing is deleted: the table,
    | the models and the admin moderation queue all stay in place, so existing
    | threads survive and turning this back on restores the feature exactly as
    | it was.
    |
    | While it is off, the comment form and existing threads are hidden from
    | the public pages and the POST endpoint refuses new comments, so the flag
    | cannot be bypassed by posting directly.
    |
    */

    'comments' => (bool) env('FEATURE_COMMENTS', false),

];
