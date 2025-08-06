<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EjemploTest extends TestCase
{
    public function test_la_pagina_de_inicio_carga_correctamente()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
