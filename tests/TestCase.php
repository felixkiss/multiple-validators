<?php

class TestCase extends PHPUnit_Framework_TestCase
{
    function tearDown()
    {
        Mockery::close();
    }
}
