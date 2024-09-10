<?php
include'landlorddash.php';
// Database connection
$conn = new mysqli('localhost', 'root', '', 'amani_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$propertyNames = [];
$houseNumbers = [];
$rent = null;
$notification = '';

// Fetch property names
$propertyResult = $conn->query("SELECT id, property_name FROM properties");
if ($propertyResult) {
    while ($row = $propertyResult->fetch_assoc()) {
        $propertyNames[] = $row;
    }
} else {
    echo "Error fetching property names: " . $conn->error;
}

// Fetch house numbers based on selected property
if (isset($_POST['propertyName'])) {
    $propertyName = $_POST['propertyName'];
    $propertyIdResult = $conn->prepare("SELECT id FROM properties WHERE property_name = ?");
    $propertyIdResult->bind_param('s', $propertyName);
    $propertyIdResult->execute();
    $propertyIdResult = $propertyIdResult->get_result()->fetch_assoc();
    $propertyId = $propertyIdResult['id'];

    $houseResult = $conn->prepare("SELECT house_number FROM houses WHERE property_id = ? AND is_occupied = FALSE");
    $houseResult->bind_param('i', $propertyId);
    $houseResult->execute();
    $houseNumbers = $houseResult->get_result()->fetch_all(MYSQLI_ASSOC);

    // Fetch rent for selected house number
    if (isset($_POST['houseNumber'])) {
        $houseNumber = $_POST['houseNumber'];
        $rentResult = $conn->prepare("SELECT rent FROM houses WHERE house_number = ? AND property_id = ?");
        $rentResult->bind_param('si', $houseNumber, $propertyId);
        $rentResult->execute();
        $rent = $rentResult->get_result()->fetch_assoc()['rent'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $fullName = $_POST['fullName'];
    $dateOfBirth = $_POST['dateOfBirth'];
    $idNumber = $_POST['idNumber'];
    $email = $_POST['email'];
    $mobileNumber = $_POST['mobileNumber'];
    $gender = $_POST['gender'];
    $occupation = $_POST['occupation'];
    $emergencyContactName = $_POST['emergencyContactName'];
    $emergencyContactPhone = $_POST['emergencyContactPhone'];
    $propertyName = $_POST['propertyName'];
    $houseNumber = $_POST['houseNumber'];
    $startLeaseDate = $_POST['startLeaseDate'];

    // Fetch property ID from property name
    $propertyIdResult = $conn->prepare("SELECT id FROM properties WHERE property_name = ?");
    $propertyIdResult->bind_param('s', $propertyName);
    $propertyIdResult->execute();
    $propertyId = $propertyIdResult->get_result()->fetch_assoc()['id'];

    // Generate tenant ID
    $tenantId = substr($houseNumber, 0, 3) . substr($idNumber, 0, 3);

    // Insert tenant data into the database
    $stmt = $conn->prepare("INSERT INTO tenants (tenant_id, full_name, date_of_birth, id_number, email, mobile_number, gender, occupation, emergency_contact_name, emergency_contact_phone, property_id, house_number, start_lease_date, rent, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL)");
    $stmt->bind_param('ssssssssssssss', $tenantId, $fullName, $dateOfBirth, $idNumber, $email, $mobileNumber, $gender, $occupation, $emergencyContactName, $emergencyContactPhone, $propertyId, $houseNumber, $startLeaseDate, $rent);
    
    if ($stmt->execute()) {
        $notification = "<div class='success-message'><h3>Tenant registered successfully!</h3></div>";
    } else {
        $notification = "<div class='error-message'><h3>Error registering tenant. Please try again.</h3></div>";
    }
    
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant Registration Form</title>
    <link rel="stylesheet" type="text/css" href="resources/css/style3.css">
</head>
<body>
    <div class="container">
        <header>Tenant Registration</header>
        <?php if ($notification): ?>
            <div class="notification">
                <?php echo $notification; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form first">
                <div class="details personal">
                    <span class="title">Personal Details</span>
                    <div class="fields">
                        <div class="input-field">
                            <label>Full Name</label>
                            <input type="text" id="fullName" name="fullName" placeholder="Enter your name" required>
                        </div>

                        <div class="input-field">
                            <label>Date of Birth</label>
                            <input type="date" id="dateOfBirth" name="dateOfBirth" placeholder="Enter birth date" required>
                        </div>
                        <div class="input-field">
                            <label>ID Number</label>
                            <input type="text" id="idNumber" name="idNumber" placeholder="Enter ID number" required>
                        </div>
                        <div class="input-field">
                            <label>Email</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>

                        <div class="input-field">
                            <label>Mobile Number</label>
                            <input type="tel" id="mobileNumber" name="mobileNumber" placeholder="Enter mobile number" required>
                        </div>

                        <div class="input-field">
                            <label>Gender</label>
                            <select id="gender" name="gender" required>
                                <option disabled selected>Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="details ID">
                    <span class="title">Other Details</span>

                    <div class="fields">
                        <div class="input-field">
                            <label>Occupation</label>
                            <input type="text" id="occupation" name="occupation" placeholder="Enter your occupation" required>
                        </div>
                        <div class="input-field">
                            <label>Emergency Contact Name</label>
                            <input type="text" id="emergencyContactName" name="emergencyContactName" placeholder="Emergency Contact Name" required>
                        </div>
                        <div class="input-field">
                            <label>Emergency Contact Phone number</label>
                            <input type="tel" id="emergencyContactPhone" name="emergencyContactPhone" placeholder="Emergency Contact Number" required>
                        </div>

                        <div class="input-field">
                            <label for="propertyName">Property Name:</label>
                            <select id="propertyName" name="propertyName" onchange="this.form.submit()" required>
                                <option value="" disabled selected>Select Property Name</option>
                                <?php foreach ($propertyNames as $property): ?>
                                    <option value="<?php echo htmlspecialchars($property['property_name']); ?>" <?php echo (isset($_POST['propertyName']) && $_POST['propertyName'] == $property['property_name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($property['property_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input-field">
                            <label for="houseNumber">House Number:</label>
                            <select id="houseNumber" name="houseNumber" required>
                                <option value="" disabled selected>Select House Number</option>
                                <?php if (isset($_POST['propertyName'])): ?>
                                    <?php foreach ($houseNumbers as $house): ?>
                                        <option value="<?php echo htmlspecialchars($house['house_number']); ?>" <?php echo (isset($_POST['houseNumber']) && $_POST['houseNumber'] == $house['house_number']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($house['house_number']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="input-field">
                            <label for="rent">Rent Amount (KSH):</label>
                            <input type="number" id="rent" name="rent" value="<?php echo htmlspecialchars($rent); ?>" readonly required>
                        </div>

                        <div class="input-field">
                            <label for="startLeaseDate">Start Lease Date</label>
                            <input type="date" id="startLeaseDate" name="startLeaseDate" placeholder="Start lease date" required>
                        </div>
                        <button type="submit" name="register" class="submit">Register Tenant</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
