<?php
include'landlorddash.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>House Type and Rent Form</title>
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">
    <script>
        // Function to handle form submission and display house type and rent fields
        function handleHouseSelection() {
            var houseSelect = document.getElementById("houseNumber");
            var houseTypeDiv = document.getElementById("houseTypeDiv");
            var rentDiv = document.getElementById("rentDiv");

            if (houseSelect.value) {
                houseTypeDiv.style.display = "block";
                rentDiv.style.display = "block";
            } else {
                houseTypeDiv.style.display = "none";
                rentDiv.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <header>Assign House Type and Rent</header>

        <?php
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'amani_db');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['propertyName']) && isset($_POST['houseNumber']) && isset($_POST['houseType']) && isset($_POST['rent'])) {
                $houseNumber = $_POST['houseNumber'];
                $houseType = $_POST['houseType'];
                $rent = $_POST['rent'];

                // Update the house type and rent in the database
                $stmt = $conn->prepare("UPDATE houses SET house_type = ?, rent = ? WHERE house_number = ?");
                $stmt->bind_param("sis", $houseType, $rent, $houseNumber);

                if ($stmt->execute()) {
                    echo "<div class='success-message'><h3>House type and rent updated successfully!</h3></div>";
                } else {
                    echo "<div class='error-message'><h3>There was an error updating the house information. Please try again.</h3></div>";
                }

                $stmt->close();
            }
        }

        // Fetch properties from the database
        $properties = $conn->query("SELECT id, property_name FROM properties");

        // Initialize house numbers array
        $houseNumbers = [];
        $selectedPropertyId = isset($_POST['propertyName']) ? intval($_POST['propertyName']) : 0;

        // Fetch houses based on the selected property if a property is selected
        if ($selectedPropertyId > 0) {
            $result = $conn->query("SELECT house_number FROM houses WHERE property_id = $selectedPropertyId AND (house_type IS NULL OR rent IS NULL)");

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $houseNumbers[] = $row;
                }
            }
        }
        ?>

        <form id="houseForm" method="post" action="">
            <div class="form">
                <div class="details personal">
                    <span class="title">House Assignment</span>

                    <div class="fields">
                        <!-- Dropdown to select property name -->
                        <div class="input-field">
                            <label for="propertyName">Select Property Name:</label>
                            <select id="propertyName" name="propertyName" onchange="this.form.submit()" required>
                                <option value="" disabled selected>Select Property</option>
                                <?php while ($row = $properties->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($selectedPropertyId == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo $row['property_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Dropdown to select house number, populated dynamically based on property selection -->
                        <div class="input-field">
                            <label for="houseNumber">Select House Number:</label>
                            <select id="houseNumber" name="houseNumber" onchange="handleHouseSelection()" required>
                                <option value="" disabled selected>Select House Number</option>
                                <?php foreach ($houseNumbers as $house): ?>
                                    <option value="<?php echo $house['house_number']; ?>">
                                        <?php echo $house['house_number']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Section to input house type and rent, shown when a house number is selected -->
                        <div id="houseTypeDiv" class="details personal" style="display:none;">
                            <div class="input-field">
                                <label for="houseType">Select House Type:</label>
                                <select id="houseType" name="houseType" required>
                                    <option value="" disabled selected>Select House Type</option>
                                    <option value="Single Room">Single Room</option>
                                    <option value="Bedsitter">Bedsitter</option>
                                    <option value="1 Bedroom">1 Bedroom</option>
                                    <option value="2 Bedroom">2 Bedroom</option>
                                    <option value="3 Bedroom">3 Bedroom</option>
                                </select>
                            </div>
                        </div>

                        <div id="rentDiv" class="details personal" style="display:none;">
                            <div class="input-field">
                                <label for="rent">Enter Rent Amount (KSH):</label>
                                <input type="number" id="rent" name="rent" min="0" step="0.01" placeholder="Enter rent amount" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="submit">
                        <span class="btnText">Submit</span>
                        <i class="uil uil-navigator"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
