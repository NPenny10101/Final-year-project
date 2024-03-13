<?php

session_start();

echo "<pre>";
print_r($_POST);
print_r($_FILES);
echo "</pre>";

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
        echo 'hiiiidfbid fid f';
        // Read the contents of the uploaded CSV file
        $csvData = file_get_contents($tempName);
//        print_r($csvData);
        // Process the CSV data (you can customize this part based on your requirements)
        // Example: Display CSV data
   //     echo "<pre>" . $csvData . "</pre>";
        
        $file = $_SESSION["csvFile"];
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
       $totalPageViews = 0;
    
       //for loops to go through each value in the csv array
        foreach ($csvArray as $row) {
            $uniqueVisitorIds[] = $row['fullVisitorId'];
            
            $visitId[] = $row['visitId'];
            
            $bounces[] = $row['bounces'];

            $dates[] = $row['date'];

            if ($row['bounces'] != 1 && $csvArray[$c - 1]['visitId'] != $csvArray[$c]['visitId']) {
                $totalTime = $totalTime + intval($row['timeOnSite']);
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


<h1>Title</h1>
<p>description of the tool</p>

<!-- <form class="row g-2" action="process_csv.php" method="POST" enctype="multipart/form-data">
<div id="uploadForm" class="col-auto">
    <label for="staticEmail2" class="visually-hidden">Email</label>
    <input id="upload" type="file" name="csvFile" accept=".csv"> 
  </div>
  <div class="col-auto">
    <button type="submit" class="btn btn-primary mb-3">Upload CSV</button>
  </div>
  <div class="row-auto">
    <label>URL</label>
    <input id="url" type="text" name="url"> 
  </div>
</form> -->


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" language="JavaScript" src="/scripts/script.js"></script>
</body>
</html>