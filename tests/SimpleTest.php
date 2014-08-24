<?php

use crodas\Intl;

class SimpleTest extends \phpunit_framework_testcase
{
    public function testNoInit()
    {
        $this->assertTrue(class_exists('crodas\Intl')); // load class
        $this->assertEquals(__("foobar"), "foobar");
        $this->assertEquals(__("foobar %s", 'foo'), "foobar foo");
    }
    
    public function testScanner()
    {
        Intl::init("/tmp/languages.php", "es");
        Intl::build(__DIR__, __DIR__ . "/intl");
        $this->assertEquals(1, count(glob(__DIR__. "/intl/*.yml")));
        $this->assertTrue(is_file(__DIR__. "/intl/template.yml"));
    }

    public function testCompile()
    {
        copy(__DIR__ . "/es1.yml", __DIR__ . "/intl/es.yml");
        Intl::build(__DIR__, __DIR__ . "/intl");
        Intl::compile(__DIR__ . "/intl");
        $this->assertEquals(__("Hi %s, welcome", "crodas"), "Hola crodas, bienvenido");
    }

    /**
     *  Add a text that is not handled by the scanner (we use a variable). 
     *  We have a es2.yml which already have its definition and we recompile. 
     *  It must not override its previous content
     */
    public function testCompileAppend()
    {
        copy(__DIR__ . "/es2.yml", __DIR__ . "/intl/es.yml");
        Intl::build(__DIR__, __DIR__ . "/intl");
        Intl::compile(__DIR__ . "/intl");
        $text = "Hi %s, welcome!"; 
        $this->assertEquals(__($text, "crodas"), "Hola crodas, bienvenido!");
        $this->assertEquals(__("Hi %s, welcome", "crodas"), "Hi crodas, welcome"); // we dont have this definition
    }
}
