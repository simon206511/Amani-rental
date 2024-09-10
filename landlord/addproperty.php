<?php
include 'landlorddash.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="resources/css/style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script>
        function handlePropertyTypeChange() {
            const propertyType = document.getElementById("propertyType").value;
            const apartmentDetails = document.getElementById("apartmentDetails");

            if (propertyType === "apartment") {
                apartmentDetails.style.display = "block";
            } else {
                apartmentDetails.style.display = "none";
                clearApartmentDetails();
            }
        }

        function handleHouseCountChange() {
            const numberOfHouses = document.getElementById("numberOfHouses").value;
            const floorDetails = document.getElementById("floorDetails");

            floorDetails.innerHTML = ""; // Clear previous floor inputs

            if (numberOfHouses > 1) {
                const numberOfFloorsLabel = document.createElement("label");
                numberOfFloorsLabel.innerHTML = "Number of Floors:";
                floorDetails.appendChild(numberOfFloorsLabel);

                const numberOfFloorsInput = document.createElement("input");
                numberOfFloorsInput.type = "number";
                numberOfFloorsInput.id = "numberOfFloors";
                numberOfFloorsInput.name = "numberOfFloors";
                numberOfFloorsInput.min = 1;
                numberOfFloorsInput.onchange = handleFloorCountChange;
                floorDetails.appendChild(numberOfFloorsInput);
            }
        }

        function handleFloorCountChange() {
            const numberOfFloors = document.getElementById("numberOfFloors").value;
            const floorDetails = document.getElementById("floorDetails");

            // Clear existing floor inputs
            const existingFloorInputs = document.querySelectorAll(".floorInput");
            existingFloorInputs.forEach(input => input.remove());

            for (let i = 1; i <= numberOfFloors; i++) {
                const floorLabel = document.createElement("label");
                floorLabel.innerHTML = `Number of Houses on Floor ${i}:`;
                floorLabel.className = "floorInput";
                floorDetails.appendChild(floorLabel);

                const floorInput = document.createElement("input");
                floorInput.type = "number";
                floorInput.className = "floorInput";
                floorInput.name = `floor${i}Houses`;
                floorInput.min = 1;
                floorDetails.appendChild(floorInput);
            }
        }

        function clearApartmentDetails() {
            document.getElementById("numberOfHouses").value = "";
            document.getElementById("floorDetails").innerHTML = "";
        }
    </script>
</head>
<body>
    <div class="container">
        <header>Property Registration Form</header>

        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Database connection
            $conn = new mysqli('localhost', 'root', '', 'amani_db');

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $propertyName = strtoupper(trim($_POST['propertyName'])); // Trim whitespace and convert to uppercase
            $propertyType = $_POST['propertyType'];
            $success = false;

            // Check if the property name already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM properties WHERE property_name = ?");
            $stmt->bind_param("s", $propertyName);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                echo "<div class='error-message'><h3>The property name already exists. Please choose a different name.</h3></div>";
            } else {
                try {
                    if ($propertyType == 'standalone') {
                        $houseNumber = $propertyName . "0001";

                        // Insert the property into the database
                        $stmt = $conn->prepare("INSERT INTO properties (property_name, property_type) VALUES (?, ?)");
                        $stmt->bind_param("ss", $propertyName, $propertyType);
                        $stmt->execute();
                        $propertyId = $stmt->insert_id;

                        // Insert the house number into the database
                        $stmt = $conn->prepare("INSERT INTO houses (property_id, house_number) VALUES (?, ?)");
                        $stmt->bind_param("is", $propertyId, $houseNumber);
                        $stmt->execute();

                        $success = true;
                    } elseif ($propertyType == 'apartment') {
                        $numberOfHouses = (int)$_POST['numberOfHouses'];
                        $numberOfFloors = (int)$_POST['numberOfFloors'];
                        $houseCount = 1;

                        // Insert the property into the database
                        $stmt = $conn->prepare("INSERT INTO properties (property_name, property_type, number_of_houses, number_of_floors) VALUES (?, ?, ?, ?)");
                        $stmt->bind_param("ssii", $propertyName, $propertyType, $numberOfHouses, $numberOfFloors);
                        $stmt->execute();
                        $propertyId = $stmt->insert_id;

                        for ($i = 1; $i <= $numberOfFloors; $i++) {
                            $floorHouses = (int)$_POST["floor{$i}Houses"];
                            for ($j = 1; $j <= $floorHouses; $j++) {
                                $houseNumber = $propertyName . sprintf("%02d", $i) . sprintf("%02d", $houseCount);

                                // Insert each house number into the database
                                $stmt = $conn->prepare("INSERT INTO houses (property_id, house_number, floor_number) VALUES (?, ?, ?)");
                                $stmt->bind_param("isi", $propertyId, $houseNumber, $i);
                                $stmt->execute();

                                $houseCount++;
                            }
                        }

                        $success = ($houseCount - 1) == $numberOfHouses;
                    }
                } catch (mysqli_sql_exception $e) {
                    echo "<div class='error-message'><h3>Error: " . $e->getMessage() . "</h3></div>";
                }
            }

            $conn->close();

            if ($success) {
                echo "<div class='success-message'><h3>Data added successfully!</h3></div>";
            } else {
                echo "<div class='error-message'><h3>There was an error processing your request. Please try again.</h3></div>";
            }
        }
        ?>

        <form id="propertyForm" method="post" action="">
            <div class="form">
                <div class="details personal">
                    <span class="title">Property Details</span>

                    <div class="fields">
                        <div class="input-field">
                            <label for="propertyName">Property Name:</label>
                            <input type="text" id="propertyName" name="propertyName" placeholder="Enter property name" required>
                        </div>

                        <div class="input-field">
                            <label for="propertyType">Property Type:</label>
                            <select id="propertyType" name="propertyType" onchange="handlePropertyTypeChange()" required>
                                <option value="" disabled selected>Select Property Type</option>
                                <option value="apartment">Apartment</option>
                                <option value="standalone">Standalone</option>
                            </select>
                        </div>
                    </div>

                    <!-- Apartment-specific fields -->
                    <div id="apartmentDetails" class="details ID" style="display:none;">
                        <div class="input-field">
                            <label for="numberOfHouses">How many houses?</label>
                            <input type="number" id="numberOfHouses" name="numberOfHouses" min="1" placeholder="Enter number of houses" onchange="handleHouseCountChange()">
                        </div>

                        <div class="fields" id="floorDetails"></div>
                    </div>
                </div>           

                <button type="submit" class="submit">
                    <span class="btnText">Submit</span>
                    <i class="uil uil-navigator"></i>
                </button>
            </div>
        </form>
    </div>
</body>
</html>
