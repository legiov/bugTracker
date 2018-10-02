<?php
/**
 * Created by PhpStorm.
 * User: legio
 * Date: 01.10.18
 * Time: 23:32
 */

namespace Test;


use PHPUnit\Framework\TestCase;
use Tek\BugTracker;
use Tek\WrongBugConditionException;

class BugTrackerTest extends TestCase
{
    /**
     * @var BugTracker
     */
    private $bugTracker;

    protected function setUp()
    {
        $this->bugTracker = new BugTracker();
    }

    public function testBadCondition():void
    {
        $this->expectException(WrongBugConditionException::class);
        $this->bugTracker->run(8, 20);

    }



    public function testRunSpeed()
    {
        $t1 = microtime(true);
        $result = $this->bugTracker->run(4000000000, 3000000000);
        $t2 = microtime(true);

        $timeResult = $t2 - $t1;
        echo $timeResult;
        var_dump($result);
        self::assertLessThan(0.001, $timeResult);
    }

    public function testRun2Speed()
    {
        $t1 = microtime(true);
        $result = $this->bugTracker->run2(100000000, 100000000);
        $t2 = microtime(true);
//        self::assertSame(0, $result->getLeft());
//        self::assertSame(0, $result->getRight());
        $timeResult = $t2 - $t1;
        echo $timeResult;
        var_dump($result);
        self::assertLessThan(2, $timeResult);
    }

    public function testRun3Speed()
    {
        $t1 = microtime(true);
        $this->bugTracker->run3(10000000, 20000);
        $t2 = microtime(true);
        $timeResult = $t2 - $t1;
        echo $timeResult;

        self::assertLessThan(25, $timeResult);
    }

    public function testRun4Speed()
    {
        $t1 = microtime(true);
        $this->bugTracker->run4(4000000000, 5000000);
        $t2 = microtime(true);
        $timeResult = $t2 - $t1;
        echo $timeResult;

        self::assertLessThan(25, $timeResult);
    }

    /**
     * @dataProvider getTestData
     */
    public function testRunWithRun3($rocks, $bugs)
    {
        $result3 = $this->bugTracker->run3($rocks, $bugs);
        $result1 = $this->bugTracker->run($rocks, $bugs);

        self::assertEquals($result3, $result1, 'was bugs -'.$bugs);

    }

    /**
     * @dataProvider getTestData
     */
    public function testRun4($rocks, $bugs)
    {
        $result3 = $this->bugTracker->run4($rocks, $bugs);
        $result1 = $this->bugTracker->run3($rocks, $bugs);

        self::assertEquals($result3, $result1, 'was bugs -'.$bugs);
    }

    public function getTestData()
    {
        return array(
            array(8, 1),
            array(8, 2),
            array(8, 3),
            array(64661, 5691),
            array(658681, 6810),
            array(215541, 154),
            array(500, 35),
            array(500, 55),
        );
    }


}