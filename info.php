<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>NOVA Festival — Info</title>
  <link rel="stylesheet" href="style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap" rel="stylesheet" />
</head>
<style>
  /* ========== SPONSORS SECTION ========== */

.sponsors-section {
  padding: 110px 32px;
  border-top: 1px solid rgba(255,255,255,0.08);
  border-bottom: 1px solid rgba(255,255,255,0.08);
}

.sponsors-top {
  max-width: 1300px;
  margin: 0 auto 90px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 80px;
}

.sponsors-heading {
  flex: 1;
}

.sponsors-mini {
  display: inline-block;
  font-size: 0.9rem;
  letter-spacing: 0.22em;
  color: #f0b128;
  margin-bottom: 22px;
  font-weight: 600;
}

.sponsors-heading h2 {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(4rem, 8vw, 7rem);
  line-height: 0.95;
  font-weight: 400;
  color: #fff;
  margin: 0;
}

.sponsors-text {
  flex: 1;
  display: flex;
  justify-content: flex-end;
  padding-top: 30px;
}

.sponsors-text p {
  max-width: 520px;
  font-size: 1.5rem;
  line-height: 1.5;
  color: rgba(255,255,255,0.92);
  font-family: monospace;
}

.sponsors-logos {
  max-width: 1300px;
  margin: 0 auto;

  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 40px;
  align-items: center;
}

.sponsor-item {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 90px;

  opacity: 0.75;
  transition: all 0.3s ease;
}

.sponsor-item:hover {
  opacity: 1;
  transform: translateY(-4px);
}

.sponsor-item img {
  max-width: 160px;
  width: 100%;
  height: auto;

  object-fit: contain;
  filter: brightness(0) invert(1);
}

/* ===== RESPONSIVE ===== */

@media (max-width: 1100px) {

  .sponsors-top {
    flex-direction: column;
    gap: 40px;
    margin-bottom: 70px;
  }

  .sponsors-text {
    padding-top: 0;
    justify-content: flex-start;
  }

  .sponsors-logos {
    grid-template-columns: repeat(3, 1fr);
    gap: 35px;
  }
}

@media (max-width: 700px) {

  .sponsors-section {
    padding: 90px 22px;
  }

  .sponsors-heading h2 {
    font-size: 3.7rem;
  }

  .sponsors-text p {
    font-size: 1.1rem;
    line-height: 1.7;
  }

  .sponsors-logos {
    grid-template-columns: repeat(2, 1fr);
    gap: 28px;
  }

  .sponsor-item img {
    max-width: 120px;
  }
}
.info-faq-section {
  padding: 120px 24px;
  display: flex;
  justify-content: center;
}
.info-faq-block {
  width: 100%;
  max-width: 880px;
  /* nese du me u bo si e futme ne box
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);

  border-radius: 32px;

  padding: 60px;

  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);

  box-shadow:
    0 10px 40px rgba(0,0,0,0.25),
    inset 0 1px 0 rgba(255,255,255,0.04);
    */
}
.info-block-header {
   text-align: center;
  margin-bottom: 45px;
}

.info-block-header .mini-tag {
  display: inline-flex;
  align-items: center;
  justify-content: center;

  padding: 7px 16px;

  border-radius: 50px;

  background: rgba(248,166,1,0.12);
  border: 1px solid rgba(248,166,1,0.25);

  color: var(--accent);

  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.22em;

  text-transform: uppercase;

  margin-bottom: 20px;
}

.info-block-header h2 {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(3rem, 6vw, 5rem);
  line-height: 0.95;
  letter-spacing: 0.03em;

  color: #fff;

  margin-bottom: 18px;
}

.info-block-header p {
  color: rgba(255,255,255,0.62);

  font-size: 1rem;
  line-height: 1.7;

  max-width: 500px;
  margin: 0 auto;
}

.faq-toolbar {
  display: flex;
  justify-content: center;
  gap: 14px;

  margin-bottom: 32px;
}

.faq-expand-btn,
.faq-collapse-btn {
  height: 44px;

  padding: 0 20px;

  border-radius: 999px;

  border: 1px solid rgba(255,255,255,0.08);

  background: rgba(255,255,255,0.04);

  color: #fff;

  font-size: 0.85rem;
  font-weight: 600;

  display: inline-flex;
  align-items: center;
  gap: 8px;

  cursor: pointer;

  transition: all 0.25s ease;

  font-family: inherit;
}
.faq-expand-btn:hover,
.faq-collapse-btn:hover {
  background: rgba(248,166,1,0.12);
  border-color: rgba(248,166,1,0.28);

  transform: translateY(-2px);

  color: var(--accent);
}

.faq-expand-btn span,
.faq-collapse-btn span {
  font-size: 1.1rem;
  line-height: 1;
}

.faq-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.faq-item {
  border-radius: 22px;

  background: rgba(255,255,255,0.04);

  border: 1px solid rgba(255,255,255,0.06);

  overflow: hidden;

  transition:
    border-color 0.3s ease,
    background 0.3s ease,
    transform 0.3s ease;
}

.faq-item:hover {
  transform: translateY(-2px);

  border-color: rgba(255,255,255,0.12);
}

.faq-item.open {
  background: rgba(255,255,255,0.06);

  border-color: rgba(248,166,1,0.35);
}
.faq-trigger {
  width: 100%;

  background: transparent;
  border: none;

  padding: 24px 26px;

  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;

  cursor: pointer;

  text-align: left;

  font-family: inherit;

  color: #fff;

  font-size: 1rem;
  font-weight: 700;
  letter-spacing: 0.02em;

  transition: color 0.25s ease;
}

.faq-trigger:hover {
  color: var(--accent);
}

.faq-icon {
  width: 34px;
  height: 34px;

  border-radius: 50%;

  background: rgba(248,166,1,0.12);

  border: 1px solid rgba(248,166,1,0.22);

  display: flex;
  align-items: center;
  justify-content: center;

  color: var(--accent);

  font-size: 1.2rem;
  font-weight: 300;

  flex-shrink: 0;

  transition: all 0.25s ease;
}
.faq-item.open .faq-icon {
  transform: rotate(180deg);
}

.faq-panel {
  max-height: 0;
  overflow: hidden;

  transition:
    max-height 0.4s ease,
    padding 0.4s ease;

  padding: 0 28px;
}

.faq-item.open .faq-panel {
  max-height: 500px;
  padding: 0 28px 24px;
}

.faq-panel ul {
  margin: 0;
  padding-left: 20px;
}

.faq-panel li {
  color: rgba(255,255,255,0.72);

  font-size: 0.94rem;
  line-height: 1.8;

  margin-bottom: 10px;
}

.faq-panel li::marker {
  color: var(--accent);
}
/* ========== CONTACT SECTION ========== */

.contact-section {
  width: 100%;
  max-width: 1180px;

  margin: 0 auto;

  padding: 120px 24px;
}

.contact-header {
  margin-bottom: 55px;
}

.contact-mini {
  display: inline-block;

  color: #f0b128;

  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.22em;

  margin-bottom: 18px;
}

.contact-header h2 {
  font-family: 'Bebas Neue', sans-serif;

  font-size: clamp(4rem, 9vw, 7rem);
  line-height: 0.92;

  color: #fff;

  margin: 0;
}

/* FORM */

.contact-form {
  width: 100%;
  max-width: 1100px;

  margin: 0 auto;
}

/* GRID */

.contact-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 26px;

  margin-bottom: 26px;
}

/* INPUT GROUP */

.input-group {
  position: relative;
}

.input-group input,
.input-group textarea {
  width: 100%;

  background: rgba(255,255,255,0.02);

  border: 1px solid rgba(255,255,255,0.45);

  color: #fff;

  font-size: 1rem;
  font-family: inherit;

  padding: 30px 22px 18px;

  outline: none;

  transition:
    border-color 0.25s ease,
    background 0.25s ease,
    box-shadow 0.25s ease;
}

/* INPUT HEIGHT */

.input-group input {
  height: 78px;
}

/* TEXTAREA */

.textarea-group textarea {
  min-height: 280px;
  resize: vertical;
}

/* LABEL */

.input-group label {
  position: absolute;

  top: 28px;
  left: 22px;

  color: rgba(255,255,255,0.72);

  font-size: 0.82rem;
  font-weight: 600;
  letter-spacing: 0.18em;

  pointer-events: none;

  transition: all 0.25s ease;
}

/* FOCUS */

.input-group input:focus,
.input-group textarea:focus {
  border-color: #f0b128;

  background: rgba(255,255,255,0.04);

  box-shadow: 0 0 0 3px rgba(240,177,40,0.08);
}

/* FLOAT LABEL */

.input-group input:focus + label,
.input-group input:valid + label,
.input-group textarea:focus + label,
.input-group textarea:valid + label {

  top: 12px;

  font-size: 0.68rem;

  letter-spacing: 0.16em;

  color: #f0b128;
}

/* BUTTON */

.contact-btn {
  margin: 34px auto 0;

  display: flex;
  align-items: center;
  justify-content: center;

  width: 340px;
  height: 64px;

  border: none;

  background: #f0b128;

  color: #111;

  font-size: 1rem;
  font-weight: 700;
  letter-spacing: 0.16em;

  cursor: pointer;

  transition:
    transform 0.25s ease,
    background 0.25s ease,
    box-shadow 0.25s ease;
}

.contact-btn:hover {
  transform: translateY(-3px);

  background: #ffc43a;

  box-shadow: 0 12px 30px rgba(240,177,40,0.28);
}

/* MOBILE */

@media (max-width: 900px) {

  .contact-grid {
    grid-template-columns: 1fr;
  }

  .contact-section {
    padding: 90px 20px;
  }

  .contact-header h2 {
    font-size: 4rem;
  }

  .contact-btn {
    width: 100%;
  }
}

</style>
<body class="info-page">
  <?php include "header.php"; ?>

 <section class="past-section">
    <div class="text-box">
      <h1>2026 <br> festival info</h1>
      <div class="line"></div>
      <p style="margin-top:20px; color: rgba(255,255,255,0.85); max-width: 520px;">
       Everything you need to plan your NOVA weekend — schedule, rules, team, and on-site services.
      </p>
    </div>

    <div class="image-box">
      <img src="images/concert3.jpg" alt="festival">
    </div>
  </section>

  <section class="organisers-showcase">
    <h2 class="organisers-showcase-title">Meet the organisers</h2>

    <div class="organisers-row">
      <article class="organiser-profile">
        <div class="organiser-badge organiser-badge--gold">
          <div class="organiser-photo" data-initials="AW">
            <img src="images/org.webp" alt="Ashton Wesley" onerror="this.parentElement.classList.add('no-photo')" />
          </div>
        </div>
        <h3>Ashton Wesley</h3>
        <p>Canada-born Leeds-adopted lad.<br>Founder of Leeds Digital Drinks.</p>
        <div class="organiser-socials">
          <a href="#" aria-label="Ashton Wesley on Twitter">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="#" aria-label="Ashton Wesley on LinkedIn">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 4.126 0 2.062 2.062 0 0 1-2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </article>
      <article class="organiser-profile">
        <div class="organiser-badge organiser-badge--pink">
          <div class="organiser-photo" data-initials="IE">
            <img src="images/org1.jpg" alt="Krenar Fazliu" onerror="this.parentElement.classList.add('no-photo')" />
          </div>
        </div>
        <h3>Krenar Fazliu</h3>
        <p>Finance & Sponsorship Manager.<br>Handles partnerships, budgeting, and festival funding.</p>
        <div class="organiser-socials">
          <a href="#" aria-label="Krenar Fazliu on Twitter">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="#" aria-label="Krenar Fazliu on LinkedIn">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 4.126 0 2.062 2.062 0 0 1-2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </article>

      <article class="organiser-profile">
        <div class="organiser-badge organiser-badge--orange">
          <div class="organiser-photo" data-initials="JF">
            <img src="images/organiser3.jpg" alt="Joshua Fry" onerror="this.parentElement.classList.add('no-photo')" />
          </div>
        </div>
        <h3>Joshua Fry</h3>
        <p>Music Nerd, Avid Webflower,<br>Head of Design for Endlesss FM.</p>
        <div class="organiser-socials">
          <a href="#" aria-label="Joshua Fry on Twitter">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="#" aria-label="Joshua Fry on LinkedIn">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 4.126 0 2.062 2.062 0 0 1-2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </article>

      <article class="organiser-profile">
        <div class="organiser-badge organiser-badge--rose">
          <div class="organiser-photo" data-initials="BR">
            <img src="images/organiser4.jpg" alt="Arta Krasniqi" onerror="this.parentElement.classList.add('no-photo')" />
          </div>
        </div>
        <h3>Arta Krasniqi</h3>
        <p>Marketing & Media Manager.<br>Handles campaigns, branding, and social promotion.</p>
        <div class="organiser-socials">
          <a href="#" aria-label="Arta Krasniqi on Twitter">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="#" aria-label="Arta Krasniqi on LinkedIn">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 4.126 0 2.062 2.062 0 0 1-2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </article>

      <article class="organiser-profile">
        <div class="organiser-badge organiser-badge--orange">
          <div class="organiser-photo" data-initials="SH">
            <img src="images/organiser5.jpg" alt="Diona Selimi" onerror="this.parentElement.classList.add('no-photo')" />
          </div>
        </div>
        <h3>Diona Selimi</h3>
        <p>Artist Relations Manager.<br>Works directly with performers and VIP guests.</p>
        <div class="organiser-socials">
          <a href="#" aria-label="Diona Selimi on Twitter">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          </a>
          <a href="#" aria-label="Diona Selimi on LinkedIn">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 4.126 0 2.062 2.062 0 0 1-2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
          </a>
        </div>
      </article>
    </div>
  </section>
  <section class="sponsors-section">
  <div class="sponsors-top">
    
    <div class="sponsors-heading">
      <span class="sponsors-mini">MEET OUR PARTNERS</span>
      <h2>SPONSORS <br> AND PARTNERS</h2>
    </div>

    <div class="sponsors-text">
      <p>
        A diverse group of sponsors and partners passionately supports the festival,
        adding flavor to our NOVA experience.
      </p>
    </div>

  </div>

  <div class="sponsors-logos">

    <div class="sponsor-item">
      <img src="images/sponsor1.png" alt="Sponsor">
    </div>

    <div class="sponsor-item">
      <img src="images/sponsor2.png" alt="Sponsor">
    </div>

    <div class="sponsor-item">
      <img src="images/sponsor3.png" alt="Sponsor">
    </div>

    <div class="sponsor-item">
      <img src="images/sponsor4.png" alt="Sponsor">
    </div>

    <div class="sponsor-item">
      <img src="images/sponsor5.png" alt="Sponsor">
    </div>

    <div class="sponsor-item">
      <img src="images/sponsor6.png" alt="Sponsor">
    </div>

  </div>
</section>
  <section class="info-faq-section">
    <div class="info-faq-block">
      <div class="info-block-header">
        <span class="mini-tag">GUIDELINES</span>
        <h2>Festival Rules & Tips</h2>
        <p>Tap a section to read the full details</p>
      </div>

      <div class="faq-toolbar">
        <button type="button" class="faq-expand-btn" id="expandAll">
          Expand all
          <span aria-hidden="true">+</span>
        </button>
        <button type="button" class="faq-collapse-btn" id="collapseAll">
          Collapse all
          <span aria-hidden="true">−</span>
        </button>
      </div>

      <div class="faq-list">
        <div class="faq-item open">
          <button type="button" class="faq-trigger" aria-expanded="true">
            <span>General Info</span>
            <span class="faq-icon" aria-hidden="true">−</span>
          </button>
          <div class="faq-panel">
            <ul>
              <li>All ages welcome to the festival grounds.</li>
              <li>Kids 5 & under free (General Admission only).</li>
              <li>Both weekends feature the same lineup, art, food and activities.</li>
              <li>Rain or shine — festival proceeds regardless of weather.</li>
              <li>All patrons subject to search at entry.</li>
              <li>Lineup & set times are subject to change without notice.</li>
              <li>Merchandise, food and beverage vendors accept card/mobile pay only.</li>
              <li>Onsite camping is 18+ unless accompanied by a parent or legal guardian.</li>
            </ul>
          </div>
        </div>

        <div class="faq-item">
          <button type="button" class="faq-trigger" aria-expanded="false">
            <span>What to Bring</span>
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-panel">
            <ul>
              <li>Valid ID (required for entry)</li>
              <li>Comfortable shoes for walking around the festival grounds</li>
              <li>Sunscreen and hat for daytime activities</li>
              <li>Reusable water bottle (can be refilled for free)</li>
              <li>Light jacket or sweater for evening events</li>
              <li>Phone charger or portable battery pack</li>
              <li>Cash is not accepted — bring cards or mobile payment</li>
            </ul>
          </div>
        </div>

        <div class="faq-item">
          <button type="button" class="faq-trigger" aria-expanded="false">
            <span>Prohibited Items</span>
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-panel">
            <ul>
              <li>Outside food and beverages (except sealed water bottles)</li>
              <li>Glass containers and bottles</li>
              <li>Weapons of any kind</li>
              <li>Illegal drugs and controlled substances</li>
              <li>Professional cameras and video recording equipment</li>
              <li>Drones and flying devices</li>
              <li>Audio recording devices</li>
              <li>Items that obstruct views of the stage</li>
            </ul>
          </div>
        </div>

        <div class="faq-item">
          <button type="button" class="faq-trigger" aria-expanded="false">
            <span>Safety & Health</span>
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-panel">
            <ul>
              <li>Medical staff available on-site 24/7</li>
              <li>First aid stations located throughout the festival</li>
              <li>Emergency services easily accessible</li>
              <li>Designated quiet areas for rest and recovery</li>
              <li>Water stations available every 50 meters</li>
              <li>Lost & Found located at main information booth</li>
              <li>All attendees must follow health and safety guidelines</li>
            </ul>
          </div>
        </div>

        <div class="faq-item">
          <button type="button" class="faq-trigger" aria-expanded="false">
            <span>Parking & Transportation</span>
            <span class="faq-icon" aria-hidden="true">+</span>
          </button>
          <div class="faq-panel">
            <ul>
              <li>Multiple parking areas available (parking fee may apply)</li>
              <li>Shuttle service from parking to festival entrance</li>
              <li>Public transportation options nearby</li>
              <li>Bike parking available free of charge</li>
              <li>Accessible parking for those with disabilities</li>
              <li>EV charging stations on-site</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
</section>
    <section class="contact-section">

  <div class="contact-header">
    <span class="contact-mini">WE’D LOVE TO HEAR FROM YOU.</span>
    <h2>CONTACT US</h2>
  </div>

  <form class="contact-form" action="" method="POST">

    <div class="contact-grid">

      <div class="input-group">
        <input type="text" name="full_name" required>
        <label>FULL NAME</label>
      </div>

      <div class="input-group">
        <input type="email" name="email" required>
        <label>EMAIL</label>
      </div>

      <div class="input-group">
        <input type="text" name="company">
        <label>COMPANY</label>
      </div>

      <div class="input-group">
        <input type="text" name="subject" required>
        <label>SUBJECT</label>
      </div>

    </div>

    <div class="input-group textarea-group">
      <textarea name="message" required></textarea>
      <label>MESSAGE . .</label>
    </div>

    <button type="submit" name="contact_submit" class="contact-btn">
      SUBMIT
    </button>

  </form>

</section>
  <?php include "footer.php"; ?>

  <script>
    const header = document.querySelector('.site-header');
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 30);
    });

    const faqItems = document.querySelectorAll('.faq-item');

    function setFaqState(item, open) {
      item.classList.toggle('open', open);
      const trigger = item.querySelector('.faq-trigger');
      const icon = item.querySelector('.faq-icon');
      trigger.setAttribute('aria-expanded', open);
      icon.textContent = open ? '−' : '+';
    }

    document.querySelectorAll('.faq-trigger').forEach(trigger => {
      trigger.addEventListener('click', () => {
        const item = trigger.closest('.faq-item');
        const isOpen = item.classList.contains('open');
        setFaqState(item, !isOpen);
      });
    });

    document.getElementById('expandAll').addEventListener('click', () => {
      faqItems.forEach(item => setFaqState(item, true));
    });

    document.getElementById('collapseAll').addEventListener('click', () => {
      faqItems.forEach(item => setFaqState(item, false));
    });
  </script>
</body>
</html>
