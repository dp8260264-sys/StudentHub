# StudentHub - Quick Reference Guide

## 🚀 Quick Start

### Open in Browser
```
1. Double-click: index.html (or dashboard.html)
2. Or drag file → browser window
3. Or File → Open → Select dashboard.html
```

### Test Responsiveness
```
Press F12 → Click responsive mode icon → Select device
Mobile: 375px - 639px
Tablet: 640px - 899px
Desktop: 900px+
```

---

## 📱 Responsive Breakpoints

### Mobile (320px - 639px)
- Single column layout
- Sidebar navigation (2-col on very small)
- Tables scroll horizontally
- Full-width forms
- Stacked cards

### Tablet (640px - 899px)
- 2-column grid layouts
- Improved padding (30px)
- Better typography sizing
- Sidebar navigation visible

### Desktop (900px+)
- Sidebar: 248px fixed + sticky
- Content: 3-column grids
- Tables: Full display
- Max-width: 1100px

---

## 🎨 Colors & Spacing

### Main Colors
```
Text:       #1f1f1f (dark)
Muted:      #6f6f6f (gray)
Background: #f7f7f5 (light)
Surface:    #ffffff (white)
Accent:     #171717 (black)
Error:      #d94a4a (red)
```

### Spacing Scale
```
Gaps:    5px, 14px, 24px
Padding: 20px (mobile) → 48px (desktop)
Radius:  9px (buttons), 16px (cards), 999px (pills)
```

---

## 📄 All Pages (14 Total)

| Page | File | Purpose |
|---|---|---|
| Dashboard | dashboard.html | Main hub |
| Announcements | announcements.html | News feed |
| Timetable | timetable.html | Schedule |
| Attendance | attendance.html | Tracking |
| Results | results.html | Grades |
| Materials | materials.html | Resources |
| Events | events.html | Campus events |
| Profile | profile.html | User settings |
| Support | contact.html | Help form |
| Feedback | feedback.html | Suggestions |
| FAQ | faq.html | Q&A |
| Login | login.html | Authentication |
| Register | register.html | Sign up |
| About | about.html | Info |

---

## 🔧 Key CSS Classes

### Layout
```
.layout         - Main grid container
.sidebar        - Side navigation
.content        - Main content area
```

### Cards & Sections
```
.card           - Content card
.section        - Major section
.cards          - Card grid container
```

### Tables
```
.timetable-section     - Table wrapper
.timetable             - Actual table
.timetable-header      - Table header info
.course-table          - Course details
.course-table-wrapper  - Course table wrapper
```

### Forms
```
form            - Form container
label           - Input labels
input, textarea - Form fields
button          - Submit buttons
```

### Responsive
```
@media (min-width: 640px)  - Tablet
@media (min-width: 900px)  - Desktop
@media (max-width: 639px)  - Mobile only
```

---

## 🔗 Navigation Structure

### Sidebar Links (All Pages Except Auth)
```
✓ Dashboard
✓ Announcements
✓ Courses & Timetable
✓ Attendance
✓ Results
✓ Study Resources
✓ Events
✓ Support
✓ Profile & Settings
✓ FAQ
```

### Breadcrumb (All Pages)
```
Home / Current Page
```

### Footer (All Pages)
```
StudentHub Student Portal
```

---

## 🧪 Testing Quick Checks

### Mobile (375px)
```
□ Single column
□ Sidebar nav 2-column
□ Tables scroll
□ Buttons clickable (44px+)
□ Text readable
```

### Tablet (768px)
```
□ 2-column layouts
□ Better spacing
□ Tables improving
□ Navigation visible
```

### Desktop (1440px)
```
□ Sidebar fixed left
□ 3-column grid
□ Tables full width
□ Max-width applied
```

---

## 🎯 CSS Grid Examples

### Dashboard Cards
```css
/* Mobile */
.cards { grid-template-columns: 1fr; }

/* Tablet */
@media (min-width: 640px) {
  .cards { grid-template-columns: repeat(2, 1fr); }
}

/* Desktop */
@media (min-width: 900px) {
  .cards { grid-template-columns: repeat(3, 1fr); }
}
```

### Main Layout
```css
/* Mobile - Single column */
.layout { grid-template-columns: 1fr; }

/* Desktop - Sidebar + Content */
@media (min-width: 900px) {
  .layout { grid-template-columns: 248px 1fr; }
}
```

---

## 🎨 Typography

### Headings
```
h1: clamp(2rem, 4vw, 3rem)  - Scales 2rem → 3rem
h2: 20px (section headers)
h3: 18px (sub-headers)
```

### Body Text
```
Font-size: 13px-14px
Line-height: 1.55
Letter-spacing: varies (0.2em for labels)
```

---

## ✨ Accessibility Features

### Semantic HTML
```
<header>  <nav>  <main>  <section>  <article>  <aside>  <footer>
```

### ARIA Labels
```
aria-label="Student Portal Navigation"
aria-current="page"
aria-label="Breadcrumb"
```

### Form Labels
```
<label for="email">Email:</label>
<input id="email" type="email">
```

---

## 🔴 Common Issues & Fixes

### Issue: Table Not Scrolling
**Fix:** Ensure `.timetable-section` has `overflow-x: auto`

### Issue: Sidebar Overlapping
**Fix:** Check `.layout` has `grid-template-columns: 248px 1fr` on desktop

### Issue: Content Too Wide
**Fix:** Main should have `max-width: 1100px` set

### Issue: Cards Not Stacking
**Fix:** Check `grid-template-columns` in media query

### Issue: Forms Not Centering
**Fix:** Main should be `margin: 0 auto`

---

## 📊 File Sizes

```
styles.css:      ~14 KB
dashboard.html:  ~5 KB
timetable.html:  ~13 KB (largest due to table)
Other pages:     ~2-8 KB each
Total Project:   ~100 KB
```

---

## 🚀 GitHub Commands Quick Ref

```bash
# Initial setup
git init
git add .
git commit -m "Initial commit"
git remote add origin [URL]
git push -u origin main

# Future commits
git add .
git commit -m "Description"
git push origin main

# Create tag
git tag -a p3-complete -m "Practical 3"
git push origin p3-complete
```

---

## 📋 Submission Steps

1. ✅ Test all pages (mobile, tablet, desktop)
2. ✅ Verify no console errors (F12)
3. ✅ Check accessibility (Tab key navigation)
4. ✅ Create GitHub repository
5. ✅ Push all files to GitHub
6. ✅ Copy repository URL
7. ✅ Submit to instructor

---

## 🎓 Next: Practical 4 Prep

Ready for JavaScript:
- ✓ HTML structure set up
- ✓ CSS variables for theming
- ✓ Class names for JS hooks (.faq-item, .modal, etc.)
- ✓ Form ready for validation
- ✓ localStorage ready for theme switching

---

## 💡 Pro Tips

1. **Test Often:** Use browser dev tools constantly
2. **Keep it Simple:** Complex code is hard to maintain
3. **Comment CSS:** Future you will thank you
4. **Git Commits:** Make small, meaningful commits
5. **Mobile First:** Start with mobile, then enhance
6. **Accessibility:** Built-in improves UX for everyone

---

## 📚 Quick Links

- CSS Grid: https://css-tricks.com/snippets/css/complete-guide-grid/
- Flexbox: https://css-tricks.com/snippets/css/a-guide-to-flexbox/
- Responsive: https://web.dev/responsive-web-design-basics/
- Accessibility: https://www.w3.org/WAI/tutorials/
- Git Help: https://git-scm.com/doc

---

**Version:** 1.0  
**Last Updated:** August 31, 2026  
**Status:** ✅ Ready for Practical 3 Submission
