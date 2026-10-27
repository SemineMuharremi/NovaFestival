<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NOVA Festival Archive</title>
  <link rel="stylesheet" href="style.css" />
</head>
<style>
    /*Pjesa e archive (galeris)*/
.archive {
  padding: 140px 40px 100px;
  max-width: 1500px;
  margin: auto;
  color: white;
  position: relative;
}

.archive::before{
  content:"";
  position:absolute;
  top:0;
  left:50%;
  transform:translateX(-50%);
  width:70%;
  height:1px;
  background:rgba(255,255,255,0.08);
}

/* HEADER */
.archive-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 55px;
  gap: 30px;
  flex-wrap: wrap;
}

.archive-header h1 {
  font-weight: 900;
  letter-spacing: -3px;
  line-height: 0.95;
}

/* YEAR TABS */
.year-tabs {
  display: flex;
  gap: 34px;
  flex-wrap: wrap;
}

.year-btn {
  background: transparent;
  border: none;
  color: rgba(255,255,255,0.32);
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  position: relative;
  padding-bottom: 10px;
  transition: 0.25s ease;
  letter-spacing: 0.5px;
}

.year-btn:hover {
  color: white;
}

.year-btn.active {
  color: white;
}

.year-btn.active::after {
  content: "";
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  height: 2px;
  background: #ffb400;
}

/* GRID */
.archive-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 16px;
}

/* CARDS */
.img-card {
  position: relative;
  overflow: hidden;
  border-radius: 10px;
  background: #111;
  height: 420px;
  cursor: pointer;
}

/* FOTO */
.img-card img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}




/* HIDDEN */
.img-card.hidden {
  display: none;
}

/* BUTTON */
.load-more {
  display: block;
  margin: 60px auto 0;
  padding: 0;
  border: none;
  background: transparent;
  color: white;
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: 1px;
  cursor: pointer;
  position: relative;
}

.load-more::after{
  content:"";
  position:absolute;
  left:0;
  bottom:-6px;
  width:100%;
  height:1px;
  background:white;
  opacity:0.5;
}

/* RESPONSIVE */
@media (max-width: 1100px) {
  .archive-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 700px) {

  .archive {
    padding: 100px 20px;
  }

  .archive-grid {
    grid-template-columns: 1fr;
  }

  .year-tabs {
    gap: 18px;
  }

  .archive-header {
    align-items: flex-start;
  }
}

  </style>
<body>
  <?php
  include "header.php";
  ?>
   <section class="past-section">
    <div class="text-box">
      <h1>Archive <br> Highlights</h1>
      <div class="line"></div>
      <p style="margin-top:20px; color: rgba(255,255,255,0.85); max-width: 520px;">
       Discover the best NOVA Festival moments captured in light, sound, and energy — presented in a clean, immersive layout.
      </p>
    </div>

    <div class="image-box">
      <img src="images/concert3.jpg" alt="festival">
    </div>
  </section>
  <section class="archive">
  <div class="archive-header">
    <h1>GALLERY</h1>

    <div class="year-tabs">
      <button class="year-btn active" data-year="2025">2025</button>
      <button class="year-btn" data-year="2024">2024</button>
      <button class="year-btn" data-year="2022">2022</button>
      <button class="year-btn" data-year="2019">2019</button>
      <button class="year-btn" data-year="2018">2018</button>
    </div>
  </div>

  <div class="archive-grid" id="galleryGrid">

    <!-- 2025 -->
    <div class="img-card" data-year="2025">
      <img src="images/festival2025-1.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-2.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-3.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-4.jpg">
    </div>
    <div class="img-card" data-year="2025">
      <img src="images/festival2025-5.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-6.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-7.jpg">
    </div>
    
    <div class="img-card" data-year="2025">
      <img src="images/festival2025-8.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-9.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-10.jpg">
    </div>
    
    <div class="img-card" data-year="2025">
      <img src="images/festival2025-11.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-12.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-13.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-14.jpg">
    </div>

    <div class="img-card" data-year="2025">
      <img src="images/festival2025-15.jpg">
    </div>
    <!-- 2024 -->
    <div class="img-card" data-year="2024">
      <img src="images/festival2024-1.jpg">
    </div>

     <div class="img-card" data-year="2024">
      <img src="images/festival2024-2.jpg">
    </div>

     <div class="img-card" data-year="2024">
      <img src="images/festival2024-3.jpg">
    </div>

     <div class="img-card" data-year="2024">
      <img src="images/festival2024-4.jpg">
    </div>
     <div class="img-card" data-year="2024">
      <img src="images/festival2024-5.jpg">
    </div>
     <div class="img-card" data-year="2024">
      <img src="images/festival2024-6.jpg">
    </div>
     <div class="img-card" data-year="2024">
      <img src="images/festival2024-7.jpg">
    </div>
     <div class="img-card" data-year="2024">
      <img src="images/festival2024-8.jpg">
    </div>
     <div class="img-card" data-year="2024">
      <img src="images/festival2024-9.jpg">
    </div>
     <div class="img-card" data-year="2024">
      <img src="images/festival2024-10.jpg">
    </div>

    <!-- 2022 -->
    <div class="img-card" data-year="2022">
      <img src="images/festival2022-1.jpg">
    </div>
    <div class="img-card" data-year="2022">
      <img src="images/festival2022-2.jpg">
    </div>
     <div class="img-card" data-year="2022">
      <img src="images/festival2022-3.jpg">
    </div>
     <div class="img-card" data-year="2022">
      <img src="images/festival2022-4.jpg">
    </div>
     <div class="img-card" data-year="2022">
      <img src="images/festival2022-5.jpg">
    </div>
     <div class="img-card" data-year="2022">
      <img src="images/festival2022-6.jpg">
    </div>
    <div class="img-card" data-year="2022">
      <img src="images/festival2022-7.jpg">
    </div>

    <!-- 2019 -->
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-1.jpg">
    </div>
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-2.jpg">
    </div>
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-3.jpg">
    </div>
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-4.jpg">
    </div>
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-5.jpg">
    </div>
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-6.jpg">
    </div>
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-7.jpg">
    </div>
    <div class="img-card" data-year="2019">
      <img src="images/festival2019-8.jpg">
    </div>

    <!-- 2018 -->
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-1.jpg">
    </div>
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-2.jpg">
    </div>
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-3.jpg">
    </div>
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-4.jpg">
    </div>
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-5.jpg">
    </div>
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-6.jpg">
    </div>
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-7.jpg">
    </div>
    <div class="img-card" data-year="2018">
      <img src="images/festival2018-8.jpg">
    </div>

  </div>
  <button id="loadMoreBtn" class="load-more">Load More</button>
</section>
<?php 
include "footer.php";
?>

<script>
 const buttons = document.querySelectorAll(".year-btn");
const images = document.querySelectorAll(".img-card");
const loadBtn = document.getElementById("loadMoreBtn");

let currentYear = "2025";
let visibleCount = 6;

/* FILTER FUNCTION */
function filterGallery() {

  const filtered = [...images].filter(img =>
    img.dataset.year === currentYear
  );

  /* hide krejt */
  images.forEach(img => {
    img.classList.add("hidden");
  });

  /* show vetem visibleCount */
  filtered.forEach((img, index) => {

    if(index < visibleCount){
      img.classList.remove("hidden");
    }

  });

  /* BUTTON */
  if(visibleCount >= filtered.length){
    loadBtn.style.display = "none";
  } else {
    loadBtn.style.display = "block";
  }
}

/* YEAR BUTTONS */
buttons.forEach(btn => {

  btn.addEventListener("click", () => {

    buttons.forEach(b => b.classList.remove("active"));
    btn.classList.add("active");

    currentYear = btn.dataset.year;

    visibleCount = 6;

    filterGallery();

  });

});

/* LOAD MORE */
loadBtn.addEventListener("click", () => {

  visibleCount += 3;

  filterGallery();

});

/* INIT */
filterGallery();
  </script>
</body>
</html>
