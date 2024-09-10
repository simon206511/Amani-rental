<?php
include 'config.php';

// Check if property name is provided
if (!isset($_POST['propertyName'])) {
    die("Property name is required.");
}

$propertyName = $_POST['propertyName'];

// Fetch property ID
$propertyIdResult = $conn->prepare("SELECT id FROM properties WHERE property_name = ?");
$propertyIdResult->bind_param('s', $propertyName);
$propertyIdResult->execute();
$propertyId = $propertyIdResult->get_result()->fetch_assoc()['id'];

if (!$propertyId) {
    die("Property not found.");
}

// Fetch house numbers for the property
$houseNumbers = [];
$houseResult = $conn->prepare("SELECT house_number FROM houses WHERE property_id = ?");
$houseResult->bind_param('i', $propertyId);
$houseResult->execute();
$houseResult = $houseResult->get_result();
while ($row = $houseResult->fetch_assoc()) {
    $houseNumbers[] = $row;
}

// Generate options for the house numbers dropdown
$options = '<option value="" disabled>Select House Number</option>';
foreach ($houseNumbers as $house) {
    $options .= '<option value="' . htmlspecialchars($house['house_number']) . '">' . htmlspecialchars($house['house_number']) . '</option>';
}

echo $options;
?>
