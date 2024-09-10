<?php
include 'landlorddash.php';
include 'config.php'; 

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Tenant ID is required.");
}

$tenantId = $_GET['id'];

// Fetch tenant details
$stmt = $conn->prepare("SELECT * FROM tenants WHERE tenant_id = ?");
$stmt->bind_param('s', $tenantId);
$stmt->execute();
$tenant = $stmt->get_result()->fetch_assoc();

if (!$tenant) {
    die("Tenant not found.");
}

// Fetch properties for the dropdown
$propertyNames = [];
$propertyResult = $conn->query("SELECT id, property_name FROM properties");
if ($propertyResult) {
    while ($row = $propertyResult->fetch_assoc()) {
        $propertyNames[] = $row;
    }
} else {
    echo "Error fetching property names: " . $conn->error;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
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

    // Fetch property ID
    $propertyIdResult = $conn->prepare("SELECT id FROM properties WHERE property_name = ?");
    $propertyIdResult->bind_param('s', $propertyName);
    $propertyIdResult->execute();
    $propertyId = $propertyIdResult->get_result()->fetch_assoc()['id'];

    // Update tenant details
    $stmt = $conn->prepare("UPDATE tenants SET full_name = ?, date_of_birth = ?, id_number = ?, email = ?, mobile_number = ?, gender = ?, occupation = ?, emergency_contact_name = ?, emergency_contact_phone = ?, property_id = ?, house_number = ?, start_lease_date = ? WHERE tenant_id = ?");
    $stmt->bind_param('sssssssssssss', $fullName, $dateOfBirth, $idNumber, $email, $mobileNumber, $gender, $occupation, $emergencyContactName, $emergencyContactPhone, $propertyId, $houseNumber, $startLeaseDate, $tenantId);
    
    if ($stmt->execute()) {
        $notification = "<div class='success-message'><h3>Tenant details updated successfully!</h3></div>";
    } else {
        $notification = "<div class='error-message'><h3>Error updating tenant details. Please try again.</h3></div>";
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
    <title>Edit Tenant</title>
    <link rel="stylesheet" type="text/css" href="resources/css/style3.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#propertyName').change(function() {
                var propertyName = $(this).val();
                if (propertyName) {
                    $.ajax({
                        url: 'get_house_numbers.php',
                        type: 'POST',
                        data: { propertyName: propertyName },
                        success: function(data) {
                            $('#houseNumber').html(data);
                        }
                    });
                } else {
                    $('#houseNumber').html('<option value="" disabled>Select House Number</option>');
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <header>Edit Tenant</header>
        <?php if (isset($notification)): ?>
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
                            <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($tenant['full_name']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label>Date of Birth</label>
                            <input type="date" id="dateOfBirth" name="dateOfBirth" value="<?php echo htmlspecialchars($tenant['date_of_birth']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label>ID Number</label>
                            <input type="text" id="idNumber" name="idNumber" value="<?php echo htmlspecialchars($tenant['id_number']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label>Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($tenant['email']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label>Mobile Number</label>
                            <input type="tel" id="mobileNumber" name="mobileNumber" value="<?php echo htmlspecialchars($tenant['mobile_number']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label>Gender</label>
                            <select id="gender" name="gender" required>
                                <option disabled>Select gender</option>
                                <option value="Male" <?php echo ($tenant['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($tenant['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Others" <?php echo ($tenant['gender'] == 'Others') ? 'selected' : ''; ?>>Others</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="details ID">
                    <span class="title">Other Details</span>
                    <div class="fields">
                        <div class="input-field">
                            <label>Occupation</label>
                            <input type="text" id="occupation" name="occupation" value="<?php echo htmlspecialchars($tenant['occupation']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label>Emergency Contact Name</label>
                            <input type="text" id="emergencyContactName" name="emergencyContactName" value="<?php echo htmlspecialchars($tenant['emergency_contact_name']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label>Emergency Contact Phone Number</label>
                            <input type="tel" id="emergencyContactPhone" name="emergencyContactPhone" value="<?php echo htmlspecialchars($tenant['emergency_contact_phone']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="propertyName">Property Name:</label>
                            <select id="propertyName" name="propertyName" required>
                                <option value="" disabled>Select Property Name</option>
                                <?php foreach ($propertyNames as $property): ?>
                                    <option value="<?php echo htmlspecialchars($property['property_name']); ?>" <?php echo ($tenant['property_id'] == $property['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($property['property_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <label for="houseNumber">House Number:</label>
                            <select id="houseNumber" name="houseNumber" required>
                                <option value="" disabled>Select House Number</option>
                                <?php
                                // Fetch house numbers based on the tenant's property
                                $houseNumbers = [];
                                $houseResult = $conn->prepare("SELECT house_number FROM houses WHERE property_id = ?");
                                $houseResult->bind_param('i', $tenant['property_id']);
                                $houseResult->execute();
                                $houseResult = $houseResult->get_result();
                                while ($row = $houseResult->fetch_assoc()) {
                                    $houseNumbers[] = $row;
                                }
                                foreach ($houseNumbers as $house): ?>
                                    <option value="<?php echo htmlspecialchars($house['house_number']); ?>" <?php echo ($tenant['house_number'] == $house['house_number']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($house['house_number']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-field">
                            <label for="rent">Rent Amount (KSH):</label>
                            <input type="number" id="rent" name="rent" value="<?php echo htmlspecialchars($tenant['rent']); ?>" readonly required>
                        </div>
                        <div class="input-field">
                            <label for="startLeaseDate">Start Lease Date</label>
                            <input type="date" id="startLeaseDate" name="startLeaseDate" value="<?php echo htmlspecialchars($tenant['start_lease_date']); ?>" required>
                        </div>
                        <button type="submit" name="update" class="submit">Update Tenant</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
