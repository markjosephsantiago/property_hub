<?php
session_start();
require '../../includes/conn.php';

// ✅ Check kung may reservation_id sa URL
if (!isset($_GET['reservation_id'])) {
    $_SESSION['error'] = "No reservation found.";
    header("Location: booking.form.php");
    exit();
}

$reservation_id = $_GET['reservation_id'];

// 🔍 Fetch booking details
$stmt = $conn->prepare("
    SELECT r.*, rm.room_number, rm.room_type, rm.price 
    FROM tbl_reservations r
    JOIN tbl_rooms rm ON r.room_id = rm.room_id
    WHERE r.reservation_id = ?
");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Reservation not found.";
    header("Location: booking.form.php");
    exit();
}

$booking = $result->fetch_assoc();

// ✅ Compute total price (price per 24h * hours_booked / 24)
$total_price = ($booking['price'] / 24) * $booking['duration'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Form</title>
    <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <style>
        body {
            background: #f4f6f9;
        }
        .payment-card {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .payment-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .payment-header h3 {
            font-weight: 700;
            color: #007bff;
        }
        .summary-table th {
            width: 40%;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="payment-header">
            <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
            <h3>Payment Information</h3>
            <p>Please review your booking details before payment.</p>
        </div>

        <table class="table table-bordered summary-table">
            <tr><th>Guest Name</th><td><?= htmlspecialchars($booking['guestName']) ?></td></tr>
            <tr><th>Room</th><td>Room <?= htmlspecialchars($booking['room_number']) ?> - <?= htmlspecialchars($booking['room_type']) ?></td></tr>
            <tr><th>Check-in</th><td><?= date("F d, Y h:i A", strtotime($booking['checkin'])) ?></td></tr>
            <tr><th>Check-out</th><td><?= date("F d, Y h:i A", strtotime($booking['checkout'])) ?></td></tr>
            <tr><th>Duration</th><td><?= $booking['duration'] ?> hour(s)</td></tr>
            <tr><th>Total Price</th><td><strong>₱<?= number_format($total_price, 2) ?></strong></td></tr>
        </table>

        <form action="../bookingProcess/ctrl.payment.php" method="POST">
            <input type="hidden" name="reservation_id" value="<?= $reservation_id ?>">
            <input type="hidden" name="amount" value="<?= $total_price ?>">

            <div class="form-group mt-3">
                <label for="payment_method">Select Payment Method</label>
                <select name="payment_method" id="payment_method" class="form-control" required>
                    <option value="">-- Choose Method --</option>
                    <option value="cash">Cash</option>
                    <option value="gcash">GCash</option>
                    <option value="credit_card">Credit Card</option>
                </select>
            </div>

            <div class="form-group mt-3">
                <label for="reference">Reference Number (if applicable)</label>
                <input type="text" name="reference" id="reference" class="form-control" placeholder="Enter GCash or Card Reference">
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-4">
                <i class="fas fa-check-circle"></i> Confirm Payment
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="booking.confirmation.php?code=<?= urlencode($booking['confirmation_code']) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Confirmation
            </a>
        </div>
    </div>
</body>
</html>
