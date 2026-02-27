<?php

test('the application returns a successful response', function () {
    $response = $this->get(route('app.entry'));

    $response->assertRedirect(route('login'));
});
