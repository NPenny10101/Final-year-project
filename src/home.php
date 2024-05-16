<?php
session_start();



// Check if there are any error messages from previous submissions
if (isset($_SESSION['errors'])) {
    foreach ($_SESSION['errors'] as $error) {
        echo "<p>Error: $error</p>";
    }
    unset($_SESSION['errors']); // Clear the errors
}

include 'header.php';
?>


<!DOCTYPE html>
<html>

<h1>Website Analytics Report Generator</h1>
<h3>This report should enlighten you regarding the perforamnce and users interaction wtih your website as it collates various key metrics from the analytics of the website.
  Comparing your website to industry standards can be misleading as your own expected results may differ from the norms however the information below is a good guidline to show you how your website is performing 
  compared to various industry standards taken from thousands of websites.
</h3>

<form class="row g-3" action="report.php" method="POST" enctype="multipart/form-data">
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