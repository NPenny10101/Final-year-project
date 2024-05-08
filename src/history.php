<?php
$hostname = "localhost"; // Change to your MySQL server hostname
$username = "root"; // Change to your MySQL username
$password = "88vdmC6yawFPHf1"; // Change to your MySQL password
$database_name = "final_project"; // Change to your database name
$conn = new mysqli($hostname, $username, $password, $database_name);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}else{

            // SQL to retrieve the informatioin            

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

}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Analytics Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/style.css">
    <script>
/*         function sortTable(columnIndex, type) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("myTable");
            switching = true;
            dir = "asc";  // Set the initial sorting direction to ascending
            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[columnIndex];
                    y = rows[i + 1].getElementsByTagName("TD")[columnIndex];
                    if (dir == "asc") {
                        if ((type === 'num' ? parseFloat(x.innerHTML) : x.innerHTML.toLowerCase()) > 
                            (type === 'num' ? parseFloat(y.innerHTML) : y.innerHTML.toLowerCase())) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if ((type === 'num' ? parseFloat(x.innerHTML) : x.innerHTML.toLowerCase()) < 
                            (type === 'num' ? parseFloat(y.innerHTML) : y.innerHTML.toLowerCase())) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        } */

/*         function partition(rows, low, high, columnIndex, type) {
            const pivot = rows[high].getElementsByTagName("TD")[columnIndex];
            let i = low - 1;

            for (let j = low; j <= high - 1; j++) {
                let current = rows[j].getElementsByTagName("TD")[columnIndex];
                if ((type === 'num' ? parseFloat(current.innerHTML) : current.innerHTML.toLowerCase()) <
                    (type === 'num' ? parseFloat(pivot.innerHTML) : pivot.innerHTML.toLowerCase())) {
                    i++;
                    rows[i].parentNode.insertBefore(rows[j], rows[i]);
                }
            }
            rows[i + 1].parentNode.insertBefore(rows[high], rows[i + 1]);
            return i + 1;
        }

        function quickSort(rows, low, high, columnIndex, type) {
            if (low < high) {
                let pi = partition(rows, low, high, columnIndex, type);
                quickSort(rows, low, pi - 1, columnIndex, type);
                quickSort(rows, pi + 1, high, columnIndex, type);
            }
        } */

        function sortTable(columnIndex, type) {
            var table = document.getElementById("myTable");
            var tbody = table.getElementsByTagName('tbody')[0];
            var rows = Array.prototype.slice.call(tbody.getElementsByTagName("tr"));

            // Toggle the sorting direction
            if (table.getAttribute('data-sort-column') == columnIndex && table.getAttribute('data-sort-direction') == 'asc') {
                table.setAttribute('data-sort-direction', 'desc');
            } else {
                table.setAttribute('data-sort-column', columnIndex);
                table.setAttribute('data-sort-direction', 'asc');
            }
            var sortDirection = table.getAttribute('data-sort-direction');

            rows.sort(function(a, b) {
                var x = a.getElementsByTagName("TD")[columnIndex];
                var y = b.getElementsByTagName("TD")[columnIndex];
                x = type === 'num' ? parseFloat(x.innerHTML) : x.innerHTML.toLowerCase();
                y = type === 'num' ? parseFloat(y.innerHTML) : y.innerHTML.toLowerCase();

                // Apply sorting direction
                if (sortDirection === 'asc') {
                    return x > y ? 1 : (x < y ? -1 : 0);
                } else {
                    return x < y ? 1 : (x > y ? -1 : 0);
                }
            });

            // Reinsert sorted rows
            for (var i = 0; i < rows.length; i++) {
                tbody.appendChild(rows[i]);
            }
        }





        var currentPage = 1;
        var recordsPerPage = 10;

        function changePage(page) {
            var btn_next = document.getElementById("btn_next");
            var btn_prev = document.getElementById("btn_prev");
            var listing_table = document.getElementById("myTable");
            var rows = listing_table.getElementsByTagName("tr");
            var numPages = Math.ceil((rows.length - 1) / recordsPerPage); // Exclude the header row

            if (page < 1) page = 1;
            if (page > numPages) page = numPages;

            for (var i = 1; i < rows.length; i++) { // Start with 1 to avoid the header
                rows[i].style.display = "none";
            }

            for (var i = (page - 1) * recordsPerPage + 1; i < (page * recordsPerPage) + 1; i++) {
                if (rows[i]) rows[i].style.display = "";
            }

            btn_prev.style.display = (page == 1) ? "none" : "inline";
            btn_next.style.display = (page == numPages) ? "none" : "inline";
        }

        window.onload = function() {
            changePage(1);
        };

    </script>
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
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</nav>


  <h1>Past Analytics Reports</h1>
  <p>This page will provide a summary for the all of the past repots that have been submitted and are currently stored in our database. To sort this table please click on the headers of the column you wish to order by.</p>

  <div class="search-container mb-3">
      <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search for URLs..." class="form-control">
  </div>


  <table class="table table-striped table-dark" id="myTable">
      <thead>
          <tr>
          <th onclick="sortTable(0, 'num')">Web ID</th>
              <th onclick="sortTable(1, 'str')">URL</th>
              <th onclick="sortTable(2, 'num')">Report ID</th>
              <th onclick="sortTable(3, 'num')">Date</th>
              <th onclick="sortTable(4, 'num')">Date Range</th>
              <th onclick="sortTable(5, 'num')">Visits</th>
              <th onclick="sortTable(6, 'num')">Unique Visitors</th>
              <th onclick="sortTable(7, 'num')">Average Time</th>
              <th onclick="sortTable(8, 'num')">Page Views</th>
              <th onclick="sortTable(9, 'num')">Bounce Rate</th>
              <th onclick="sortTable(10, 'num')">Performance</th>
              <th onclick="sortTable(11, 'num')">Accessibility</th>
              <th onclick="sortTable(12, 'num')">SEO</th>
          </tr>
      </thead>
      <tbody>
          <?php
          // Assuming $data is your array of results;
          foreach ($dBDataset as $row) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['web_Id']) . "</td>";
              echo "<td>" . htmlspecialchars($row['url']) . "</td>";
              echo "<td>" . htmlspecialchars($row['report_Id']) . "</td>";
              echo "<td>" . htmlspecialchars($row['date']) . "</td>";
              echo "<td>" . htmlspecialchars($row['dateRange']) . "</td>";
              echo "<td>" . htmlspecialchars($row['visits']) . "</td>";
              echo "<td>" . htmlspecialchars($row['uniqueVisitors']) . "</td>";
              echo "<td>" . htmlspecialchars($row['averageTime']) . "</td>";
              echo "<td>" . htmlspecialchars($row['pageViews']) . "</td>";
              echo "<td>" . htmlspecialchars($row['bounceRate']) . "%</td>";
              echo "<td>" . htmlspecialchars($row['performance']) . "%</td>";
              echo "<td>" . htmlspecialchars($row['accessibility']) . "%</td>";
              echo "<td>" . htmlspecialchars($row['SEO']) . "%</td>";
              echo "</tr>";
          }
          ?>
      </tbody>
  </table>

  <div id="pagination_controls">
        <button id="btn_prev" onclick="changePage(currentPage - 1);">Prev</button>
        <button id="btn_next" onclick="changePage(currentPage + 1);">Next</button>
  </div>

<script> 
    function searchTable() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("searchInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("myTable");
      tr = table.getElementsByTagName("tr");

      // Loop through all table rows, and hide those who don't match the search query
      for (i = 1; i < tr.length; i++) { // Start with 1 to avoid the header
          td = tr[i].getElementsByTagName("td")[1]; // Index 1 is the URL column
          if (td) {
              txtValue = td.textContent || td.innerText;
              if (txtValue.toUpperCase().indexOf(filter) > -1) {
                  tr[i].style.display = "";
              } else {
                  tr[i].style.display = "none";
              }
          }       
      }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>