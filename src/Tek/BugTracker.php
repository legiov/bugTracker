<?php
/**
 * Created by PhpStorm.
 * User: legio
 * Date: 01.10.18
 * Time: 23:31
 */

namespace Tek;


class BugTracker
{
    /**
     * @param int $maxRange
     *
     * @return array
     */
    private function placeBug(int $maxRange): array
    {
        $bugPlaced    = $maxRange - 1;
        $restDivision = $bugPlaced % 2;

        $half  = ($bugPlaced - $restDivision) / 2;
        $left  = $half;
        $right = $half + $restDivision;

        return array($left, $right);
    }

    /**
     * Самый быстрый из точных алгоритмов
     * сложность O(n *log n)
     * @param int $rocks
     * @param int $bugs
     *
     * @return ResultInterface
     */
    public function run4(int $rocks, int $bugs):ResultInterface
    {
        $heap = new \SplMaxHeap();
        $heap->insert($rocks);
        $newRoks = 8;
        $odd = 0;

        for($i = 1; $i <= $bugs; $i++) {
            $rocks = $heap->extract() - 1;
            $newRoks = (int)floor($rocks / 2);
            $odd = $rocks % 2;
            $heap->insert($newRoks);
            $heap->insert($newRoks + $odd);
        }

        return $this->getResult($newRoks, $newRoks + $odd);
    }

    /**
     * Самый быстрый алгоритм, очень быстро работает на любом количестве данных
     * сложность O(log n) но есть погрешность т.к. не возможно определить точно что было на предыдущей итереции
     * @param int $rocks
     * @param int $bugs
     *
     * @return ResultInterface
     * @throws WrongBugConditionException
     */
    public function run(int $rocks, int $bugs):ResultInterface
    {
        if($this->wrongCondition($rocks, $bugs)) {
            throw new WrongBugConditionException('Слишком много жуков');
        }

        $log   = log($bugs, 2);
        // серии проходов
        $tries = (int)floor($log);

        $bugsForTurn = 0;
        $previousRocks = $rocks;
        for ($i = 0; $i < $tries; $i++) {
            $bugsForTurn        = 2 ** $i;
            $previousRocks = $rocks;
            $bugs      -= $bugsForTurn;
            $rocks   = (int)floor($rocks / 2);

        }

        // Если предыдущее число камней не четное сохраняем 1
        $odd = $previousRocks % 2;


        // на этом проходе часть промежутков на 1 больше и последний жук из первой половины занимающий большую часть(не совсем верное утверждение т.к.
        // количество камней на предыдущей итерации делится не равномерно половина +1, половина +0)
        if($odd === 1 && $bugs <= $bugsForTurn) {
            $rocks++;
        }

        [$left, $right] = $this->placeBug($rocks);

        return $this->getResult($left, $right);

    }




    /**
     * Оптимизация решения влоб, есть небольшая погрешность будет идти 8,4,3,4,3
     * но работает на порядок быстрее
     *
     * @param int $rocks
     * @param int $bugs
     *
     * @return ResultInterface
     * @throws WrongBugConditionException
     */
    public function run2(int $rocks, int $bugs):ResultInterface
    {
        if($this->wrongCondition($rocks, $bugs)) {
            throw new WrongBugConditionException('Слишком много жуков');
        }
        $arrRocks = [];
        $arrRocks[] = $rocks;
        $left = 0;
        $right = 0;
        for($i = 0; $i <= $bugs - 1; $i++) {
            // в $i всегда хранится максимальный интервал
            $maxRange = $arrRocks[$i];

            //чтобы не занимать память, если памяти много можно не удалять и будет быстрее
//            unset($arrRocks[$i]);
            list($left, $right) = $this->placeBug($maxRange);
            $arrRocks[] = $right;
            $arrRocks[] = $left;
        }

        return $this->getResult($left, $right);
    }


    /**
     * Наивный алгоритм
     * В дальнейшем служило эталоном правильных результатов
     *
     * @param int $rocks
     * @param int $bugs
     *
     * @return ResultInterface
     * @throws WrongBugConditionException
     */
    public function run3(int $rocks, int $bugs):ResultInterface
    {
        if($this->wrongCondition($rocks, $bugs)) {
            throw new WrongBugConditionException('Слишком много жуков');
        }

        $arrRocks = [];
        $arrRocks[] = $rocks;
        $left = 0;
        $right = 0;
        for($i = 1; $i <= $bugs; $i++) {
            // в 0 всегда хранится максимальный интервал
            $maxRange = $arrRocks[0];
            $bugPlaced = $maxRange - 1;
            $restDivision = $bugPlaced%2;
            unset($arrRocks[0]);
            $half = ($bugPlaced - $restDivision)/2;
            $left = $half;
            $right = $half + $restDivision;
            $arrRocks[] = $right;
            $arrRocks[] = $left;
            // сортируем чтобы в 0 оказался наиболиший интервал
            rsort($arrRocks);
        }

        return $this->getResult($left, $right);
    }

    /**
     * @param int $left
     * @param int $right
     *
     * @return ResultInterface
     */
    private function getResult(int $left, int $right):ResultInterface
    {
        return new class ($left, $right) implements ResultInterface {
            private $left;
            private $right;

            public function __construct($left, $right)
            {

                $this->left = $left;
                $this->right = $right;
            }

            /**
             * @return mixed
             */
            public function getLeft()
            {
                return $this->left;
            }

            /**
             * @return mixed
             */
            public function getRight()
            {
                return $this->right;
            }


        };
    }

    /**
     * @param int $rocks
     * @param int $bugs
     *
     * @return bool
     */
    private function wrongCondition(int $rocks, int $bugs): bool
    {
        return $bugs > $rocks;
    }
}