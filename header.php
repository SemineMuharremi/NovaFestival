
<?php
$showModal = isset($_SESSION['error']);
$errorMessage = $_SESSION['error'] ?? '';
?>

<header class="site-header">
  <div class="header-inner">
    <div class="brand">NOVA Festival</div>
    <nav>
      <a href="index.php" class="nav-link">Home</a>
      <a href="events.php" class="nav-link">Events</a>
      <a href="tickets.php" class="nav-link">Tickets</a>
      <a href="gallery.php" class="nav-link">Archive</a>
      <a href="info.php" class="nav-link">Info</a>
    </nav>
    <button onclick="openAuthModal()" class="header-action">Get Tickets</button>
  </div>
</header>

<!-- AUTH MODAL (Global) -->
<div id="authModal" class="auth-modal">

  <div class="auth-container">

    <!-- CLOSE BUTTON -->
    <button onclick="closeAuthModal()" class="close-btn">×</button>

    <!-- HEADER SECTION -->
    <div class="auth-header">
      <h2 id="formTitle">Login to NOVA</h2>
      <p id="formSubtitle" class="auth-subtitle">Access your account and secure your spot at the festival</p>
    </div>
    
    <?php
        if(isset($_SESSION['error'])){
        echo "<div class='auth-message error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
        unset($_SESSION['error']);
}
?>

    <!-- MESSAGE -->
    <div id="authMessage" class="auth-message"></div>

    <!-- BOOKING INFO -->
    <div class="booking-info">
      <div class="info-icon">🎟️</div>
      <p><strong>Want to book tickets?</strong> You need to create an account or log in first. It's quick, easy, and secure!</p>
    </div>

    <!-- LOGIN FORM -->
    <form  id="loginForm" >

      <div class="input-group">
        <label for="loginEmail"><span class="label-icon">✉️</span> Email Address</label>
        <input type="email" id="loginEmail" name="email" placeholder="your@email.com" required>
      </div>

      <div class="input-group">
        <label for="loginPassword"><span class="label-icon">🔐</span> Password</label>
        <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
      </div>

      <button type="submit" name="login" class="submit-btn">
        <span class="btn-icon">→</span> Login Now
      </button>

    </form>

    <!-- REGISTER FORM -->
    <form id="registerForm" style="display:none;" onsubmit="handleRegister(event)">

      <div class="input-group">
        <label for="regName"><span class="label-icon">👤</span> Full Name</label>
        <input type="text" id="regName" placeholder="Your full name" required>
      </div>

      <div class="input-group">
        <label for="regEmail"><span class="label-icon">✉️</span> Email Address</label>
        <input type="email" id="regEmail" placeholder="your@email.com" required>
      </div>

      <div class="input-group">
        <label for="regPhone"><span class="label-icon">📱</span> Phone Number <span class="optional">(Optional)</span></label>
        <input type="tel" id="regPhone" placeholder="+1 (555) 000-0000">
      </div>

      <div class="input-group">
        <label for="regPassword"><span class="label-icon">🔐</span> Password</label>
        <input type="password" id="regPassword" minlength="6" placeholder="At least 6 characters" required>
      </div>

      <button type="submit" class="submit-btn">
        <span class="btn-icon">✨</span> Create Account
      </button>

    </form>

    <!-- TOGGLE -->
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

<script>
// =========================
// HIGHLIGHT ACTIVE NAV LINK
// =========================

document.addEventListener('DOMContentLoaded', function() {
  const currentPage = window.location.pathname.split('/').pop() || 'index.php';
  const navLinks = document.querySelectorAll('.site-header nav .nav-link');
  
  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (href === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
  });
});


// =========================
// OPEN / CLOSE MODAL (Global)
// =========================
<?php
$showModal = false;

if(isset($_SESSION['error'])){
    $showModal = true;
}
?>
<?php
if($showModal){
    echo "<div class='auth-message error'>$errorMessage</div>";
}
?>

function openAuthModal() {
  document.getElementById("authModal").style.display = "flex";
  document.body.style.overflow = "hidden";
}
<?php if($showModal): ?>
window.addEventListener("load", function() {
    openAuthModal();
});
<?php endif; ?>

function closeAuthModal() {
  document.getElementById("authModal").style.display = "none";
  document.body.style.overflow = "auto";
}

// CLOSE WHEN CLICK OUTSIDE
window.addEventListener("click", function(e) {
  const modal = document.getElementById("authModal");
  if (e.target === modal) {
    closeAuthModal();
  }
});

// OPEN MODAL FROM BUTTONS (for btn-primary, btn-ticket classes)
document.addEventListener('click', function(e) {
  if (
    e.target.classList.contains('btn-primary') ||
    e.target.classList.contains('btn-ticket')
  ) {
    e.preventDefault();
    openAuthModal();
  }
});

// SWITCH FORMS
function switchToRegister() {
  document.getElementById("loginForm").style.display = "none";
  document.getElementById("registerForm").style.display = "block";

  document.getElementById("formTitle").textContent = "Join NOVA Festival";
  document.getElementById("formSubtitle").textContent = "Create your account and start booking your tickets today!";

  document.getElementById("toggleText").style.display = "none";
  document.getElementById("backToLogin").style.display = "inline";
}

function switchToLogin() {
  document.getElementById("registerForm").style.display = "none";
  document.getElementById("loginForm").style.display = "block";

  document.getElementById("formTitle").textContent = "Welcome Back";
  document.getElementById("formSubtitle").textContent = "Log in to access your account and manage your bookings";

  document.getElementById("toggleText").style.display = "inline";
  document.getElementById("backToLogin").style.display = "none";
}

// HANDLE REGISTER
async function handleRegister(e) {
  e.preventDefault();

  const name = document.getElementById("regName").value;
  const email = document.getElementById("regEmail").value;
  const phone = document.getElementById("regPhone").value;
  const password = document.getElementById("regPassword").value;

  try {
    const response = await fetch("Backend/api/register.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ name, email, phone, password })
    });

    const data = await response.json();

    if (data.success) {
      showMessage("Account created! Logging you in...", "success");
      setTimeout(() => {
        switchToLogin();
        document.getElementById("loginEmail").value = email;
        document.getElementById("loginPassword").value = password;
      }, 1500);
    } else {
      showMessage(data.message || "Registration failed", "error");
    }
  } catch (error) {
    console.error("Register error:", error);
    showMessage("Registration error. Please try again.", "error");
  }
}

// MESSAGE FUNCTION
function showMessage(message, type) {
  const msg = document.getElementById("authMessage");
  msg.textContent = message;
  msg.className = "auth-message " + type;
}
document.getElementById("loginForm").addEventListener("submit", async function(e){

    e.preventDefault();

    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("loginPassword").value;
    const submitBtn = this.querySelector('.submit-btn');
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="btn-icon">⏳</span> Logging in...';

    try{

        const response = await fetch("Backend/api/login.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                email: email,
                password: password
            })
        });

        const data = await response.json();

        if(data.success){

            // Redirect immediately without showing success message
            window.location.href = data.redirect;

        }else{

            showMessage(data.message, "error");
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;

        }

    }catch(error){

        console.error("Login error:", error);
        showMessage("❌ Ndodhi një gabim. Ju lutem provoni përsëri.", "error");
        
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

    }

});
</script>
<?php
unset($_SESSION['error']);

?>