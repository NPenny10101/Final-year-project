<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if CSV file is uploaded
  if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] == UPLOAD_ERR_NO_FILE) {
      $_SESSION['errors'][] = "Please upload a CSV file.";
      header("Location: home.php");
      exit();
  }

  // Check if URL is provided (optional)
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

            // Group CSV data by an integer value inside the arrays
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
       $totalPageViews = 0;
    
       //for loops to go through each value in the csv array
        foreach ($csvArray as $row) {
            $uniqueVisitorIds[] = $row['fullVisitorId'];
            
            $visitId[] = $row['visitId'];
            
            $bounces[] = $row['bounces'];

            $dates[] = $row['date'];

            if ($row['bounces'] != 1 && $csvArray[$c - 1]['visitId'] != $csvArray[$c]['visitId']) {
                $totalTime = $totalTime + intval($row['timeOnSite']);
                $timeOSArray[] = intval($row['timeOnSite']);
                $totalPageViews = $totalPageViews + intval($row['pageviews']);
                $realVisits = $realVisits + 1;
                
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

        print_r($numUniqueVisitorIds);
        echo "\n";
        print_r($numVisits);
        echo "\n";
        print_r($bouncerate);
        echo "\n";
        print_r($averageTimeOnSite);
        echo "\n";
        print_r($averagePageViews);
        echo "\n";
        print_r($earliestDate);
        echo "\n";
        //print_r($timeOSArray);

        // Function to calculate the standard deviation of an array
        function standardDeviation($arr) {
          $mean = array_sum($arr) / count($arr);
          $squaredDeviations = array_map(function($x) use ($mean) {
              return pow($x - $mean, 2);
          }, $arr);
          $variance = sqrt(array_sum($squaredDeviations) / count($arr));
          $lowerBound = max(0, $mean - $variance);
          $upperBound = $mean + $variance;
          return array($lowerBound, $upperBound, $variance);
        }
        
        $timeSD = standardDeviation($timeOSArray);

        // Calculate the upper and lower bounds

      //  echo "Mean: $averageTimeOnSite\n";
        print_r($timeSD);
      // echo "Upper Bound: $timeUpperBound\n";
       // echo "Lower Bound: $timeLowerBound\n";

        // exterpolation for the unique visitiors for a monthly total
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



        // URL of the Performance API endpoint
/*         $api_url = 'https://example.com/performance-api-endpoint';

        // Parameters to be sent in the request
        $params = [
            'param1' => 'value1',
            'param2' => 'value2'
        ];

        // Build query string
        $query_string = http_build_query($params);

        // Combine URL with query string
        $request_url = $api_url . '?' . $query_string;

        // Send HTTP GET request using file_get_contents
        $response = file_get_contents($request_url);

        // Check if response is received
        if ($response) {
            // Process the response (e.g., decode JSON)
            $data = json_decode($response, true);

            // Display or use the data
            print_r($data);
        } else {
            // Handle error if request fails
            echo "Error: Unable to fetch data from the Performance API.";
        }
 */


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
        $url = "https://google.com";

        // Get the performance score for the provided URL
        $performanceScore = getLighthousePerformanceScore($url);

        // Check if the performance score was retrieved successfully
        if ($performanceScore !== false) {
          // Output the performance score
          echo "Performance Score: " . $performanceScore . "/100";
        } else {
          // Report generation failed or performance score not found
          echo "Failed to retrieve the performance score for the provided URL.";
        }
        
        



        // Establish connection to MySQL database
        $hostname = "localhost"; // Change to your MySQL server hostname
        $username = "root"; // Change to your MySQL username
        $password = "88vdmC6yawFPHf1"; // Change to your MySQL password
        $database_name = "final_project"; // Change to your database name

        $conn = new mysqli($hostname, $username, $password, $database_name);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }else{
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
            $stmt1->execute();

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
            $stmt2->execute();

            
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
          <a class="nav-link" href="#">Features</a>
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
            <h2>Left Block</h2>
            <p>This is some information about the left block. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis efficitur urna a augue hendrerit tincidunt. Vivamus at elit vel urna dignissim tincidunt non sed mi. Nulla ut massa ipsum.</p>
        </div>
        <div class="block">
            <h2>Right Block</h2>
            <p>This is some information about the right block. Phasellus pretium, lorem non ullamcorper fermentum, sapien nunc fermentum lacus, ut bibendum nunc velit nec libero. Fusce vel lorem eget nunc egestas interdum non in lorem.</p>
        </div>
        <div class="block">
            <h2>Left Block</h2>
            <p>This is some information about the left block. Curabitur nec ligula et urna tincidunt laoreet. Fusce nec arcu vel enim elementum sollicitudin ac non ipsum. Fusce non libero ut lorem rhoncus mattis.</p>
        </div>
        <div class="block">
            <h2>Right Block</h2>
            <p>This is some information about the right block. Proin tempor velit vel lorem laoreet, id mattis eros bibendum. Integer fermentum purus non orci venenatis ullamcorper. Phasellus eu purus vel metus bibendum vestibulum.</p>
        </div>
  </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" language="JavaScript" src="/scripts/script.js"></script>
</body>
</html>