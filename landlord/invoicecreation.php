<?php
include 'tenantd.php';
include 'config.php';

// Get the invoice ID from the query string
$invoice_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if connection is valid
if ($conn->ping()) {
    // Query to fetch details of a specific invoice
    $query = "SELECT * FROM invoice WHERE id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $invoice = $result->fetch_assoc();
    } else {
        die("Failed to prepare the statement: " . $conn->error);
    }
} else {
    die("Connection is not valid.");
}

// Fetch tenant details for the dropdown
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
                            <h3>Create Invoice</h3>
                        </div>
                    </div>
                    <div class="hr"></div>
                    <div class="invoice-head-middle">
                        <div class="invoice-head-middle-left text-start">
                            <p><span class="text-bold">Date</span>: <input type="date" name="invoice-date" value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>" readonly></p>
                        </div>
                        <div class="invoice-head-middle-right text-end">
                            <p><span class="text-bold">Invoice No:</span> <input type="text" name="invoice-no" value="<?php echo htmlspecialchars($invoice['invoice_no']); ?>" readonly></p>
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
                                            <option value="<?php echo htmlspecialchars($tenantRow['full_name']); ?>" <?php echo isset($tenantFullName) && $tenantFullName == $tenantRow['full_name'] ? 'selected' : ''; ?>>
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
                                <td><input type="number" name="rent-amount" value="<?php echo htmlspecialchars($invoice['rent_amount']); ?>" placeholder="Enter Amount" readonly></td>
                            </tr>
                            <tr>
                                <td>Water Bill</td>
                                <td><input type="number" name="water-amount" value="<?php echo htmlspecialchars($invoice['water_amount']); ?>" placeholder="Enter Amount"></td>
                            </tr>
                            <tr>
                                <td>Garbage Bill</td>
                                <td><input type="number" name="garbage-amount" value="<?php echo htmlspecialchars($invoice['garbage_amount']); ?>" placeholder="Enter Amount"></td>
                            </tr>
                            <tr>
                                <td>Electricity Bill</td>
                                <td><input type="number" name="electricity-amount" value="<?php echo htmlspecialchars($invoice['electricity_amount']); ?>" placeholder="Enter Amount"></td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="invoice-body-bottom">
                            <div class="invoice-body-info-item">
                                <div class="info-item-td text-end text-bold">Total:</div>
                                <div class="info-item-td text-end"><input type="number" name="total" value="<?php echo htmlspecialchars($invoice['total_amount']); ?>" placeholder="Total Amount" readonly></div>
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

<?php
// Clean up
$stmt->close();
$conn->close();
?>
