<?php
include "config.php";
include "functions.php";
include "clustering.php";
include "lighthouse.php";
include "database_functions.php";
include 'header.php'; 

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if CSV file is uploaded
  if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] == UPLOAD_ERR_NO_FILE) {
      $_SESSION['errors'][] = "Please upload a CSV file.";
      header("Location: home.php");
      exit();
  } elseif ($_FILES['csvFile']['type'] != 'text/csv' && $_FILES['csvFile']['type'] != 'application/vnd.ms-excel') {
    $_SESSION['errors'][] = "Please upload a CSV file.";
    header("Location: home.php");
    exit();
  }

  // Check if URL is provided 
  $url = $_POST['url'] ?? '';
  if (empty($url)) {
      $_SESSION['errors'][] = "Please provide a URL.";
      header("Location: home.php");
      exit();
  }

// Parse the URL and check components
$parsedUrl = parse_url($url);
if ($parsedUrl === false || !isset($parsedUrl['scheme'], $parsedUrl['host'])) {
    $_SESSION['errors'][] = "Invalid URL format.";
    header("Location: home.php");
    exit();
}

// Ensure the URL is the base/homepage URL
if ((isset($parsedUrl['path']) && trim($parsedUrl['path'], '/') != '') || isset($parsedUrl['query'])) {
  $_SESSION['errors'][] = "Please provide only the base/homepage URL without any additional paths or query parameters.";
  header("Location: home.php");
  exit();
}


  // Check if Audience is selected
  $audience = $_POST['audience'] ?? '';
  if (empty($audience) || $audience == 'Choose...') {
      $_SESSION['errors'][] = "Please select an audience.";
      header("Location: home.php");
      exit();
  }
        $tempName = $_FILES["csvFile"]["tmp_name"];
        $fileName = $_FILES["csvFile"]["name"];
        // Read the contents of the uploaded CSV file
        $csvData = file_get_contents($tempName);
        $csvArray = array(); 

        // Example: Parse CSV data
        // Process the CSV data
        $rows = str_getcsv($csvData, "\n"); // Split rows
        $header = null;
        foreach ($rows as $rowIndex => $row) {
            $data = str_getcsv($row); // Split columns

            // Use the first row of the CSV file as the header
            if ($rowIndex === 0) {
                if($row != 'visitId,visitNumber,visitStartTime,date,pageviews,timeOnSite,bounces,source,medium,isTrueDirect,browser,isMobile,deviceCategory,subContinent,country,isInteraction,isEntrance,isExit,pageTitle,fullVisitorId,channelGrouping'){
                  $_SESSION['errors'][] = "Please ensure your CSV abides by our standards otherwise the report will not work";
                  header("Location: home.php");
                  exit();
                }
                $header = $data;
                continue; // Skip processing the header row
            }            


            // Use the strings from the first row of the CSV file as the keys for the items within the array
            $rowData = array_combine($header, $data);

            // Append the grouped and keyed data to the $csvArray
            $csvArray[] = $rowData;
        }

       $c = 0;
       $realVisits = 0;
       $uniqueVisitorIds = [];
       $visitId = [];
       $bounces = [];
       $dates = [];
       $totalTime = 0;
       $timeOSArrayMobile = [];
       $timeOSArrayDesktop = [];
       $pageViewsArray = [];
       $totalPageViews = 0;

      $deviceCatagory = [];
      $medium = [];
      $browser = [];
      $subContinent = [];
      $entryPage = [];
      $exitPage = [];
      $searchType = [];
      $bounceratePages = [];

      $chrome = 0;
      $IE = 0;
      $firefox = 0;
      $safari = 0;
      $edge = 0;
      $organic = 0;
      $direct = 0;
      $paid = 0;
      $referral = 0;
      $mobile = 0;
      $tablet = 0;
      $desktop = 0;
      // deviceCategory, source (direct,ask, partners or url), medium (refereal, organic, affiliate none), browser, subContinent, Country, cannelGrouping (organic refereal paid direct) ,entry page, exit page

      //for loops to go through each value in the csv array
      foreach ($csvArray as $row) {
          $uniqueVisitorIds[] = $row['fullVisitorId'];
          
          $visitId[] = $row['visitId'];
          
          $bounces[] = $row['bounces'];

          $dates[] = $row['date'];

          if (isset($csvArray[$c + 1])) {
            if ($row['bounces'] != 1 && $csvArray[$c + 1]['visitId'] != $csvArray[$c]['visitId']) {
                $totalTime = $totalTime + intval($row['timeOnSite']);
                $totalPageViews = $totalPageViews + intval($row['pageviews']);
                $pageViewsArray[] = intval($row['pageviews']);
                $realVisits = $realVisits + 1;


                $deviceCatagory[] = $row['deviceCategory'];
                if($row['browser'] == 'Chrome'){
                  $chrome = $chrome + 1;
                }elseif ($row['browser'] == 'Safari'){
                  $safari = $safari + 1;
                }elseif ($row['browser'] == 'Firefox'){
                  $firefox = $firefox + 1;
                }elseif ($row['browser'] == 'Internet Explorer'){
                  $IE = $IE + 1;
                }elseif ($row['browser'] == 'Edge'){
                  $edge = $edge + 1;
                }

                if($row['deviceCategory'] == 'desktop'){
                  $desktop = $desktop + 1;
                  $timeOSArrayDesktop[] = intval($row['timeOnSite']);
                }elseif ($row['deviceCategory'] == 'mobile'){
                  $mobile = $mobile + 1;
                  $timeOSArrayMobile[] = intval($row['timeOnSite']);
                }elseif ($row['deviceCategory'] == 'tablet'){
                  $tablet = $tablet + 1;
                  $timeOSArrayMobile[] = intval($row['timeOnSite']);
                }

                if($row['channelGrouping'] == 'Organic Search'){
                  $organic = $organic + 1;
                }elseif ($row['channelGrouping'] == 'Referral'){
                  $referral = $referral + 1;
                }elseif ($row['channelGrouping'] == 'Paid Search'){
                  $paid = $paid + 1;
                }elseif ($row['channelGrouping'] == 'Direct'){
                  $direct = $direct + 1;
                }

                $medium[] = $row['medium'];
                $browser[] = $row['browser'];
                $country[] = $row['country'];

                
                if($row['isExit'] == true){
                  $exitPage[] = $row['pageTitle'];
                }
                if($row['isEntrance'] == true){
                  $entryPage[] = $row['pageTitle'];
                }
                
            }

          }
          if (isset($csvArray[$c + 1])) {
            // if statement to collate all the pages that contribute to the bouncerate and entry and exit pages
            if($row['isEntrance'] == true && $row['isExit'] == true && $row['bounces'] == 1 && $csvArray[$c + 1]['visitId'] != $csvArray[$c]['visitId']){
              $bounceratePages[] = $row['pageTitle'];
            }
            if($row['isExit'] == true && $csvArray[$c + 1]['visitId'] != $csvArray[$c]['visitId']){
              $exitPage[] = $row['pageTitle'];
            }
            if($row['isEntrance'] == true && $csvArray[$c + 1]['visitId'] != $csvArray[$c]['visitId']){
              $entryPage[] = $row['pageTitle'];
            }
          }

          foreach ($row as $data){
              //identifying all unique visitor ids and adding them to the array
              if (isset($data['fullVisitorId'])) {
                  $uniqueVisitorIds[] = $data['fullVisitorId'];
              }
              // identifying the date range
              
              // total visits
              if (isset($data['visitId'])) {
                  $visitId[] = $data['visitId'];
              }
          }
          $c = $c + 1;
      }

      $numUniqueVisitorIds = count(array_unique($uniqueVisitorIds));
      $numVisits = count(array_unique($visitId));
      $bouncerate = round(array_sum($bounces) / $numVisits * 100);
      $averageTimeOnSite = round($totalTime / $realVisits);
      $averagePageViews = round($totalPageViews / $realVisits);

      // Convert string dates to Unix timestamps
      $timestamps = array_map(function($dates) {
          return strtotime($dates);
      }, $dates);
      // Find the minimum and maximum timestamps
      $earliestTimestamp = min($timestamps);
      $latestTimestamp = max($timestamps);
      // Calculate the difference in days
      $daysDifference = ceil(($latestTimestamp - $earliestTimestamp) / (60 * 60 * 24));
      $earliestDate = date('Y-m-d', $earliestTimestamp);


      $result = arrayConverter($country);
      $topCountry = $result['firstName'];

      $result = arrayConverter($entryPage);
      $topEntryPage = $result['firstName'];

      $result = arrayConverter($exitPage);
      $topExitPage = $result['firstName'];

      $result = arrayConverter($bounceratePages);
      $topBounceratedPage = $result['firstName'];

  
      $keyMobileTimes = confidenceInterval($timeOSArrayMobile);
      $keyDesktopTimes = confidenceInterval($timeOSArrayDesktop);
      $mobileOverThreshold = calculatePercentageAboveThreshold($timeOSArrayMobile, 'Mobile');
      $desktopOverThreshold = calculatePercentageAboveThreshold($timeOSArrayMobile, 'Desktop');


      // Calculate the upper and lower bounds

      //  echo "Mean: $averageTimeOnSite\n";
      // Display confidence interval
      //echo "Confidence Interval: [" . $confidenceInterval[0] . ", " . $confidenceInterval[1] . ", " . $confidenceInterval[2] ."]";


      //page views standard deviation  
      //$pageViewsSD = standardDeviation($pageViewsArray);



      // extrapolation for the unique visitiors for a monthly total
      $averageVisitorsPerDay = $numVisits / ($daysDifference + 1);
      $averageVisitorsPer30Days =  $averageVisitorsPerDay * 30;

      // Given minimum standard deviation
      $minStandardDeviation = 0.1; // 10%

      // analysing the url that is passed in to determine the market for the website
      function getCountryFromDomain($url) {
        // Extract the domain from the URL
        $parsedUrl = parse_url($url);
        if (empty($parsedUrl['host'])) {
          echo "<script>alert('Invalid URL. Please contact the development team.'); window.location.href='home.php';</script>";
          exit;
        }
        $host = $parsedUrl['host'];
    
        // Find the last two segments of the domain name
        $domainParts = explode('.', $host);
        // Initialize TLD variables
        $tld = end($domainParts); // Get the last part as the basic TLD
        $combinedTld = $tld; // Default to the simplest TLD

        // Check if there are enough parts to form a combined TLD
        if (count($domainParts) > 1) {
            $secondLast = prev($domainParts); // Get the second last part
            $combinedTld = $secondLast . '.' . $tld; // Combine the last two parts
        }

        // Define a list of known country TLDs
        $countryTLDs = [
            'co.uk' => 'United Kingdom',
            'com.au' => 'Australia',
            'de' => 'Germany',
            'fr' => 'France',
            'nl' => 'Netherlands',
            'ca' => 'Canada',
            'co.jp' => 'Japan',
            'co.in' => 'India',
            'us' => 'United States',
            'ru' => 'Russia',
            'com' => 'Global', // Consider remov  ing if you do not want to default to 'Global'
        ];

        // Check the combined and then the simple TLD against known country TLDs
        if (isset($countryTLDs[$combinedTld])) {
            return $countryTLDs[$combinedTld];
        } elseif (isset($countryTLDs[$tld])) {
            return $countryTLDs[$tld];
        } else {
            echo "<script>alert('The URL submitted is not accepted. Please contact the development team to add a new domain ending to our preset list.'); window.location.href='home.php';</script>";
            exit;
        }
      }

      $site_location = getCountryFromDomain($url);

      $lighthouse = getLighthouseScores($url);
      //print_r($lighthouse);

      $exists = checkUrlExists($url);

      if ($exists) {
          $web_Id = $exists['web_Id'];
      } else {
          $web_Id = insertWebsite($url, $audience, $site_location);
      }

/*         $result = mysqli_query($conn, $sql2);

        // Check if the insert was successful
        if ($result) {
            // Get the number of affected rows
            $affectedRows = mysqli_affected_rows($conn);
            
            if ($affectedRows > 0) {
                echo "Insert successful. $affectedRows rows inserted.";
            } else {
                echo "Insert failed. No rows inserted.";
            }
        } else {
            echo "Error: " . mysqli_error($conn);
        }
*/

      $web_Id = insertWebsite($url, $audience, $site_location);

      $reportDetails =[
        'daysDifference' => $daysDifference,
        'earliestDate' => $earliestDate,
        'currentDateTime' => date("Y-m-d H:i:s"),
        'numVisits' => $numVisits,
        'numUniqueVisitorIds' => $numUniqueVisitorIds,
        'averageTimeOnSite' => $averageTimeOnSite,
        'averagePageViews' => $averagePageViews,
        'bouncerate' => $bouncerate,
        'performance' => $lighthouse['performance_score'],
        'accessibility' => $lighthouse['accessibility_score'],
        'seo'=> $lighthouse['seo_score']
        ];

      $report_Id = insertReport($web_Id, $reportDetails);

      $sourceDetails = [
        'direct' => $direct,
        'organic' => $organic,
        'paid' => $paid,
        'referral' => $referral,
        'chrome' => $chrome,
        'firefox' => $firefox,
        'IE' => $IE,
        'safari' => $safari,
        'mobile' => $mobile,
        'tablet' => $tablet,
        'desktop' => $desktop,
        'topCountry' => $topCountry,
        'topEntryPage' => $topEntryPage,
        'topExitPage' => $topExitPage
      ];

      insertSource($report_Id, $sourceDetails);

      $result = fetchAllData();
      
      if ($result->num_rows > 0) {
          // Initialize an empty array to store the results
          $dBDataset = [];
          // Fetch each row from the result set
          while ($row = $result->fetch_assoc()) {
              // Append the row to the linked array
              $dBDataset[] = $row;
          }
          // Output the linked array (you can process it further as needed)
      } else {
          echo "No results found";
      }
      
      // Close connection
      $result->close();

      //performing the clustering algorithm in clustering.php
      $k = 3;  // Example: Number of clusters
      $clusters = kMeans($dBDataset, $k);
      $jsonData = json_encode($clusters);


      $webId = 112; // this will be removed when the database excecutions are removed as well - for testing purposes

      $dataPointInfo = findDataPointAndCluster($clusters, $webId);
      $targetWebsite = $dataPointInfo['dataPoint'];
      $clusterId = $dataPointInfo['clusterId'];

    
      // get the monthly number of visitors
      $dailyVisitors = $numUniqueVisitorIds / ($daysDifference+1);
      $numUniqueVisitorIds = $dailyVisitors * 30;
    
    $conn->close();



    }

//print_r($csvArray);



?>

<!DOCTYPE html>
<html>
<h1>Key website analytic KPI's from your website</h1>
<h3>Below you will find the report for <?php echo"$url" ?>.
</h3>

<div class="container">
        <div class="block" style="position: relative;">
            <h2>Unique Visitors: <?php echo"$numUniqueVisitorIds"; ?></h2>
            <p> 
              <b>Description: </b>Unique visitors is the number of individual users that have visited your website over the course of 30 days. <br>
              <b>Why this is useful: </b> This metric can signal whether the marketing for your website is working effectively 
              however, it is also very dependant on your expected results so if your target market is small then dont be discouraged from lower results. <br>
              </p>
              <?php


            // conditions to find the percentile of the unique visitors based on their audience
            echo "<p><b>Your site</b> has " . number_format($numUniqueVisitorIds) . " unique visitors per month, ";

            if($audience == 'Healthcare Professional'){ // B2B
                if($numUniqueVisitorIds <= 10000){
                    echo "which places you in the bottom 41% of websites. In order to improve this please look into your SEO score and marketing strategy for your website.</p>";
                }elseif($numUniqueVisitorIds > 10000 && $numUniqueVisitorIds <= 40000){
                    echo "which places you in the top 59% - 33% of websites. In order to improve this please look into your SEO score and marketing strategy for your website.</p>";
                }elseif($numUniqueVisitorIds > 40000 && $numUniqueVisitorIds <= 100000){
                    echo "which places you in the top 33% - 17% of websites. There is no need for any improvement on this metric.</p>";
                }elseif($numUniqueVisitorIds > 100000 && $numUniqueVisitorIds <= 2000000){
                    echo "which places you in the top 17% - 2% of websites. There is no need for any improvement on this metric.</p>";
                }elseif($numUniqueVisitorIds > 2000000){
                    echo "which places you in the top 2% of websites. There is no need for any improvement on this metric.</p>";
                }
            } elseif ($audience == 'Patients' || $audience == 'General Public'){ // B2C
                if($numUniqueVisitorIds <= 10000){
                    echo "which places you in the bottom 39% of websites. In order to improve this please look into your SEO score and marketing strategy for your website.</p>";
                }elseif($numUniqueVisitorIds > 10000 && $numUniqueVisitorIds <= 40000){
                    echo "which places you in the top 61% - 37% of websites. In order to improve this please look into your SEO score and marketing strategy for your website.</p>";
                }elseif($numUniqueVisitorIds > 40000 && $numUniqueVisitorIds <= 100000){
                    echo "which places you in the top 37% - 15% of websites. There is no need for any improvement on this metric.</p>";
                }elseif($numUniqueVisitorIds > 100000 && $numUniqueVisitorIds <= 2000000){
                    echo "which places you in the top 17% - 1% of websites. There is no need for any improvement on this metric..</p>";
                }elseif($numUniqueVisitorIds > 2000000){
                    echo "which places you in the top 1% of websites. There is no need for any improvement on this metric.</p>";
                }
            } else {
                echo "Error determining the unique visitors segment.</p>";
            }
            ?>
        </div>

        <div class="block" >
            <h2>Bounce Rate: <?php echo"$bouncerate"; ?>%</h2>
            <p><b>Description: </b>The bounce rate is a metric used to measure the percentage of visitors who land on a single page of a website and then leave without interacting further with the site, 
              a higher percentage means that more people are leaving without any interaction with the website. There are many factors that can affect the bounce rate of a website like its purpose, 
              for example software websites average bounce rate is over 15% higher than and industry standard and not having your homepage as the most visited entry page can also cause an increase as users
               may find what they are looking for immediately. <br>
               <b>Why this is useful: </b> Although there is ambiguity as to how it cna be interpretted it is still a very good indicator of how engaging and user firendly your website is. <br>
               This is the page with the most bounces on <?php echo"$topBounceratedPage";?> page from your website.
            </p>
              <?php
              // Check the value of the variable and display different content accordingly
              if ($bouncerate <= 40 ) {
                  // Display this content if $condition is true
                  echo "<p><b>Your website</b> has a very good bounce rate of $bouncerate%, there is no need for improvement in this area.</p>";
              } elseif ($bouncerate > 40 && $bouncerate <= 48) {
                  // Display this content if $condition is false
                  echo "<p><b>Your websites</b> bounce rate is just above average with $bouncerate% so there is no need for any improvements.</p>";
              } elseif ($bouncerate > 48 && $bouncerate <= 65) { //65 is used as it is the average bouncerate for software companies
                // Display this content if $condition is false
                echo "<p><b>Your websites</b> bounce rate is very poor: $bouncerate%. Possible ways to reduce your high bounce rate are: 
                ensuring your website is mobile friendly as the majority of website visitors are now on mobile devices and
                ensure that there are minimal distractions on the site like full screen pop ups. </p>";
              } elseif ($bouncerate > 65) {
                // Display this content if $condition is false
                echo "<p><b>Your websites</b> bounce rate is very poor: $bouncerate%. Possible ways to reduce your high bounce rate are: 
                ensuring your website is mobile friendly as the majority of website visitors are now on mobile devices and
                ensure that there are minimal distractions on the site like full screen pop ups. </p>";
              }
              ?>      
        </div>

        <div class="block ">
            <h2>Average Time Spent on Site: <?php echo"$averageTimeOnSite"; ?> seconds</h2>
            <p>
            <b>Description: </b>The average time on a website, often referred to as "average session duration," measures the total duration that visitors spend on a website divided by the number of visits. <br>
            <b>Why this is useful: </b>This metric is useful because it indicates how engaging and relevant the website's content is to visitors. A longer average time suggests that visitors find the content valuable 
              and are more likely to interact with it. Also on average users on mobile devices are likly to spend 3 minutes less time on the website.
            </p>
            <?php
            $avTimeClass = 'poor';
            $mobilePercentage = ($mobile+$tablet) / ($mobile+$desktop+$tablet) * 100;
            $desktopPercentage = $desktop / ($mobile+$desktop+$tablet) * 100;
            $averageMobileTime = 150;
            $averageDesktopTime = 360;

            if ($keyMobileTimes[1] >= $averageMobileTime && $mobileOverThreshold >= 50) {
                echo "<p><b>Mobile</b> users are spending an above-average time on the website, averaging {$keyMobileTimes[1]} minutes which is high compared to the expected {$averageMobileTime} minutes.</p>";
            } elseif ($keyMobileTimes[1] < $averageMobileTime && $mobileOverThreshold >= 50) {
                echo "<p><b>Mobile</b> users are spending less time on the website than expected, averaging {$keyMobileTimes[1]} minutes compared to the expected {$averageMobileTime} minutes. Even though most of the users are spending over the average time on the website please still consider improving mobile user experience.</p>";
            } elseif ($keyMobileTimes[1] >= $averageMobileTime && $mobileOverThreshold < 50) {
                echo "<p><b>Mobile</b> users, while spending more time on average ({$keyMobileTimes[1]} minutes) than the industry standard {$averageMobileTime}. Most of the users are still spending less than the standard amount of time on the website please consider improving mobile user experience..</p>";
            } elseif ($keyMobileTimes[1] < $averageMobileTime && $mobileOverThreshold < 50) {
                echo "<p><b>Mobile</b> users are not only spending less time on the website, averaging {$keyMobileTimes[1]} minutes, but also show low engagement levels. It may indicate poor mobile experience or unengaging content.</p>";
            }

            if ($keyDesktopTimes[1] >= $averageDesktopTime && $desktopOverThreshold >= 50) {
                echo "<p><b>Desktop</b> users are spending an above-average time on the website, averaging {$keyDesktopTimes[1]} minutes which is high compared to the expected {$averageDesktopTime} minutes.</p>";
            } elseif ($keyDesktopTimes[1] < $averageDesktopTime && $desktopOverThreshold >= 50) {
                echo "<p><b>Desktop</b> users are spending less time on the website than expected, averaging {$keyDesktopTimes[1]} minutes compared to the expected {$averageDesktopTime} minutes. Even though most users are spending over the average time on the website, please still consider optimizing content for desktop users.</p>";
            } elseif ($keyDesktopTimes[1] >= $averageDesktopTime && $desktopOverThreshold < 50) {
                echo "<p><b>Desktop</b> users, while spending more time on average ({$keyDesktopTimes[1]} minutes) than the industry standard {$averageDesktopTime} minutes, most of the users are still spending less than the standard amount of time on the website. Please consider optimizing content for desktop users.</p>";
            } elseif ($keyDesktopTimes[1] < $averageDesktopTime && $desktopOverThreshold < 50) {
                echo "<p><b>Desktop</b> users are not only spending less time on the website, averaging {$keyDesktopTimes[1]} minutes, but also show low engagement levels. It may indicate poor desktop experience or unengaging content.</p>";
            }
            ?>
        </div>

        <div class="block">
            <h2>Average Page Views: <?php echo"$averagePageViews"; ?> pages</h2>
            <p> 
            <b>Description: </b>Average page views per visit on a website represent the number of individual pages a visitor views during a single session. <br>
            <b>Why this is useful: </b>This metric is significant as it indicates user engagement and interest; 
              higher page views can suggest that visitors find the content compelling or are deeply exploring what the site has to offer. This metric also helps in assessing the effectiveness of site layout and navigation, 
              as more intuitive designs tend to encourage more page views. For mobile users, the average page views are typically lower at 3.8 compared to desktop users, who average about 5.2 page views per session</p>
            <?php 

              if($averagePageViews >= 6.5){
                echo "<p><b>Your page views</b> are very good and in the top 35%. Your average is {$averagePageViews}.</p>";
              }elseif($averagePageViews < 6.5 && $averagePageViews >= 3.5){
                echo "<p><b>Your page views</b> per session is as exptected due to 50% of websites having a similar number of pages per visit. Your average is {$averagePageViews}.</p>";
              }elseif($averagePageViews < 3.5 && $averagePageViews >= 1){
                echo "<p><b>Your page views</b> per session is in the bottom 15% of websites with an average of {$averagePageViews}. Please consider making you website more navigable and that the content is clearly laid out</p>";
              }elseif($averagePageViews < 1){
                echo "<p><b>Your page views</b> are very bad as your visitors dont seem to be moving off their landing page at all. Your average is {$averagePageViews}.</p>";
              }

            ?>
        </div>

        <div class="block">
            <h2>Referer Type</h2>
            <p>
            <b>Description: </b>The referrer type indicates how users are entering your website when given 4 categories: Organic, Paid, Referral and Direct. <br>
            <b>Why this is useful: </b>it is useful for understanding your audience and how to better target them. If your website has no paid traffic then these comparisons will not be relevant as the results will be skewed. 
            The below statistics show the percentage split between all referer types and their similarity with the industry average for that refere type.
            </p>
            <?php 
            $directPerc = $direct / ($direct + $paid + $organic + $referral) * 100; 
            $paidPerc = $paid / ($direct + $paid + $organic + $referral) * 100; 
            $organicPerc = $organic / ($direct + $paid + $organic + $referral) * 100; 
            $referralPerc = $referral / ($direct + $paid + $organic + $referral) * 100; 

            // Baseline percentages
            $baselineDirect = 28;
            $baselineOrganic = 28;
            $baselinePaid = 29;
            $baselineReferral = 15;

            // Check if new report percentages are within the tolerance level of the baseline
            $tolerance = 3; 

            // Function to determine comparison result
            function compareTraffic($percentage, $baseline, $tolerance) {
                if (abs($percentage - $baseline) <= $tolerance) {
                    return "similar";
                } elseif ($percentage > $baseline) {
                    return "higher";
                } else {
                    return "lower";
                }
            }
            
            // Determine the comparison for each type of traffic
            $comparisonDirect = compareTraffic($directPerc, $baselineDirect, $tolerance);
            $comparisonPaid = compareTraffic($paidPerc, $baselinePaid, $tolerance);
            $comparisonOrganic = compareTraffic($organicPerc, $baselineOrganic, $tolerance);
            $comparisonReferral = compareTraffic($referralPerc, $baselineReferral, $tolerance);
            
            
            echo "<p><b>Direct Traffic</b> is visitors who arrive by typing your URL directly into their browser. High direct traffic can indicate strong brand recognition: " . round($directPerc, 2) . "% - <b>$comparisonDirect</b> to the average.</p>";
            echo "<p><b>Paid Traffic</b> is visitors from paid advertisements, like Google Ads. This shows the effectiveness of your paid marketing efforts: " . round($paidPerc, 2) . "% - <b>$comparisonPaid</b> to the average.</p>";
            echo "<p><b>Organic Traffic</b> is visitors who come from a search engine result. Good organic traffic suggests effective SEO: " . round($organicPerc, 2) . "% - <b>$comparisonOrganic</b> to the average.</p>";
            echo "<p><b>Referral Traffic</b> is visitors who clicked on a link from another site. High referral traffic can mean good networking and presence on the web: " . round($referralPerc, 2) . "% - <b>$comparisonReferral</b> to the average.</p>";
            
            ?>
        </div>

        <div class="block">
            <h2>Device type</h2>
            <p> 
            <b>Description: </b>This section shows the relationship between mobile and dekstop traffic on your website. <br>
            <b>Why this is useful: </b>This is useful to know as there is a relationship between the device the user is on and how they interact with your website as shown in the 'Average Time Spend on Site' section. Mobile usage has been on the rise for 
            the past few years and websites are expected to have more mobile traffic than desktop. <br>
            <b>your results: </b>Below is a pie chart representing the percentage split for your website:
              </p>
            <ul>
              <?php 
              $totalMobile = $mobile + $tablet;
              echo "<li>Total Mobile Views: " . $totalMobile . "</li>";
              echo "<li>Total Desktop Views: " . $desktop . "</li>";
              ?>
            </ul> 
            
            <div id="chartContainer" style="height: 370px; width: 100%;"></div>
              <script>
                  window.onload = function() {
                      var chart = new CanvasJS.Chart("chartContainer", {
                          animationEnabled: true,
                          title: {
                              text: "Mobile vs Desktop Usage"
                          },
                          data: [{
                              type: "pie",
                              startAngle: 240,
                              yValueFormatString: "##0.00\"%\"",
                              indexLabel: "{label} {y}",
                              dataPoints: [
                                  {y: <?php echo $mobilePercentage; ?>, label: "Mobile"},
                                  {y: <?php echo $desktopPercentage; ?>, label: "Desktop"}
                              ]
                          }]
                      });
                      chart.render();
                  }
              </script>
        </div>

        <div class="block">
            <h2>Lighthouse</h2>
            <p>
            <b>Description: </b>Lighthouse is a tool created by Google that identifies key aspects of your website and provides scores out of 100. 
              Lighthouse provides audits on the websites performance, accessibility (for users with disabilities), search engine optimization (SEO), and more, offering guidance on how to make the webpage better.
              Below i have shared the performance, SEO and accessibility score for your website as it currently is, therefore depending on the dates of the report you have shared this section may not be directly 
              relevant the information you have uploaded. <br>
            <b>Why this is useful: </b>Lighthouse scores can provide metrics and insights that are not available via standards analytics gathering tools.
            </p>
            <ul>
                <?php 
                echo "<li><b>Performance</b>: " . categorizeScore($lighthouse['performance_score']) . "</li>";
                echo "<li><b>Accessibility</b>: " . categorizeScore($lighthouse['accessibility_score']) . "</li>";
                echo "<li><b>SEO</b>: " . categorizeScore($lighthouse['seo_score']) . ". SEO is the measure of how visible your website is for search engines like Google to pick up.</li>";
                echo "<li><b>First Contentful paint(FCP)</b>: ". categorizeValue($lighthouse['FCP_score'], $lighthouse['FCP_value']). ". This marks the first point in the page load timeline where the user can see anything on the screen. Fast FCP reasurres the user that something is happening</li>";
                echo "<li><b>Speed Index</b>: ". categorizeValue($lighthouse['SI_score'], $lighthouse['SI_value']) . ". Speed Index measures how quickly content is visually displayed during page load.</li>";
                echo "<li><b>Largest Contentful paint(LCP)</b>: ". categorizeValue($lighthouse['LCP_score'], $lighthouse['LCP_value']) . ".This marks the point in the page load timeline when the page's main content has likely loaded.</li>";
                echo "<li><b>Total Blocking Time</b>: ". categorizeValue($lighthouse['TBT_score'], $lighthouse['TBT_value']) . "It measures the total time after FCP where the main thread was blocked for long enough to prevent responses to user input. A low TBT helps ensure that the page is usable.</li>";
                ?>
            </ul> 
            
            
            <?php 
            // Function to determine the category based on the score
            function categorizeScore($score) {
              if ($score >= 90 && $score <= 100) {
                  return "<span style='color: green;'>Good, no improvement is needed on your score of $score, however for more information please do run a lighthouse report for any possible areas for improvement</span>";
              } elseif ($score >= 50 && $score <= 89) {
                  return "<span style='color: orange;'>Needs Improvement, please run a lighthouse report on your website for suggested feedback and ways to improve your score of $score</span>";
              } elseif ($score >= 0 && $score <= 49) {
                  return "<span style='color: red;'>Poor, please run a lighthouse report on your website for suggested feedback and ways to improve your score of $score</span>";
              } else {
                  return "<span>Invalid Score</span>";
              }
            }
            function categorizeValue($score, $value) {
              if ($score >= 90 && $score <= 100) {
                  return "<span style='color: green;'>Good, no improvement is needed with a time of $value, however for more information please do run a lighthouse report for any possible areas for improvement</span>";
              } elseif ($score >= 50 && $score <= 89) {
                  return "<span style='color: orange;'>Needs Improvement, please run a lighthouse report on your website for suggested feedback and ways to improve your time of $value</span>";
              } elseif ($score >= 0 && $score <= 49) {
                  return "<span style='color: red;'>Poor, please run a lighthouse report on your website for suggested feedback and ways to improve your time of $value</span>";
              } else {
                  return "<span>Invalid Score</span>";
              }
            }

            ?>
        </div>

        <div class="block">
            <h2>General Information - Browser - Entry/Exit Pages</h2>
            <p>
            <b>Description: </b> This block displays the most common browser types that are used to run your website and also the top entry/exit pages. <br>
            <b>Why this is useful: </b>Understanding the most common browsers used by visitors to your website helps in optimizing the site's performance and compatibility. 
              Similarly, knowing the top entry and exit pages can shed light on user behavior and content effectiveness.</p>
            <?php
            // Assume $totalVisits holds the total number of visits, to calculate the percentages
            $totalVisits = $chrome + $IE + $firefox + $safari + $edge;

            echo "<p>Browser Usage:</p>";
            echo "<ul>";
            echo "<li>Chrome: " . round($chrome / $totalVisits * 100, 2) . "%</li>";
            echo "<li>Internet Explorer: " . round($IE / $totalVisits * 100, 2) . "%</li>";
            echo "<li>Firefox: " . round($firefox / $totalVisits * 100, 2) . "%</li>";
            echo "<li>Safari: " . round($safari / $totalVisits * 100, 2) . "%</li>";
            echo "<li>Microsoft Edge: " . round($edge / $totalVisits * 100, 2) . "%</li>";
            echo "</ul>";

            echo "<p><b>Top Entry Page:</b> " . htmlspecialchars($topEntryPage) . "</p>";
            echo "<p>This is the first page that users visit on your site and can indicate the most engaging content or the effectiveness of your marketing efforts. Usually this would be the 
            homepage unless there are any promotions for specific pages that you are running.</p>";

            echo "<p><b>Top Exit Page:</b> " . htmlspecialchars($topExitPage) . "</p>";
            echo "<p>This is the last page users visit before leaving your site. A high exit rate on specific pages may signal areas where the user experience can be improved to retain visitors.</p>";
            ?>
        </div>

        <div class="block">
            <h2>How your website compares against similar websites</h2>
            <p><b>Description: </b>In this block we compare your websites metrics against websites that are deemed to be similar by our machine learning program.
              <b>Why it is useful: </b> The statistics provide a different perspective on your websites performance especially as some industry standards are very subjective to the type of website they are used on. This section aims to eliminate that subjectivity.
            </p>
            <ul>
                <?php
                $metrics = ['SEO', 'performance', 'accessibility', 'bounceRate', 'averageTime', 'pageViews'];
                $averages = array_fill_keys($metrics, 0);
                $count = count($clusters[$clusterId]);
                $tolerance = 5; // Tolerance in percentage

                // Calculate averages for the cluster
                foreach ($clusters[$clusterId] as $website) {
                    foreach ($metrics as $metric) {
                        $averages[$metric] += $website[$metric];
                    }
                }

                foreach ($metrics as $metric) {
                    $averages[$metric] /= $count; // Get average per metric
                }

                // Compare target website metrics to cluster averages
                foreach ($metrics as $metric) {
                  if ($averages[$metric] != 0) { // To avoid division by zero
                      $percentageDifference = (($targetWebsite[$metric] - $averages[$metric]) / $averages[$metric]) * 100;
                      $absPercentageDifference = abs($percentageDifference);
            
                      if ($absPercentageDifference <= $tolerance) {
                        $comparison = 'similar';
                        echo "<li>Your website's <b>$metric</b> is <b>$comparison</b> to the cluster average as the difference is within the tolerance level of the result.</li>";
                      } else {
                        $comparison = $percentageDifference > 0 ? 'better' : 'worse';
                        echo "<li>Your website's <b>$metric</b> is <b>$comparison</b> than the cluster average by <b>" . number_format($absPercentageDifference, 2) . "%</b>.</li>";
                      } 
      
                  } else {
                        echo "<li>The average <b>$metric</b> for the cluster is zero, making a percentage comparison not meaningful.</li>";
                  }
                }
                ?>
            </ul>
        </div>
      
  </div>
  <div id="scatterChartContainer" style="height: 700px; width: 100%;"></div>

  <script>
    var data = <?php echo $jsonData; ?>; // Parses JSON data from PHP into a JavaScript object
    console.log(data); // Outputs parsed data to the console

    // Map for cluster colors
    var clusterColors = {
        1: "#FF5733", // Cluster ID 1
        2: "#33FF57", // Cluster ID 2
        3: "#3357FF", // Cluster ID 3
    };

    // Prepare data for visualization
    var dataPoints = data.map(function(item) {
        return {
            x: parseFloat(item.visits), // Use 'visits' for the x-axis
            y: parseFloat(item.uniqueVisitors), // Use 'uniqueVisitors' for the y-axis
            markerColor: clusterColors[item.cluster], // Assign color based on cluster ID
            markerSize: 5, // Adjust marker size
            toolTipContent: "Cluster: " + item.cluster + "<br/>Visits: " + item.visits + "<br/>Unique Visitors: " + item.uniqueVisitors
        };
    });

    // Create a scatter plot using CanvasJS
    windows.onload = function() {
    var chart = new CanvasJS.Chart("scatterChartContainer", {
        animationEnabled: true,
        theme: "light2",
        title: {
            text: "Clustered Dataset Visualization"
        },
        axisX: {
            title: "Number of Visits",
            includeZero: false
        },
        axisY: {
            title: "Unique Visitors",
            includeZero: false
        },
        data: [{
            type: "scatter",
            dataPoints: dataPoints
        }]
    });

    // Render the chart
    chart.render();
  }
</script>




<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" language="JavaScript" src="/scripts/script.js"></script>
</body>
</html>