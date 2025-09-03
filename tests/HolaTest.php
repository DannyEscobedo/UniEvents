<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../src/HolaMundo.php';

class HolaTest extends TestCase {
    public function testSaludar() {
        $hola = new Hola();
        $this->assertEquals("Hola Mundo", $hola->saludar());
    }
}
