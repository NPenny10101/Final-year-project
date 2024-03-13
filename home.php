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
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


<header>Title</header>
<p>description of the tool</p>

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
            <option value="1">General Public</option>
            <option value="2">Patients</option>
            <option value="3">Healthcare Professionals</option>
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