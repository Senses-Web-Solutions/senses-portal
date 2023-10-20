<?php

Route::group([
    'prefix' => 'api/v2/' . config('senses.client'),
    'middleware' => ['auth:api']
], function() {

});
