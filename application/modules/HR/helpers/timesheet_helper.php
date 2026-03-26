<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Round a time string to the nearest 15-minute quarter.
 * If more than 5 minutes past a quarter (0, 15, 30, 45), round up to next quarter.
 * If 5 minutes or less past a quarter, round back to that quarter.
 * 
 * @param string $timeStr Time string (H:i:s, h:i A, or full datetime)
 * @return string|null Rounded time in H:i:s format, or null if input is empty
 */
function round_time_to_quarter($timeStr) {
    if (empty($timeStr)) return null;
    
    $timestamp = strtotime('2000-01-01 ' . $timeStr);
    if ($timestamp === false) return null;
    
    $minutes = (int)date('i', $timestamp);
    $minutesPastQuarter = $minutes % 15;
    
    if ($minutesPastQuarter > 5) {
        $roundedMinutes = $minutes + (15 - $minutesPastQuarter);
    } else {
        $roundedMinutes = $minutes - $minutesPastQuarter;
    }
    
    $hours = (int)date('H', $timestamp);
    if ($roundedMinutes >= 60) {
        $hours++;
        $roundedMinutes -= 60;
    }
    
    if ($hours >= 24) {
        $hours -= 24;
    }
    
    return sprintf('%02d:%02d:00', $hours, $roundedMinutes);
}
