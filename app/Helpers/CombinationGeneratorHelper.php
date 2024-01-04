<?php

namespace App\Helpers;

class CombinationGeneratorHelper
{
    /**
     * Generate combinations from array of combinations and placeholder
     *
     * @param array $combinations
     * @param string $placeholder
     * @return array
     */
    public static function generateCombination(array $combinations, string $placeholder): array
    {
        $values = $columnDisplayName['map'][$placeholder] ?? [$placeholder];
        $newCombinations = [];
        foreach ($combinations as $combination) {
            foreach ($values as $value) {
                $newCombination = $combination;
                $newCombination[$placeholder] = $value;
                $newCombinations[] = $newCombination;
            }
        }

        return $newCombinations;
    }

}
