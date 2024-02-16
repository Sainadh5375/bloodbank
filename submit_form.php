<?php
// Include the database configuration file
include "db.inc.php";
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

// Check if the connection was successful
if (mysqli_connect_errno()) {
    die("Failed to connect to MySQL: " . mysqli_connect_error());
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Escape and retrieve data from the form
    $name = mysqli_real_escape_string($connection, $_POST['fname']);
    $age = mysqli_real_escape_string($connection, $_POST['Age']);
    $gender = mysqli_real_escape_string($connection, $_POST['Gender']);
    $bloodGroup = mysqli_real_escape_string($connection, $_POST['Blood_Group']);
    $reason = mysqli_real_escape_string($connection, $_POST['Reason']);
    $quantity = mysqli_real_escape_string($connection, $_POST['Quantity']);
    $optionalDetails = mysqli_real_escape_string($connection, $_POST['Optional']);
    $latitude = mysqli_real_escape_string($connection, $_POST['latitude']);
    $longitude = mysqli_real_escape_string($connection, $_POST['longitude']);

    // Convert coordinates to address
    $address = coordinatesToAddress($latitude, $longitude);

    // Generate a unique registration code
    $registrationCode = generateRegistrationCode($connection);

    // Insert the new request into the database
    $insertQuery = "INSERT INTO requests (name, age, gender, blood_group, reason, quantity, optional_details, address, registration_code) VALUES ('$name', '$age', '$gender', '$bloodGroup', '$reason', '$quantity', '$optionalDetails', '$address', '$registrationCode')";

    if (mysqli_query($connection, $insertQuery)) {
        echo "Request registered successfully. Your registration code is: $registrationCode";
    } else {
        echo "Error registering request: " . mysqli_error($connection);
    }
}

// Function to convert coordinates to address
function coordinatesToAddress($latitude, $longitude) {
    // Use Python code to convert coordinates to address
    $pythonScriptPath = "trail.py";
    $command = "python3 $pythonScriptPath $latitude $longitude";
    $output = shell_exec($command);
    return $output;
}

// Function to generate a unique registration code
function generateRegistrationCode($connection) {
    // Loop until a unique registration code is obtained
    do {
        // Generate a random 6-digit numerical code
        $registrationCode = mt_rand(100000, 999999);

        // Check if the code already exists in the database
        $query = "SELECT * FROM requests WHERE registration_code = '$registrationCode'";
        $result = mysqli_query($connection, $query);

        // If the code doesn't exist, break the loop
        if (mysqli_num_rows($result) == 0) {
            break;
        }
    } while (true);

    // Return the unique registration code
    return $registrationCode;
}

// Close the database connection
mysqli_close($connection);
?>