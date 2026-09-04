/* =========================================================
   StudentHub Practical 4
   JavaScript DOM Manipulation, Event Handling & UI Interactivity
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
  const $ = (selector, parent = document) => parent.querySelector(selector);
  const $$ = (selector, parent = document) => [...parent.querySelectorAll(selector)];

  /* ---------- 1. Theme switcher + localStorage ---------- */
  const savedTheme = localStorage.getItem("studentHubTheme");
  if (savedTheme === "dark") document.body.classList.add("dark-theme");

  const themeButton = document.createElement("button");
  themeButton.type = "button";
  themeButton.className = "theme-toggle";
  themeButton.setAttribute("aria-label", "Switch theme");
  themeButton.setAttribute("title", "Switch light/dark theme");
  themeButton.textContent = document.body.classList.contains("dark-theme") ? "☀️ Light" : "🌙 Dark";

  themeButton.addEventListener("click", () => {
    document.body.classList.toggle("dark-theme");
    const dark = document.body.classList.contains("dark-theme");
    localStorage.setItem("studentHubTheme", dark ? "dark" : "light");
    themeButton.textContent = dark ? "☀️ Light" : "🌙 Dark";
    showToast(`${dark ? "Dark" : "Light"} theme enabled`);
  });

  /* ---------- 2. Mobile hamburger menu ---------- */
  const sidebar = $(".sidebar");
  if (sidebar) {
    const menuButton = document.createElement("button");
    menuButton.type = "button";
    menuButton.className = "menu-toggle";
    menuButton.setAttribute("aria-label", "Open navigation menu");
    menuButton.setAttribute("aria-expanded", "false");
    menuButton.textContent = "☰";

    document.body.prepend(menuButton);

    menuButton.addEventListener("click", () => {
      const open = sidebar.classList.toggle("menu-open");
      menuButton.setAttribute("aria-expanded", String(open));
      menuButton.setAttribute("aria-label", open ? "Close navigation menu" : "Open navigation menu");
      menuButton.textContent = open ? "✕" : "☰";
    });

    $$("a", sidebar).forEach(link => {
      link.addEventListener("click", () => {
        sidebar.classList.remove("menu-open");
        menuButton.setAttribute("aria-expanded", "false");
        menuButton.textContent = "☰";
      });
    });
  }

  /* ---------- 3. Shared top toolbar ---------- */
  const content = $(".content") || $("main");
  if (content) {
    const toolbar = document.createElement("div");
    toolbar.className = "js-toolbar";
    toolbar.appendChild(themeButton);
    content.prepend(toolbar);
  } else {
    document.body.prepend(themeButton);
  }

  /* ---------- 4. Notification banner ---------- */
  if (!sessionStorage.getItem("studentHubNoticeClosed")) {
    const banner = document.createElement("div");
    banner.className = "notification-banner";
    banner.setAttribute("role", "status");
    banner.innerHTML = `
      <span>🔔 <strong>StudentHub update:</strong> Mid-semester examinations begin September 8, 2026.</span>
      <button type="button" class="banner-close" aria-label="Close notification">×</button>
    `;
    document.body.prepend(banner);

    $(".banner-close", banner).addEventListener("click", () => {
      banner.remove();
      sessionStorage.setItem("studentHubNoticeClosed", "true");
    });
  }

  /* ---------- 5. Student information from localStorage ---------- */
  const name = localStorage.getItem("studentName");
  const email = localStorage.getItem("studentEmail");

  if (name) {
    ["studentName", "sidebarName", "profileName"].forEach(id => {
      const element = document.getElementById(id);
      if (element) element.textContent = name;
    });
    const avatar = $("#avatar");
    if (avatar) avatar.textContent = name.charAt(0).toUpperCase();
  }

  if (email) {
    ["sidebarEmail", "profileEmail"].forEach(id => {
      const element = document.getElementById(id);
      if (element) element.textContent = email;
    });
  }

  /* ---------- 6. FAQ accordion (DOM manipulation) ---------- */
  $$(".content section article").forEach(article => {
    const heading = $("h3", article);
    const answer = $("p", article);
    if (!heading || !answer) return;

    article.classList.add("faq-item");
    const button = document.createElement("button");
    button.type = "button";
    button.className = "faq-question";
    button.setAttribute("aria-expanded", "false");
    button.innerHTML = `${heading.textContent.trim()} <span aria-hidden="true">+</span>`;

    heading.replaceWith(button);
    answer.classList.add("faq-answer");
    answer.hidden = true;

    button.addEventListener("click", () => {
      const expanded = button.getAttribute("aria-expanded") === "true";
      button.setAttribute("aria-expanded", String(!expanded));
      answer.hidden = expanded;
      button.querySelector("span").textContent = expanded ? "+" : "−";
      article.classList.toggle("faq-open", !expanded);
    });
  });

  /* ---------- 7. Event/resource filters ---------- */
  $$(".filter").forEach(button => {
    button.type = "button";
    button.addEventListener("click", () => {
      const filter = button.textContent.trim().toLowerCase();
      $$(".filter").forEach(item => {
        item.classList.toggle("active", item === button);
      });

      $$(".event").forEach(event => {
        const type = $(".event-type", event)?.textContent.trim().toLowerCase();
        event.hidden = filter !== "all" && type !== filter;
      });

      $$(".resource-card").forEach(card => {
        const type = $(".resource-type", card)?.textContent.trim().toLowerCase() || "";
        card.hidden = filter !== "all" && !type.includes(filter);
      });
    });
  });

  /* ---------- 8. Dashboard content slider ---------- */
  const dashboard = document.querySelector(".cards");
  if (dashboard && !$(".studenthub-slider")) {
    const slider = document.createElement("section");
    slider.className = "studenthub-slider";
    slider.setAttribute("aria-label", "StudentHub highlights");
    slider.innerHTML = `
      <div class="slider-head">
        <div>
          <span class="page-label">HIGHLIGHTS</span>
          <h2>What's happening</h2>
        </div>
        <div class="slider-controls">
          <button type="button" class="slider-prev" aria-label="Previous slide">←</button>
          <button type="button" class="slider-next" aria-label="Next slide">→</button>
        </div>
      </div>
      <div class="slides" aria-live="polite">
        <article class="slide active-slide">
          <span>📚 Academic</span>
          <h3>Mid-semester examination timetable released</h3>
          <p>Check the latest exam schedule and plan your preparation early.</p>
        </article>
        <article class="slide">
          <span>🏆 Campus</span>
          <h3>Inter-department Football League</h3>
          <p>Opening matches take place on August 23 at the Main Ground.</p>
        </article>
        <article class="slide">
          <span>💻 Workshop</span>
          <h3>Building with React Router</h3>
          <p>Join the hands-on navigation workshop in Lab-3, Block B.</p>
        </article>
      </div>
      <div class="slider-dots" role="tablist" aria-label="Choose highlight"></div>
    `;
    dashboard.after(slider);

    const slides = $$(".slide", slider);
    const dots = $(".slider-dots", slider);
    let current = 0;

    slides.forEach((_, index) => {
      const dot = document.createElement("button");
      dot.type = "button";
      dot.className = index === 0 ? "slider-dot active" : "slider-dot";
      dot.setAttribute("role", "tab");
      dot.setAttribute("aria-label", `Show slide ${index + 1}`);
      dot.addEventListener("click", () => goToSlide(index));
      dots.appendChild(dot);
    });

    function goToSlide(index) {
      current = (index + slides.length) % slides.length;
      slides.forEach((slide, i) => slide.classList.toggle("active-slide", i === current));
      $$(".slider-dot", slider).forEach((dot, i) => dot.classList.toggle("active", i === current));
    }

    $(".slider-prev", slider).addEventListener("click", () => goToSlide(current - 1));
    $(".slider-next", slider).addEventListener("click", () => goToSlide(current + 1));
  }

  /* ---------- 9. Modal popup ---------- */
  const modal = document.createElement("div");
  modal.className = "modal-overlay";
  modal.hidden = true;
  modal.innerHTML = `
    <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <button type="button" class="modal-close" aria-label="Close dialog">×</button>
      <p class="page-label">STUDENTHUB</p>
      <h2 id="modal-title">Details</h2>
      <p id="modal-message"></p>
      <button type="button" class="modal-action">Close</button>
    </div>
  `;
  document.body.appendChild(modal);

  function openModal(title, message) {
    $("#modal-title", modal).textContent = title;
    $("#modal-message", modal).textContent = message;
    modal.hidden = false;
    $(".modal-close", modal).focus();
  }

  function closeModal() {
    modal.hidden = true;
  }

  $(".modal-close", modal).addEventListener("click", closeModal);
  $(".modal-action", modal).addEventListener("click", closeModal);
  modal.addEventListener("click", event => {
    if (event.target === modal) closeModal();
  });
  document.addEventListener("keydown", event => {
    if (event.key === "Escape" && !modal.hidden) closeModal();
  });

  /* Add modal behaviour to event cards without changing their original HTML. */
  $$(".event").forEach(eventCard => {
    if ($(".event-more", eventCard)) return;
    const title = $("h2", eventCard)?.textContent.trim() || "Event";
    const details = $("p", eventCard)?.textContent.trim() || "See the Events page for more information.";
    const button = document.createElement("button");
    button.type = "button";
    button.className = "event-more";
    button.textContent = "View details";
    button.addEventListener("click", () => openModal(title, details));
    $(".event-details", eventCard)?.appendChild(button);
  });

  /* ---------- 10. Profile settings + localStorage ---------- */
  ["enrollment", "department", "semester", "phone"].forEach(id => {
    const field = document.getElementById(id);
    if (field) field.value = localStorage.getItem(id) || "";
  });

  ["announcementToggle", "eventToggle"].forEach(id => {
    const toggle = document.getElementById(id);
    if (!toggle) return;
    const key = id === "announcementToggle" ? "announcementNotifications" : "eventNotifications";
    const saved = localStorage.getItem(key);
    if (saved !== null) toggle.checked = saved === "true";
  });

  const saveBtn = $("#saveBtn");
  if (saveBtn) {
    saveBtn.addEventListener("click", () => {
      ["enrollment", "department", "semester", "phone"].forEach(id => {
        const field = document.getElementById(id);
        if (field) localStorage.setItem(id, field.value);
      });
      const announcementToggle = $("#announcementToggle");
      const eventToggle = $("#eventToggle");
      if (announcementToggle) localStorage.setItem("announcementNotifications", announcementToggle.checked);
      if (eventToggle) localStorage.setItem("eventNotifications", eventToggle.checked);
      showToast("Profile changes saved successfully.");
    });
  }

  /* ---------- 11. Registration form ---------- */
  const registerForm = $('form[action="login.html"]');
  if (registerForm && $("#full-name") && $("#register-email")) {
    registerForm.addEventListener("submit", event => {
      const password = $("#register-password")?.value || "";
      const confirm = $("#confirm-password")?.value || "";

      if (password !== confirm) {
        event.preventDefault();
        showToast("Passwords do not match.");
        $("#confirm-password")?.focus();
        return;
      }

      localStorage.setItem("studentName", $("#full-name").value.trim());
      localStorage.setItem("studentEmail", $("#register-email").value.trim());
    });
  }

  /* ---------- 12. Login feedback ---------- */
  const loginForm = $('form[action="dashboard.html"]');
  if (loginForm) {
    loginForm.addEventListener("submit", () => {
      showToast("Login successful. Opening dashboard…");
    });
  }

  /* ---------- 13. Support/feedback forms ---------- */
  $$("form").forEach(form => {
    if (form === registerForm || form === loginForm) return;

    form.addEventListener("submit", event => {
      event.preventDefault();
      const heading = $("h1")?.textContent.trim() || "Request";
      showToast(`${heading} submitted successfully.`);
      form.reset();
    });
  });

  /* ---------- 14. Toast notification ---------- */
  function showToast(message) {
    const oldToast = $(".js-toast");
    if (oldToast) oldToast.remove();

    const toast = document.createElement("div");
    toast.className = "js-toast";
    toast.setAttribute("role", "status");
    toast.textContent = message;
    document.body.appendChild(toast);

    requestAnimationFrame(() => toast.classList.add("show"));
    setTimeout(() => {
      toast.classList.remove("show");
      setTimeout(() => toast.remove(), 250);
    }, 2800);
  }
});
