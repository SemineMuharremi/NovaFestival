<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tickets</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body>

<?php 
include "header.php";
include "Backend/config/db.php";

/* =========================
   GET TICKETS FROM DATABASE
========================= */

$query = "SELECT * FROM ticket_types";
$result = mysqli_query($conn, $query);

?>

<!-- AUTH MODAL -->
<div id="authModal" class="auth-modal">

  <div class="auth-container">

    <button onclick="closeAuthModal()" class="close-btn">×</button>

    <h2 id="formTitle">Login</h2>

    <div id="authMessage" class="auth-message"></div>

    <!-- LOGIN -->
    <form id="loginForm" onsubmit="handleLogin(event)">

      <div class="input-group">
        <label>Email</label>
        <input type="email" id="loginEmail" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" id="loginPassword" required>
      </div>

      <button type="submit" class="submit-btn">
        Login
      </button>

    </form>

    <!-- REGISTER -->
    <form id="registerForm" style="display:none;" onsubmit="handleRegister(event)">

      <div class="input-group">
        <label>Full Name</label>
        <input type="text" id="regName" required>
      </div>

      <div class="input-group">
        <label>Email</label>
        <input type="email" id="regEmail" required>
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" id="regPassword" required>
      </div>

      <button type="submit" class="submit-btn">
        Create Account
      </button>

    </form>

    <div class="toggle-auth">

      <span id="toggleText">
        Don't have an account?

        <button type="button" onclick="switchToRegister()">
          Sign Up
        </button>
      </span>

      <span id="backToLogin" style="display:none;">
        Already have an account?

        <button type="button" onclick="switchToLogin()">
          Login
        </button>
      </span>

    </div>

  </div>

</div>

<!-- HERO -->
<section class="past-section">

  <div class="text-box">

    <h1>Tickets <br> for NOVA 2026</h1>

    <div class="line"></div>

    <p style="margin-top:20px; color: rgba(255,255,255,0.85); max-width: 520px;">

      Choose the best access level for your festival weekend.
      All tickets include official entry, stage access,
      and festival updates.

    </p>

  </div>

  <div class="image-box">
    <img src="images/ticket1.jpg" alt="Festival Tickets" />
  </div>

</section>

<!-- TICKETS -->
<section class="section" id="tickets">

  <div class="ticket-header">

    <div class="ticket-header-label">
      GET YOUR ACCESS
    </div>

    <h2>Choose Your NOVA Experience</h2>

    <p>
      Secure your spot at the most anticipated festival
      of the year. Each ticket level offers unique benefits
      and unforgettable moments.
    </p>

    <div class="header-divider"></div>

  </div>

  <!-- GRID -->
  <div class="ticket-grid">

    <?php 
    while($row = mysqli_fetch_assoc($result)) {
    ?>

    <div class="ticket-card-2">

      <!-- IMAGE -->
      <div class="ticket-image img<?php echo $row['id']; ?>"></div>

      <!-- CONTENT -->
      <div class="ticket-content">

        <h3>
          <?php echo htmlspecialchars($row['ticket_name']); ?>
        </h3>

        <div class="ticket-price">

          <span class="price">
            €<?php echo number_format($row['price'], 2); ?>
          </span>

          <span class="fee">
            + €<?php echo number_format($row['booking_fee'], 2); ?>
            booking fee
          </span>

        </div>

        <p>
          <?php echo htmlspecialchars($row['description'] ?? ''); ?>
        </p>

        <?php if ($row['availability'] > 0): ?>
          <a href="#" class="btn-ticket">
            Get Ticket
          </a>
        <?php else: ?>
          <button class="btn-ticket" disabled style="cursor: not-allowed; opacity: 0.6;">
            SOLD OUT
          </button>
        <?php endif; ?>

      </div>

    </div>

    <?php } ?>

  </div>

</section>

<!-- TICKET DETAILS -->
<section class="section ticket-details">

  <div>
    <h1>Ticket Details</h1>
  </div>

  <div class="accordion-container">

    <div class="top-expand" id="expandAll">
      Expand All Sections <span>+</span>
    </div>

    <!-- ACCESS -->
    <div class="accordion-item">

      <div class="accordion-header">
        <h2>ACCESS INFORMATION</h2>
        <div class="icon">−</div>
      </div>

      <div class="accordion-content active">

        <ul>
          <li>All tickets are digital and delivered by email.</li>
          <li>Entry starts at 10:00 AM each festival day.</li>
          <li>VIP ticket holders get priority arrival lanes.</li>
          <li>Group passes are valid for groups of 4 or more.</li>
          <li>All patrons must present a valid ID at entry.</li>
        </ul>

      </div>

    </div>

    <!-- INCLUDED -->
    <div class="accordion-item">

      <div class="accordion-header">
        <h2>WHAT’S INCLUDED</h2>
        <div class="icon">+</div>
      </div>

      <div class="accordion-content">

        <ul>
          <li>Official festival access and daily programs.</li>
          <li>Food court access across the venue.</li>
          <li>Access to art spaces and lounge areas.</li>
        </ul>

      </div>

    </div>

    <!-- FAQ -->
    <div class="accordion-item">

      <div class="accordion-header">
        <h2>FAQ</h2>
        <div class="icon">+</div>
      </div>

      <div class="accordion-content">

        <ul>
          <li>Tickets are non-refundable but transferable.</li>
          <li>Children under 5 enter free with adult ticket.</li>
          <li>Food and drinks are paid separately inside.</li>
        </ul>

      </div>

    </div>

  </div>

</section>

<?php include "footer.php"; ?>

<script>

  /* =========================
     ACCORDION
  ========================= */

  const headers = document.querySelectorAll('.accordion-header');

  headers.forEach(header => {

    header.addEventListener('click', () => {

      const content = header.nextElementSibling;
      const icon = header.querySelector('.icon');

      content.classList.toggle('active');

      icon.innerHTML =
        content.classList.contains('active')
        ? '−'
        : '+';

    });

  });


  /* =========================
     EXPAND ALL
  ========================= */

  document.getElementById('expandAll')
  .addEventListener('click', () => {

    document.querySelectorAll('.accordion-content')
    .forEach(content => {

      content.classList.add('active');

    });

    document.querySelectorAll('.icon')
    .forEach(icon => icon.innerHTML = '−');

  });


  /* =========================
     HEADER SCROLL
  ========================= */

  const header = document.querySelector('.site-header');

  window.addEventListener('scroll', () => {

    if (window.scrollY > 30) {

      header.classList.add('scrolled');

    } else {

      header.classList.remove('scrolled');

    }

  });


  /* =========================
     MODAL
  ========================= */

  function openAuthModal() {

    document.getElementById("authModal").style.display = "flex";

    document.body.style.overflow = "hidden";

  }

  function closeAuthModal() {

    document.getElementById("authModal").style.display = "none";

    document.body.style.overflow = "auto";

  }


  /* CLICK OUTSIDE */

  window.addEventListener("click", function(e) {

    const modal = document.getElementById("authModal");

    if (e.target === modal) {

      closeAuthModal();

    }

  });


  /* OPEN MODAL */

  document.addEventListener("click", function(e) {

    if (e.target.classList.contains("btn-ticket")) {

      e.preventDefault();

      openAuthModal();

    }

  });


  /* =========================
     SWITCH FORMS
  ========================= */

  function switchToRegister() {

    document.getElementById("loginForm").style.display =
      "none";

    document.getElementById("registerForm").style.display =
      "block";

    document.getElementById("formTitle").textContent =
      "Create Account";

    document.getElementById("toggleText").style.display =
      "none";

    document.getElementById("backToLogin").style.display =
      "inline";

  }

  function switchToLogin() {

    document.getElementById("registerForm").style.display =
      "none";

    document.getElementById("loginForm").style.display =
      "block";

    document.getElementById("formTitle").textContent =
      "Login";

    document.getElementById("toggleText").style.display =
      "inline";

    document.getElementById("backToLogin").style.display =
      "none";

  }


  /* =========================
     LOGIN
  ========================= */

  function handleLogin(event) {

    event.preventDefault();

    showMessage(
      "Login successful!",
      "success"
    );

    setTimeout(() => {

      closeAuthModal();

    }, 1500);

  }


  /* =========================
     REGISTER
  ========================= */

  function handleRegister(event) {

    event.preventDefault();

    showMessage(
      "Account created successfully!",
      "success"
    );

    setTimeout(() => {

      switchToLogin();

    }, 1500);

  }


  /* =========================
     MESSAGE
  ========================= */

  function showMessage(message, type) {

    const msg = document.getElementById("authMessage");

    msg.textContent = message;

    msg.className =
      "auth-message " + type;

  }

</script>

</body>
</html>