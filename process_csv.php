<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if CSV file is uploaded
  if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] == UPLOAD_ERR_NO_FILE) {
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
//        print_r($csvData);
        // Process the CSV data (you can customize this part based on your requirements)
        // Example: Display CSV data
   //     echo "<pre>" . $csvData . "</pre>";
        
        //$file = $_SESSION["csvData"];
        $csvArray = array(); 

        // Example: Parse CSV data
        // Process the CSV data
        $rows = str_getcsv($csvData, "\n"); // Split rows
   //     print_r($rows);
        $header = null;
        foreach ($rows as $rowIndex => $row) {
            $data = str_getcsv($row); // Split columns

            // Use the first row of the CSV file as the header
            if ($rowIndex === 0) {
                $header = $data;
                continue; // Skip processing the header row
            }

            // Group CSV data by an integer value insid
            
    //        $groupKey = intval($data[0]); // Assuming the integer value is in the first column
    //        if (!isset($csvArray[$groupKey])) {
    //            $csvArray[$groupKey] = array();
    //        }

            // Use the strings from the first row of the CSV file as the keys for the items within the array
            $rowData = array_combine($header, $data);

            // Append the grouped and keyed data to the $csvArray
            $csvArray[] = $rowData;
        }
        //print_r($csvArray);
        // Output the grouped and keyed $csvArray
        //echo "<pre>";
        //print_r($csvArray);
       // echo "</pre>";

       //print_r(count($csvArray));
       $c = 0;
       $realVisits = 0;
       $uniqueVisitorIds = [];
       $visitId = [];
       $bounces = [];
       $dates = [];
       $totalTime = 0;
       $timeOSArray = [];
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
                  $timeOSArray[] = intval($row['timeOnSite']);
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
                  }

                  if($row['deviceCategory'] == 'desktop'){
                    $desktop = $desktop + 1;
                  }elseif ($row['deviceCategory'] == 'mobile'){
                    $mobile = $mobile + 1;
                  }elseif ($row['deviceCategory'] == 'tablet'){
                    $tablet = $tablet + 1;
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

            // if statement to collate all the pages that contribute to the bouncerate and entry and exit pages
            if($row['isEntrance'] == true && $row['isExit'] == true && $row['bounces'] == 1 && $csvArray[$c + 1]['visitId'] != $csvArray[$c]['visitId']){
              $bounceratePages[] = $row['pageTitle'];
            }

            if (isset($csvArray[$c + 1])) {
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




        //function to count the number of occurances in an array of strings
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

        $result = arrayConverter($country);
        $topCountry = $result['firstName'];

        $result = arrayConverter($entryPage);
        $topEntryPage = $result['firstName'];
        print_r($topEntryPage);

        $result = arrayConverter($exitPage);
        $topExitPage = $result['firstName'];

        $result = arrayConverter($bounceratePages);
        $topBounceratedPage = $result['firstName'];




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


   
        $confidenceInterval = confidenceInterval($timeOSArray);


        // Calculate the upper and lower bounds

        //  echo "Mean: $averageTimeOnSite\n";
        // Display confidence interval
        echo "Confidence Interval: [" . $confidenceInterval[0] . ", " . $confidenceInterval[1] . ", " . $confidenceInterval[2] ."]";


        //page views standard deviation  
        //$pageViewsSD = standardDeviation($pageViewsArray);

        //print_r($pageViewsSD);


        // extrapolation for the unique visitiors for a monthly total
        $averageVisitorsPerDay = $numVisits / ($daysDifference + 1);
        $averageVisitorsPer30Days =  $averageVisitorsPerDay * 30;

        // Given monthly views statistics
        $monthlyViewsStats = array(
          array(1001, 15000, 0.46),   // 1,001-15K monthly views (46%)
          array(15001, 50000, 0.193), // 15,001-50K monthly views (19.3%)
          array(50001, 250000, 0.232), // 50,001-250K monthly views (23.2%)
          array(250001, 10000000, 0.11), // 250,001-10M monthly views (11%)
          array(10000001, PHP_INT_MAX, 0.005) // 10M+ monthly views (0.5%)
        );

        // Given minimum standard deviation
        $minStandardDeviation = 0.1; // 10%

        // Calculate the lower bound of the acceptable range
        $lowerBound = 0;
        foreach ($monthlyViewsStats as $stat) {
          if ($stat[2] < $minStandardDeviation) {
              $lowerBound = $stat[0];
              break;
          }
        }

        echo "Minimum requirement for an acceptable number of views: " . $lowerBound . " monthly views";
        
        // bounce rate analysis






        // LIGHTHOUSE REPORT
        // Function to generate Lighthouse report for a given URL and extract the performance score
        function getLighthousePerformanceScore($url) {
          // Define the command to execute Lighthouse
          $command = "lighthouse --output=json --output-path=report.json $url";

          // Execute the command
          exec($command, $output, $return);

          // Check if Lighthouse command executed successfully
          if ($return === 0) {
              // Read the generated report
              $report = file_get_contents('report.json');

              // Decode the JSON report
              $reportData = json_decode($report, true);

              // Check if the report data is valid
              if ($reportData !== null && isset($reportData['categories']['performance'])) {
                  // Extract and return the performance score
                  return $reportData['categories']['performance']['score'] * 100;
              } else {
                  // Invalid report data or missing performance score
                  return false;
              }
          } else {
              // Lighthouse command failed
              return false;
          }
        }

        // Function to extract accessibility information from a Lighthouse report
        function extractAccessibilityInfo($report) {
          // Decode the JSON report
          $reportData = json_decode($report, true);

          // Check if the report data is valid and contains accessibility information
          if ($reportData !== null && isset($reportData['categories']['accessibility'])) {
              // Extract accessibility information
              $accessibilityInfo = $reportData['categories']['accessibility'];

              // Return accessibility information
              return $accessibilityInfo;
          } else {
              // Invalid report data or missing accessibility information
              return false;
          }
        }

        // Example URL provided by the user
        $url = "https://www.cardiffhalfmarathon.co.uk/";

        // Get the performance score for the provided URL
        // Function to get performance metrics and accessibility information for a given URL
        function getPerformanceMetricsFromURL($url) {
          // Define the command to execute Lighthouse
          $command = "lighthouse --output=json --output-path=report.json $url";

          // Execute the command
          exec($command, $output, $return);

          // Check if Lighthouse command executed successfully
          if ($return === 0) {
              // Read the generated report
              $report = file_get_contents('report.json');

              // Call the function to extract metrics from the report
              return getPerformanceMetrics($report);
          } else {
              // Lighthouse command failed
              return null;
          }
        }

        // Function to get performance metrics and accessibility information from a Lighthouse report
        function getPerformanceMetrics($report) {
          // Decode the JSON report
          $reportData = json_decode($report, true);
      
          // Initialize an array to store the results
          $metrics = [];
      
          // Check if the report data is valid
          if ($reportData !== null) {
              // Extract performance score
              if (isset($reportData['categories']['performance']['score'])) {
                  $metrics['performance_score'] = $reportData['categories']['performance']['score'] * 100;
              } else {
                  $metrics['performance_score'] = null;
              }
      
              // Extract accessibility score
              if (isset($reportData['categories']['accessibility']['score'])) {
                  $metrics['accessibility_score'] = $reportData['categories']['accessibility']['score'] * 100;
              } else {
                  $metrics['accessibility_score'] = null;
              }
      
              // Extract accessibility opportunities
              if (isset($reportData['categories']['accessibility']['auditRefs'])) {
                  $opportunities = [];
                  foreach ($reportData['categories']['accessibility']['auditRefs'] as $auditRef) {
                      // Check if the necessary keys are present before accessing them
                      if (isset($auditRef['scoreDisplayMode']) && isset($auditRef['result']) && $auditRef['scoreDisplayMode'] === 'manual' && $auditRef['result'] === 'failed') {
                          $opportunities[] = $auditRef['description'];
                      }
                  }
                  $metrics['accessibility_opportunities'] = $opportunities;
              } else {
                  $metrics['accessibility_opportunities'] = [];
              }
          } else {
              // Invalid report data
              $metrics['performance_score'] = null;
              $metrics['accessibility_score'] = null;
              $metrics['accessibility_opportunities'] = [];
          }
      
          return $metrics;
      }


        $lighthouse = getPerformanceMetricsFromURL($url);
        print_r($lighthouse);



        // Establish connection to MySQL database
        $hostname = "localhost"; // Change to your MySQL server hostname
        $username = "root"; // Change to your MySQL username
        $password = "88vdmC6yawFPHf1"; // Change to your MySQL password
        $database_name = "final_project"; // Change to your database name

        $conn = new mysqli($hostname, $username, $password, $database_name);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }else{

            // SQL to retrieve the informatioin            
/*            $dataset = [
               ['visitors' => 100, 'avg_time_spent' => 60, 'country' => 'US', 'bounce_rate' => 20, 'pageviews' => 200, 'browser' => 'Chrome'],
              ['visitors' => 200, 'avg_time_spent' => 45, 'country' => 'UK', 'bounce_rate' => 25, 'pageviews' => 180, 'browser' => 'Firefox'],
              // More data points...
          ]; */
          $sql = "SELECT w.*, r.*, s.*
                  FROM website w
                  JOIN reports r ON w.web_Id = r.web_Id
                  JOIN source s ON r.report_Id = s.report_Id";
  
          $result = $conn->query($sql);
          
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


            // machine learning algorithm

            function dbscan($dataset, $epsilon, $minPts) {
                $clusters = array();
                $clusterId = 0;


                foreach ($dataset as $pointKey => &$point) {
                    if (!is_null($point['cluster'])) continue; // Already assigned to a cluster
                    
                    $neighbors = regionQuery($dataset, $point, $epsilon);
                    
                    if (count($neighbors) < $minPts) {
                        $point['cluster'] = -1; // Noise
                    } else {
                        $clusterId++;
                        expandCluster($dataset, $point, $neighbors, $clusterId, $epsilon, $minPts);
                    }
                }
                
                return $dataset;
            }

            function regionQuery($dataset, $point, $epsilon) {
                $neighbors = array();
                
                foreach ($dataset as $neighborKey => $neighbor) {
                    if (calculateDistance($point, $neighbor) <= $epsilon) {
                        $neighbors[$neighborKey] = $neighbor;
                    }
                }
                
                return $neighbors;
            }

            function expandCluster(&$dataset, $point, $neighbors, $clusterId, $epsilon, $minPts) {
                $point['cluster'] = $clusterId;
                
                foreach ($neighbors as $neighborKey => &$neighbor) {
                    if (is_null($neighbor['cluster'])) { // Unassigned or marked as noise
                        $neighbor['cluster'] = $clusterId;
                        $newNeighbors = regionQuery($dataset, $neighbor, $epsilon);
                        
                        if (count($newNeighbors) >= $minPts) {
                            $neighbors += $newNeighbors;
                        }
                    }
                }
            }

            function calculateDistance($point1, $point2) {
              // Ensure both points are arrays
              if (!is_array($point1) || !is_array($point2)) {
                  throw new InvalidArgumentException('Both inputs must be arrays');
              }
          
              // Ensure both arrays have the same number of dimensions
              if (count($point1) !== count($point2)) {
                  throw new InvalidArgumentException('Both arrays must have the same number of dimensions');
              }
          
              // Calculate Euclidean distance
              $sum = 0;
              foreach ($point1 as $key => $value) {
                  $sum += pow((float)$value - (float)$point2[$key], 2); // Explicitly cast to float
              }
              return sqrt($sum);
          }


            foreach ($dBDataset as &$point) {
              $point['cluster'] = null; // or any other default value you prefer
            }

            $epsilon = 1;
            $minPts = 3;
            $clusteredDataset = dbscan($dBDataset, $epsilon, $minPts);
            //print_r($clusteredDataset);

            // Prepare the data for the scatter plot
            $dataPoints = [];
            foreach ($clusteredDataset as $data) {
                // Extract the relevant data points for the scatter plot
                $x = $data['organic']; // Example: average time spent
                $y = $data['paid']; // Example: bounce rate
                $cluster = $data['cluster']; // Cluster ID

                // Add the data point to the array
                $dataPoints[] = ["x" => $x, "y" => $y, "cluster" => $cluster];
            }
            

            $jsonData = json_encode($clusteredDataset);





      //      $sql1 = "INSERT INTO `website` ( `url`, `audience`, `type`, `site_location`) VALUES ('youtube.com', 'general public', 'e-commerce', 'USA')";
            $sql1 = "INSERT INTO `website` (`url`, `audience`, `type`, `site_location`) VALUES (?, ?, ?, ?)";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("ssss", $url, $audience, $type, $site_location);

            // Set parameter values
            $url = "youtube.com";
            $audience = "general public";
            $type = "e-commerce";
            $site_location = "USA";

            // Execute the statement
            //$stmt1->execute();

            // Fetch the web_Id of the inserted row
            $web_Id = $conn->insert_id;

            // Close the statement
            $stmt1->close();



            $sql2 = "INSERT INTO `reports` (`web_Id`, `dateRange`, `date`, `visits`, `uniqueVisitors`, `averageTime`, `pageViews`, `bounceRate`, `performance`, `accessibility`, `SEO`) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            //Bind parameters to placeholders
            $stmt2 = $conn->prepare($sql2);
          //  $stmt->bindParam(1, 1);
            $seo = 100;
            $accessibility = 100;
            $performance = 100;

            $stmt2->bind_param("iisiiiiiiii", $web_Id, $daysDifference, $earliestDate, $numVisits, $numUniqueVisitorIds, $averageTimeOnSite, $averagePageViews, $bouncerate, $performance, $accessibility, $seo); // 

            // Execute the statement
            //$stmt2->execute();

            $report_Id = $conn->insert_id;
            // Close the statement
            $stmt2->close();



            $sql3 = "INSERT INTO `source` (`report_Id`, `direct`, `organic`, `paid`, `referral`, `chrome`, `firefox`, `internetExplorer`, `safari`, `mobile`, `tablet`, `desktop`, `topCountry`, `entry_page`, `exit_page`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ?)";
    
            //Bind parameters to placeholders
            $stmt3 = $conn->prepare($sql3);
            $stmt3->bind_param("iiiiiiiiiiiisss", $report_Id, $direct, $organic, $paid, $referral, $chrome, $firefox, $IE, $safari, $mobile, $tablet, $desktop, $topCountry, $topEntryPage, $topExitPage); // 

            // Execute the statement
            //$stmt3->execute();

            // Close the statement
            $stmt3->close();

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
        $conn->close();

}
  /* else {
  // Redirect back to home.php if accessed directly without a valid POST request
  echo "<script>alert('going back home.');</script>";
  header("Location: home.php");
  exit();
}  */
        

//print_r($csvArray);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style.css">
    <script src="https://cdn.canvasjs.com/ga/canvasjs.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Navbar</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>      
    <div class="collapse navbar-collapse" id="navbarNavDropdown">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="home.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="history.php">History</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Dropdown link
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#">Action</a></li>
            <li><a class="dropdown-item" href="#">Another action</a></li>
            <li><a class="dropdown-item" href="#">this needs to be changed</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


<h1>Key website analytic KPI's from your website</h1>
<p>description of thdffdfddfdffde tool</p>

<div class="container">
        <div class="block">
            <h2>Views</h2>
            <p>explain what their results are hten compare against industry standards</p>
            
          </div>
        <div class="block">
            <h2>Visitors</h2>
            <p>This is some information about the right block. Phasellus pretium, lorem non ullamcorper fermentum, sapien nunc fermentum lacus, ut bibendum nunc velit nec libero. Fusce vel lorem eget nunc egestas interdum non in lorem.</p>
        </div>
        <div class="block">
            <h2>Bounce Rate</h2>
            <p>The bounce rate is a metric used to measure the percentage of visitors who land on a single page of a website and then leave without interacting further with the site, a higher percentage means that more people are leaving without any interaction with the website </p>
              <?php
              // Sample variable
              $condition = true;

              // Check the value of the variable and display different content accordingly
              if ($bouncerate <= 40 ) {
                  // Display this content if $condition is true
                  echo "<p>This is a very good bounce rate of $bouncerate </p>";
              } elseif ($bouncerate > 40 && $bouncerate <= 58) {
                  // Display this content if $condition is false
                  echo "<h1>above average bounce rate for a healthcare website</h1>";
              } elseif ($bouncerate > 58 && $bouncerate <= 65) {
                // Display this content if $condition is false
                echo "<h1>just below average for a healthcare website </h1>";
              } elseif ($bouncerate > 65) {
                // Display this content if $condition is false
                echo "<h1> this is a poor performaing bounce rate assuming that the content of the website is not designed for a high bouncerate</h1>";
              }
              ?>      
        </div>
        <div class="block">
            <h2>Referer Type</h2>
            <p>This is some information about the right block. Proin tempor velit vel lorem laoreet, id mattis eros bibendum. Integer fermentum purus non orci venenatis ullamcorper. Phasellus eu purus vel metus bibendum vestibulum.</p>
        </div>
  </div>
  <div id="scatterChartContainer" style="height: 700px; width: 100%;"></div>

    <script>
        // Parse the JSON data passed from PHP
        var data = <?php echo $jsonData; ?>;
        console.log(data); // Log the received data to verify

        // Prepare data for CanvasJS
        var dataPoints = [];
        for (var i = 0; i < data.length; i++) {
            dataPoints.push({ x: data[i].organic, y: data[i].paid, color: data[i].cluster });
        }

        // Create scatter plot using CanvasJS
        var chart = new CanvasJS.Chart("scatterChartContainer", {
            title: {
                text: "Clustered Dataset"
            },
            data: [{
                type: "scatter",
                markerSize: 5,
                toolTipContent: "<b>{color}</b><br/>Average Time: {x}, Bounce Rate: {y}%",
                dataPoints: dataPoints
            }]
        });

        // Render the chart
        chart.render();
    </script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" language="JavaScript" src="/scripts/script.js"></script>
</body>
</html>