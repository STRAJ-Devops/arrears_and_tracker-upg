<?php

// MySQL database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "data_imports";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if districts_sorted table exists, if not, create it
$table_check_query = "SHOW TABLES LIKE 'districts_sorted'";
$table_check_result = mysqli_query($conn, $table_check_query);

if (!$table_check_result) {
    die("Error checking table existence: " . mysqli_error($conn));
}

if (mysqli_num_rows($table_check_result) == 0) {
    // Table does not exist, create it
    $create_table_query = "CREATE TABLE districts_sorted (
                            region_id INT,
                            district_id INT,
                            district_name VARCHAR(255)
                        )";

    if (!mysqli_query($conn, $create_table_query)) {
        die("Error creating table: " . mysqli_error($conn));
    }
}

// Fetch districts from the database
$query = "SELECT region, district FROM districts";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error fetching districts: " . mysqli_error($conn));
}

$districts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $districts[] = [$row['region'], $row['district']];
}

// Generate SQL INSERT statements
$inserts = [];
foreach ($districts as $district) {
    $region_district = explode('-', $district[0]);
    $region_id = trim($region_district[0]);
    $district_district = explode('-', $district[1]);
    $district_id = trim($district_district[0]);
    $district_name = trim($district_district[1]);
    $inserts[] = "('" . $region_id . "', '" . $district_id . "', '" . $district_name . "')";
}

$insert_sql = "INSERT INTO `districts_sorted` (`region_id`, `district_id`, `district_name`) VALUES" . implode(",", $inserts) . ";";

echo $insert_sql;

// Insert districts into the districts_sorted table
if (!mysqli_query($conn, $insert_sql)) {
    die("Error inserting districts: " . mysqli_error($conn));
}

// Close the database connection
mysqli_close($conn);
?>
