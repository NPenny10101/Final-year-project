<?php
// database_functions.php
include 'config.php'; // include the database connection file

function checkUrlExists($url) {
    global $conn;
    $sqlCheck = "SELECT web_Id FROM website WHERE url = ?";
    $stmtCheck = $conn->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $url);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();
    $exists = $result->fetch_assoc();
    $stmtCheck->close();
    return $exists;
}

function insertWebsite($url, $audience, $site_location) {
    global $conn;
    $sql = "INSERT INTO `website` (`url`, `audience`, `site_location`) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $url, $audience, $site_location);
    $stmt->execute();
    $web_Id = $conn->insert_id;
    $stmt->close();
    return $web_Id;
}

function insertReport( $web_Id, $details) {
    global $conn;
    $sql = "INSERT INTO `reports` (`web_Id`, `dateRange`, `date`, `report_date`, `visits`, `uniqueVisitors`, `averageTime`, `pageViews`, `bounceRate`, `performance`, `accessibility`, `SEO`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissiiiiiiii", $web_Id, $details['daysDifference'], $details['earliestDate'], $details['currentDateTime'], $details['numVisits'], $details['numUniqueVisitorIds'], $details['averageTimeOnSite'], $details['averagePageViews'], $details['bouncerate'], $details['performance'], $details['accessibility'], $details['seo']);
    $stmt->execute();
    $report_Id = $conn->insert_id;
    $stmt->close();
    return $report_Id;
}

function insertSource($report_Id, $sourceDetails) {
    global $conn;
    $sql = "INSERT INTO `source` (`report_Id`, `direct`, `organic`, `paid`, `referral`, `chrome`, `firefox`, `internetExplorer`, `safari`, `mobile`, `tablet`, `desktop`, `topCountry`, `entry_page`, `exit_page`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiiiiiiiiiisss", $report_Id, $sourceDetails['direct'], $sourceDetails['organic'], $sourceDetails['paid'], $sourceDetails['referral'], $sourceDetails['chrome'], $sourceDetails['firefox'], $sourceDetails['IE'], $sourceDetails['safari'], $sourceDetails['mobile'], $sourceDetails['tablet'], $sourceDetails['desktop'], $sourceDetails['topCountry'], $sourceDetails['topEntryPage'], $sourceDetails['topExitPage']);
    $stmt->execute();
    $stmt->close();
}

function fetchAllData() {
    global $conn;
    $sql = "SELECT w.*, r.*, s.*
            FROM website w
            JOIN reports r ON w.web_Id = r.web_Id
            JOIN source s ON r.report_Id = s.report_Id";
    $result = $conn->query($sql);

    return $result;
}
