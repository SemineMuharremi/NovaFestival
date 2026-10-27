<?php 
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>NOVA Festival</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body class="home-page">
  <?php include 'header.php';
  include "Backend/config/db.php";

$query = "SELECT * FROM ticket_types";
$result = mysqli_query($conn, $query);

$ticketData = [];

while($row = mysqli_fetch_assoc($result)){
    $ticketData[] = [
        "id" => $row['id'],
        "title" => $row['ticket_name'],
        "price" => "€" . number_format($row['price'], 2),
        "booking_fee" => "€" . number_format($row['booking_fee'], 2),
        "image" => "img" . $row['id'],
        "description" => $row['description'],
        "availability" => $row['availability']
    ];
}
 ?>


  <section class="hero">
    <div class="hero-inner">
      <h1>NOVA Festival 2026 <span>Feel the Sound</span></h1>
      <p>3 days of unforgettable music, lights and art. Secure your ticket now and join the strongest festival energy of the year.</p>

      <div class="buttons">
        <a href="#login-section" class="btn-primary">Get Tickets</a>
        <a href="gallery.html" class="btn-secondary">Explore</a>
      </div>
    </div>
  </section>

  <div class="ribbon">31 July • 1 & 2 August</div>

  <section class="section">
    <div class="events">
      <div class="event-card day1">
        <h2>Day One</h2>
        <div class="lineup">Lana Del Rey • Lola Young • Neon Vibes</div>
      </div>

      <div class="event-card day2">
        <h2>Day Two</h2>
        <div class="lineup">Shawn Mendes • Dua Lipa • Electric Soul</div>
      </div>

      <div class="event-card day3">
        <h2>Day Three</h2>
        <div class="lineup">BTS • All 7 Members</div>
      </div>
    </div>
  </section>

  <!-- HEADLINERS SECTION -->
  <section class="story-section headliners-showcase">
    <div class="story-box">
      <div class="story-text">
        <span class="mini-title">Festival Headliner</span>

        <h2>BTS Takes the Stage</h2>

        <p>
          Experience the electrifying performance of BTS, one of the world's most iconic and influential groups. All 7 members will deliver an unforgettable night of music, energy, and connection that will define this year's festival. BTS represents the pinnacle of global music influence, with a fanbase spanning 190+ countries.
        </p>

        <p>
          At NOVA 2026, they bring an unmatched level of artistry, technical excellence, and emotional connection with a 90+ minute setlist featuring hits from all eras, state-of-the-art choreography, and exclusive arrangements never performed before.
        </p>

        
      </div>

      <div class="story-image">
        <img src="images/headlinerartist.jpg" alt="BTS - Festival Headliners">
      </div>
    </div>
  </section>

  <section class="section" id="tickets">
    <div class="ticket-header">
      <h2>Tickets</h2>
      <p>Securely purchase and receive your event tickets while they are still available.</p>
    </div>

    <div class="ticket-showcase">
      <div class="ticket-description">
        <div class="description-content">
          <h3 id="ticketTitle">Early Bird</h3>
          <p id="ticketDesc">Seize the opportunity to secure your ticket and save 30 Euros off the regular price.</p>
          <div class="ticket-details-info">
            <div class="detail-item">
              <span class="label">Price:</span>
              <span class="value" id="ticketPrice">€165.00</span>
            </div>
            <div class="detail-item">
              <span class="label">Booking Fee:</span>
              <span class="value">+ €3.75</span>
            </div>
          </div>
        </div>

        <div class="ticket-nav">
          <button class="nav-btn prev" id="prevTicket">‹</button>
          <span class="ticket-counter"><span id="currentTicket">1</span> / <span id="totalTickets">6</span></span>
          <button class="nav-btn next" id="nextTicket">›</button>
        </div>
      </div>

      <div class="ticket-display">
        <div class="ticket-card-2" id="displayCard">
          <div class="ticket-image img1"></div>
          <div class="ticket-content">
            <h3>Early Bird</h3>
            <div class="ticket-price">
              <span class="price">€165.00</span>
              <span class="fee">+ €3.75 booking fee</span>
            </div>
            <p>Seize the opportunity to secure your ticket and save 30 Euros off the regular price.</p>
            <a href="#" class="btn-ticket" style="border: none; cursor: pointer; background: var(--accent); color: black; padding: 12px 24px; border-radius: 8px; font-weight: 600; text-decoration: none; display: inline-block; transition: background 0.3s ease;">Get Ticket</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="gallery" id="gallery">
    <div class="gallery-header">
      <h2>Festival Moments</h2>
      <p>A glimpse of the energy, lights and unforgettable memories from past editions.</p>
    </div>

    <div class="gallery-grid">
      <div class="gallery-item">
        <img src="images/concert1.jpg" alt="2021" />
        <div class="gallery-overlay"><span>Live Stage</span></div>
      </div>
      <div class="gallery-item">
        <img src="images/concert2.jpg" alt="concert" />
        <div class="gallery-overlay"><span>Electric Crowd</span></div>
      </div>
      <div class="gallery-item">
        <img src="images/concert3.jpg" alt="concert" />
        <div class="gallery-overlay"><span>The Energy</span></div>
      </div>
      <div class="gallery-item">
        <img src="images/concert4.jpg" alt="concert" />
        <div class="gallery-overlay"><span>Artist Moment</span></div>
      </div>
      <div class="gallery-item">
        <img src="images/concert5.jpg" alt="concert" />
        <div class="gallery-overlay"><span>Festival Vibe</span></div>
      </div>
      <div class="gallery-item">
        <img src="images/concert6.jpg" alt="concert" />
        <div class="gallery-overlay"><span>Night Show</span></div>
      </div>
      <div class="gallery-item">
        <img src="images/concert7.jpg" alt="concert" />
        <div class="gallery-overlay"><span>Open Air</span></div>
      </div>
      <div class="gallery-item">
        <img src="images/concert8.jpg" alt="concert" />
        <div class="gallery-overlay"><span>After Party</span></div>
      </div>
    </div>
  </section>

  <section class="story-section">
    <div class="story-tabs">
      <button class="tab-btn active" data-tab="story">OUR STORY</button>
      <button class="tab-btn" data-tab="foundation">FOUNDATION</button>
    </div>

    <div class="tab-content active" id="story">
      <div class="story-box">
        <div class="story-text">
          <span class="mini-title">SINCE 2018</span>

          <h2>Where Music, Art & Freedom Meet.</h2>

          <p>
            NOVA Festival started as a small summer gathering created by a group
            of friends who wanted to build a space where music felt alive again.
            What began with one stage, a few lights and hundreds of dreamers
            quickly transformed into one of the most immersive festival experiences
            for a new generation.
          </p>

          <p>
            Every year NOVA brings together artists, creators and people from
            different cultures for three unforgettable nights filled with music,
            visuals, freedom and connection under the night sky.
          </p>

          <div class="story-stats">
            <div>
              <h3>50K+</h3>
              <span>Visitors</span>
            </div>

            <div>
              <h3>120+</h3>
              <span>Artists</span>
            </div>

            <div>
              <h3>3 Days</h3>
              <span>Experience</span>
            </div>
          </div>
        </div>

        <div class="story-image">
          <img src="images/festival.jpg" alt="Festival Experience">
        </div>
      </div>
    </div>

    <div class="tab-content" id="foundation">
      <div class="story-box">
        <div class="story-text">
          <span class="mini-title">OUR FOUNDATION</span>

          <h2>Built On Creativity & Community.</h2>

          <p>
            NOVA was created with one mission: to give young artists and music
            lovers a place where creativity has no limits. Beyond concerts,
            the festival supports independent creators, visual art projects,
            local performers and sustainable event experiences.
          </p>

          <div class="foundation-cards">
            <div class="card">
              <h4>Creative Freedom</h4>
              <p>Supporting new artists and fresh ideas.</p>
            </div>

            <div class="card">
              <h4>Community</h4>
              <p>Connecting people through music and experiences.</p>
            </div>
          </div>
        </div>

        <div class="story-image">
          <img src="images/crowd.jpg" alt="Festival Crowd">
        </div>
      </div>
    </div>
  </section>

<?php 
include "footer.php";
?>

  <script>
    const header = document.querySelector('.site-header');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 30) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });

    // Story Tabs Functionality
    const tabs = document.querySelectorAll(".tab-btn");
    const contents = document.querySelectorAll(".tab-content");

    tabs.forEach(tab => {
      tab.addEventListener("click", () => {
        tabs.forEach(btn => btn.classList.remove("active"));
        contents.forEach(content => content.classList.remove("active"));

        tab.classList.add("active");

        const target = document.getElementById(tab.dataset.tab);
        target.classList.add("active");
      });
    });

    // Ticket Data
    const ticketData = <?php echo json_encode($ticketData); ?>;

    const prevBtn = document.getElementById('prevTicket');
    const nextBtn = document.getElementById('nextTicket');
    const titleEl = document.getElementById('ticketTitle');
    const descEl = document.getElementById('ticketDesc');
    const priceEl = document.getElementById('ticketPrice');
    const displayCard = document.getElementById('displayCard');
    const currentTicketEl = document.getElementById('currentTicket');
    const totalTicketsEl = document.getElementById('totalTickets');

    totalTicketsEl.textContent = ticketData.length;

    let currentIndex = 0;

    function updateTicket() {
      const ticket = ticketData[currentIndex];
      
      // Update left side description
      titleEl.textContent = ticket.title;
      descEl.textContent = ticket.description;
      priceEl.textContent = ticket.price;
      currentTicketEl.textContent = currentIndex + 1;

      // Trigger animation
      displayCard.style.animation = 'none';
      setTimeout(() => {
        displayCard.style.animation = '';
      }, 10);

      // Update right side card
      displayCard.className = 'ticket-card-2';
      const ticketImage = displayCard.querySelector('.ticket-image');
      ticketImage.className = `ticket-image ${ticket.image}`;
      
      const ticketContent = displayCard.querySelector('.ticket-content');
      const buttonHTML = ticket.availability > 0 
        ? '<a href="#" class="btn-ticket">Get Ticket</a>'
        : '<button class="btn-ticket" disabled style="cursor: not-allowed; opacity: 0.6;">SOLD OUT</button>';
      
      ticketContent.innerHTML = `
        <h3>${ticket.title}</h3>
        <div class="ticket-price">
          <span class="price">${ticket.price}</span>
          <span class="fee">+ €3.75 booking fee</span>
        </div>
        <p>${ticket.description}</p>
        ${buttonHTML}
      `;

      // Update button states
      prevBtn.classList.toggle('disabled', currentIndex === 0);
      nextBtn.classList.toggle('disabled', currentIndex === ticketData.length - 1);
    }

    prevBtn.addEventListener('click', () => {
      if (currentIndex > 0) {
        currentIndex--;
        updateTicket();
      }
    });

    nextBtn.addEventListener('click', () => {
      if (currentIndex < ticketData.length - 1) {
        currentIndex++;
        updateTicket();
      }
    });

    updateTicket();

  
  </script>
</body>
</html>