<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Events</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
   <?php
  include "header.php";
  ?>
  <section class="past-section">
    <div class="text-box">
      <h1>Performers for <br> this year</h1>
      <div class="line"></div>
      <p style="margin-top:20px; color: rgba(255,255,255,0.85); max-width: 520px;">
        Discover the main artists and the festival program for each day. Every performance is curated to deliver maximum energy.
      </p>
    </div>

    <div class="image-box">
      <img src="images/concert3.jpg" alt="festival">
    </div>
  </section>

  <section class="lineup-section">
    <h2 class="title">Festival Lineup</h2>
    <p class="lineup-hint">✨ Click on any artist to discover their performance setlist</p>

    <div class="day-block">
      <h3>Day One</h3>
      <div class="artists-row">
        <div class="artist-card" data-artist="Lana Del Rey" data-songs="Video Games, Summertime Sadness, Young and Beautiful, Burning Desire, West Coast">
          <img src="images/artist1.webp" alt="Lana Del Rey">
          <div class="overlay">
            <span class="artist-name">Lana Del Rey</span>
          </div>
        </div>

        <div class="artist-card" data-artist="Lola Young" data-songs="Ickabog, NTMI, Pick Up the Phone, You &amp; I, Never Again">
          <img src="images/artist2.webp" alt="Lola Young">
          <div class="overlay">
            <span class="artist-name">Lola Young</span>
          </div>
        </div>

        <div class="artist-card" data-artist="Neon Vibes" data-songs="Electric Dreams, Neon Nights, Synthetic Love, Digital Pulse, Future Sounds">
          <img src="images/artist3.webp" alt="Neon Vibes">
          <div class="overlay">
            <span class="artist-name">Neon Vibes</span>
          </div>
        </div>
      </div>
    </div>

    <div class="day-block">
      <h3>Day Two</h3>
      <div class="artists-row">
        <div class="artist-card" data-artist="Shawn Mendes" data-songs="Stitches, There's Nothing Holdin' Me Back, Treat You Better, Señorita, Wonder">
          <img src="images/artist4.jpg" alt="Shawn Mendes">
          <div class="overlay">
            <span class="artist-name">Shawn Mendes</span>
          </div>
        </div>

        <div class="artist-card" data-artist="Dua Lipa" data-songs="Levitating, Don't Start Now, Physical, Break My Heart, Hotter Than Hell">
          <img src="images/artist5.webp" alt="Dua Lipa">
          <div class="overlay">
            <span class="artist-name">Dua Lipa</span>
          </div>
        </div>

        <div class="artist-card" data-artist="Electric Soul" data-songs="Soul Ignition, Rhythm of the Night, Electric Heartbeat, Soul Motion, Night's Glow">
          <img src="images/artist6.png" alt="Electric Soul">
          <div class="overlay">
            <span class="artist-name">Electric Soul</span>
          </div>
        </div>
      </div>
    </div>

    <div class="day-block">
      <h3>Day Three</h3>
      <div class="artists-row">
        <div class="artist-card bts" data-artist="BTS" data-songs="Dynamite, Butter, Jungkook Solo, Permission to Dance, Yet to Come">
          <img src="images/artist7.jpg" alt="BTS">
          <div class="overlay">
            <span class="artist-name">BTS</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="festival-experience">

  <div class="experience-heading">
    <h2>ENTERTAINMENT ENTERTAINMENT ENTERTAINMENT</h2>

    <h3>WHAT IS WAITING FOR YOU</h3>

    <p>
      Explore festival zones you can enjoy with friends and discover
      unforgettable experiences throughout the weekend.
    </p>
  </div>

  <div class="experience-layout">

    <!-- LEFT COLUMN -->
    <div class="experience-column">

      <!-- TEXT -->
      <div class="experience-text">
        <div>
          <h4>MAIN STAGE</h4>

          <ul>
            <li>• BTS headline show</li>
            <li>• Massive LED visuals</li>
            <li>• Live special effects</li>
          </ul>
        </div>
      </div>

      <!-- IMAGE -->
      <div class="experience-image">
        <img src="images/stageelectro.jpg" alt="">
      </div>

      <!-- TEXT -->
      <div class="experience-text">
        <div>
          <h4>INDIE GARDEN</h4>

          <ul>
            <li>• Alternative performances</li>
            <li>• Chill atmosphere</li>
            <li>• Sunset live sessions</li>
          </ul>
        </div>
      </div>

      <!-- IMAGE -->
      <div class="experience-image">
        <img src="images/viplounge.jpg" alt="">
      </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="experience-column">

      <!-- IMAGE -->
      <div class="experience-image">
        <img src="images/mainstageimg.jpg" alt="">
      </div>

      <!-- TEXT -->
      <div class="experience-text">
        <div>
          <h4>ELECTRO DOME</h4>

          <ul>
            <li>• EDM & techno music</li>
            <li>• Neon atmosphere</li>
            <li>• All-night DJ sessions</li>
          </ul>
        </div>
      </div>

      <!-- IMAGE -->
      <div class="experience-image">
        <img src="images/garden.png" alt="">
      </div>

      <!-- TEXT -->
      <div class="experience-text">
        <div>
          <h4>VIP LOUNGE</h4>

          <ul>
            <li>• Lounge & relax area</li>
            <li>• Signature drinks</li>
            <li>• Premium festival access</li>
          </ul>
        </div>
      </div>

    </div>

  </div>

</section>

  <!-- Artist Modal -->
  <div class="artist-modal" id="artistModal">
    <div class="modal-content">
      <button class="modal-close" id="closeModal">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      
      <div class="modal-header">
        <h2 id="modalArtistName">Artist Name</h2>
        <p>Setlist for this performance</p>
      </div>
      
      <div class="modal-songs">
        <ul id="songsList">
          <!-- Songs will be populated here by JavaScript -->
        </ul>
      </div>
    </div>
  </div>

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
  </script>

  <script>
    // Artist Modal Functionality
    const artistCards = document.querySelectorAll('.artist-card');
    const artistModal = document.getElementById('artistModal');
    const closeModalBtn = document.getElementById('closeModal');
    const modalArtistName = document.getElementById('modalArtistName');
    const songsList = document.getElementById('songsList');

    artistCards.forEach(card => {
      card.addEventListener('click', () => {
        const artistName = card.getAttribute('data-artist');
        const songs = card.getAttribute('data-songs').split(',').map(song => song.trim());
        
        modalArtistName.textContent = artistName;
        songsList.innerHTML = '';
        
        songs.forEach((song, index) => {
          const li = document.createElement('li');
          li.textContent = song;
          li.style.animation = `slideUp ${0.3 + index * 0.08}s cubic-bezier(0.34, 1.56, 0.64, 1)`;
          songsList.appendChild(li);
        });
        
        artistModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      });
    });

    closeModalBtn.addEventListener('click', () => {
      artistModal.classList.remove('active');
      document.body.style.overflow = 'auto';
    });

    artistModal.addEventListener('click', (e) => {
      if (e.target === artistModal) {
        artistModal.classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && artistModal.classList.contains('active')) {
        artistModal.classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    });
  </script>
</body>
</html>