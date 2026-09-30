/* =========================================================
   StudentHub Practical 6
   External JSON + Fetch API + Search + Filter + Sort + Pagination
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {
    const page = document.body.dataset.practical6;
    if (!page) return;

    const config = {
        events: {
            file: "data/events.json",
            title: "Campus Events",
            empty: "No events match your search or filter.",
            categories: "All"
        },
        students: {
            file: "data/students.json",
            title: "Student Directory",
            empty: "No student profiles match your search or filter.",
            categories: "All"
        },
        notices: {
            file: "data/notices.json",
            title: "Academic Notices",
            empty: "No notices match your search or filter.",
            categories: "All"
        },
        faqs: {
            file: "data/faqs.json",
            title: "Frequently Asked Questions",
            empty: "No FAQs match your search or filter.",
            categories: "All"
        }
    };

    const current = config[page];
    if (!current) return;

    const searchInput = document.querySelector("#jsonSearch");
    const filterSelect = document.querySelector("#jsonFilter");
    const sortSelect = document.querySelector("#jsonSort");
    const results = document.querySelector("#jsonResults");
    const status = document.querySelector("#jsonStatus");
    const pagination = document.querySelector("#jsonPagination");
    const prevButton = document.querySelector("#jsonPrev");
    const nextButton = document.querySelector("#jsonNext");
    const pageNumber = document.querySelector("#jsonPageNumber");

    let records = [];
    let filteredRecords = [];
    let currentPage = 1;
    const pageSize = 6;

    const escapeHTML = value =>
        String(value ?? "").replace(/[&<>"']/g, char => ({
            "&": "&amp;", "<": "&lt;", ">": "&gt;",
            '"': "&quot;", "'": "&#039;"
        }[char]));

    function showLoading() {
        results.innerHTML = `<div class="json-message">Loading ${escapeHTML(current.title.toLowerCase())}…</div>`;
        status.textContent = "Fetching data from external JSON file…";
        pagination.hidden = true;
    }

    function showError(message) {
        results.innerHTML = `
            <div class="json-message json-error" role="alert">
                <strong>Unable to load data.</strong>
                <p>${escapeHTML(message)}</p>
                <small>Tip: run StudentHub using VS Code Live Server or another local HTTP server.</small>
            </div>`;
        status.textContent = "Data loading failed.";
        pagination.hidden = true;
    }

    function formatDate(dateString) {
        if (!dateString) return "";
        const date = new Date(dateString + "T00:00:00");
        return Number.isNaN(date.getTime())
            ? escapeHTML(dateString)
            : date.toLocaleDateString("en-IN", {
                day: "2-digit", month: "short", year: "numeric"
            });
    }

    function eventCard(item) {
        return `
        <article class="json-card event-json-card">
            <div class="json-card-meta">
                <span class="json-badge">${escapeHTML(item.category)}</span>
                <span>${formatDate(item.date)} · ${escapeHTML(item.time)}</span>
            </div>
            <h2>${escapeHTML(item.title)}</h2>
            <p>${escapeHTML(item.description)}</p>
            <span class="json-location">📍 ${escapeHTML(item.location)}</span>
        </article>`;
    }

    function studentCard(item) {
        return `
        <article class="json-card">
            <div class="json-avatar">${escapeHTML(item.name.charAt(0))}</div>
            <div class="json-card-meta">
                <span class="json-badge">${escapeHTML(item.division)}</span>
                <span>Semester ${escapeHTML(item.semester)}</span>
            </div>
            <h2>${escapeHTML(item.name)}</h2>
            <p><strong>${escapeHTML(item.rollNumber)}</strong> · ${escapeHTML(item.program)}</p>
            <p class="json-muted">${escapeHTML(item.email)}</p>
            <div class="json-tags">${item.skills.map(skill => `<span>${escapeHTML(skill)}</span>`).join("")}</div>
        </article>`;
    }

    function noticeCard(item) {
        return `
        <article class="json-card">
            <div class="json-card-meta">
                <span class="json-badge">${escapeHTML(item.category)}</span>
                <span class="${item.priority === "High" ? "priority-high" : ""}">${escapeHTML(item.priority)}</span>
            </div>
            <h2>${escapeHTML(item.title)}</h2>
            <p>${escapeHTML(item.description)}</p>
            <span class="json-location">${formatDate(item.date)}</span>
        </article>`;
    }

    function faqCard(item) {
        return `
        <article class="json-card faq-json-card">
            <div class="json-card-meta">
                <span class="json-badge">${escapeHTML(item.category)}</span>
            </div>
            <h2>${escapeHTML(item.question)}</h2>
            <p>${escapeHTML(item.answer)}</p>
        </article>`;
    }

    function renderCard(item) {
        if (page === "events") return eventCard(item);
        if (page === "students") return studentCard(item);
        if (page === "notices") return noticeCard(item);
        return faqCard(item);
    }

    function getCategories() {
        return [...new Set(records.map(item => item.category))].sort();
    }

    function buildFilters() {
        filterSelect.innerHTML = `<option value="all">All categories</option>` +
            getCategories().map(category =>
                `<option value="${escapeHTML(category)}">${escapeHTML(category)}</option>`
            ).join("");
    }

    function applyView() {
        const query = searchInput.value.trim().toLowerCase();
        const category = filterSelect.value;
        const sort = sortSelect.value;

        filteredRecords = records.filter(item => {
            const searchable = [
                item.title, item.name, item.question, item.description,
                item.answer, item.category, item.location, item.program,
                item.rollNumber, item.email, ...(item.skills || [])
            ].filter(Boolean).join(" ").toLowerCase();

            const matchesSearch = searchable.includes(query);
            const matchesCategory = category === "all" || item.category === category;
            return matchesSearch && matchesCategory;
        });

        filteredRecords.sort((a, b) => {
            if (sort === "title-asc") {
                return (a.title || a.name || a.question).localeCompare(b.title || b.name || b.question);
            }
            if (sort === "title-desc") {
                return (b.title || b.name || b.question).localeCompare(a.title || a.name || a.question);
            }
            if (sort === "date-new") {
                return (b.date || "").localeCompare(a.date || "");
            }
            if (sort === "date-old") {
                return (a.date || "").localeCompare(b.date || "");
            }
            return a.id - b.id;
        });

        currentPage = 1;
        render();
    }

    function render() {
        const total = filteredRecords.length;
        const totalPages = Math.max(1, Math.ceil(total / pageSize));
        currentPage = Math.min(currentPage, totalPages);

        const start = (currentPage - 1) * pageSize;
        const pageRecords = filteredRecords.slice(start, start + pageSize);

        if (!pageRecords.length) {
            results.innerHTML = `<div class="json-message">${escapeHTML(current.empty)}</div>`;
        } else {
            results.innerHTML = pageRecords.map(renderCard).join("");
        }

        status.textContent = `${total} record${total === 1 ? "" : "s"} found · Showing page ${currentPage} of ${totalPages}`;
        pagination.hidden = total === 0;
        pageNumber.textContent = `Page ${currentPage} of ${totalPages}`;
        prevButton.disabled = currentPage <= 1;
        nextButton.disabled = currentPage >= totalPages;
    }

    async function loadJSON() {
        showLoading();

        try {
            const response = await fetch(current.file, {
                cache: "no-store"
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status} — ${response.statusText}`);
            }

            const data = await response.json();

            if (!Array.isArray(data)) {
                throw new Error("The JSON file must contain an array of records.");
            }

            records = data;
            buildFilters();
            applyView();
        } catch (error) {
            console.error("Practical 6 Fetch Error:", error);
            showError(error.message);
        }
    }

    searchInput.addEventListener("input", applyView);
    filterSelect.addEventListener("change", applyView);
    sortSelect.addEventListener("change", applyView);

    prevButton.addEventListener("click", () => {
        if (currentPage > 1) {
            currentPage--;
            render();
        }
    });

    nextButton.addEventListener("click", () => {
        const totalPages = Math.ceil(filteredRecords.length / pageSize);
        if (currentPage < totalPages) {
            currentPage++;
            render();
        }
    });

    loadJSON();
});
