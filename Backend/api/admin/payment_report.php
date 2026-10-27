<?php

session_start();
include '../../config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../../index.php");
    exit();
}

// Get all payments with purchase and user details
$paymentsQuery = mysqli_query($conn, "
    SELECT 
        p.id as payment_id,
        p.purchase_id,
        p.payment_method,
        p.amount,
        p.payment_status,
        p.transaction_id,
        p.payment_date,
        pur.user_id,
        u.name,
        u.email,
        t.ticket_name,
        pur.quantity,
        pur.purchase_date
    FROM payments p
    INNER JOIN purchases pur ON p.purchase_id = pur.id
    INNER JOIN users u ON pur.user_id = u.id
    INNER JOIN ticket_types t ON pur.ticket_type_id = t.id
    ORDER BY p.payment_date DESC
");

// Get payment statistics
$statsQuery = mysqli_query($conn, "
    SELECT 
        COUNT(*) as total_payments,
        SUM(amount) as total_revenue,
        AVG(amount) as avg_payment,
        payment_method,
        payment_status
    FROM payments
    GROUP BY payment_method, payment_status
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Report - NOVA Festival Admin</title>
    <link rel="stylesheet" href="../../style.css">
    <style>
        body {
            background: linear-gradient(135deg, rgba(6, 22, 33, 0.98) 0%, rgba(11, 33, 64, 0.98) 50%, rgba(17, 42, 76, 0.98) 100%);
            padding: 40px 20px;
        }

        .payment-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid rgba(248, 166, 1, 0.2);
        }

        .report-header h1 {
            color: var(--accent);
            font-size: 2rem;
            margin: 0;
        }

        .btn-back {
            background: rgba(248, 166, 1, 0.2);
            color: var(--accent);
            border: 1px solid var(--accent);
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: rgba(248, 166, 1, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(248, 166, 1, 0.2);
            border-radius: 12px;
            padding: 20px;
            backdrop-filter: blur(10px);
        }

        .stat-label {
            color: var(--muted);
            font-size: 0.9rem;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .stat-value {
            color: var(--accent);
            font-size: 1.8rem;
            font-weight: 700;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(248, 166, 1, 0.2);
            border-radius: 12px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .data-table thead {
            background: rgba(248, 166, 1, 0.1);
        }

        .data-table th {
            padding: 15px;
            text-align: left;
            font-weight: 700;
            color: var(--accent);
            border-bottom: 2px solid rgba(248, 166, 1, 0.2);
        }

        .data-table td {
            padding: 12px 15px;
            border-bottom: 1px solid rgba(248, 166, 1, 0.1);
            color: var(--text);
        }

        .data-table tbody tr:hover {
            background: rgba(248, 166, 1, 0.05);
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-completed {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
        }

        .badge-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .badge-credit {
            background: rgba(58, 156, 233, 0.2);
            color: #3a9ce9;
        }

        .badge-paypal {
            background: rgba(0, 150, 220, 0.2);
            color: #0096dc;
        }

        .section-title {
            color: var(--text);
            font-size: 1.3rem;
            margin-bottom: 20px;
            padding-top: 30px;
            border-top: 2px solid rgba(248, 166, 1, 0.2);
            padding-top: 20px;
        }

        .transaction-id {
            font-family: "Courier New", monospace;
            font-size: 0.85rem;
            color: var(--accent);
            background: rgba(248, 166, 1, 0.1);
            padding: 4px 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="report-header">
            <h1>💰 Payment Report</h1>
            <button class="btn-back" onclick="history.back()">← Back</button>
        </div>

        <h2 class="section-title">Payment Summary</h2>
        <div class="stats-grid">
            <?php
            mysqli_data_seek($statsQuery, 0);
            $totalPayments = 0;
            $totalRevenue = 0;
            while ($stat = mysqli_fetch_assoc($statsQuery)) {
                $totalPayments += $stat['total_payments'];
                $totalRevenue += $stat['total_revenue'];
            }
            ?>
            <div class="stat-card">
                <div class="stat-label">Total Payments</div>
                <div class="stat-value"><?php echo $totalPayments; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value">€<?php echo number_format($totalRevenue, 2); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Average Payment</div>
                <div class="stat-value">€<?php echo $totalPayments > 0 ? number_format($totalRevenue / $totalPayments, 2) : '0.00'; ?></div>
            </div>
        </div>

        <h2 class="section-title">All Payments</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User Name</th>
                    <th>Email</th>
                    <th>Ticket</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($paymentsQuery) > 0): ?>
                    <?php while ($payment = mysqli_fetch_assoc($paymentsQuery)): ?>
                        <tr>
                            <td>
                                <span class="transaction-id" title="<?php echo $payment['transaction_id']; ?>">
                                    <?php echo substr($payment['transaction_id'], 0, 8) . '...'; ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($payment['name']); ?></td>
                            <td><?php echo htmlspecialchars($payment['email']); ?></td>
                            <td><?php echo htmlspecialchars($payment['ticket_name']); ?></td>
                            <td><?php echo $payment['quantity']; ?></td>
                            <td><strong>€<?php echo number_format($payment['amount'], 2); ?></strong></td>
                            <td>
                                <?php
                                $method = $payment['payment_method'];
                                $badgeClass = 'badge-credit';
                                if (strpos($method, 'paypal') !== false) $badgeClass = 'badge-paypal';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $method)); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $payment['payment_status']; ?>">
                                    <?php echo ucfirst($payment['payment_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($payment['payment_date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: var(--muted);">
                            No payments recorded yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php mysqli_close($conn); ?>
