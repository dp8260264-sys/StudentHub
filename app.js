document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       HELPER FUNCTIONS
       ===================================================== */

    const $ = (selector, parent = document) =>
        parent.querySelector(selector);

    const $$ = (selector, parent = document) =>
        [...parent.querySelectorAll(selector)];


    /* =====================================================
       1. THEME SWITCHER + LOCAL STORAGE
       ===================================================== */

    const savedTheme =
        localStorage.getItem("studentHubTheme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark-theme");
    }


    const themeButton = document.createElement("button");

    themeButton.type = "button";
    themeButton.className = "theme-toggle";

    themeButton.setAttribute(
        "aria-label",
        "Switch theme"
    );

    themeButton.setAttribute(
        "title",
        "Switch light/dark theme"
    );

    themeButton.textContent =
        document.body.classList.contains("dark-theme")
            ? "☀️ Light"
            : "🌙 Dark";


    themeButton.addEventListener("click", () => {

        document.body.classList.toggle("dark-theme");

        const dark =
            document.body.classList.contains("dark-theme");

        localStorage.setItem(
            "studentHubTheme",
            dark ? "dark" : "light"
        );

        themeButton.textContent =
            dark ? "☀️ Light" : "🌙 Dark";

        showToast(
            `${dark ? "Dark" : "Light"} theme enabled`
        );

    });


    /* =====================================================
       2. MOBILE HAMBURGER MENU
       ===================================================== */

    const sidebar = $(".sidebar");

    if (sidebar) {

        const menuButton =
            document.createElement("button");

        menuButton.type = "button";
        menuButton.className = "menu-toggle";

        menuButton.setAttribute(
            "aria-label",
            "Open navigation menu"
        );

        menuButton.setAttribute(
            "aria-expanded",
            "false"
        );

        menuButton.textContent = "☰";

        document.body.prepend(menuButton);


        menuButton.addEventListener("click", () => {

            const open =
                sidebar.classList.toggle("menu-open");

            menuButton.setAttribute(
                "aria-expanded",
                String(open)
            );

            menuButton.setAttribute(
                "aria-label",
                open
                    ? "Close navigation menu"
                    : "Open navigation menu"
            );

            menuButton.textContent =
                open ? "✕" : "☰";

        });


        $$("a", sidebar).forEach(link => {

            link.addEventListener("click", () => {

                sidebar.classList.remove(
                    "menu-open"
                );

                menuButton.setAttribute(
                    "aria-expanded",
                    "false"
                );

                menuButton.textContent = "☰";

            });

        });

    }


    /* =====================================================
       3. SHARED TOP TOOLBAR
       ===================================================== */

    const content =
        $(".content") || $("main");

    if (content) {

        const toolbar =
            document.createElement("div");

        toolbar.className = "js-toolbar";

        toolbar.appendChild(themeButton);

        content.prepend(toolbar);

    } else {

        document.body.prepend(themeButton);

    }


    /* =====================================================
       4. NOTIFICATION BANNER
       ===================================================== */

    if (
        !sessionStorage.getItem(
            "studentHubNoticeClosed"
        )
    ) {

        const banner =
            document.createElement("div");

        banner.className =
            "notification-banner";

        banner.setAttribute(
            "role",
            "status"
        );

        banner.innerHTML = `
            <span>
                🔔
                <strong>StudentHub update:</strong>
                Mid-semester examinations begin
                September 8, 2026.
            </span>

            <button
                type="button"
                class="banner-close"
                aria-label="Close notification">
                ×
            </button>
        `;

        document.body.prepend(banner);


        $(".banner-close", banner)
            .addEventListener("click", () => {

                banner.remove();

                sessionStorage.setItem(
                    "studentHubNoticeClosed",
                    "true"
                );

            });

    }


    /* =====================================================
       5. STUDENT INFORMATION FROM LOCAL STORAGE
       ===================================================== */

    const studentName =
        localStorage.getItem("studentName");

    const studentEmail =
        localStorage.getItem("studentEmail");


    if (studentName) {

        [
            "studentName",
            "sidebarName",
            "profileName"
        ].forEach(id => {

            const element =
                document.getElementById(id);

            if (element) {
                element.textContent =
                    studentName;
            }

        });


        const avatar = $("#avatar");

        if (avatar) {

            avatar.textContent =
                studentName
                    .charAt(0)
                    .toUpperCase();

        }

    }


    if (studentEmail) {

        [
            "sidebarEmail",
            "profileEmail"
        ].forEach(id => {

            const element =
                document.getElementById(id);

            if (element) {
                element.textContent =
                    studentEmail;
            }

        });

    }


    /* =====================================================
       6. FAQ ACCORDION
       ===================================================== */

    $$(".content section article")
        .forEach(article => {

            const heading =
                $("h3", article);

            const answer =
                $("p", article);

            if (!heading || !answer) {
                return;
            }


            article.classList.add(
                "faq-item"
            );


            const button =
                document.createElement("button");

            button.type = "button";

            button.className =
                "faq-question";

            button.setAttribute(
                "aria-expanded",
                "false"
            );


            button.innerHTML = `
                ${heading.textContent.trim()}
                <span aria-hidden="true">+</span>
            `;


            heading.replaceWith(button);


            answer.classList.add(
                "faq-answer"
            );

            answer.hidden = true;


            button.addEventListener(
                "click",
                () => {

                    const expanded =
                        button.getAttribute(
                            "aria-expanded"
                        ) === "true";


                    button.setAttribute(
                        "aria-expanded",
                        String(!expanded)
                    );


                    answer.hidden =
                        expanded;


                    button.querySelector(
                        "span"
                    ).textContent =
                        expanded ? "+" : "−";


                    article.classList.toggle(
                        "faq-open",
                        !expanded
                    );

                }
            );

        });


    /* =====================================================
       7. EVENT / RESOURCE FILTERS
       ===================================================== */

    $$(".filter").forEach(button => {

        button.type = "button";


        button.addEventListener(
            "click",
            () => {

                const filter =
                    button.textContent
                        .trim()
                        .toLowerCase();


                $$(".filter").forEach(item => {

                    item.classList.toggle(
                        "active",
                        item === button
                    );

                });


                $$(".event").forEach(event => {

                    const type =
                        $(".event-type", event)
                            ?.textContent
                            .trim()
                            .toLowerCase();


                    event.hidden =
                        filter !== "all" &&
                        type !== filter;

                });


                $$(".resource-card")
                    .forEach(card => {

                        const type =
                            $(".resource-type", card)
                                ?.textContent
                                .trim()
                                .toLowerCase() || "";


                        card.hidden =
                            filter !== "all" &&
                            !type.includes(filter);

                    });

            }
        );

    });


    /* =====================================================
       8. DASHBOARD CONTENT SLIDER
       ===================================================== */

    const dashboard =
        document.querySelector(".cards");


    if (
        dashboard &&
        !$(".studenthub-slider")
    ) {

        const slider =
            document.createElement("section");

        slider.className =
            "studenthub-slider";

        slider.setAttribute(
            "aria-label",
            "StudentHub highlights"
        );


        slider.innerHTML = `

            <div class="slider-head">

                <div>

                    <span class="page-label">
                        HIGHLIGHTS
                    </span>

                    <h2>
                        What's happening
                    </h2>

                </div>


                <div class="slider-controls">

                    <button
                        type="button"
                        class="slider-prev"
                        aria-label="Previous slide">
                        ←
                    </button>

                    <button
                        type="button"
                        class="slider-next"
                        aria-label="Next slide">
                        →
                    </button>

                </div>

            </div>


            <div
                class="slides"
                aria-live="polite">

                <article class="slide active-slide">

                    <span>
                        📚 Academic
                    </span>

                    <h3>
                        Mid-semester examination timetable released
                    </h3>

                    <p>
                        Check the latest exam schedule
                        and plan your preparation early.
                    </p>

                </article>


                <article class="slide">

                    <span>
                        🏆 Campus
                    </span>

                    <h3>
                        Inter-department Football League
                    </h3>

                    <p>
                        Opening matches take place on
                        August 23 at the Main Ground.
                    </p>

                </article>


                <article class="slide">

                    <span>
                        💻 Workshop
                    </span>

                    <h3>
                        Building with React Router
                    </h3>

                    <p>
                        Join the hands-on navigation
                        workshop in Lab-3, Block B.
                    </p>

                </article>

            </div>


            <div
                class="slider-dots"
                role="tablist"
                aria-label="Choose highlight">
            </div>
        `;


        dashboard.after(slider);


        const slides =
            $$(".slide", slider);

        const dots =
            $(".slider-dots", slider);

        let current = 0;


        slides.forEach((_, index) => {

            const dot =
                document.createElement("button");

            dot.type = "button";

            dot.className =
                index === 0
                    ? "slider-dot active"
                    : "slider-dot";


            dot.setAttribute(
                "role",
                "tab"
            );


            dot.setAttribute(
                "aria-label",
                `Show slide ${index + 1}`
            );


            dot.addEventListener(
                "click",
                () => goToSlide(index)
            );


            dots.appendChild(dot);

        });


        function goToSlide(index) {

            current =
                (index + slides.length) %
                slides.length;


            slides.forEach((slide, i) => {

                slide.classList.toggle(
                    "active-slide",
                    i === current
                );

            });


            $$(".slider-dot", slider)
                .forEach((dot, i) => {

                    dot.classList.toggle(
                        "active",
                        i === current
                    );

                });

        }


        $(".slider-prev", slider)
            .addEventListener(
                "click",
                () => goToSlide(current - 1)
            );


        $(".slider-next", slider)
            .addEventListener(
                "click",
                () => goToSlide(current + 1)
            );

    }


    /* =====================================================
       9. MODAL POPUP
       ===================================================== */

    const modal =
        document.createElement("div");

    modal.className =
        "modal-overlay";

    modal.hidden = true;


    modal.innerHTML = `

        <div
            class="modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modal-title">

            <button
                type="button"
                class="modal-close"
                aria-label="Close dialog">
                ×
            </button>

            <p class="page-label">
                STUDENTHUB
            </p>

            <h2 id="modal-title">
                Details
            </h2>

            <p id="modal-message"></p>

            <button
                type="button"
                class="modal-action">
                Close
            </button>

        </div>
    `;


    document.body.appendChild(modal);


    function openModal(title, message) {

        $("#modal-title", modal)
            .textContent = title;

        $("#modal-message", modal)
            .textContent = message;

        modal.hidden = false;

        $(".modal-close", modal)
            .focus();

    }


    function closeModal() {

        modal.hidden = true;

    }


    $(".modal-close", modal)
        .addEventListener(
            "click",
            closeModal
        );


    $(".modal-action", modal)
        .addEventListener(
            "click",
            closeModal
        );


    modal.addEventListener(
        "click",
        event => {

            if (event.target === modal) {
                closeModal();
            }

        }
    );


    document.addEventListener(
        "keydown",
        event => {

            if (
                event.key === "Escape" &&
                !modal.hidden
            ) {

                closeModal();

            }

        }
    );


    $$(".event").forEach(eventCard => {

        if ($(".event-more", eventCard)) {
            return;
        }


        const title =
            $("h2", eventCard)
                ?.textContent
                .trim() ||
            "Event";


        const details =
            $("p", eventCard)
                ?.textContent
                .trim() ||
            "See the Events page for more information.";


        const button =
            document.createElement("button");


        button.type = "button";

        button.className =
            "event-more";

        button.textContent =
            "View details";


        button.addEventListener(
            "click",
            () => openModal(title, details)
        );


        $(".event-details", eventCard)
            ?.appendChild(button);

    });


    /* =====================================================
       10. PROFILE SETTINGS + LOCAL STORAGE
       ===================================================== */

    [
        "enrollment",
        "department",
        "semester",
        "phone"
    ].forEach(id => {

        const field =
            document.getElementById(id);

        if (field) {

            field.value =
                localStorage.getItem(id) || "";

        }

    });


    [
        "announcementToggle",
        "eventToggle"
    ].forEach(id => {

        const toggle =
            document.getElementById(id);

        if (!toggle) {
            return;
        }


        const key =
            id === "announcementToggle"
                ? "announcementNotifications"
                : "eventNotifications";


        const saved =
            localStorage.getItem(key);


        if (saved !== null) {

            toggle.checked =
                saved === "true";

        }

    });


    const saveBtn =
        $("#saveBtn");


    if (saveBtn) {

        saveBtn.addEventListener(
            "click",
            () => {

                [
                    "enrollment",
                    "department",
                    "semester",
                    "phone"
                ].forEach(id => {

                    const field =
                        document.getElementById(id);

                    if (field) {

                        localStorage.setItem(
                            id,
                            field.value
                        );

                    }

                });


                const announcementToggle =
                    $("#announcementToggle");


                const eventToggle =
                    $("#eventToggle");


                if (announcementToggle) {

                    localStorage.setItem(
                        "announcementNotifications",
                        announcementToggle.checked
                    );

                }


                if (eventToggle) {

                    localStorage.setItem(
                        "eventNotifications",
                        eventToggle.checked
                    );

                }


                showToast(
                    "Profile changes saved successfully."
                );

            }
        );

    }


    /* =====================================================
       11. PRACTICAL 5
       REGISTRATION FORM VALIDATION
       ===================================================== */

    /*
       IMPORTANT FIX:

       The HTML form is:

       <form id="registrationForm" novalidate>

       Therefore we must select it using:

       $("#registrationForm")

       NOT:

       $('form[action="login.html"]')
    */


    const registerForm =
        $("#registrationForm");


    if (registerForm) {

        const nameInput =
            $("#full-name");

        const emailInput =
            $("#register-email");

        const mobileInput =
            $("#mobile");

        const passwordInput =
            $("#register-password");

        const confirmInput =
            $("#confirm-password");

        const courseInput =
            $("#course");

        const yearInput =
            $("#year");

        const termsInput =
            $("#terms");


        /* =================================================
           REGULAR EXPRESSIONS
           ================================================= */

        const nameRegex =
            /^[A-Za-z ]{2,50}$/;


        const emailRegex =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


        const mobileRegex =
            /^[6-9][0-9]{9}$/;


        const passwordRegex =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;


        /* =================================================
           ERROR FUNCTION
           ================================================= */

        function setError(
            input,
            errorId,
            message
        ) {

            const errorElement =
                document.getElementById(errorId);


            if (errorElement) {

                errorElement.textContent =
                    message;

            }


            if (input) {

                input.classList.add(
                    "invalid"
                );

                input.classList.remove(
                    "valid"
                );


                input.setAttribute(
                    "aria-invalid",
                    "true"
                );

            }

        }


        /* =================================================
           VALID FUNCTION
           ================================================= */

        function setValid(
            input,
            errorId
        ) {

            const errorElement =
                document.getElementById(errorId);


            if (errorElement) {

                errorElement.textContent =
                    "";

            }


            if (input) {

                input.classList.remove(
                    "invalid"
                );

                input.classList.add(
                    "valid"
                );


                input.setAttribute(
                    "aria-invalid",
                    "false"
                );

            }

        }


        /* =================================================
           1. NAME VALIDATION
           ================================================= */

        function validateName() {

            const name =
                nameInput.value.trim();


            if (name === "") {

                setError(
                    nameInput,
                    "name-error",
                    "Full name is required."
                );

                return false;

            }


            if (!nameRegex.test(name)) {

                setError(
                    nameInput,
                    "name-error",
                    "Name must contain only letters and spaces."
                );

                return false;

            }


            setValid(
                nameInput,
                "name-error"
            );


            return true;

        }


        /* =================================================
           2. EMAIL VALIDATION
           ================================================= */

        function validateEmail() {

            const email =
                emailInput.value.trim();


            if (email === "") {

                setError(
                    emailInput,
                    "email-error",
                    "Email is required."
                );

                return false;

            }


            if (!emailRegex.test(email)) {

                setError(
                    emailInput,
                    "email-error",
                    "Please enter a valid email address."
                );

                return false;

            }


            setValid(
                emailInput,
                "email-error"
            );


            return true;

        }


        /* =================================================
           3. MOBILE VALIDATION
           ================================================= */

        function validateMobile() {

            const mobile =
                mobileInput.value.trim();


            if (mobile === "") {

                setError(
                    mobileInput,
                    "mobile-error",
                    "Mobile number is required."
                );

                return false;

            }


            if (!mobileRegex.test(mobile)) {

                setError(
                    mobileInput,
                    "mobile-error",
                    "Enter a valid 10-digit Indian mobile number."
                );

                return false;

            }


            setValid(
                mobileInput,
                "mobile-error"
            );


            return true;

        }


        /* =================================================
           4. PASSWORD VALIDATION
           ================================================= */

        function validatePassword() {

            const password =
                passwordInput.value;


            if (password === "") {

                setError(
                    passwordInput,
                    "password-error",
                    "Password is required."
                );

                return false;

            }


            if (!passwordRegex.test(password)) {

                setError(
                    passwordInput,
                    "password-error",
                    "Password must contain 8+ characters, uppercase, lowercase, number and special character."
                );

                return false;

            }


            setValid(
                passwordInput,
                "password-error"
            );


            return true;

        }


        /* =================================================
           5. PASSWORD STRENGTH METER
           ================================================= */

        function checkPasswordStrength() {

            const password =
                passwordInput.value;


            const strength =
                document.getElementById(
                    "password-strength"
                );


            if (!strength) {
                return;
            }


            if (password.length === 0) {

                strength.textContent =
                    "";

            }

            else if (password.length < 6) {

                strength.textContent =
                    "Password Strength: Weak";

            }

            else if (
                password.length >= 8 &&
                /[A-Z]/.test(password) &&
                /[a-z]/.test(password) &&
                /[0-9]/.test(password) &&
                /[@$!%*?&]/.test(password)
            ) {

                strength.textContent =
                    "Password Strength: Strong";

            }

            else {

                strength.textContent =
                    "Password Strength: Medium";

            }

        }


        /* =================================================
           6. CONFIRM PASSWORD
           ================================================= */

        function validateConfirmPassword() {

            const password =
                passwordInput.value;


            const confirm =
                confirmInput.value;


            if (confirm === "") {

                setError(
                    confirmInput,
                    "confirm-error",
                    "Please confirm your password."
                );

                return false;

            }


            if (password !== confirm) {

                setError(
                    confirmInput,
                    "confirm-error",
                    "Passwords do not match."
                );

                return false;

            }


            setValid(
                confirmInput,
                "confirm-error"
            );


            return true;

        }


        /* =================================================
           7. COURSE VALIDATION
           ================================================= */

        function validateCourse() {

            if (courseInput.value === "") {

                setError(
                    courseInput,
                    "course-error",
                    "Please select your course."
                );

                return false;

            }


            setValid(
                courseInput,
                "course-error"
            );


            return true;

        }


        /* =================================================
           8. YEAR VALIDATION
           ================================================= */

        function validateYear() {

            if (yearInput.value === "") {

                setError(
                    yearInput,
                    "year-error",
                    "Please select your year."
                );

                return false;

            }


            setValid(
                yearInput,
                "year-error"
            );


            return true;

        }


        /* =================================================
           9. GENDER VALIDATION
           ================================================= */

        function validateGender() {

            const gender =
                document.querySelector(
                    'input[name="gender"]:checked'
                );


            const errorElement =
                document.getElementById(
                    "gender-error"
                );


            if (!gender) {

                errorElement.textContent =
                    "Please select your gender.";

                return false;

            }


            errorElement.textContent =
                "";


            return true;

        }


        /* =================================================
           10. TERMS VALIDATION
           ================================================= */

        function validateTerms() {

            const errorElement =
                document.getElementById(
                    "terms-error"
                );


            if (!termsInput.checked) {

                errorElement.textContent =
                    "You must accept the Terms and Conditions.";

                termsInput.classList.add(
                    "invalid"
                );

                termsInput.classList.remove(
                    "valid"
                );


                return false;

            }


            errorElement.textContent =
                "";


            termsInput.classList.remove(
                "invalid"
            );

            termsInput.classList.add(
                "valid"
            );


            return true;

        }


        /* =================================================
           REAL-TIME VALIDATION
           ================================================= */


        nameInput.addEventListener(
            "input",
            validateName
        );


        emailInput.addEventListener(
            "input",
            validateEmail
        );


        mobileInput.addEventListener(
            "input",
            function () {

                // Allow numbers only

                mobileInput.value =
                    mobileInput.value.replace(
                        /\D/g,
                        ""
                    );


                validateMobile();

            }
        );


        passwordInput.addEventListener(
            "input",
            function () {

                validatePassword();

                checkPasswordStrength();


                if (
                    confirmInput.value !== ""
                ) {

                    validateConfirmPassword();

                }

            }
        );


        confirmInput.addEventListener(
            "input",
            validateConfirmPassword
        );


        courseInput.addEventListener(
            "change",
            validateCourse
        );


        yearInput.addEventListener(
            "change",
            validateYear
        );


        document
            .querySelectorAll(
                'input[name="gender"]'
            )
            .forEach(radio => {

                radio.addEventListener(
                    "change",
                    validateGender
                );

            });


        termsInput.addEventListener(
            "change",
            validateTerms
        );


        /* =================================================
           FORM SUBMISSION
           ================================================= */

        registerForm.addEventListener(
            "submit",
            event => {

                /*
                   VERY IMPORTANT:

                   Prevent registration if
                   ANY field is invalid.
                */

                event.preventDefault();


                /* Validate every field */

                const validName =
                    validateName();


                const validEmail =
                    validateEmail();


                const validMobile =
                    validateMobile();


                const validPassword =
                    validatePassword();


                const validConfirm =
                    validateConfirmPassword();


                const validCourse =
                    validateCourse();


                const validYear =
                    validateYear();


                const validGender =
                    validateGender();


                const validTerms =
                    validateTerms();


                /* =========================================
                   CHECK ALL VALIDATIONS
                   ========================================= */

                if (
                    !validName ||
                    !validEmail ||
                    !validMobile ||
                    !validPassword ||
                    !validConfirm ||
                    !validCourse ||
                    !validYear ||
                    !validGender ||
                    !validTerms
                ) {

                    showToast(
                        "Please correct the errors in the form."
                    );


                    /*
                       Focus the first invalid field
                       for keyboard accessibility.
                    */

                    const firstInvalid =
                        registerForm.querySelector(
                            ".invalid"
                        );


                    if (firstInvalid) {

                        firstInvalid.focus();

                    }


                    return;

                }


                /* =========================================
                   SAVE REGISTRATION INFORMATION
                   ========================================= */

                localStorage.setItem(
                    "studentName",
                    nameInput.value.trim()
                );


                localStorage.setItem(
                    "studentEmail",
                    emailInput.value.trim()
                );


                localStorage.setItem(
                    "studentMobile",
                    mobileInput.value.trim()
                );


                localStorage.setItem(
                    "studentCourse",
                    courseInput.value
                );


                localStorage.setItem(
                    "studentYear",
                    yearInput.value
                );


                /* =========================================
                   SUCCESS MESSAGE
                   ========================================= */

                const successMessage =
                    document.getElementById(
                        "success-message"
                    );


                if (successMessage) {

                    successMessage.textContent =
                        "Registration successful!";

                }


                showToast(
                    "Registration successful!"
                );


                /* =========================================
                   REDIRECT TO LOGIN
                   ========================================= */

                setTimeout(() => {

                    window.location.href =
                        "login.html";

                }, 1000);

            }
        );

    }


    /* =====================================================
       12. LOGIN FEEDBACK
       ===================================================== */

    const loginForm =
        $('form[action="dashboard.html"]');


    if (loginForm) {

        loginForm.addEventListener(
            "submit",
            () => {

                showToast(
                    "Login successful. Opening dashboard…"
                );

            }
        );

    }


    /* =====================================================
       13. SUPPORT / FEEDBACK FORMS
       ===================================================== */

    $$("form").forEach(form => {

        /*
           Do NOT apply generic submit handler
           to registration or login form.
        */

        if (
            form === registerForm ||
            form === loginForm
        ) {

            return;

        }


        form.addEventListener(
            "submit",
            event => {

                event.preventDefault();


                const heading =
                    $("h1")
                        ?.textContent
                        .trim() ||
                    "Request";


                showToast(
                    `${heading} submitted successfully.`
                );


                form.reset();

            }
        );

    });


    /* =====================================================
       14. TOAST NOTIFICATION
       ===================================================== */

    function showToast(message) {

        const oldToast =
            $(".js-toast");


        if (oldToast) {
            oldToast.remove();
        }


        const toast =
            document.createElement("div");


        toast.className =
            "js-toast";


        toast.setAttribute(
            "role",
            "status"
        );


        toast.textContent =
            message;


        document.body.appendChild(toast);


        requestAnimationFrame(() => {

            toast.classList.add("show");

        });


        setTimeout(() => {

            toast.classList.remove("show");


            setTimeout(() => {

                toast.remove();

            }, 250);

        }, 2800);

    }

});
