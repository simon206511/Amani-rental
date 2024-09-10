<?php
include 'landlorddash.php';
include 'config.php'; 

// Create a new database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Generate Invoice Number
$monthYear = date('my'); // MMYY format
$invoiceNo = "inv" . $monthYear;

// Get current date
$invoiceDate = date('Y-m-d');

// Initialize variables
$rentAmount = 0;
$tenantId = '';
$tenantFullName = '';
$waterAmount = 0;
$garbageAmount = 0;
$electricityAmount = 0;
$totalAmount = 0;

// Handle invoice selection
if (isset($_GET['invoice_no']) && !empty($_GET['invoice_no'])) {
    $invoiceNo = $_GET['invoice_no'];

    // Fetch invoice details
    $invoiceQuery = $conn->prepare("SELECT * FROM invoice WHERE invoice_no = ?");
    $invoiceQuery->bind_param('s', $invoiceNo);
    $invoiceQuery->execute();
    $invoiceResult = $invoiceQuery->get_result();
    
    if ($invoiceResult->num_rows > 0) {
        $invoiceRow = $invoiceResult->fetch_assoc();
        $invoiceDate = $invoiceRow['invoice_date'];
        $tenantId = $invoiceRow['tenant_id'];
        $rentAmount = $invoiceRow['rent_amount'];
        $waterAmount = $invoiceRow['water_amount'];
        $garbageAmount = $invoiceRow['garbage_amount'];
        $electricityAmount = $invoiceRow['electricity_amount'];
        $totalAmount = $invoiceRow['total_amount'];

        // Fetch tenant details
        $tenantQuery = $conn->prepare("SELECT full_name FROM tenants WHERE tenant_id = ?");
        $tenantQuery->bind_param('s', $tenantId);
        $tenantQuery->execute();
        $tenantResult = $tenantQuery->get_result();
        
        if ($tenantResult->num_rows > 0) {
            $tenantRow = $tenantResult->fetch_assoc();
            $tenantFullName = $tenantRow['full_name'];
        }
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit'])) {
    $tenantFullName = $_POST['tenant_name'] ?? '';
    $waterAmount = $_POST['water-amount'] ?? 0;
    $garbageAmount = $_POST['garbage-amount'] ?? 0;
    $electricityAmount = $_POST['electricity-amount'] ?? 0;
    $totalAmount = $rentAmount + $waterAmount + $garbageAmount + $electricityAmount;

    // Fetch tenant_id based on tenant_full_name
    $tenantQuery = $conn->prepare("SELECT tenant_id FROM tenants WHERE full_name = ?");
    $tenantQuery->bind_param('s', $tenantFullName);
    $tenantQuery->execute();
    $tenantResult = $tenantQuery->get_result();
    
    if ($tenantResult->num_rows > 0) {
        $tenantRow = $tenantResult->fetch_assoc();
        $tenantId = $tenantRow['tenant_id'];
    } else {
        $tenantId = '';
    }

    // Update the invoice table
    $updateQuery = $conn->prepare("UPDATE invoice SET tenant_id = ?, rent_amount = ?, water_amount = ?, garbage_amount = ?, electricity_amount = ?, total_amount = ? WHERE invoice_no = ?");
    $updateQuery->bind_param('sddddds', $tenantId, $rentAmount, $waterAmount, $garbageAmount, $electricityAmount, $totalAmount, $invoiceNo);

    if ($updateQuery->execute()) {
        echo "<script>alert('Invoice updated successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $updateQuery->error . "');</script>";
    }
}

// Fetch all tenants
$tenantQuery = "SELECT full_name FROM tenants";
$tenantResult = $conn->query($tenantQuery);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Amani Nyumba Invoice</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="resources/css/invoice.css">
</head>
<body>

<div class="invoice-wrapper" id="print-area">
    <div class="invoice">
        <div class="invoice-container">
            <form method="POST" action="">
                <div class="invoice-head">
                    <div class="invoice-head-top">
                        <div class="invoice-head-top-left text-start">
                            <img src="resources/img/amani-logo.png" alt="Logo">
                        </div>
                        <div class="invoice-head-top-right text-end">
                            <h3>Edit Invoice</h3>
                        </div>
                    </div>
                    <div class="hr"></div>
                    <div class="invoice-head-middle">
                        <div class="invoice-head-middle-left text-start">
                            <p><span class="text-bold">Date</span>: <input type="date" name="invoice-date" value="<?php echo htmlspecialchars($invoiceDate); ?>" readonly></p>
                        </div>
                        <div class="invoice-head-middle-right text-end">
                            <p><span class="text-bold">Invoice No:</span> <input type="text" name="invoice-no" value="<?php echo htmlspecialchars($invoiceNo); ?>" readonly></p>
                        </div>
                    </div>
                    <div class="hr"></div>
                    <div class="invoice-head-bottom">
                        <div class="invoice-head-bottom-left">
                            <ul>
                                <li class="text-bold">Invoiced To:</li>
                                <li>
                                    <select name="tenant_name" id="tenant_name" onchange="this.form.submit()">
                                        <option value="">Select Tenant</option>
                                        <?php while ($tenantRow = $tenantResult->fetch_assoc()) { ?>
                                            <option value="<?php echo htmlspecialchars($tenantRow['full_name']); ?>" <?php echo $tenantFullName === $tenantRow['full_name'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tenantRow['full_name']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </li>
                            </ul>
                        </div>
                        <div class="invoice-head-bottom-right">
                            <ul class="text-end">
                                <li class="text-bold">Pay To:</li>
                                <li>Amani Nyumba</li>
                                <li>Paybill number</li>
                                <li>522 522</li>
                                <li>Account number 0769465261</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="overflow-view">
                    <div class="invoice-body">
                        <table>
                            <thead>
                            <tr>
                                <td class="text-bold">Service</td>
                                <td class="text-bold">Amount (KSH)</td>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Rent</td>
                                <td><input type="number" name="rent-amount" value="<?php echo htmlspecialchars($rentAmount); ?>" placeholder="Enter Amount" readonly></td>
                            </tr>
                            <tr>
                                <td>Water Bill</td>
                                <td><input type="number" name="water-amount" value="<?php echo htmlspecialchars($waterAmount); ?>" placeholder="Enter Amount"></td>
                            </tr>
                            <tr>
                                <td>Garbage Bill</td>
                                <td><input type="number" name="garbage-amount" value="<?php echo htmlspecialchars($garbageAmount); ?>" placeholder="Enter Amount"></td>
                            </tr>
                            <tr>
                                <td>Electricity Bill</td>
                                <td><input type="number" name="electricity-amount" value="<?php echo htmlspecialchars($electricityAmount); ?>" placeholder="Enter Amount"></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="invoice-body-bottom">
                            <div class="invoice-body-info-item">
                                <div class="info-item-td text-end text-bold">Total:</div>
                                <div class="info-item-td text-end"><input type="number" name="total" value="<?php echo htmlspecialchars($totalAmount); ?>" placeholder="Total Amount" readonly></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="invoice-foot text-center">
                    <p><span class="text-bold text-center">NOTE:&nbsp;</span>This is computer generated and does not require physical signature.</p>

                    <div class="invoice-btns">
                        <button type="button" class="invoice-btn" onclick="printInvoice()">
                            <span>
                                <i class="fa-solid fa-print"></i>
                            </span>
                            <span>Print</span>
                        </button>
                        <button type="button" class="invoice-btn">
                            <span>
                                <i class="fa-solid fa-download"></i>
                            </span>
                            <span>Download</span>
                        </button>
                    </div>
                    <div class="text-center">
                        <button type="submit" name="submit" class="invoice-btn">
                            <span>Save Invoice</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function printInvoice() {
        window.print();
    }
</script>

</body>
</html>

<?php $conn->close(); ?>
