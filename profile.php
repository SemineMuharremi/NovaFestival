<?php 
session_start();
include "Backend/config/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: tickets.php");
  exit();
}

// Get logged-in user's data from database
$user_id = $_SESSION['user_id'];
$query = "SELECT id, name, email, password, phone, registered_date FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Format registered date
$registered_date = date('F d, Y', strtotime($user['registered_date']));

// Get user's purchases
$purchases_query = "SELECT p.id, tt.ticket_name, p.quantity, p.total_price, p.purchase_date, p.status
                   FROM purchases p
                   JOIN ticket_types tt ON p.ticket_type_id = tt.id
                   WHERE p.user_id = ? 
                   ORDER BY p.purchase_date DESC";
$purchases_stmt = $conn->prepare($purchases_query);
$purchases_stmt->bind_param("i", $user_id);
$purchases_stmt->execute();
$purchases_result = $purchases_stmt->get_result();

// Get user's payment methods from payments table (join with purchases)
$payment_methods_query = "SELECT DISTINCT payment_method FROM payments 
                         WHERE purchase_id IN (SELECT id FROM purchases WHERE user_id = ?)
                         ORDER BY payment_date DESC";
$payment_stmt = $conn->prepare($payment_methods_query);
$payment_stmt->bind_param("i", $user_id);
$payment_stmt->execute();
$payment_methods_result = $payment_stmt->get_result();

// Payment method will be managed via client-side localStorage
$saved_payment = null;

// Get all ticket types for Buy Tickets section
$ticket_types_query = "SELECT id, ticket_name, description, price, availability FROM ticket_types ORDER BY id ASC";
$ticket_types_stmt = $conn->prepare($ticket_types_query);
$ticket_types_stmt->execute();
$ticket_types_result = $ticket_types_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile - NOVA Festival</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
  
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="css/profile.css">
  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  
  <script>
    // Set current user ID for localStorage key
    const currentUserId = <?php echo $user_id; ?>;
    
    // ===========================
    // Section Navigation Function
    // ===========================
    function switchSection(sectionName) {
      console.log('=== SWITCH ===');
      console.log('Target:', sectionName);
      
      const id = sectionName + '-section';
      console.log('Looking for ID:', id);
      
      // Check what sections exist
      const allSections = document.querySelectorAll('.profile-section');
      console.log('Total sections found:', allSections.length);
      allSections.forEach((s, i) => {
        console.log(i + ':', s.id, '- active:', s.classList.contains('active'), '- display:', window.getComputedStyle(s).display);
      });
      
      // Find target
      const target = document.getElementById(id);
      console.log('Target element:', target);
      
      if (!target) {
        console.error('SECTION NOT FOUND:', id);
        return;
      }
      
      // Hide all sections
      allSections.forEach(s => {
        console.log('Removing active from:', s.id);
        s.classList.remove('active');
      });
      
      // Remove active class from all menu items
      document.querySelectorAll('.profile-menu-item').forEach(item => {
        item.classList.remove('active');
      });
      
      // Show target section
      console.log('Adding active to:', id);
      target.classList.add('active');
      
      // Highlight target menu item
      const activeButton = document.querySelector('[data-section="' + sectionName + '"]');
      if (activeButton) {
        activeButton.classList.add('active');
        console.log('Active button updated');
      }
      
      console.log('After action - display:', window.getComputedStyle(target).display);
      console.log('=== END ===');
    }
  </script>
</head>

<body class="profile-page">

<div class="profile-wrapper">

<!-- ================= Sidebar ================= -->

<aside class="profile-sidebar">
<div class="profile-avatar-section">
<div class="profile-avatar">
<i class="fa-solid fa-user"></i>
</div>
<h3 class="profile-name"><?php echo htmlspecialchars($user['name']); ?></h3>
<p class="profile-email"><?php echo htmlspecialchars($user['email']); ?></p>
</div>

<nav class="profile-menu">

<button type="button" class="profile-menu-item active" data-section="buy-tickets" onclick="switchSection('buy-tickets'); return false;">
<span>Buy Tickets</span>
</button>

<button type="button" class="profile-menu-item" data-section="profile" onclick="switchSection('profile'); return false;">
<span>My Profile</span>
</button>

<button type="button" class="profile-menu-item" data-section="payment" onclick="switchSection('payment'); return false;">
<span>Payment Info</span>
</button>

<button type="button" class="profile-menu-item" data-section="tickets" onclick="switchSection('tickets'); return false;">
<span>My Tickets</span>
</button>

</nav>

<a href="Backend/api/logout.php" class="profile-logout-btn">
<span>Logout</span>
</a>

</aside>

<!-- ================= Main Content ================= -->

<main class="profile-main">

<!-- Buy Tickets Section -->
<section id="buy-tickets-section" class="profile-section active">
<div class="section-header">
<h2>Buy Tickets</h2>
<p>Choose and purchase tickets for upcoming events</p>
</div>

<div class="buy-tickets-grid">

<?php 
if ($ticket_types_result->num_rows > 0) {
  $ticket_counter = 1;
  while ($ticket = $ticket_types_result->fetch_assoc()) {
    $ticket_id = $ticket['id'];
    $ticket_name = htmlspecialchars($ticket['ticket_name']);
    $description = htmlspecialchars($ticket['description']);
    $price = floatval($ticket['price']);
    $available = intval($ticket['availability']);
    $icon_class = strtolower(str_replace(' ', '', $ticket_name));
?>

<div class="ticket-type-card-large">
<div class="ticket-icon-large <?php echo $icon_class; ?>">
<?php echo str_pad($ticket_counter, 2, '0', STR_PAD_LEFT); ?>
</div>
<div class="ticket-info-large">
<h3 class="ticket-name-large"><?php echo $ticket_name; ?></h3>
<p class="ticket-desc"><?php echo $description; ?></p>
<p class="ticket-price-large">€<?php echo number_format($price, 2); ?></p>
<p class="ticket-fee-large">+€3.75 fee</p>
<p class="ticket-available">Available: <?php echo $available; ?></p>
</div>
<div class="ticket-quantity-section">
<div class="quantity-controls">
<button class="btn-quantity" onclick="decreaseQuantity(this)">
<i class="fa-solid fa-minus"></i>
</button>
<input type="number" class="quantity-input" value="1" min="1" max="<?php echo $available; ?>" readonly>
<button class="btn-quantity" onclick="increaseQuantity(this)">
<i class="fa-solid fa-plus"></i>
</button>
</div>
<div class="total-price-section">
<p class="total-label">Total:</p>
<p class="total-price" data-base-price="<?php echo $price; ?>">€<?php echo number_format($price, 2); ?></p>
</div>
</div>
<button class="btn-purchase" onclick="buyTicket(this, '<?php echo $ticket_name; ?>', <?php echo $price; ?>, <?php echo $ticket_id; ?>)">
Buy Now
</button>
</button>
</div>

<?php 
    $ticket_counter++;
  }
} else {
  echo '<p style="text-align: center; color: var(--muted); padding: 40px; grid-column: 1 / -1;">No tickets available at the moment.</p>';
}
?>

</div>
</section>

<!-- Profile Section -->
<section id="profile-section" class="profile-section">
<div class="section-header">
<h2>My Profile</h2>
<p>Update your personal information</p>
</div>

<?php 
if (isset($_SESSION['success'])) {
  echo '<div class="message-box success">' . htmlspecialchars($_SESSION['success']) . '</div>';
  unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
  echo '<div class="message-box error">' . htmlspecialchars($_SESSION['error']) . '</div>';
  unset($_SESSION['error']);
}
?>

<div class="profile-form-container">
<form class="profile-form" method="POST" action="Backend/api/update_profile.php">

<div class="form-group">
<label for="email">Email Address</label>
<input type="email" id="email" name="email" placeholder="your.email@example.com" value="<?php echo htmlspecialchars($user['email']); ?>">
</div>

<div class="form-group">
<label for="password">Password</label>
<input type="password" id="password" name="password" placeholder="••••••••" value="">
<small>Leave empty to keep current password</small>
</div>

<div class="form-group">
<label for="phone">Phone Number</label>
<input type="tel" id="phone" name="phone" placeholder="+355 69 123 4567" value="<?php echo htmlspecialchars($user['phone']); ?>">
</div>

<div class="form-group">
<label for="registered">Member Since</label>
<input type="text" id="registered" name="registered" placeholder="January 15, 2026" value="<?php echo $registered_date; ?>" disabled>
<small>Account creation date</small>
</div>

<button type="submit" class="btn-update-profile">
Save Changes
</button>

</form>
</div>

</section>

</section>

<!-- Payment Information Section -->
<section id="payment-section" class="profile-section">
<div class="section-header">
<h2>Payment Information</h2>
</div>

<?php 
if (isset($_SESSION['success'])) {
  echo '<div class="message-box success">' . htmlspecialchars($_SESSION['success']) . '</div>';
  unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
  echo '<div class="message-box error">' . htmlspecialchars($_SESSION['error']) . '</div>';
  unset($_SESSION['error']);
}
?>

<div class="payment-methods-container">

<!-- Saved Payment Methods -->
<div class="saved-payments-section">
<h3>Saved Payment Method</h3>

<div id="payment-display" style="display: none;">
<div class="payment-method-item" style="border: 2px solid #f8a601; background: rgba(248, 166, 1, 0.1);">
<div>
<p class="card-number"><strong id="display-payment-method"></strong> ✓</p>
<p style="font-size: 12px; color: #cbd9ff;">Cardholder: <span id="display-cardholder-name"></span></p>
<p style="font-size: 11px; color: #10b981;">✓ Active - Used for all purchases</p>
</div>
<div style="display: flex; gap: 8px;">
<button type="button" class="btn-remove-payment" onclick="changePayment()">Change</button>
<button type="button" class="btn-remove-payment" onclick="removePayment(event)" style="background-color: #ef4444;">Remove</button>
</div>
</div>
</div>

<div id="payment-empty" style="display: none;">
<p style="color: #f59e0b; margin-bottom: 20px;">⚠️ No payment method saved yet. Add one below.</p>
</div>

</div>

<!-- Add/Edit Payment Method Form (Hidden by default) -->
<div class="add-payment-section" id="payment-form-container">
<h3 id="form-title">Add Payment Method</h3>

<form class="payment-form-simple" method="POST" action="Backend/api/add_payment.php" onsubmit="handlePaymentForm(event)">

<div class="form-group">
<label>Card Type</label>
<select name="payment_method" id="payment_method_select" required>
<option value="">Select</option>
<option value="credit_card">Credit Card (Visa, Mastercard, etc.)</option>
<option value="debit_card">Debit Card</option>
<option value="paypal">PayPal</option>
<option value="bank_transfer">Bank Transfer</option>
</select>
</div>

<div class="form-group">
<label>Name</label>
<input type="text" name="cardholder_name" id="cardholder_name_input" placeholder="Full name" required>
</div>

<div class="form-group">
<label>Card Number</label>
<input type="text" name="card_number" id="card_number_input" placeholder="Card number" required>
</div>

<div class="form-row">
<div class="form-group">
<label>Expiry Date</label>
<input type="text" name="expiry_date" id="expiry_date_input" placeholder="MM/YY" required>
</div>
<div class="form-group">
<label>CVC</label>
<input type="text" name="cvc" id="cvc_input" placeholder="123" required>
</div>
</div>

<div style="display: flex; gap: 8px;">
<button type="submit" class="btn-add-payment" id="submit-btn">Save Method</button>
<button type="button" class="btn-add-payment" id="cancel-btn" onclick="cancelEditPayment()" style="background-color: #6b7280; display: none;">Cancel</button>
</div>

</form>

</div>

</div>

</section>

<!-- My Tickets Section -->
<section id="tickets-section" class="profile-section">
<div class="section-header">
<h2>My Tickets</h2>
<p>View your purchased tickets and details</p>
</div>

<?php
// Check for cancelled purchases and show refund message
$cancelled_purchases_query = "SELECT p.id, p.total_price 
                              FROM purchases p
                              WHERE p.user_id = ? AND p.status = 'cancelled'";
$cancelled_stmt = $conn->prepare($cancelled_purchases_query);
$cancelled_stmt->bind_param("i", $user_id);
$cancelled_stmt->execute();
$cancelled_result = $cancelled_stmt->get_result();

if ($cancelled_result->num_rows > 0) {
  $total_refunded = 0;
  while ($cancelled = $cancelled_result->fetch_assoc()) {
    $total_refunded += $cancelled['total_price'];
  }
  echo '<div class="message-box" style="background-color: #dbeafe; border-left: 4px solid #3b82f6; color: #1e40af; margin-bottom: 20px; padding: 15px; border-radius: 4px;">';
  echo '  <strong>ℹ️ Refund Notification:</strong><br>';
  echo '  Some of your purchases were cancelled by admin.<br>';
  echo '  <strong>Refunded amount: €' . number_format($total_refunded, 2) . '</strong><br>';
  echo '  The money has been returned to your account. Cancelled tickets are hidden from below.';
  echo '</div>';
}
?>

<div class="tickets-grid-profile">

<?php 
// Fetch purchases excluding cancelled ones
$purchases_query = "SELECT p.id, tt.ticket_name, p.quantity, p.total_price, p.purchase_date, p.status
                   FROM purchases p
                   JOIN ticket_types tt ON p.ticket_type_id = tt.id
                   WHERE p.user_id = ? AND p.status != 'cancelled'
                   ORDER BY p.purchase_date DESC";
$purchases_stmt = $conn->prepare($purchases_query);
$purchases_stmt->bind_param("i", $user_id);
$purchases_stmt->execute();
$purchases_result = $purchases_stmt->get_result();

if ($purchases_result->num_rows > 0) {
  while ($purchase = $purchases_result->fetch_assoc()) {
    $purchase_date = date('M d, Y', strtotime($purchase['purchase_date']));
    $status_class = strtolower($purchase['status']);
?>

<div class="profile-ticket-card">
<div class="profile-ticket-image" style="background: linear-gradient(135deg, rgba(248, 166, 1, 0.15), rgba(248, 166, 1, 0.05))"></div>
<div class="profile-ticket-overlay"></div>
<div class="profile-ticket-content">
<h3><?php echo htmlspecialchars($purchase['ticket_name']); ?></h3>
<div class="profile-ticket-price">
<span class="profile-price">€<?php echo number_format($purchase['total_price'], 2); ?></span>
<span class="profile-fee">x<?php echo $purchase['quantity']; ?> ticket<?php echo $purchase['quantity'] > 1 ? 's' : ''; ?></span>
</div>
<p><?php echo $purchase_date; ?></p>
<div class="profile-ticket-meta">
<span class="profile-ticket-status <?php echo $status_class; ?>"><?php echo ucfirst($purchase['status']); ?></span>
<?php if ($purchase['status'] === 'confirmed'): ?>
<button class="profile-btn-download" onclick="downloadETicket(<?php echo $purchase['id']; ?>)">
<i class="fa-solid fa-download"></i> Download
</button>
<?php else: ?>
<button class="profile-btn-download" disabled style="opacity: 0.5; cursor: not-allowed;" title="E-ticket available after admin confirmation">
<i class="fa-solid fa-download"></i> Download
</button>
<?php endif; ?>
</div>
</div>
</div>

<?php 
  }
} else {
  echo '<p style="text-align: center; color: var(--muted); padding: 40px; grid-column: 1 / -1;">No tickets purchased yet.</p>';
}
?>

</div>
</section>

</main>

</div>

<script>
// ===========================
// Payment Form Handler
// ===========================

function handlePaymentForm(event) {
  event.preventDefault();
  
  const payment_method = document.getElementById('payment_method_select').value;
  const cardholder_name = document.getElementById('cardholder_name_input').value;
  const card_number = document.getElementById('card_number_input').value;
  const expiry_date = document.getElementById('expiry_date_input').value;
  const cvc = document.getElementById('cvc_input').value;
  
  if (!payment_method || !cardholder_name || !card_number || !expiry_date || !cvc) {
    alert('❌ Please fill in all fields');
    return false;
  }
  
  // Save to localStorage with user ID key
  const paymentData = {
    payment_method: payment_method,
    cardholder_name: cardholder_name,
    card_number: card_number,
    expiry_date: expiry_date,
    cvc: cvc,
    saved_date: new Date().toISOString()
  };
  
  const storageKey = 'savedPaymentMethod_' + currentUserId;
  localStorage.setItem(storageKey, JSON.stringify(paymentData));
  
  // Update hasPaymentInfo variable
  hasPaymentInfo = true;
  
  // Show success and update display
  alert('✅ Payment method saved successfully!');
  displaySavedPayment();
  
  // Hide form and clear inputs
  document.getElementById('payment-form-container').style.display = 'none';
  document.getElementById('payment_method_select').value = '';
  document.getElementById('cardholder_name_input').value = '';
  document.getElementById('card_number_input').value = '';
  document.getElementById('expiry_date_input').value = '';
  document.getElementById('cvc_input').value = '';
  
  return false;
}

// ===========================
// Buy Ticket Function
// ===========================

// Check if payment info exists in localStorage (with user ID key)
let hasPaymentInfo = localStorage.getItem('savedPaymentMethod_' + currentUserId) !== null;

function buyTicket(button, ticketType, basePrice, ticketTypeId) {
  const card = button.closest('.ticket-type-card-large');
  const quantityInput = card.querySelector('.quantity-input');
  const totalPriceEl = card.querySelector('.total-price');
  
  const quantity = parseInt(quantityInput.value);
  const totalPrice = parseFloat(totalPriceEl.getAttribute('data-total-price') || basePrice * quantity);
  
  if (!hasPaymentInfo) {
    alert('⚠️ Please add a payment method first before buying tickets!');
    document.querySelector('[data-section="payment"]').click();
    return;
  }
  
  // Disable button to prevent double-click
  button.disabled = true;
  button.textContent = 'Processing...';
  
  // Get payment method from localStorage
  const storageKey = 'savedPaymentMethod_' + currentUserId;
  const savedPaymentData = JSON.parse(localStorage.getItem(storageKey));
  const payment_method = savedPaymentData ? savedPaymentData.payment_method : '';
  
  // Send purchase request with payment method
  const formData = new FormData();
  formData.append('ticket_type_id', ticketTypeId);
  formData.append('quantity', quantity);
  formData.append('payment_method', payment_method);
  
  fetch('Backend/api/purchase_ticket.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Show pending confirmation message
      alert('✅ Purchase successful!\n\nTicket: ' + data.ticket_name + '\nQuantity: ' + data.quantity + '\nTotal: $' + data.total_price.toFixed(2) + '\nTransaction ID: ' + data.transaction_id + '\n\n⏳ Waiting for admin confirmation...\n\nYou will receive your e-ticket once approved.');
      
      // Reset quantity
      quantityInput.value = 1;
      updateTotalPrice(totalPriceEl, basePrice, 1);
      
      // Reload page to show updated tickets
      location.reload();
    } else {
      alert('❌ Purchase failed: ' + data.message);
      button.disabled = false;
      button.textContent = 'Buy Now';
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('❌ An error occurred during purchase');
    button.disabled = false;
    button.textContent = 'Buy Now';
  });
}

// E-Ticket Modal Display (Deprecated - Old Success Modal)
// ===========================
// Note: This old function is replaced by the new inline modal design
// The new showETicketModal() is defined below with parameters
// ===========================

function copyToClipboard(text) {
  navigator.clipboard.writeText(text).then(() => {
    alert('✅ E-ticket token copied to clipboard!');
  });
}

// ===========================
// Download E-Ticket Function
// ===========================

function downloadETicket(purchaseId) {
  fetch('Backend/api/get_user_eticket.php?id=' + purchaseId)
    .then(response => response.text())
    .then(data => {
      // Log the raw response for debugging
      console.log('Raw response:', data);
      
      // Trim any whitespace
      data = data.trim();
      
      if (data.startsWith('Error:')) {
        const errorMsg = data.substring(6);
        alert('❌ ' + errorMsg);
        return;
      }
      
      if (!data.startsWith('SUCCESS:')) {
        console.error('Invalid response:', data);
        alert('❌ Invalid response from server: ' + data.substring(0, 100));
        return;
      }
      
      // Parse pipe-delimited data
      const payload = data.substring(8);
      console.log('Payload:', payload);
      
      const parts = payload.split('|');
      if (parts.length < 8) {
        alert('❌ Invalid e-ticket data format');
        return;
      }
      
      const eticket_token = parts[0];
      const id = parts[1];
      const full_name = decodeURIComponent(parts[2]);
      const email = decodeURIComponent(parts[3]);
      const ticket_name = decodeURIComponent(parts[4]);
      const quantity = parts[5];
      const purchase_date = parts[6];
      const total_price = parts[7];
      
      // Display e-ticket in modal
      showETicketModal(eticket_token, id, full_name, email, ticket_name, quantity, purchase_date, total_price);
    })
    .catch(error => {
      console.error('Fetch error:', error);
      alert('❌ Network error: ' + error.message);
    });
}

function showETicketModal(token, id, name, email, type, qty, date, price) {
  // Populate the modal with e-ticket data
  document.getElementById('modal-ticket-code').textContent = token;
  document.getElementById('modal-attendee-name').textContent = name;
  document.getElementById('modal-attendee-email').textContent = email;
  document.getElementById('modal-ticket-type').textContent = type;
  document.getElementById('modal-ticket-type-info').textContent = type;
  document.getElementById('modal-quantity').textContent = qty;
  document.getElementById('modal-purchase-date').textContent = date;
  document.getElementById('modal-verification-code').textContent = token ? token.substring(0, 20) + '...' : 'INVALID';
  
  // Generate Barcode (using first 12 digits of token)
  const barcodeSvg = document.getElementById('barcode');
  try {
    if (typeof JsBarcode === 'undefined') {
      console.error('JsBarcode library not loaded');
    } else {
      const barcodeData = token.substring(9, 21).replace(/[^0-9]/g, '0').substring(0, 12);
      JsBarcode(barcodeSvg, barcodeData, {
        format: 'CODE128',
        width: 2,
        height: 50,
        margin: 5,
        lineColor: '#000'
      });
    }
  } catch (e) {
    console.error('Barcode generation error:', e);
  }
  
  // Show the modal
  const modal = document.getElementById('eticket-modal');
  modal.classList.add('active');
}

function closeETicketModal() {
  const modal = document.getElementById('eticket-modal');
  modal.classList.remove('active');
}

function downloadETicketPDF() {
  const element = document.querySelector('.ticket');
  
  if (!element) {
    alert('❌ Ticket not found. Please open the e-ticket first.');
    return;
  }
  
  if (!window.html2canvas) {
    alert('❌ PDF library loading. Please try again in a moment.');
    return;
  }
  
  html2canvas(element, {
    scale: 2,
    backgroundColor: '#111',
    useCORS: true,
    allowTaint: true
  }).then(canvas => {
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF({
      orientation: 'landscape',
      unit: 'px',
      format: [canvas.width, canvas.height]
    });
    
    pdf.addImage(canvas.toDataURL('image/png'), 'PNG', 0, 0, canvas.width, canvas.height);
    pdf.save('NOVA-Festival-eticket.pdf');
  }).catch(error => {
    console.error('PDF generation error:', error);
    alert('❌ Error generating PDF: ' + error.message);
  });
}

// ===========================
// Quantity Control Functions
// ===========================

function increaseQuantity(button) {
  const card = button.closest('.ticket-type-card-large');
  const quantityInput = card.querySelector('.quantity-input');
  const totalPriceEl = card.querySelector('.total-price');
  const basePrice = parseFloat(totalPriceEl.getAttribute('data-base-price'));
  
  let quantity = parseInt(quantityInput.value);
  const maxQuantity = parseInt(quantityInput.getAttribute('max'));
  
  if (quantity < maxQuantity) {
    quantity++;
    quantityInput.value = quantity;
    updateTotalPrice(totalPriceEl, basePrice, quantity);
  } else {
    alert(`⚠️ Maximum quantity is ${maxQuantity}`);
  }
}

function decreaseQuantity(button) {
  const card = button.closest('.ticket-type-card-large');
  const quantityInput = card.querySelector('.quantity-input');
  const totalPriceEl = card.querySelector('.total-price');
  const basePrice = parseFloat(totalPriceEl.getAttribute('data-base-price'));
  
  let quantity = parseInt(quantityInput.value);
  
  if (quantity > 1) {
    quantity--;
    quantityInput.value = quantity;
    updateTotalPrice(totalPriceEl, basePrice, quantity);
  }
}

function updateTotalPrice(priceElement, basePrice, quantity) {
  const total = basePrice * quantity;
  priceElement.textContent = `€${total.toFixed(2)}`;
  priceElement.setAttribute('data-total-price', total);
}

// ===========================
// Save Payment Information
// ===========================

// ===========================
// PAYMENT FUNCTIONS
// ===========================

// ===========================
// PAYMENT FUNCTIONS
// ===========================

function displaySavedPayment() {
  const storageKey = 'savedPaymentMethod_' + currentUserId;
  const saved = localStorage.getItem(storageKey);
  
  if (saved) {
    const paymentData = JSON.parse(saved);
    document.getElementById('display-payment-method').textContent = paymentData.payment_method;
    document.getElementById('display-cardholder-name').textContent = paymentData.cardholder_name;
    document.getElementById('payment-display').style.display = 'block';
    document.getElementById('payment-empty').style.display = 'none';
    document.getElementById('payment-form-container').style.display = 'none';
  } else {
    document.getElementById('payment-display').style.display = 'none';
    document.getElementById('payment-empty').style.display = 'block';
    document.getElementById('payment-form-container').style.display = 'block';
  }
}

function changePayment() {
  const formContainer = document.getElementById('payment-form-container');
  formContainer.style.display = formContainer.style.display === 'none' ? 'block' : 'none';
  document.getElementById('submit-btn').textContent = 'Update Method';
  document.getElementById('cancel-btn').style.display = 'inline-block';
}

function cancelEditPayment() {
  const formContainer = document.getElementById('payment-form-container');
  formContainer.style.display = 'none';
  document.getElementById('submit-btn').textContent = 'Save Method';
  document.getElementById('cancel-btn').style.display = 'none';
  // Clear form fields
  document.getElementById('payment_method_select').value = '';
  document.getElementById('cardholder_name_input').value = '';
  document.getElementById('card_number_input').value = '';
  document.getElementById('expiry_date_input').value = '';
  document.getElementById('cvc_input').value = '';
}

function removePayment(event) {
  event.preventDefault();
  
  if (confirm('⚠️ Are you sure you want to remove this payment method?')) {
    const storageKey = 'savedPaymentMethod_' + currentUserId;
    localStorage.removeItem(storageKey);
    alert('✅ Payment method removed successfully!');
    displaySavedPayment();
  }
}

// ===========================
// Page Load
// ===========================

document.addEventListener('DOMContentLoaded', function() {
  console.log('Profile page loaded for user:', currentUserId);
  
  // Display saved payment method from localStorage
  displaySavedPayment();
  
  // Update hasPaymentInfo
  hasPaymentInfo = localStorage.getItem('savedPaymentMethod_' + currentUserId) !== null;
  
  // Show initial section
  switchSection('buy-tickets');
});
</script>

<!-- E-Ticket Modal -->
<div id="eticket-modal" class="eticket-modal">
  <div class="eticket-modal-overlay" onclick="closeETicketModal()"></div>
  <div class="eticket-modal-container">
    <button class="modal-close-btn" onclick="closeETicketModal()">✕</button>
    
    <div class="ticket">
      <!-- LEFT PANEL -->
      <div class="ticket-left">
        <div class="ticket-logo">NOVA</div>

        <div class="barcode-area">
          <svg id="barcode"></svg>
        </div>

        <div class="left-details">
          <span class="detail-left" id="modal-purchase-date"></span>
          <span class="detail-left" id="modal-ticket-type"></span>
          <span class="detail-left">Qty: <span id="modal-quantity"></span></span>
          <span class="detail-left ticket-code-left" id="modal-ticket-code"></span>
        </div>
      </div>

      <!-- RIGHT PANEL -->
      <div class="ticket-right">
        <h1>NOVA</h1>
        <h2>FESTIVAL 2026</h2>
        <p class="subtitle">LIVE MUSIC • OPEN AIR • SUMMER FESTIVAL</p>

        <div class="bottom-content">
          <div class="ticket-info">
            <h3 id="modal-attendee-name"></h3>
            <p id="modal-attendee-email"></p>
            <p>Ticket: <span id="modal-ticket-type-info"></span></p>
            <p>Verification: <span id="modal-verification-code"></span></p>
            <p style="margin-top: 15px; color: #3a9fd9; font-weight: 600;">Valid: July 31 - August 2</p>
          </div>
        </div>

        <img class="artist-image" alt="Artist">
      </div>
    </div>

    <div class="modal-actions">
      <button class="btn-modal-print" onclick="window.print()">🖨 Print</button>
      <button class="btn-modal-download" onclick="downloadETicketPDF()">⬇ Download PDF</button>
      <button class="btn-modal-close" onclick="closeETicketModal()">✕ Close</button>
    </div>
  </div>
</div>

</body>
</html>
