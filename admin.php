<?php 
require_once 'Backend/config/auth.php';
require_admin_page();

include 'Backend/config/db.php';

// Get Total Users
$users_query = "SELECT COUNT(*) as total_users FROM users";
$users_result = $conn->query($users_query);
$users_data = $users_result->fetch_assoc();
$total_users = $users_data['total_users'];

// Get Active Users (users who have purchased tickets - excluding cancelled purchases)
$active_users_query = "SELECT COUNT(DISTINCT user_id) as active_users FROM purchases WHERE status != 'cancelled'";
$active_users_result = $conn->query($active_users_query);
$active_users_data = $active_users_result->fetch_assoc();
$active_users = intval($active_users_data['active_users'] ?? 0);

// Get Total Revenue (sum of all purchases - excluding cancelled)
$revenue_query = "SELECT SUM(total_price) as total_revenue FROM purchases WHERE status != 'cancelled'";
$revenue_result = $conn->query($revenue_query);
$revenue_data = $revenue_result->fetch_assoc();
$total_revenue = floatval($revenue_data['total_revenue'] ?? 0);

// Get Active Events count
$events_query = "SELECT COUNT(*) as total_events FROM events";
$events_result = $conn->query($events_query);
$events_data = $events_result->fetch_assoc();
$total_events = $events_data['total_events'] ?? 0;

// Get Total Tickets Sold (sum of all quantities - excluding cancelled)
$tickets_query = "SELECT SUM(quantity) as total_tickets FROM purchases WHERE status != 'cancelled'";
$tickets_result = $conn->query($tickets_query);
$tickets_data = $tickets_result->fetch_assoc();
$total_tickets = intval($tickets_data['total_tickets'] ?? 0);

// Get Total Available Tickets (sum of availability from all ticket types)
$availability_query = "SELECT SUM(availability) as total_availability FROM ticket_types";
$availability_result = $conn->query($availability_query);
$availability_data = $availability_result->fetch_assoc();
$total_availability = intval($availability_data['total_availability'] ?? 0);

// Calculate Total Capacity (sold + available)
$total_capacity = $total_tickets + $total_availability;

// Calculate Potential Revenue based on each ticket type's price and TOTAL capacity
// Total capacity = current availability + all tickets ever sold for that type
$potential_revenue_query = "SELECT SUM(tt.price * (tt.availability + COALESCE(sold.total_sold, 0))) as potential_revenue
                             FROM ticket_types tt
                             LEFT JOIN (
                                 SELECT ticket_type_id, SUM(quantity) as total_sold
                                 FROM purchases
                                 WHERE status != 'cancelled'
                                 GROUP BY ticket_type_id
                             ) sold ON tt.id = sold.ticket_type_id";
$potential_revenue_result = $conn->query($potential_revenue_query);
$potential_revenue_data = $potential_revenue_result->fetch_assoc();
$potential_revenue = floatval($potential_revenue_data['potential_revenue'] ?? 1);

// Get Conversion Rate (unique users who made purchases)
$conversion_rate = $total_users > 0 ? intval(($active_users / $total_users) * 100) : 0;

// Calculate Percentages
$users_percent = $total_users > 0 ? intval(($active_users / $total_users) * 100) : 0; // Percentage of active users (who purchased) vs total users
$revenue_percent = $potential_revenue > 0 ? intval(($total_revenue / $potential_revenue) * 100) : 0; // Percentage based on potential revenue if all tickets of each type sold
$events_percent = 100; // Always 100% since max is 3 events
$tickets_percent = $total_capacity > 0 ? intval(($total_tickets / $total_capacity) * 100) : 0; // Percentage based on total capacity (sold / sold+available)

// Get Revenue Data for Chart (Total Potential vs Actual)
// No monthly breakdown - just overall totals
$revenue_chart_data = array(
    'potential' => $potential_revenue,  // Already calculated above
    'actual' => $total_revenue          // Already calculated above
);

// Get Ticket Distribution by Type
$ticket_distribution_query = "SELECT tt.ticket_name, SUM(p.quantity) as quantity_sold
                               FROM purchases p
                               JOIN ticket_types tt ON p.ticket_type_id = tt.id
                               WHERE p.status != 'cancelled'
                               GROUP BY p.ticket_type_id, tt.ticket_name
                               ORDER BY quantity_sold DESC
                               LIMIT 6";
$ticket_distribution_result = $conn->query($ticket_distribution_query);
$ticket_dist_data = array();
while($row = $ticket_distribution_result->fetch_assoc()) {
    $ticket_dist_data[] = $row;
}

// ===== ANALYTICS DATA =====

// Revenue by Payment Method (only methods with actual transactions and valid names)
$revenue_by_payment_query = "SELECT payment_method, SUM(amount) as total
                             FROM payments
                             WHERE amount > 0 AND payment_method IS NOT NULL AND payment_method != ''
                             GROUP BY payment_method
                             HAVING total > 0
                             ORDER BY total DESC";
$revenue_by_payment_result = $conn->query($revenue_by_payment_query);
$revenue_by_payment = array();
while($row = $revenue_by_payment_result->fetch_assoc()) {
    $revenue_by_payment[] = $row;
}

// Top 5 Customers by Revenue
$top_customers_query = "SELECT u.name, SUM(p.total_price) as spent, COUNT(p.id) as purchases
                        FROM users u
                        JOIN purchases p ON u.id = p.user_id
                        WHERE p.status != 'cancelled'
                        GROUP BY u.id, u.name
                        ORDER BY spent DESC
                        LIMIT 5";
$top_customers_result = $conn->query($top_customers_query);
$top_customers = array();
while($row = $top_customers_result->fetch_assoc()) {
    $top_customers[] = $row;
}

// Purchase Status Breakdown
$purchase_status_query = "SELECT status, COUNT(*) as count
                          FROM purchases
                          GROUP BY status";
$purchase_status_result = $conn->query($purchase_status_query);
$purchase_status = array();
while($row = $purchase_status_result->fetch_assoc()) {
    $purchase_status[] = $row;
}

// Daily Registration Trends (Last 7 days)
$registration_trends_query = "SELECT DATE(registered_date) as date, COUNT(*) as count
                              FROM users
                              WHERE registered_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                              GROUP BY DATE(registered_date)
                              ORDER BY date ASC";
$registration_trends_result = $conn->query($registration_trends_query);
$registration_trends = array();
while($row = $registration_trends_result->fetch_assoc()) {
    $registration_trends[] = $row;
}

// Average Order Value
$avg_order_query = "SELECT AVG(total_price) as avg_value, MIN(total_price) as min_value, MAX(total_price) as max_value
                    FROM purchases
                    WHERE status != 'cancelled'";
$avg_order_result = $conn->query($avg_order_query);
$avg_order_data = $avg_order_result->fetch_assoc();
$avg_order_value = floatval($avg_order_data['avg_value'] ?? 0);
$min_order_value = floatval($avg_order_data['min_value'] ?? 0);
$max_order_value = floatval($avg_order_data['max_value'] ?? 0);

// Payment Success Rate
$total_payments_query = "SELECT COUNT(*) as total FROM payments";
$total_payments_result = $conn->query($total_payments_query);
$total_payments = $total_payments_result->fetch_assoc()['total'];

$successful_payments_query = "SELECT COUNT(*) as successful FROM payments WHERE payment_status = 'completed'";
$successful_payments_result = $conn->query($successful_payments_query);
$successful_payments = $successful_payments_result->fetch_assoc()['successful'];

$payment_success_rate = $total_payments > 0 ? intval(($successful_payments / $total_payments) * 100) : 0;

// ===== EVENTS DATA =====

// All Events with Artists and Sales Info
$events_query = "SELECT e.id, e.event_name, e.event_date, e.description,
                 GROUP_CONCAT(a.artist_name SEPARATOR ', ') as performers
                 FROM events e
                 LEFT JOIN event_artists ea ON e.id = ea.event_id
                 LEFT JOIN artists a ON ea.artist_id = a.id
                 GROUP BY e.id, e.event_name, e.event_date, e.description
                 ORDER BY e.event_date DESC";
$events_result = $conn->query($events_query);
$events_data = array();
while($row = $events_result->fetch_assoc()) {
    $events_data[] = $row;
}

// All Artists for dropdown
$artists_query = "SELECT id, artist_name FROM artists ORDER BY artist_name ASC";
$artists_result = $conn->query($artists_query);
$artists_list = array();
while($row = $artists_result->fetch_assoc()) {
    $artists_list[] = $row;
}

// Event Statistics
$event_stats_query = "SELECT COUNT(*) as total_events, 
                      MAX(event_date) as latest_event,
                      MIN(event_date) as earliest_event
                      FROM events";
$event_stats_result = $conn->query($event_stats_query);
$event_stats = $event_stats_result->fetch_assoc();

// ===== TICKETS DATA =====

// All Ticket Types with Sales Info
$ticket_types_query = "SELECT tt.id, tt.ticket_name, tt.price, tt.availability,
                       COALESCE(SUM(p.quantity), 0) as quantity_sold
                       FROM ticket_types tt
                       LEFT JOIN purchases p ON tt.id = p.ticket_type_id AND p.status != 'cancelled'
                       GROUP BY tt.id, tt.ticket_name, tt.price, tt.availability
                       ORDER BY tt.id ASC";
$ticket_types_result = $conn->query($ticket_types_query);
$ticket_types_list = array();
while($row = $ticket_types_result->fetch_assoc()) {
    $ticket_types_list[] = $row;
}

// Ticket Statistics
$ticket_stats_query = "SELECT COUNT(*) as total_types,
                       COALESCE(AVG(price), 0) as avg_price,
                       MIN(price) as min_price,
                       MAX(price) as max_price
                       FROM ticket_types";
$ticket_stats_result = $conn->query($ticket_stats_query);
$ticket_stats = $ticket_stats_result->fetch_assoc();

// ===== CUSTOMERS DATA =====

// All Customers with Purchase History
$customers_query = "SELECT u.id, u.name, u.email, u.phone, u.registered_date,
                    COUNT(p.id) as total_purchases,
                    COALESCE(SUM(p.total_price), 0) as total_spent,
                    COALESCE(SUM(p.quantity), 0) as total_tickets
                    FROM users u
                    INNER JOIN purchases p ON u.id = p.user_id
                    WHERE p.status != 'cancelled'
                    GROUP BY u.id, u.name, u.email, u.phone, u.registered_date
                    ORDER BY total_spent DESC";
$customers_result = $conn->query($customers_query);
$customers_data = array();
while($row = $customers_result->fetch_assoc()) {
    $customers_data[] = $row;
}

// Customer Statistics
$customer_stats_query = "SELECT 
                         COUNT(DISTINCT u.id) as total_customers,
                         AVG(spent_summary.total_spent) as avg_spent,
                         MIN(u.registered_date) as first_customer,
                         MAX(u.registered_date) as last_customer
                         FROM users u
                         INNER JOIN (
                            SELECT user_id, SUM(total_price) as total_spent
                            FROM purchases
                            GROUP BY user_id
                         ) AS spent_summary ON u.id = spent_summary.user_id";
$customer_stats_result = $conn->query($customer_stats_query);
$customer_stats = $customer_stats_result->fetch_assoc();

// ===== PURCHASES DATA =====

// All Purchases with details
$purchases_query = "SELECT p.id, u.name, u.email, tt.ticket_name, p.quantity, p.unit_price, p.total_price, 
                    p.purchase_date, p.status, py.payment_method, py.transaction_id
                    FROM purchases p
                    JOIN users u ON p.user_id = u.id
                    JOIN ticket_types tt ON p.ticket_type_id = tt.id
                    LEFT JOIN payments py ON p.id = py.purchase_id
                    ORDER BY p.purchase_date DESC";
$purchases_result = $conn->query($purchases_query);
$purchases_data = array();
while($row = $purchases_result->fetch_assoc()) {
    $purchases_data[] = $row;
}

// Purchase Statistics
$purchase_stats_query = "SELECT 
                         COUNT(*) as total_purchases,
                         SUM(CASE WHEN status != 'cancelled' THEN total_price ELSE 0 END) as total_revenue,
                         COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                         COUNT(CASE WHEN status = 'confirmed' THEN 1 END) as confirmed_count,
                         COUNT(CASE WHEN status = 'refunded' THEN 1 END) as refunded_count,
                         COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count
                         FROM purchases";
$purchase_stats_result = $conn->query($purchase_stats_query);
$purchase_stats = $purchase_stats_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>NOVA Festival - Admin Dashboard</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="css/admin-dashboard.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="admin-page">

<div class="admin-wrapper">

<!-- ================= Sidebar ================= -->

<aside class="admin-sidebar">

<div class="sidebar-logo">
<h3>Control Panel</h3>
</div>

<nav class="sidebar-menu">

<a href="#overview" class="menu-item active" data-section="overview">
<span>Overview</span>
</a>

<a href="#analytics" class="menu-item" data-section="analytics">
<span>Analytics</span>
</a>

<a href="#events" class="menu-item" data-section="events">
<span>Events</span>
</a>

<a href="#artists" class="menu-item" data-section="artists">
<span>Artists</span>
</a>

<a href="#tickets" class="menu-item" data-section="tickets">
<span>Tickets</span>
</a>

<a href="#customers" class="menu-item" data-section="customers">
<span>Customers</span>
</a>

<a href="#purchases" class="menu-item" data-section="purchases">
<span>Purchases</span>
</a>

</nav>

<a href="Backend/api/logout.php" class="logout-btn">
<span>Logout</span>
</a>

</aside>

<!-- ================= Main Content ================= -->

<main class="admin-main">

<!-- Overview Section -->
<section id="overview-section" class="admin-section active">

<div class="dashboard-header">
<div>
<h1>Dashboard</h1>
<p class="header-subtitle">Welcome back! Here's your performance overview.</p>
</div>

<div class="header-actions">
<button class="btn-icon-only" title="Refresh" onclick="location.reload()">
<i class="fa-solid fa-arrows-rotate"></i>
</button>
</div>
</div>

<!-- Stats Box Section -->

<div class="stats-box-grid">

<div class="stats-box">
<div class="circular-progress" data-value="<?php echo $users_percent; ?>">
<svg viewBox="0 0 120 120">
<circle cx="60" cy="60" r="54" class="progress-bg"></circle>
<circle cx="60" cy="60" r="54" class="progress-fill"></circle>
</svg>
<div class="progress-text"><?php echo $users_percent; ?>%</div>
</div>
<div class="stats-box-content">
<p class="stats-box-label">Total Users</p>
<h3 class="stats-box-value"><?php echo number_format($total_users); ?></h3>
</div>
</div>

<div class="stats-box">
<div class="circular-progress" data-value="<?php echo $revenue_percent; ?>">
<svg viewBox="0 0 120 120">
<circle cx="60" cy="60" r="54" class="progress-bg"></circle>
<circle cx="60" cy="60" r="54" class="progress-fill"></circle>
</svg>
<div class="progress-text"><?php echo $revenue_percent; ?>%</div>
</div>
<div class="stats-box-content">
<p class="stats-box-label">Total Revenue</p>
<h3 class="stats-box-value">€<?php echo number_format($total_revenue, 2); ?></h3>
</div>
</div>

<div class="stats-box">
<div class="circular-progress" data-value="<?php echo $events_percent; ?>">
<svg viewBox="0 0 120 120">
<circle cx="60" cy="60" r="54" class="progress-bg"></circle>
<circle cx="60" cy="60" r="54" class="progress-fill"></circle>
</svg>
<div class="progress-text"><?php echo $events_percent; ?>%</div>
</div>
<div class="stats-box-content">
<p class="stats-box-label">Active Events</p>
<h3 class="stats-box-value"><?php echo $total_events; ?></h3>
</div>
</div>

<div class="stats-box">
<div class="circular-progress" data-value="<?php echo $tickets_percent; ?>">
<svg viewBox="0 0 120 120">
<circle cx="60" cy="60" r="54" class="progress-bg"></circle>
<circle cx="60" cy="60" r="54" class="progress-fill"></circle>
</svg>
<div class="progress-text"><?php echo $tickets_percent; ?>%</div>
</div>
<div class="stats-box-content">
<p class="stats-box-label">Tickets Sold</p>
<h3 class="stats-box-value"><?php echo number_format($total_tickets); ?></h3>
</div>
</div>

</div>

<!-- Charts Section -->

<div class="charts-section">

<div class="chart-container large">
<div class="chart-header">
<h3>
<i class="fa-solid fa-chart-line"></i>
Revenue Comparison
</h3>
</div>
<canvas id="activityChart"></canvas>
</div>

<div class="chart-container">
<div class="chart-header">
<h3>
<i class="fa-solid fa-ticket"></i>
Ticket Distribution
</h3>
</div>
<canvas id="ticketChart"></canvas>
</div>

</section>

<!-- Analytics Section -->
<section id="analytics-section" class="admin-section">
<div class="dashboard-header">
<h1>Analytics</h1>
<p class="header-subtitle">Detailed performance analytics and insights</p>
</div>

<!-- Key Metrics -->
<div class="analytics-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; padding: 40px;">
    <div class="metric-card" style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Average Order Value</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent); margin-bottom: 8px;">$<?php echo number_format($avg_order_value, 2); ?></div>
        <div style="font-size: 12px; color: #777;">Min: $<?php echo number_format($min_order_value, 2); ?> | Max: $<?php echo number_format($max_order_value, 2); ?></div>
    </div>

    <div class="metric-card" style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Payment Success Rate</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent); margin-bottom: 8px;"><?php echo $payment_success_rate; ?>%</div>
        <div style="font-size: 12px; color: #777;"><?php echo $successful_payments; ?>/<?php echo $total_payments; ?> payments successful</div>
    </div>

    <div class="metric-card" style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Conversion Rate</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent); margin-bottom: 8px;"><?php echo $conversion_rate; ?>%</div>
        <div style="font-size: 12px; color: #777;"><?php echo $active_users; ?>/<?php echo $total_users; ?> users purchased</div>
    </div>
</div>

<!-- Charts Row 1 -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px; padding: 40px;">
    <!-- Revenue by Payment Method -->
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">Revenue by Payment Method</h3>
        <canvas id="paymentMethodChart"></canvas>
    </div>

    <!-- Purchase Status -->
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">Purchase Status</h3>
        <canvas id="purchaseStatusChart"></canvas>
    </div>
</div>

<!-- Top Customers Table -->
<div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0; margin: 0 40px 40px 40px;">
    <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">Top Customers</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.2);">
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Name</th>
                <th style="padding: 12px; text-align: right; color: var(--accent); font-weight: 600;">Total Spent</th>
                <th style="padding: 12px; text-align: right; color: var(--accent); font-weight: 600;">Purchases</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($top_customers as $customer): ?>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.1); color: #ddd;">
                <td style="padding: 12px; text-align: left;"><?php echo htmlspecialchars($customer['name']); ?></td>
                <td style="padding: 12px; text-align: right;">$<?php echo number_format($customer['spent'], 2); ?></td>
                <td style="padding: 12px; text-align: right;"><?php echo $customer['purchases']; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
// Revenue by Payment Method - Pie Chart
const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
const paymentMethodData = {
    labels: <?php echo json_encode(array_column($revenue_by_payment, 'payment_method')); ?>,
    datasets: [{
        data: <?php echo json_encode(array_column($revenue_by_payment, 'total')); ?>,
        backgroundColor: [
            'rgba(248, 166, 1, 0.8)',
            'rgba(248, 166, 1, 0.6)',
            'rgba(248, 166, 1, 0.4)',
            'rgba(248, 166, 1, 0.5)',
            'rgba(248, 166, 1, 0.3)'
        ],
        borderColor: 'rgba(248, 166, 1, 0.3)',
        borderWidth: 1
    }]
};
new Chart(paymentMethodCtx, {
    type: 'doughnut',
    data: paymentMethodData,
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                labels: {
                    color: '#999',
                    font: { size: 12 }
                }
            }
        }
    }
});

// Purchase Status - Bar Chart
const purchaseStatusCtx = document.getElementById('purchaseStatusChart').getContext('2d');
const purchaseStatusData = {
    labels: <?php echo json_encode(array_column($purchase_status, 'status')); ?>,
    datasets: [{
        label: 'Number of Purchases',
        data: <?php echo json_encode(array_column($purchase_status, 'count')); ?>,
        backgroundColor: 'rgba(248, 166, 1, 0.7)',
        borderColor: 'rgba(248, 166, 1, 0.3)',
        borderWidth: 1
    }]
};
new Chart(purchaseStatusCtx, {
    type: 'bar',
    data: purchaseStatusData,
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                labels: {
                    color: '#999',
                    font: { size: 12 }
                }
            }
        },
        scales: {
            x: {
                ticks: { color: '#999' },
                grid: { color: 'rgba(248, 166, 1, 0.1)' }
            },
            y: {
                ticks: { color: '#999' },
                grid: { color: 'rgba(248, 166, 1, 0.1)' }
            }
        }
    }
});

</script>
</section>

<!-- Events Section -->
<section id="events-section" class="admin-section">
<div class="dashboard-header">
<h1>Events</h1>
<p class="header-subtitle">Manage your events</p>
<button onclick="openEventModal()" style="margin-top: 10px; padding: 10px 20px; background: rgba(248, 166, 1, 0.2); border: 1px solid rgba(248, 166, 1, 0.5); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">+ Add Event</button>
</div>

<!-- Add/Edit Event Modal -->
<div id="eventModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #0a1f2e; border: 1px solid rgba(248, 166, 1, 0.3); padding: 40px; border-radius: 0; width: 90%; max-width: 500px; max-height: 80vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 id="modalTitle" style="margin: 0; color: white;">Add Event</h2>
            <button onclick="closeEventModal()" style="background: none; border: none; color: var(--accent); font-size: 24px; cursor: pointer;">✕</button>
        </div>

        <form id="eventForm" style="display: flex; flex-direction: column; gap: 20px;">
            <input type="hidden" id="eventId" name="event_id" value="">

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Event Name</label>
                <input type="text" id="eventName" name="event_name" required style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0;">
            </div>

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Event Date & Time</label>
                <input type="datetime-local" id="eventDate" name="event_date" required style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0;">
            </div>

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Description</label>
                <textarea id="eventDescription" name="description" rows="4" style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0; resize: vertical;"></textarea>
            </div>

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Select Artists (Ctrl+Click or Cmd+Click to add multiple)</label>
                <select id="eventArtists" name="artists" multiple style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0; min-height: 120px; cursor: pointer;">
                    <?php foreach($artists_list as $artist): ?>
                    <option value="<?php echo $artist['id']; ?>" style="background: #0a1f2e; color: white; padding: 8px;"><?php echo htmlspecialchars($artist['artist_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px;">
                <button type="button" onclick="closeEventModal()" style="padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">Cancel</button>
                <button type="submit" style="padding: 12px; background: rgba(248, 166, 1, 0.2); border: 1px solid rgba(248, 166, 1, 0.5); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">Save Event</button>
            </div>
        </form>
    </div>
</div>

<!-- Event Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; padding: 40px;">
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Total Events</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);"><?php echo $event_stats['total_events']; ?></div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Latest Event</div>
        <div style="font-size: 14px; color: white; margin-bottom: 4px;">
            <?php 
                $latest = new DateTime($event_stats['latest_event']);
                echo $latest->format('M d, Y');
            ?>
        </div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Earliest Event</div>
        <div style="font-size: 14px; color: white; margin-bottom: 4px;">
            <?php 
                $earliest = new DateTime($event_stats['earliest_event']);
                echo $earliest->format('M d, Y');
            ?>
        </div>
    </div>
</div>

<!-- Events Table -->
<div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0; margin: 0 40px 40px 40px; overflow-x: auto;">
    <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">All Events</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.2);">
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Event Name</th>
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Date</th>
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Performers</th>
                <th style="padding: 12px; text-align: center; color: var(--accent); font-weight: 600;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($events_data as $event): ?>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.1); color: #ddd;">
                <td style="padding: 12px; text-align: left; font-weight: 500;"><?php echo htmlspecialchars($event['event_name']); ?></td>
                <td style="padding: 12px; text-align: left;">
                    <?php 
                        $date = new DateTime($event['event_date']);
                        echo $date->format('M d, Y');
                    ?>
                </td>
                <td style="padding: 12px; text-align: left; font-size: 13px;">
                    <?php echo $event['performers'] ? htmlspecialchars($event['performers']) : '<span style="color: #666;">No performers</span>'; ?>
                </td>
                <td style="padding: 12px; text-align: center;">
                    <button onclick="editEvent(<?php echo $event['id']; ?>)" style="background: none; border: none; color: var(--accent); cursor: pointer; font-size: 12px; padding: 4px 8px; margin-right: 4px;">Edit</button>
                    <button onclick="deleteEvent(<?php echo $event['id']; ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 12px; padding: 4px 8px;">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(count($events_data) == 0): ?>
    <div style="padding: 24px; text-align: center; color: var(--muted);">
        <p>No events found</p>
    </div>
    <?php endif; ?>
</div>

<!-- Event Details Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px; padding: 0 40px 40px 40px;">
    <?php foreach($events_data as $event): ?>
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
            <h4 style="margin: 0; color: white; font-size: 15px;"><?php echo htmlspecialchars($event['event_name']); ?></h4>
        </div>
        
        <div style="margin-bottom: 12px; font-size: 12px;">
            <div style="color: var(--muted); margin-bottom: 4px;">📅 Date</div>
            <div style="color: white;">
                <?php 
                    $date = new DateTime($event['event_date']);
                    echo $date->format('F d, Y \a\t H:i');
                ?>
            </div>
        </div>

        <div style="margin-bottom: 12px; font-size: 12px;">
            <div style="color: var(--muted); margin-bottom: 4px;">🎤 Performers</div>
            <div style="color: white;">
                <?php echo $event['performers'] ? htmlspecialchars($event['performers']) : '<span style="color: #666;">No performers</span>'; ?>
            </div>
        </div>

        <div style="margin-bottom: 12px; font-size: 12px;">
            <div style="color: var(--muted); margin-bottom: 4px;">Description</div>
            <div style="color: white; line-height: 1.4; font-size: 11px;">
                <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . (strlen($event['description']) > 100 ? '...' : ''); ?>
            </div>
        </div>


    </div>
    <?php endforeach; ?>
</div>

</section>

<!-- Add/Edit Artist Modal -->
<div id="artistModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #0a1f2e; border: 1px solid rgba(248, 166, 1, 0.3); padding: 40px; border-radius: 0; width: 90%; max-width: 400px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 id="artistModalTitle" style="margin: 0; color: white;">Add Artist</h2>
            <button onclick="closeArtistModal()" style="background: none; border: none; color: var(--accent); font-size: 24px; cursor: pointer;">✕</button>
        </div>

        <form id="artistForm" style="display: flex; flex-direction: column; gap: 20px;">
            <input type="hidden" id="artistId" name="artist_id" value="">

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Artist Name</label>
                <input type="text" id="artistName" name="artist_name" required placeholder="Enter artist name" style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px;">
                <button type="button" onclick="closeArtistModal()" style="padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">Cancel</button>
                <button type="submit" style="padding: 12px; background: rgba(248, 166, 1, 0.2); border: 1px solid rgba(248, 166, 1, 0.5); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">Save Artist</button>
            </div>
        </form>
    </div>
</div>

<!-- Artists Section -->
<section id="artists-section" class="admin-section">
<div class="dashboard-header">
<h1>Artists</h1>
<p class="header-subtitle">Manage performers</p>
<button onclick="openArtistModal()" style="margin-top: 10px; padding: 10px 20px; background: rgba(248, 166, 1, 0.2); border: 1px solid rgba(248, 166, 1, 0.5); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">+ Add Artist</button>
</div>

<!-- Artists Table -->
<div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0; margin: 0 40px 40px 40px; overflow-x: auto;">
    <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">All Artists</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.2);">
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Artist Name</th>
                <th style="padding: 12px; text-align: center; color: var(--accent); font-weight: 600;">Actions</th>
            </tr>
        </thead>
        <tbody id="artistsTableBody">
            <?php foreach($artists_list as $artist): ?>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.1); color: #ddd;">
                <td style="padding: 12px; text-align: left; font-weight: 500;"><?php echo htmlspecialchars($artist['artist_name']); ?></td>
                <td style="padding: 12px; text-align: center;">
                    <button onclick="deleteArtist(<?php echo $artist['id']; ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 12px; padding: 4px 8px;">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(count($artists_list) == 0): ?>
    <div style="padding: 24px; text-align: center; color: var(--muted);">
        <p>No artists found</p>
    </div>
    <?php endif; ?>
</div>

</section>

<!-- Tickets Section -->
<section id="tickets-section" class="admin-section">
<div class="dashboard-header">
<h1>Tickets</h1>
<p class="header-subtitle">Ticket management and sales</p>
<button onclick="openTicketModal()" style="margin-top: 10px; padding: 10px 20px; background: rgba(248, 166, 1, 0.2); border: 1px solid rgba(248, 166, 1, 0.5); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">+ Add Ticket</button>
</div>

<!-- Add/Edit Ticket Modal -->
<div id="ticketModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: #0a1f2e; border: 1px solid rgba(248, 166, 1, 0.3); padding: 40px; border-radius: 0; width: 90%; max-width: 450px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 id="ticketModalTitle" style="margin: 0; color: white;">Add Ticket</h2>
            <button onclick="closeTicketModal()" style="background: none; border: none; color: var(--accent); font-size: 24px; cursor: pointer;">✕</button>
        </div>

        <form id="ticketForm" style="display: flex; flex-direction: column; gap: 20px;">
            <input type="hidden" id="ticketId" name="ticket_id" value="">

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Ticket Name</label>
                <input type="text" id="ticketName" name="ticket_name" required placeholder="e.g., VIP, Standard, Economy" style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0;">
            </div>

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Price ($)</label>
                <input type="number" id="ticketPrice" name="price" required step="0.01" min="0" placeholder="0.00" style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0;">
            </div>

            <div>
                <label style="color: var(--muted); font-size: 12px; display: block; margin-bottom: 8px;">Availability</label>
                <input type="number" id="ticketQuantity" name="availability" required step="1" min="1" placeholder="1000" style="width: 100%; padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: white; font-family: Poppins; border-radius: 0;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 20px;">
                <button type="button" onclick="closeTicketModal()" style="padding: 12px; background: transparent; border: 1px solid rgba(248, 166, 1, 0.3); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">Cancel</button>
                <button type="submit" style="padding: 12px; background: rgba(248, 166, 1, 0.2); border: 1px solid rgba(248, 166, 1, 0.5); color: var(--accent); border-radius: 0; cursor: pointer; font-family: Poppins; font-weight: 500;">Save Ticket</button>
            </div>
        </form>
    </div>
</div>

<!-- Ticket Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; padding: 40px;">
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Total Ticket Types</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);"><?php echo $ticket_stats['total_types']; ?></div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Total Sold</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);"><?php echo $total_tickets; ?></div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Average Price</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);">$<?php echo number_format($ticket_stats['avg_price'], 2); ?></div>
        <div style="font-size: 12px; color: #777;">Min: $<?php echo number_format($ticket_stats['min_price'], 2); ?> | Max: $<?php echo number_format($ticket_stats['max_price'], 2); ?></div>
    </div>
</div>

<!-- Tickets Table -->
<div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0; margin: 0 40px 40px 40px; overflow-x: auto;">
    <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">All Ticket Types</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.2);">
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Ticket Type</th>
                <th style="padding: 12px; text-align: right; color: var(--accent); font-weight: 600;">Available</th>
                <th style="padding: 12px; text-align: center; color: var(--accent); font-weight: 600;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($ticket_types_list as $ticket): ?>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.1); color: #ddd;">
                <td style="padding: 12px; text-align: left; font-weight: 500;"><?php echo htmlspecialchars($ticket['ticket_name']); ?></td>
                <td style="padding: 12px; text-align: right; color: var(--accent);"><?php echo $ticket['availability']; ?></td>
                <td style="padding: 12px; text-align: center;">
                    <button onclick="editTicket(<?php echo $ticket['id']; ?>)" style="background: none; border: none; color: var(--accent); cursor: pointer; font-size: 12px; padding: 4px 8px; margin-right: 4px;">Edit</button>
                    <button onclick="deleteTicket(<?php echo $ticket['id']; ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 12px; padding: 4px 8px;">Delete</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(count($ticket_types_list) == 0): ?>
    <div style="padding: 24px; text-align: center; color: var(--muted);">
        <p>No ticket types found</p>
    </div>
    <?php endif; ?>
</div>

<!-- Ticket Details Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px; padding: 0 40px 40px 40px;">
    <?php foreach($ticket_types_list as $ticket): ?>
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 16px;">
            <h4 style="margin: 0; color: white; font-size: 15px;"><?php echo htmlspecialchars($ticket['ticket_name']); ?></h4>
            <div style="background: rgba(248, 166, 1, 0.2); padding: 4px 8px; border-radius: 3px;">
                <span style="color: var(--accent); font-weight: 600; font-size: 12px;">$<?php echo number_format($ticket['price'], 2); ?></span>
            </div>
        </div>

        <div style="background: rgba(248, 166, 1, 0.1); padding: 12px; border-radius: 3px; text-align: center;">
            <div style="color: var(--muted); font-size: 11px; margin-bottom: 4px;">Tickets Sold</div>
            <div style="color: var(--accent); font-size: 24px; font-weight: 600;"><?php echo $ticket['quantity_sold']; ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

</section>

<!-- Customers Section -->
<section id="customers-section" class="admin-section">
<div class="dashboard-header">
<h1>Customers</h1>
<p class="header-subtitle">Customer information and analytics</p>
</div>

<!-- Customer Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; padding: 40px;">
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Total Customers</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);"><?php echo $customer_stats['total_customers']; ?></div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Average Spent</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);">$<?php echo number_format($customer_stats['avg_spent'], 2); ?></div>
    </div>
</div>

<!-- Customers Table -->
<div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0; margin: 0 40px 40px 40px; overflow-x: auto;">
    <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">All Customers</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.2);">
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Name</th>
                <th style="padding: 12px; text-align: right; color: var(--accent); font-weight: 600;">Purchases</th>
                <th style="padding: 12px; text-align: right; color: var(--accent); font-weight: 600;">Total Spent</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($customers_data as $customer): ?>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.1); color: #ddd;">
                <td style="padding: 12px; text-align: left; font-weight: 500;"><?php echo htmlspecialchars($customer['name']); ?></td>
                <td style="padding: 12px; text-align: right; color: var(--accent); font-weight: 500;"><?php echo $customer['total_purchases']; ?></td>
                <td style="padding: 12px; text-align: right; color: var(--accent); font-weight: 500;">$<?php echo number_format($customer['total_spent'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(count($customers_data) == 0): ?>
    <div style="padding: 24px; text-align: center; color: var(--muted);">
        <p>No customers found</p>
    </div>
    <?php endif; ?>
</div>

<!-- Top Customers Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; padding: 0 40px 40px 40px;">
    <?php 
        $top_customers_cards = array_slice($customers_data, 0, 6);
        foreach($top_customers_cards as $idx => $customer): 
    ?>
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="margin-bottom: 16px;">
            <div style="color: var(--muted); font-size: 11px; margin-bottom: 4px;">Member Info</div>
            <h4 style="margin: 0 0 8px 0; color: white; font-size: 14px;"><?php echo htmlspecialchars($customer['name']); ?></h4>
            <div style="font-size: 11px; color: #999; margin-bottom: 6px;">#{<?php echo ($idx + 1); ?></div>
        </div>

        <div style="margin-bottom: 12px; font-size: 12px;">
            <div style="color: var(--muted); font-size: 10px; margin-bottom: 3px;">📧 Email</div>
            <div style="color: white; font-size: 11px; word-break: break-all;"><?php echo htmlspecialchars($customer['email']); ?></div>
        </div>

        <div style="margin-bottom: 12px; font-size: 12px;">
            <div style="color: var(--muted); font-size: 10px; margin-bottom: 3px;">📱 Phone</div>
            <div style="color: white; font-size: 11px;"><?php echo $customer['phone'] ? htmlspecialchars($customer['phone']) : '<span style="color: #666;">Not provided</span>'; ?></div>
        </div>

        <div style="padding-top: 12px; border-top: 1px solid rgba(248, 166, 1, 0.1); font-size: 11px;">
            <div style="color: var(--muted); margin-bottom: 3px;">Joined</div>
            <div style="color: white;">
                <?php 
                    $reg_date = new DateTime($customer['registered_date']);
                    echo $reg_date->format('M d, Y');
                ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

</section>

<!-- Purchases Section -->
<section id="purchases-section" class="admin-section">
<div class="dashboard-header">
<h1>Purchases</h1>
<p class="header-subtitle">Manage ticket purchases and orders</p>
</div>

<!-- Purchase Statistics -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 24px; padding: 40px;">
    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Total Purchases</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);"><?php echo $purchase_stats['total_purchases']; ?></div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Total Revenue</div>
        <div style="font-size: 32px; font-weight: 600; color: var(--accent);">€<?php echo number_format($purchase_stats['total_revenue'], 2); ?></div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Confirmed</div>
        <div style="font-size: 32px; font-weight: 600; color: #10b981;"><?php echo $purchase_stats['confirmed_count']; ?></div>
    </div>

    <div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0;">
        <div style="color: var(--muted); font-size: 13px; margin-bottom: 8px;">Cancelled</div>
        <div style="font-size: 32px; font-weight: 600; color: #8b5cf6;"><?php echo $purchase_stats['cancelled_count']; ?></div>
    </div>
</div>

<!-- Purchases Table -->
<div style="border: 1px solid rgba(248, 166, 1, 0.3); padding: 24px; border-radius: 0; margin: 0 40px 40px 40px; overflow-x: auto;">
    <h3 style="margin: 0 0 20px 0; color: white; font-size: 16px;">All Purchases</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.2);">
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Customer</th>
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Ticket</th>
                <th style="padding: 12px; text-align: center; color: var(--accent); font-weight: 600;">Qty</th>
                <th style="padding: 12px; text-align: right; color: var(--accent); font-weight: 600;">Total</th>
                <th style="padding: 12px; text-align: left; color: var(--accent); font-weight: 600;">Date</th>
                <th style="padding: 12px; text-align: center; color: var(--accent); font-weight: 600;">Status</th>
                <th style="padding: 12px; text-align: center; color: var(--accent); font-weight: 600;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($purchases_data as $purchase): ?>
            <tr style="border-bottom: 1px solid rgba(248, 166, 1, 0.1); color: #ddd;">
                <td style="padding: 12px; text-align: left; font-weight: 500;"><?php echo htmlspecialchars($purchase['name']); ?></td>
                <td style="padding: 12px; text-align: left;"><?php echo htmlspecialchars($purchase['ticket_name']); ?></td>
                <td style="padding: 12px; text-align: center;"><?php echo $purchase['quantity']; ?></td>
                <td style="padding: 12px; text-align: right; color: var(--accent); font-weight: 600;">€<?php echo number_format($purchase['total_price'], 2); ?></td>
                <td style="padding: 12px; text-align: left; font-size: 12px;">
                    <?php 
                        $date = new DateTime($purchase['purchase_date']);
                        echo $date->format('M d, Y H:i');
                    ?>
                </td>
                <td style="padding: 12px; text-align: center;">
                    <span style="padding: 4px 8px; border-radius: 3px; font-size: 11px; font-weight: 600; 
                    <?php 
                        if($purchase['status'] == 'pending') echo 'background: rgba(245, 158, 11, 0.15); color: #f59e0b;';
                        elseif($purchase['status'] == 'confirmed') echo 'background: rgba(16, 185, 129, 0.15); color: #10b981;';
                        else echo 'background: rgba(239, 68, 68, 0.15); color: #ef4444;';
                    ?>">
                    <?php echo ucfirst($purchase['status']); ?>
                    </span>
                </td>
                <td style="padding: 12px; text-align: center;">
                    <?php if($purchase['status'] == 'pending'): ?>
                    <button onclick="confirmPurchase(<?php echo $purchase['id']; ?>)" style="background: none; border: none; color: #10b981; cursor: pointer; font-size: 12px; padding: 4px 6px; margin-right: 4px;" title="Confirm">✓</button>
                    <?php endif; ?>
                    <?php if($purchase['status'] != 'cancelled'): ?>
                    <button onclick="cancelPurchase(<?php echo $purchase['id']; ?>)" style="background: none; border: none; color: #ef4444; cursor: pointer; font-size: 12px; padding: 4px 6px;" title="Cancel">✕</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if(count($purchases_data) == 0): ?>
    <div style="padding: 24px; text-align: center; color: var(--muted);">
        <p>No purchases yet</p>
    </div>
    <?php endif; ?>
</div>

</section>

</main>

</div>

<script>
  // ===========================
  // Circular Progress Animation
  // ===========================
  
  const progressElements = document.querySelectorAll('.circular-progress');
  
  function animateProgress() {
    progressElements.forEach(element => {
      const value = parseInt(element.getAttribute('data-value'));
      const circumference = 2 * Math.PI * 54; // radius is 54
      const offset = circumference - (value / 100) * circumference;
      
      const progressFill = element.querySelector('.progress-fill');
      
      setTimeout(() => {
        progressFill.style.strokeDashoffset = offset;
      }, 100);
    });
  }
  
  // Trigger animation on page load
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', animateProgress);
  } else {
    animateProgress();
  }
  
  
  // ===========================
  // Revenue Comparison Chart (Potential vs Actual)
  // ===========================
  
  const activityCtx = document.getElementById("activityChart");
  
  new Chart(activityCtx, {
    type: "bar",
    data: {
      labels: ["Potential Revenue", "Actual Revenue"],
      datasets: [{
        label: "Revenue (€)",
        data: [<?php echo $revenue_chart_data['potential'] . ", " . $revenue_chart_data['actual']; ?>],
        backgroundColor: [
          "rgba(248, 166, 1, 0.3)",
          "#f8a601"
        ],
        borderColor: [
          "#f8a601",
          "#f8a601"
        ],
        borderWidth: 2,
        borderRadius: 6,
        hoverBackgroundColor: [
          "rgba(248, 166, 1, 0.5)",
          "#f8a601"
        ]
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          labels: {
            color: "#cbd9ff",
            font: { weight: "600", size: 13 },
            padding: 20
          }
        }
      },
      scales: {
        x: {
          ticks: {
            color: "#cbd9ff",
            font: { weight: "500", size: 12 },
            callback: function(value) {
              return "€" + value.toLocaleString();
            }
          },
          grid: {
            color: "rgba(248, 166, 1, 0.08)",
            drawBorder: false
          }
        },
        y: {
          ticks: {
            color: "#cbd9ff",
            font: { weight: "500", size: 12 }
          },
          grid: {
            drawBorder: false
          }
        }
      }
    }
  });

  
  // ===========================
  // Ticket Distribution Chart
  // ===========================
  
  const ticketCtx = document.getElementById("ticketChart");
  
  const colors = ["#f8a601", "#2268b3", "#10b981", "#3a9ce9", "#ec4899", "#f97316"];
  
  new Chart(ticketCtx, {
    type: "bar",
    data: {
      labels: [<?php foreach($ticket_dist_data as $row) { echo "\"" . htmlspecialchars($row['ticket_name']) . "\", "; } ?>],
      datasets: [{
        label: "Tickets Sold",
        data: [<?php foreach($ticket_dist_data as $row) { echo $row['quantity_sold'] . ", "; } ?>],
        backgroundColor: colors.slice(0, <?php echo count($ticket_dist_data); ?>),
        borderRadius: 10,
        borderSkipped: false,
        borderWidth: 0
      }]
    },
    options: {
      indexAxis: "y",
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          labels: {
            color: "#cbd9ff",
            font: { weight: "600", size: 13 },
            padding: 20
          }
        }
      },
      scales: {
        x: {
          ticks: {
            color: "#cbd9ff",
            font: { weight: "500", size: 12 }
          },
          grid: {
            color: "rgba(248, 166, 1, 0.08)",
            drawBorder: false
          }
        },
        y: {
          ticks: {
            color: "#cbd9ff",
            font: { weight: "500", size: 13 }
          },
          grid: {
            display: false
          }
        }
      }
    }
  });
  
  
  // ===========================
  // Counter Animation
  // ===========================
  
  const statValues = document.querySelectorAll(".stat-value");
  
  statValues.forEach(element => {
    let target = element.innerText
      .replace("€", "")
      .replace(/,/g, "");
    
    target = parseInt(target);
    
    if (isNaN(target)) return;
    
    let count = 0;
    const speed = target / 100;
    
    const update = () => {
      count += speed;
      
      if (count < target) {
        if (element.innerText.includes("€")) {
          element.innerHTML = "€" + Math.floor(count).toLocaleString();
        } else {
          element.innerHTML = Math.floor(count).toLocaleString();
        }
        
        requestAnimationFrame(update);
      } else {
        if (element.innerText.includes("€")) {
          element.innerHTML = "€" + target.toLocaleString();
        } else {
          element.innerHTML = target.toLocaleString();
        }
      }
    };
    
    update();
  });
  
  
  // ===========================
  // Sidebar Menu Navigation
  // ===========================
  
  const menuItems = document.querySelectorAll(".menu-item");
  const sections = document.querySelectorAll(".admin-section");
  
  menuItems.forEach(item => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      
      const sectionId = item.getAttribute("data-section") + "-section";
      
      // Remove active class from all items and sections
      menuItems.forEach(i => i.classList.remove("active"));
      sections.forEach(s => s.classList.remove("active"));
      
      // Add active class to clicked item and corresponding section
      item.classList.add("active");
      const section = document.getElementById(sectionId);
      if (section) {
        section.classList.add("active");
      }
    });
  });
  
  
  // ===========================
  // Stat Card Hover Effect
  // ===========================
  
  const statCards = document.querySelectorAll(".stat-card");
  
  statCards.forEach(card => {
    card.addEventListener("mouseenter", () => {
      card.style.transform = "translateY(-6px) scale(1.01)";
    });
    
    card.addEventListener("mouseleave", () => {
      card.style.transform = "translateY(0) scale(1)";
    });
  });
  
  
  // ===========================
  // Smooth Scroll
  // ===========================
  
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
      const href = this.getAttribute('href');
      if (href !== '#') {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({ behavior: 'smooth' });
        }
      }
    });
  });

  // ===========================
  // Events Management
  // ===========================

  const eventModal = document.getElementById('eventModal');
  const eventForm = document.getElementById('eventForm');
  const modalTitle = document.getElementById('modalTitle');
  const eventIdInput = document.getElementById('eventId');
  const eventNameInput = document.getElementById('eventName');
  const eventDateInput = document.getElementById('eventDate');
  const eventDescriptionInput = document.getElementById('eventDescription');
  const eventArtistsInput = document.getElementById('eventArtists');

  function openEventModal() {
    eventIdInput.value = '';
    eventNameInput.value = '';
    eventDateInput.value = '';
    eventDescriptionInput.value = '';
    eventArtistsInput.value = '';
    modalTitle.textContent = 'Add Event';
    eventModal.style.display = 'flex';
  }

  function closeEventModal() {
    eventModal.style.display = 'none';
  }

  function editEvent(eventId) {
    // Load event data from PHP (make API call)
    fetch('Backend/api/get_event.php?id=' + eventId)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          eventIdInput.value = data.event.id;
          eventNameInput.value = data.event.event_name;
          // Format datetime
          const eventDateTime = new Date(data.event.event_date).toISOString().slice(0, 16);
          eventDateInput.value = eventDateTime;
          eventDescriptionInput.value = data.event.description;
          
          // Set selected artists (properly for multi-select)
          Array.from(eventArtistsInput.options).forEach(option => {
            option.selected = data.artist_ids && data.artist_ids.includes(parseInt(option.value));
          });
          
          modalTitle.textContent = 'Edit Event';
          eventModal.style.display = 'flex';
        }
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
      fetch('Backend/api/delete_event.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'event_id=' + eventId
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Event deleted successfully');
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => console.error('Error:', error));
    }
  }

  eventForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('event_id', eventIdInput.value);
    formData.append('event_name', eventNameInput.value);
    formData.append('event_date', eventDateInput.value);
    formData.append('description', eventDescriptionInput.value);
    
    // Get selected artists
    const selectedArtists = Array.from(eventArtistsInput.selectedOptions).map(option => option.value);
    formData.append('artist_ids', JSON.stringify(selectedArtists));

    const endpoint = eventIdInput.value ? 'Backend/api/update_event.php' : 'Backend/api/add_event.php';

    fetch(endpoint, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Event saved successfully');
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => console.error('Error:', error));
  });

  // Close modal when clicking outside
  eventModal.addEventListener('click', function(e) {
    if (e.target === eventModal) {
      closeEventModal();
    }
  });

  // ===== ARTIST MANAGEMENT FUNCTIONS =====
  const artistModal = document.getElementById('artistModal');
  const artistForm = document.getElementById('artistForm');
  const artistIdInput = document.getElementById('artistId');
  const artistNameInput = document.getElementById('artistName');
  const artistModalTitle = document.getElementById('artistModalTitle');
  const artistsTableBody = document.getElementById('artistsTableBody');

  function openArtistModal() {
    artistIdInput.value = '';
    artistNameInput.value = '';
    artistModalTitle.textContent = 'Add Artist';
    artistModal.style.display = 'flex';
  }

  function closeArtistModal() {
    artistModal.style.display = 'none';
  }

  function deleteArtist(artistId) {
    if (confirm('Are you sure you want to delete this artist?')) {
      fetch('Backend/api/delete_artist.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'artist_id=' + artistId
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Artist deleted successfully');
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => console.error('Error:', error));
    }
  }

  artistForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('artist_id', artistIdInput.value);
    formData.append('artist_name', artistNameInput.value);

    fetch('Backend/api/add_artist.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Artist saved successfully');
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => console.error('Error:', error));
  });

  // Close artist modal when clicking outside
  artistModal.addEventListener('click', function(e) {
    if (e.target === artistModal) {
      closeArtistModal();
    }
  });

  // ===== TICKET MANAGEMENT FUNCTIONS =====
  const ticketModal = document.getElementById('ticketModal');
  const ticketForm = document.getElementById('ticketForm');
  const ticketIdInput = document.getElementById('ticketId');
  const ticketNameInput = document.getElementById('ticketName');
  const ticketPriceInput = document.getElementById('ticketPrice');
  const ticketQuantityInput = document.getElementById('ticketQuantity');
  const ticketModalTitle = document.getElementById('ticketModalTitle');

  function openTicketModal() {
    ticketIdInput.value = '';
    ticketNameInput.value = '';
    ticketPriceInput.value = '';
    ticketQuantityInput.value = '';
    ticketModalTitle.textContent = 'Add Ticket';
    ticketModal.style.display = 'flex';
  }

  function closeTicketModal() {
    ticketModal.style.display = 'none';
  }

  function editTicket(ticketId) {
    // Load ticket data from PHP (make API call)
    fetch('Backend/api/get_ticket.php?id=' + ticketId)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          ticketIdInput.value = data.ticket.id;
          ticketNameInput.value = data.ticket.ticket_name;
          ticketPriceInput.value = data.ticket.price;
          ticketQuantityInput.value = data.ticket.availability;
          ticketModalTitle.textContent = 'Edit Ticket';
          ticketModal.style.display = 'flex';
        }
      })
      .catch(error => console.error('Error:', error));
  }

  function deleteTicket(ticketId) {
    if (confirm('Are you sure you want to delete this ticket type?')) {
      fetch('Backend/api/delete_ticket.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ticket_id=' + ticketId
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Ticket deleted successfully');
          location.reload();
        } else {
          alert('Error: ' + data.message);
        }
      })
      .catch(error => console.error('Error:', error));
    }
  }

  ticketForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('ticket_id', ticketIdInput.value);
    formData.append('ticket_name', ticketNameInput.value);
    formData.append('price', ticketPriceInput.value);
    formData.append('availability', ticketQuantityInput.value);

    const endpoint = ticketIdInput.value ? 'Backend/api/update_ticket.php' : 'Backend/api/add_ticket.php';

    fetch(endpoint, {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Ticket saved successfully');
        location.reload();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => console.error('Error:', error));
  });

  // Close ticket modal when clicking outside
  ticketModal.addEventListener('click', function(e) {
    if (e.target === ticketModal) {
      closeTicketModal();
    }
  });

  // ===== PURCHASE MANAGEMENT FUNCTIONS =====

  function confirmPurchase(purchaseId) {
    if (confirm('Confirm this purchase?')) {
      // Just update purchase status to confirmed
      fetch('Backend/api/admin/update_purchase_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ purchase_id: purchaseId, status: 'confirmed' })
      })
      .then(response => response.text())
      .then(statusResponse => {
        if (statusResponse.startsWith('SUCCESS')) {
          alert('✅ Purchase confirmed! User will see e-ticket in their profile.');
          location.reload();
        } else {
          alert('❌ Error: ' + statusResponse);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('❌ Error confirming purchase');
      });
    }
  }

  function cancelPurchase(purchaseId) {
    if (confirm('Cancel this purchase? This will process a refund.')) {
      fetch('Backend/api/admin/process_refund.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          purchase_id: purchaseId
        })
      })
      .then(response => response.text())
      .then(data => {
        if (data.startsWith('SUCCESS')) {
          alert('✅ Purchase cancelled and refund processed');
          location.reload();
        } else {
          alert('❌ Error: ' + data);
        }
      })
      .catch(error => console.error('Error:', error));
    }
  }

</script>

</body>
</html>