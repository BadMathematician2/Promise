<?php


namespace App\Packages\Promise\Tests;


use App\Packages\Promise\Promise;
use Brick\Math\Exception\DivisionByZeroException;
use Tests\TestCase;

class PromiseTest extends TestCase
{

    public function testPromiseTest()
    {
        $closures = [];

        $closures[] = function ($a) {
            return $a;
        };
        $closures[] = function ($a) {
            return 2 * $a;
        };
        $closures[] = function ($a) {
            return [$a * 2, $a * $a];
        };
        $closures[] = function ($arr) {
            return array_sum($arr);
        };
        $closures[] = function ($a, $b) {
            return [$a * $b, $a / $b];
        };

        $promise = new Promise();
        $a = 10;
        $this->assertEquals($promise
        ->promise($closures[2], [2])
        ->promise($closures[3])->getData(), 8);

        $this->assertEquals($promise->promise($closures[0], [$a], [\Exception::class => DivisionByZeroException::class])
            ->promise($closures[1])
            ->promise($closures[2])
            ->map($closures[1])
            ->promise($closures[3])->getData(), 880 );

    }
}
