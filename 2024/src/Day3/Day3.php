<?php

namespace AdventOfCode2024;

use Exception;

class Day3 {
    public function main(string $filename) {
        $startTime = microtime(true);

        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        $result = $this->getResultOfUncorruptedMemoryInstructions($input);
        $resultWithDoAndDontInstructions = $this->getResultOfUncorruptedMemoryInstructionsUsingDoAndDont($input);

        echo "Part1 - The result of uncorrupted memory instructions is " . $result . "\n"; 
        echo "Part2 - The result of uncorrupted memory instructions with do/don't instruction is " . $resultWithDoAndDontInstructions . "\n";

        echo (microtime(true) - $startTime) * 1000, " ms \n";
    }

    private function getResultOfUncorruptedMemoryInstructions(string $input): int {
        $pattern = '/mul\((\d{1,3}),(\d{1,3})\)/';
        preg_match_all($pattern, $input, $matches);
        $sum = 0;

        foreach ($matches[0] as $match) {
            $multiplicators = explode(",", str_replace(['mul(', ')'], '', $match));
            $sum += $multiplicators[0] * $multiplicators[1];
        }

        return $sum;
    }

    private function getResultOfUncorruptedMemoryInstructionsUsingDoAndDont(string $input): int {
        $pattern = '/do\(\)|don\'t\(\)|mul\((\d{1,3}),(\d{1,3})\)/';
        preg_match_all($pattern, $input, $matches);
        $sum = 0;
        $instruction = true;

        foreach ($matches[0] as $match) {
            if ($match == 'do()') {
                $instruction = true;
            } elseif ($match == 'don\'t()') {
                $instruction = false;
            } elseif ($instruction) {
                $multiplicators = explode(",", str_replace(['mul(', ')'], '', $match));
                $sum += $multiplicators[0] * $multiplicators[1];
            }
        }

        return $sum;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day3 = new Day3();
$Day3->main($filename);