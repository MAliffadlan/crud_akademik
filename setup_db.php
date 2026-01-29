<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = file_get_contents('database_setup.sql');

if ($conn->multi_query($sql)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
        // print divider
        if ($conn->more_results()) {
            printf("------------\n");
        }
    } while ($conn->next_result());
    echo "Database setup completed successfully.\n";
} else {
    echo "Error executing multi_query: " . $conn->error;
}

$conn->close();
?>
