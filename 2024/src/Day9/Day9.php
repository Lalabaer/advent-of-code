<?php

namespace AdventOfCode2024;

use Exception;

class Day9 {
    public function main(string $filename) {
        $startTime = microtime(true);
        $input = file_get_contents(__DIR__.'/'.$filename) ?: throw new Exception('Can not read input file');
        
        // part 1
        $disk = $this->parseMapToDisk($input);
        $rearrangedDisk = $this->rearrangeDiskOneByOne($disk);
        $checksum = $this->checksum($rearrangedDisk);
        echo "Part1 - checksum for the disk is " . $checksum . "\n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";

        // part 2
        $disk = $this->parseMapToDiskInFilesAndEmptySpaces($input);
        $rearrangedDisk = $this->rearrangeDiskPerFile($disk);
        $checksum = $this->checksum($rearrangedDisk);
        echo "Part2 - improved checksum is " . $checksum . "\n";
        echo (microtime(true) - $startTime) * 1000, " ms \n";
    }

    private function parseMapToDisk(string $input): array {
        $disk = [];
        $isFile = true;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $disk = $isFile 
                ? array_merge($disk, array_fill(0, (int)$input[$i], (int)($i / 2)))
                : array_merge($disk, array_fill(0, (int)$input[$i], '.'));
            $isFile = !$isFile;
        }

        return $disk;
    }

    private function parseMapToDiskInFilesAndEmptySpaces(string $input): array {
        $disk = [];
        $isFile = true;
        
        for ($i = 0; $i < strlen($input); $i++) {
            $disk[] = $isFile 
                ? array_fill(0, (int)$input[$i], (int)($i / 2))
                : array_fill(0, (int)$input[$i], '.');
            $isFile = !$isFile;
        }

        return array_values(array_filter($disk, fn($value) => !empty($value)));
    }

    private function rearrangeDiskOneByOne(array $disk): array {
        for ($i = 0; $i < count($disk); $i++) {
            if ($disk[$i] !== '.') {
                continue;
            }

            for ($j = count($disk) - 1; $j >= 0; $j--) {
                if ($disk[$j] === '.') {
                    unset($disk[$j]);
                    continue;
                }

                $disk[$i] = $disk[$j];
                unset($disk[$j]);
                break;
            }
        }

        // removes free disk space
        return array_values(array_filter($disk, fn($value) => $value !== '.'));
    }

    private function rearrangeDiskPerFile(array $disk): array {
        for ($j = count($disk) - 1; $j >= 0; $j--) {
            if (empty($disk[$j][0]) || $disk[$j][0] === '.' || !empty($disk[$j]['swapped'])) {
                continue;
            }

            for ($i = 0; $i < count($disk); $i++) {
                if (empty($disk[$i]) || $disk[$i][0] !== '.') {
                    continue;
                }

                if ($i >= $j) {
                    break;
                }
    
                $countedBlocksToReplace = count($disk[$i]);
                $countedBlocksReplacement = count($disk[$j]);

                if ($countedBlocksReplacement > $countedBlocksToReplace) {
                    continue;
                }

                if ($countedBlocksReplacement === $countedBlocksToReplace) {
                    $prevValue = $disk[$i];
                    $disk[$i] = $disk[$j];
                    $disk[$i]['swapped'] = true;
                    $disk[$j] = $prevValue;

                    break;
                }

                if ($countedBlocksReplacement < $countedBlocksToReplace) {
                    $remainingBlocks = array_slice($disk[$i], $countedBlocksReplacement);
                    $disk[$i] = $disk[$j];
                    $disk[$i]['swapped'] = true;
                    $disk[$j] = array_fill(0, $countedBlocksReplacement, '.');

                    array_splice($disk, $i + 1, 0, [$remainingBlocks]);
                    $j++;

                    break;
                }
            }
        }

        $flattenedDisk = $this->flattenDisk($disk);
        return array_values($flattenedDisk);
    }

    private function flattenDisk(array $disk): array {
        $flattenedDisk = [];

        foreach ($disk as $blocks) {
            foreach ($blocks as $key => $block) {
                if ($key !== 'swapped') {
                    $flattenedDisk[] = $block;
                }
            }
        }

        return $flattenedDisk;
    }

    private function checksum(array $disk): int {
        $sum = 0;

        for ($i = 0; $i < count($disk); $i++) {
            $sum += (int)$disk[$i] * $i;
        }

        return $sum;
    }
}

count($argv) <= 1 ? $filename = 'example_data.txt' : $filename = $argv[1];

$Day9 = new Day9();
$Day9->main($filename);