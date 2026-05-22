<?php

namespace App\Helpers;

class ColorHelper
{
    /**
     * Color palette mapping for flash sales
     */
    private static $colorPalette = [
        'red' => [
            'start' => 'rgb(239, 68, 68)',      // red-500
            'end' => 'rgb(220, 38, 38)',        // red-600
            'text' => 'rgb(220, 38, 38)',       // red-600
        ],
        'orange' => [
            'start' => 'rgb(249, 115, 22)',     // orange-500
            'end' => 'rgb(234, 88, 12)',        // orange-600
            'text' => 'rgb(234, 88, 12)',       // orange-600
        ],
        'yellow' => [
            'start' => 'rgb(234, 179, 8)',      // yellow-500
            'end' => 'rgb(202, 138, 4)',        // yellow-600
            'text' => 'rgb(202, 138, 4)',       // yellow-600
        ],
        'amber' => [
            'start' => 'rgb(245, 158, 11)',     // amber-500
            'end' => 'rgb(217, 119, 6)',        // amber-600
            'text' => 'rgb(217, 119, 6)',       // amber-600
        ],
        'green' => [
            'start' => 'rgb(34, 197, 94)',      // green-500
            'end' => 'rgb(22, 163, 74)',        // green-600
            'text' => 'rgb(22, 163, 74)',       // green-600
        ],
        'blue' => [
            'start' => 'rgb(59, 130, 246)',     // blue-500
            'end' => 'rgb(37, 99, 235)',        // blue-600
            'text' => 'rgb(37, 99, 235)',       // blue-600
        ],
        'purple' => [
            'start' => 'rgb(147, 51, 234)',     // purple-500
            'end' => 'rgb(126, 34, 206)',       // purple-600
            'text' => 'rgb(126, 34, 206)',      // purple-600
        ],
        'pink' => [
            'start' => 'rgb(236, 72, 153)',     // pink-500
            'end' => 'rgb(219, 39, 119)',       // pink-600
            'text' => 'rgb(219, 39, 119)',      // pink-600
        ],
    ];

    /**
     * Get RGB color for flash sale badge
     * 
     * @param string $colorName
     * @param string $type 'start', 'end', or 'text'
     * @return string RGB color value
     */
    public static function getColorRGB($colorName, $type = 'start')
    {
        if (empty($colorName)) {
            $colorName = 'red';
        }
        
        // If it's a hex color, return as is
        if (strpos($colorName, '#') === 0) {
            return $colorName;
        }
        
        // Get from palette or default to red
        $color = self::$colorPalette[strtolower($colorName)] ?? self::$colorPalette['red'];
        
        return $color[$type] ?? $color['start'];
    }

    /**
     * Get all available colors
     */
    public static function getAvailableColors()
    {
        return array_keys(self::$colorPalette);
    }
}
