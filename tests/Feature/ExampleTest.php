<?php

test('returns a successful response', function () {
    $response = $this->get(route('auth.login.show'));

    $response->assertOk();
});