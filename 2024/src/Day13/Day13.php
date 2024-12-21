<?php

namespace AdventOfCode2024;

use Exception;

class Day13 {
    private int $TOKEN_COSTS_PUSH_BUTTON_A = 3;
    
    private int $TOKEN_COSTS_PUSH_BUTTON_B = 1;


    public function main(string $filename) {
        $startTime = microtime(true);
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        
        $configs = $this->getConfigs($input);
        $tokens = $this->calculateTokens($configs);
        echo "Part1 - The fewest tokens you would have to spend to win all possible prizes is " . $tokens . " \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";

        $configs = $this->getConfigs($input, 10000000000000);
        $tokens = $this->calculateTokens($configs);
        echo "Part2 - The fewest tokens you would have to spend to win all possible prizes is " . $tokens . " \n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";
    }

    private function calculateTokens(array $configs): int {
        $tokens = 0;

        foreach ($configs as $config) {
            $buttonAX = $config['button_a']['x'];
            $buttonAY = $config['button_a']['y'];
            $buttonBX = $config['button_b']['x'];
            $buttonBY = $config['button_b']['y'];
            $prizeX = $config['prize']['x'];
            $prizeY = $config['prize']['y'];

            $a = $this->solveForA($prizeX, $prizeY, $buttonAX, $buttonAY, $buttonBX, $buttonBY);

            if (!$a) {
                continue;
            }

            $b = $this->solveForB($a, $prizeY, $buttonAY, $buttonBY);

            if (!$b) {
                continue;
            }

            $tokens += $this->TOKEN_COSTS_PUSH_BUTTON_A * $a + $this->TOKEN_COSTS_PUSH_BUTTON_B * $b;
        }

        return $tokens;
    }

    /**
     * Math 7th class or so
     * thanks chatGPT for explaining me math again...
     */
    private function solveForA(int $prizeX, int $prizeY, int $buttonAX, int $buttonAY, int $buttonBX, int $buttonBY): bool|int {
        if ($buttonBX === 0 || ($buttonAY - ($buttonBY * $buttonAX) / $buttonBX) === 0) {
            // devide by zero
            return false;
        }

        $numerator = $prizeY - ($buttonBY * $prizeX) / $buttonBX;
        $denominator = $buttonAY - ($buttonBY * $buttonAX) / $buttonBX;
        $a = $numerator / $denominator;

        // The original formula contains two divisions, which can introduce small rounding errors.
        // To mitigate this, the result is checked and rounded to the nearest integer if the deviation is less than 0.001.
        // Alternatively, the formula below can be used, which only involves one division
        // and is therefore less prone to rounding errors:
        // $a = ($prizeX * $buttonBY - $prizeY * $buttonBX) / ($buttonAX * $buttonBY - $buttonAY * $buttonBX);
        // This alternative formula was sourced from reddit/github

        if (abs($a - round($a)) < 0.001) {
            return (int)round($a);
        } else {
            return false;
        }
    }

    private function solveForB(int $a, int $prizeY, int $buttonAY, int $buttonBY): int {
        $b = ($prizeY - $a * $buttonAY) / $buttonBY;

        if (is_int($b)) {
            return $b;
        } else {
            return false;
        }
    }

    private function getConfigs(string $input, int $addNumber = 0): array {
        $configs = explode("\n\n", $input);

        for ($i = 0; $i < count($configs); $i++) {
            $config = $configs[$i];

            preg_match_all('/\d+/', $config, $matches);

            $buttonAX = $matches[0][0];
            $buttonAY = $matches[0][1];
            $buttonBX = $matches[0][2];
            $buttonBY = $matches[0][3];
            $prizeX = $addNumber + $matches[0][4];
            $prizeY = $addNumber + $matches[0][5];

            $configArray = [
                'button_a' => ['x' => $buttonAX, 'y' => $buttonAY],
                'button_b' => ['x' => $buttonBX, 'y' => $buttonBY],
                'prize' => ['x' => $prizeX, 'y' => $prizeY]
            ];

            $configs[$i] = $configArray;
        }

        return $configs;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day13 = new Day13();
$Day13->main($filename);