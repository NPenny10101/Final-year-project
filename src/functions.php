<?php

//Function to count the occurances of each value in an array and order the new array in descending order
function arrayConverter($arr) {
    $occurrences = array_count_values($arr);
    $linkedArray = [];
    foreach ($occurrences as $item => $count) {
        $linkedArray[] = ['name' => $item, 'count' => $count];
    }
    // Sort the linked array by count in descending order
    usort($linkedArray, function($a, $b) {
    return $b['count'] - $a['count'];
    });
    // Extract the first name from the linked array and ensuring it is a string

    $firstName = !empty($linkedArray) ? $linkedArray[0]['name'] : '';

    return ['linkedArray' => $linkedArray, 'firstName' => $firstName];
}


// Function to calculate mean
function mean($data) {
    // Filter out non-integer values
    $filteredData = array_filter($data, 'is_int');
    return array_sum($filteredData) / count($filteredData);
    }


// Function to calculate the standard deviation of an array along with the upper and lower bound
function standardDeviation($arr) {
    // Filter out non-integer values
    $filteredArr = array_filter($arr, 'is_int');
    
    // Calculate the mean
    $mean = mean($filteredArr);
    
    // Calculate the squared deviations
    $squaredDeviations = array_map(function($x) use ($mean) {
        return pow($x - $mean, 2);
    }, $filteredArr);
    
    // Calculate the variance
    $variance = sqrt(array_sum($squaredDeviations) / count($filteredArr));
    
    return $variance;
}


// Function to calculate confidence interval
function confidenceInterval($data) {
    $confidenceLevel = 0.95;
    $mean = mean($data);
    $stdDev = standardDeviation($data);
    $n = count($data);
    $z = 0; // z-score for 95% confidence level
    if ($confidenceLevel == 0.95) {
        $z = 1.96; // For 95% confidence level
    } else {
        // You can define z-scores for other confidence levels here
    }
    $marginError = $z * ($stdDev / sqrt($n));
    $lowerBound = $mean - $marginError;
    $upperBound = $mean + $marginError;
    return [$lowerBound, $mean, $upperBound];
}        

function calculatePercentageAboveThreshold($numbers, $deviceType) {
    // Define the threshold based on the device type
    if ($deviceType == 'Desktop'){
        $threshold = 360;
    }elseif($deviceType == 'Mobile'){
        $threshold = 150;
    }

    // Initialize counter for numbers above or equal to the threshold
    $countAboveThreshold = 0;

    // Iterate through the numbers array
    foreach ($numbers as $number) {
        // Check if the number is above or equal to the threshold
        if ($number >= $threshold) {
            $countAboveThreshold++;
        }
    }
    // Calculate the percentage
    $percentage = ($countAboveThreshold / count($numbers)) * 100;
    return $percentage;
}

