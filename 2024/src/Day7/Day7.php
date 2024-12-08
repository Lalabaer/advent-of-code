<?php

namespace AdventOfCode2024;

use Exception;

class Day7 {
    public function main(string $filename) {
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $originalRowsArray = explode("\n", $input);
        $data = $this->parseInputData($originalRowsArray);
        $validEquationsSum = $this->getSumOfValidEquations($data);
        $validEquationsWithConcatenationSum = $this->getSumOfValidEquations($data, true);

        echo "Part1 - The total calibration result of valid equations is " . $validEquationsSum . "\n";
        echo "Part2 - The total calibration result of valid equations with concatinations is " . $validEquationsWithConcatenationSum . "\n";
    }

    private function parseInputData($originalRowsArray): array {
        $data = [];

        foreach ($originalRowsArray as $row) {
            $row = explode(':', $row);
            $data[] = ['result' => trim($row[0]), 'numbers' => explode(' ', trim($row[1]))];
        }

        return $data;
    }

    private function getSumOfValidEquations(array $inputData, $concatenation = false): int {
        $sum = 0;

        foreach ($inputData as $data) {
            $result = (int)$data['result'];
            $numbers = $data['numbers'];
            $countNumbers = count($numbers);

            $root = $numbers[0];
            $children = [];

            if ($countNumbers === 0) {
                continue;    
            }

            if ($countNumbers === 1 && $root === $result) {
                $validEquations[] = $data;
                continue;
            }

            $children[0][] = $children[0][] = $root;

            for ($i = 1; $i < $countNumbers; $i++) {
                if (!empty($children[$i-1])) {

                    foreach ($children[$i-1] as $child) {
                        $children[$i][] = $child > $result
                            ? $child
                            : $this->calculate($child, $numbers[$i], '+');
                        $children[$i][] = $child > $result
                            ? $child
                            : $this->calculate($child, $numbers[$i], '*');

                        if ($concatenation) {
                            $children[$i][] = $child > $result
                            ? $child
                            : $this->calculate($child, $numbers[$i], '||');
                        }
                    }
                }
            }

            if (in_array($result, $children[$countNumbers-1])) {
                $sum += $result;
            }
        }

        return $sum;
    }

    private function calculate(int $number1, int $number2, string $operator ): int {
        switch ($operator) {
            case '+':
                return $number1 + $number2;
            case '*':
                return $number1 * $number2;
            case '||':
                return $number1 . $number2;
            default:
                throw new Exception('Invalid operator');
        }
    }

}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day7 = new Day7();
$Day7->main($filename);