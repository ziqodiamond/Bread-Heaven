<?php

use App\Helpers\ColorHelper;

if (!function_exists('getColorRGB')) {
    /**
     * Get RGB color for flash sale badge
     * 
     * @param string $colorName
     * @param string $type
     * @return string
     */
    function getColorRGB($colorName, $type = 'start')
    {
        return ColorHelper::getColorRGB($colorName, $type);
    }
}
