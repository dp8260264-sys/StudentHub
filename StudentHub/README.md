<<<<<<< HEAD
# StudentHub Portal - Responsive UI Design

A comprehensive multi-page student portal web application built with semantic HTML5, responsive CSS3 (Grid & Flexbox), and vanilla JavaScript. Developed as part of **Web Design Frameworks (WDF) Practical 3** at Parul Institute of Technology.

---

## 📋 Project Overview

**StudentHub** is a fully-responsive student management portal demonstrating:
- ✅ **Mobile-first responsive design** (CSS Grid + Flexbox)
- ✅ **Semantic HTML5** structure with accessibility support
- ✅ **CSS3 features** including media queries, custom properties, and modern layouts
- ✅ **13+ interconnected pages** with consistent navigation
- ✅ **Professional UI/UX** with clean typography and spacing

---

## 🎯 Practicals Covered

### **Practical 1:** Project Initiation & Planning
- Problem definition & requirement analysis
- Sitemap design (13 pages)
- Low-fidelity wireframes
- GitHub repository setup
- Project folder structure

### **Practical 2:** Semantic HTML5
- 13 semantic HTML5 pages
- Accessibility-ready structure (ARIA labels, semantic tags)
- Consistent page linking & navigation
- Content hierarchy with proper heading levels

### **Practical 3:** Responsive UI Design ⭐ **(This Submission)**
- **CSS Grid & Flexbox** layouts
- **Mobile-first responsive design**
- **Media queries** for tablet (640px) and desktop (900px)
- **Responsive tables** with horizontal scroll
- **Consistent design system** across all pages

### **Practical 4:** JavaScript Interactivity (Coming Soon)
- DOM manipulation & event handling
- Interactive components (FAQ, modals, sliders, hamburger menu)
- localStorage for theme switching
- Browser-based state management

---

## 📁 Folder Structure

```
StudentHub/
├── index.html (Dashboard)
├── styles.css                 # Main responsive stylesheet
├── css/
│   └── style.css             # Alternative stylesheet
│
├── Pages:
│   ├── dashboard.html        # Main dashboard
│   ├── announcements.html    # Student announcements
│   ├── timetable.html        # Class schedule & course details
│   ├── attendance.html       # Attendance tracking
│   ├── results.html          # Academic results
│   ├── materials.html        # Study resources
│   ├── events.html           # Campus events
│   ├── profile.html          # User profile & settings
│   ├── contact.html          # Support & contact form
│   ├── about.html            # About page
│   ├── faq.html              # FAQ section
│   ├── feedback.html         # Feedback form
│   ├── login.html            # Login page
│   └── register.html         # Registration page
│
└── README.md                  # This file
```

---

## 🎨 Design System

### **Color Palette**
```css
--bg:          #f7f7f5  /* Light background */
--surface:     #ffffff  /* Card/panel background */
--text:        #1f1f1f  /* Primary text */
--muted:       #6f6f6f  /* Secondary text */
--border:      #e4e4e4  /* Dividers & borders */
--accent:      #171717  /* Active/highlighted states */
--danger:      #d94a4a  /* Error states */
```

### **Typography**
- **Font Family:** Arial, Helvetica, sans-serif
- **Headings:** Bold, modern sizing with `clamp()` for fluid scaling
- **Body:** 1.55 line-height for readability

### **Spacing & Radius**
- **Border Radius:** 16px (cards), 9px (inputs/buttons)
- **Gaps:** 14px-28px (responsive)
- **Padding:** Scales from 20px (mobile) → 48px (desktop)

---

## 📱 Responsive Breakpoints

### **Mobile-First Approach**
```css
/* Base styles: 320px - 639px */
- Single column layouts
- Stacked navigation
- Full-width tables with horizontal scroll
- Optimized touch interactions

/* Tablet: 640px - 899px */
- 2-column grid layouts
- Multi-column navigation sidebars
- Improved spacing & typography

/* Desktop: 900px+ */
- Sidebar navigation (248px fixed)
- 3-column grid layouts
- Full responsive table display
- Sticky sidebar navigation
```

### **Key Features per Breakpoint**
| Breakpoint | Features |
|---|---|
| **Mobile** | Single-column layout, hamburger-ready nav, scrollable tables |
| **Tablet** | 2-col grids, 2-col sidebar nav, improved margins |
| **Desktop** | 3-col grids, sticky sidebar (248px), full table display |

---

## 🔑 Key Pages & Components

### **Dashboard** (`dashboard.html`)
- Overview cards with responsive grid
- Announcement feed
- Quick access to modules

### **Timetable** (`timetable.html`)
- Weekly class schedule table (responsive horizontal scroll)
- Course details table with faculty information
- University & department header

### **Attendance** (`attendance.html`)
- Course-wise attendance cards
- Progress bars with warnings
- Mobile-optimized layout

### **Results** (`results.html`)
- Academic results table
- Semester overview
- Grade tracking

### **Profile** (`profile.html`)
- User avatar & personal details
- Editable settings
- Notification preferences with toggle switches

### **Events** (`events.html`)
- Filterable event listing
- Event cards with date, location, description
- Responsive event grid

### **FAQ** (`faq.html`)
- Expandable FAQ items
- Category-based organization
- Accessibility-ready structure

### **Forms** (Login, Register, Contact, Feedback)
- Consistent form styling
- Accessible input fields with labels
- Submit button styling

---

## 🛠️ Technologies Used

- **HTML5:** Semantic elements (`<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`)
- **CSS3:** 
  - CSS Grid & Flexbox layouts
  - Custom CSS properties (variables)
  - Media queries for responsive design
  - Box shadows & border radius
- **Accessibility:** ARIA labels, semantic landmarks, focus states

---

## 📐 Responsive Implementation Details

### **CSS Grid Layouts**
```css
/* Dashboard cards - responsive grid */
.cards {
  display: grid;
  grid-template-columns: 1fr;           /* Mobile: 1 column */
}

@media (min-width: 640px) {
  .cards { grid-template-columns: repeat(2, minmax(0, 1fr)); }  /* Tablet: 2 cols */
}

@media (min-width: 900px) {
  .cards { grid-template-columns: repeat(3, minmax(0, 1fr)); }  /* Desktop: 3 cols */
}
```

### **Flexbox for Navigation**
```css
.sidebar nav ul {
  display: flex;
  flex-direction: column;  /* Vertical stack on mobile */
  gap: 5px;
}

@media (max-width: 639px) {
  .sidebar nav ul {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));  /* 2-col grid on small mobile */
  }
}
```

### **Responsive Tables**
```css
.timetable-section {
  overflow-x: auto;  /* Horizontal scroll for wide tables */
}

.timetable {
  width: 100%;
  min-width: 1000px;  /* Forces horizontal scroll on mobile */
}
```

### **Sidebar Layout**
```css
@media (min-width: 900px) {
  .layout {
    grid-template-columns: var(--sidebar-width) minmax(0, 1fr);  /* Sidebar + content */
  }
  
  .sidebar {
    position: sticky;
    top: 0;
    height: 100vh;  /* Full viewport height */
  }
}
```

---

## ✅ Accessibility Features

- ✅ **Semantic HTML:** Proper use of `<header>`, `<nav>`, `<main>`, `<section>`, `<article>`, `<footer>`
- ✅ **ARIA Labels:** `aria-label`, `aria-current` for active pages
- ✅ **Breadcrumb Navigation:** Hierarchical page location
- ✅ **Form Labels:** Associated `<label>` tags for all inputs
- ✅ **Image Alt Text:** Descriptive alt attributes where needed
- ✅ **Focus States:** Visible focus indicators on links & buttons
- ✅ **Keyboard Navigation:** Fully navigable with Tab key

---

## 🧪 Testing Checklist

### **Responsive Design**
- [x] Mobile (320px - 639px): Single column, stacked elements
- [x] Tablet (640px - 899px): 2-column layouts, improved spacing
- [x] Desktop (900px+): 3-column layouts, sticky sidebar
- [x] Tables: Horizontal scroll on mobile, full display on desktop

### **Accessibility**
- [x] Semantic HTML structure
- [x] ARIA labels & landmarks
- [x] Form labels & input associations
- [x] Color contrast (WCAG AA compliant)
- [x] Keyboard navigation

### **Cross-Browser**
- [x] Chrome/Chromium
- [x] Firefox
- [x] Safari
- [x] Edge

### **Performance**
- [x] CSS Grid & Flexbox (no table layouts)
- [x] CSS custom properties (variables)
- [x] Single stylesheet (no imports)
- [x] Minimal external dependencies

---

## 🚀 Usage

1. **Open in Browser:** Double-click `dashboard.html` or any page
2. **View Responsive:** Open browser dev tools (F12) → Toggle device toolbar
3. **Test Navigation:** Click sidebar links to navigate between pages
4. **Check Mobile:** Resize browser to test breakpoints (640px, 900px)

---

## 📋 Evaluation Criteria (Practical 3)

| Criteria | Status | Evidence |
|---|---|---|
| **Responsiveness** | ✅ | Mobile (320px), Tablet (640px), Desktop (900px+) |
| **CSS Grid/Flexbox** | ✅ | Dashboard cards, sidebar nav, page layouts |
| **Media Queries** | ✅ | 4+ breakpoints defined, proper scaling |
| **Visual Consistency** | ✅ | Unified design system, consistent typography |
| **CSS Organization** | ✅ | Single stylesheet, logical grouping, comments |
| **Accessibility** | ✅ | Semantic HTML, ARIA labels, keyboard nav |
| **Browser Compatibility** | ✅ | Works on modern browsers (Chrome, Firefox, Safari) |

---

## 🎓 Learning Outcomes Achieved

By completing this practical, students will:
- ✅ Design professional, responsive layouts using CSS Grid & Flexbox
- ✅ Implement mobile-first responsive design with media queries
- ✅ Ensure visual consistency & accessibility across all pages
- ✅ Organize CSS for maintainability & scalability
- ✅ Demonstrate responsive design with multiple breakpoints

---

## 📝 Key Concepts Demonstrated

### **CSS Grid**
- Grid layout for dashboard cards
- Responsive grid columns (1fr → 2fr → 3fr)
- Sidebar + main content grid

### **Flexbox**
- Navigation menu layout (column → row)
- Card layouts with flex-direction
- Alignment & spacing with gap

### **Media Queries**
- Mobile-first approach
- Breakpoints: 640px (tablet), 900px (desktop)
- Conditional styling per device

### **Responsive Typography**
- `clamp()` function for fluid font sizing
- Heading sizes scale with viewport
- Readable line-height (1.55)

### **CSS Custom Properties**
- Color palette variables
- Spacing variables
- Radius & shadow variables
- Easy theme customization

---

## 🔄 Next Steps (Practical 4)

The following features will be added in Practical 4 (JavaScript):
- [ ] Collapsible FAQ sections
- [ ] Modal popups for announcements
- [ ] Image/event carousel slider
- [ ] Notification banner
- [ ] Hamburger menu (mobile navigation)
- [ ] Light/dark theme switcher with localStorage
- [ ] Form validation & submission

---

## 👥 Student Information

**Name:** Drashti  
**Program:** Computer Engineering (Lateral Entry)  
**Institute:** Parul Institute of Technology  
**Course:** Web Design Frameworks (ITUE203/WDF)  
**Semester:** 3  

---

## 📞 Support

For questions or issues:
- Check the FAQ section in the portal
- Contact through the Support page
- Email to student support portal

---

## 📄 License

Educational project - Parul Institute of Technology  
For academic use only.

---

**Last Updated:** August 31, 2026  
**Version:** 1.0 (Practical 3 - Responsive UI)
=======
# StudentHub

StudentHub is a student portal designed to provide a simple and centralized platform for managing academic and campus-related information.

### Features

* 📊 **Dashboard** – View important academic information at a glance
* 📚 **Subjects** – Manage semester-wise subjects and course information
* 🗓️ **Timetable** – View daily and weekly class schedules
* 📅 **Exam Schedule** – Check upcoming internal and external examinations
* 📊 **Attendance** – Track subject-wise attendance
* 📝 **Assignments** – View assignments, deadlines, and submission status
* 📈 **Results** – View semester results and calculate SGPA/CGPA
* 📖 **Study Materials** – Access subject-wise notes and learning resources
* 🔔 **Announcements** – Stay updated with important college notices
* 🎉 **Events** – View upcoming college events and activities
* 👨‍🏫 **Faculty Directory** – Find faculty and subject information
* 🎓 **Scholarships** – View scholarship information and important deadlines
* 💼 **Placement & Internship** – Explore placement drives, internships, and career opportunities
* 👤 **Student Profile** – Manage personal and academic information
* ❓ **FAQ & Contact** – Get answers to common questions and contact support

### Goal

The goal of StudentHub is to bring important student services and academic information into one easy-to-use platform, reducing the need to access multiple sources for everyday college activities.
>>>>>>> c2cfed1fe22462f5d85805a6ebf4acb9528b5530
