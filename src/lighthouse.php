<?php
//Lighthouse report to gather key metrics regarding the url submitted
print_r(getLighthouseScores('https://databox.com'));
function getLighthouseScores($url) {
// Define the command to execute Lighthouse for specific categories
$command = "lighthouse --output=json --output-path=report.json \"$url\"";

exec($command . ' 2>&1', $output, $return);  
if ($return === 0) {
    // Check if the report file was generated
    if (!file_exists('report.json')) {
        echo "Report file not found.";
        return false;
    }

    // Read the generated report
    $report = file_get_contents('report.json');

    // Decode the JSON report
    $reportData = json_decode($report, true);

    // Initialize scores array
    $scores = [
        'performance_score' => null,
        'accessibility_score' => null,
        'seo_score' => null,
        'FCP_score' => null, //first content paint
        'FCP_value' => null,
        'SI_score' => null, // speed-index
        'SI_value' => null,
        'LCP_score' => null, //largest content paint
        'LCP_value' => null, 
        'TBT_score' => null, //total blocking time
        'TBT_value' => null,
    ];

    // Check if the report data is valid and contains necessary categories
    if ($reportData !== null) {
        if (isset($reportData['categories']['performance'])) {
            $scores['performance_score'] = $reportData['categories']['performance']['score'] * 100;
        }
        if (isset($reportData['categories']['accessibility'])) {
            $scores['accessibility_score'] = $reportData['categories']['accessibility']['score'] * 100;
        }
        if (isset($reportData['categories']['seo'])) {
            $scores['seo_score'] = $reportData['categories']['seo']['score'] * 100;
        }
        if (isset($reportData['audits']['first-contentful-paint'])) {
            $scores['FCP_score'] = $reportData['audits']['first-contentful-paint']['score'] * 100;
            $scores['FCP_value'] = $reportData['audits']['first-contentful-paint']['displayValue'];
        }
        if (isset($reportData['audits']['speed-index'])) {
            $scores['SI_score'] = $reportData['audits']['speed-index']['score'] * 100;
            $scores['SI_value'] = $reportData['audits']['speed-index']['displayValue'];
        }
        if (isset($reportData['audits']['largest-contentful-paint'])) {
            $scores['LCP_score'] = $reportData['audits']['largest-contentful-paint']['score'] * 100;
            $scores['LCP_value'] = $reportData['audits']['largest-contentful-paint']['displayValue'];
        }
        if (isset($reportData['audits']['total-blocking-time'])) {
            $scores['TBT_score'] = $reportData['audits']['total-blocking-time']['score'] * 100;
            $scores['TBT_value'] = $reportData['audits']['total-blocking-time']['displayValue'];
        }
    }

    // Optionally, delete the report file to clean up
    //unlink('report.json');

    //print_r($scores);
    return $scores;
} else {
    // Lighthouse command failed or did not generate a report
    return false;
}
}      