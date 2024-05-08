<?php
session_start();



// Check if there are any error messages from previous submissions
if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
        echo "<p>Error: $error</p>";
    }
    unset($_SESSION['errors']); // Clear the errors
}
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
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="history.php">History</a>
        </li>
      </ul>
    </div>
  </div>
</nav>


<header>Title</header>
<p>This report should enlighten you regarding the perforamnce and users interaction wtih your website as it collates various key metrics from the analytics of the website.
  Comparing your website to industry standards can be misleading as your own expected results may differ from the norms however the information below is a good guidline to show you how your website is performing 
  compared to various industry standards taken from thousands of websites.
</p>

<form class="row g-3" action="process_csv.php" method="POST" enctype="multipart/form-data">
    <div class="col-md-auto">
        <label for="upload" class="form-label">Upload CSV</label>
        <input id="upload" type="file" class="form-control" name="csvFile" accept=".csv">
    </div>
    <div class="col-auto">
        <label for="url" class="form-label">URL</label>
        <input id="url" type="text" class="form-control" name="url">
    </div>
    <div class="col-auto">
        <label class="input-group-text" for="audience">Audience</label>
        <select class="form-select" id="audience" name="audience">
            <option selected>Choose...</option>
            <option value="General Public">General Public</option>
            <option value="Patients">Patients</option>
            <option value="Healthcare Professionals">Healthcare Professionals</option>
        </select>
    </div>
    <div class="col-md-auto">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script type="text/javascript" language="JavaScript" src="/scripts/script.js"></script>
</body>
</html>