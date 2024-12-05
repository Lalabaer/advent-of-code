<?php

namespace AdventOfCode2024;

use Exception;

class Day5 {
    public function main(string $filename) {
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $updateOrderingRulesAndUpdates = explode("\n\n", $input);
        $updateOrderingRules = explode("\n", $updateOrderingRulesAndUpdates[0]);
        $updateOrderingRulesEasy = $this->combineRulesByIndex($updateOrderingRules);
        $updates = explode("\n", $updateOrderingRulesAndUpdates[1]);
        
        // part 1 & 2
        $correctOrIncorrectUpdates = $this->splitCorrectAndIncorrectUpdates($updateOrderingRulesEasy, $updates);
        
        // part 1
        $sumCorrectUpdates = empty($correctOrIncorrectUpdates['correct']) ? 0 : $this->calculateMiddleNumbers($correctOrIncorrectUpdates['correct']);
        
        // part 2
        $fixedUpdates = $this->sortIncorrectUpdates($correctOrIncorrectUpdates['incorrect'], $updateOrderingRulesEasy);
        $sumFixedUpdates = empty($fixedUpdates) ? 0 : $this->calculateMiddleNumbers($fixedUpdates);

        echo "Part1 - The sum of middle page numbers of correct updates is " . $sumCorrectUpdates . "\n";
        echo "Part1 - The sum of middle page numbers of fixed updates is " . $sumFixedUpdates . "\n";
    }

    private function combineRulesByIndex(array $updateOrderingRules) {
        $updateOrderingRulesEasy = [];

        foreach ($updateOrderingRules as $rule) {
            $ruleArray = explode('|', $rule);
            $updateOrderingRulesEasy[$ruleArray[0]][] = $ruleArray[1];
        }

        return $updateOrderingRulesEasy;
    }

    private function splitCorrectAndIncorrectUpdates(array $updateOrderingRulesEasy, array $updates) {
        $correctUpdates = [];
        $incorrectUpdates = [];

        foreach ($updates as $updateIndex => $updateString) {
            $updateArray = explode(',', $updateString);

            if ($this->checkIsUpdateValid($updateArray, $updateOrderingRulesEasy)) {
                $correctUpdates[] = $updateArray;
            } else {
                $incorrectUpdates[] = $updateArray;
            }
        }

        return ['correct' => $correctUpdates, 'incorrect' => $incorrectUpdates];
    }

    private function checkIsUpdateValid(array $updateArray, array $updateOrderingRulesEasy): bool {
        $updateArrayLength = count($updateArray);

        foreach ($updateArray as $i => $pageNumber) {
            $leftNumber = $updateArray[$i];
            $rightNumber = isset($updateArray[$i+1]) ? $updateArray[$i+1] : null;

            if (!isset($updateOrderingRulesEasy[$leftNumber]) || !in_array($rightNumber, $updateOrderingRulesEasy[$leftNumber])) {
                return false;
            }

            // +2 because we are comparing the current number with the next number
            // on last element we don't have a right neighbour to compare
            if ($updateArrayLength === $i+2) {
                return true;
            }
        }

        return false;
    }

    private function calculateMiddleNumbers(array $correctUpdates): int {
        $sumCorrectUpdates = 0;

        foreach ($correctUpdates as $correctUpdate) {
            $middle = (int) (count($correctUpdate) / 2);
            $sumCorrectUpdates += $correctUpdate[$middle];
        }

        return $sumCorrectUpdates;
    }

    private function sortIncorrectUpdates(array $incorrectUpdates, array $updateOrderingRulesEasy): array {
        $reorderedUpdates = [];
        
        foreach ($incorrectUpdates as $update) {
            $updateCount = count($update);

            /**
             * Reminder to myself:
             * foreach ($update as $index => $leftNumber) { ... } doesn't work here because
             * foreach does not allow modifying the original array structure during iteration.
             * Using a for loop allows direct index manipulation in the $update array.
             */
            for ($index = 0; $index < $updateCount - 1; $index++) {
                $leftNumber = $update[$index];
                $rightNumber = $update[$index + 1];

                if (!isset($updateOrderingRulesEasy[$leftNumber]) || !in_array($rightNumber, $updateOrderingRulesEasy[$leftNumber])) {
                    // do swap, start again from the beginning
                    $update[$index] = $rightNumber;
                    $update[$index + 1] = $leftNumber;
                    // reordered - start from the beginning
                    $index = -1; 
                }

                // +2 because we are comparing the current number with the next number
                // on last element we don't have a right neighbour to compare
                if ($updateCount === $index+2) {
                    $reorderedUpdates[] = $update;
                    continue 2;
                }
            }
        }

        return $reorderedUpdates;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day5 = new Day5();
$Day5->main($filename);