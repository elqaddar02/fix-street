# Madinup: full end-to-end test plan, Arabic version

**Goal:** prove that every function of the platform works for both profiles (citizen and admin) when the site is used in Arabic, then review the Arabic text itself.

**Order of priority**
1. **Functionality**: does each thing work, end to end, with the right result in the database, the emails and the other profile's screens?
2. **Arabic and RTL rendering**: is the page laid out and readable right-to-left?
3. **Translation quality**: is each text correct, natural and consistent, and could it be better?

The whole run happens with the interface set to **العربية**. French and English are only switched on in the language section (section 2), to check that switching works.

---

## 0. How the run works

### 0.1 Rules
- Every case has an ID (`V-` visitor, `C-` citizen, `A-` admin, `X-` cross-profile, `S-` security, `E-` edge cases, `T-` translation).
- Each case records **Pass / Fail / Blocked**, plus a screenshot for anything visual.
- A failure becomes a numbered finding with steps to reproduce, the expected result, the actual result, a screenshot and a severity:
  - **S1 Blocker**: a main journey can't be completed, or data is lost or exposed.
  - **S2 Major**: a feature works incorrectly, or the result is wrong or misleading.
  - **S3 Minor**: cosmetic or layout issues, a wrong but understandable text.
  - **S4 Suggestion**: works, but could be clearer or better.
- **Translation notes** are recorded separately (section 9) with the page, the current text, the problem and the suggested text. "Correct but could be better" goes there as an S4.
- **Nothing is fixed during the run.** Results first; fixes come afterwards, grouped into pull requests in order of severity.

### 0.2 Environment
- Local site: `http://localhost/fix-street/public`, with real Gmail sending.
- Two screen sizes for every page: **desktop 1366 px** and **phone 375 px**. Tablet 768 px is checked once per page type.
- **Database backup before starting** and restore at the end, so the test data doesn't stay in your local copy.
- **Real phone pass (section 10):** the main journeys repeated on your actual phone once you're on a private network or the site is online.

### 0.3 Test accounts and data, created before the run
| Name | Role | Purpose |
|---|---|---|
| مواطن أ (Citizen A) | citizen, verified | main reporter |
| مواطن ب (Citizen B) | citizen, verified | confirms A's reports, checks what others see |
| مواطن ج (Citizen C) | citizen, **not verified** | verification flow and limits |
| Admin T | admin | admin side |

- **Emails:** Gmail "plus" addresses that all land in your inbox, so we can open the real emails: `mouadoptimgov+citizena@gmail.com`, `+citizenb`, `+citizenc`. **This needs your OK, because it puts your address into forms.**
- **Places:** Salé, active, with its districts and quartiers. One inactive city. One new test category.
- **Photos:** a JPG with GPS data from Salé, a JPG with GPS data from Rabat, a JPG without GPS, a PNG, a file over 6 MB, a HEIC (iPhone) photo, a PDF renamed to `.jpg`.
- **Arabic text samples:** a short title, a very long title (255 characters), mixed Arabic and French (`حفرة قرب Marjane`), numbers inside text, emojis, and HTML-like text (`<b>test</b>`).
- **A seeded case file (dossier).** Case files have no screens yet; one is created in the database to test the "locked report" behaviour.
- **One house ad** created in the admin, to test the home page banner.

---

## 1. Suspicions to check first

Found while reading the code to prepare this plan. Each one becomes a test case below, and these run first.

| # | Suspicion | Where | Case |
|---|---|---|---|
| P1 | The **verification and password emails** have no Arabic text, so they will arrive in English | Laravel default notifications | C-06, C-14, T-20 |
| P2 | **Standard form errors** ("required", "invalid email"…) have no Arabic version and may show in English | no Arabic validation file | C-03, T-21 |
| P3 | A citizen can probably **confirm their own report**; an admin can probably confirm too | `ReportLikeController` | C-40, S-09 |
| P4 | Confirming without JavaScript shows "Report liked!" in English | `ReportLikeController` | C-42 |
| P5 | A report can be sent **without a photo**: intended or not? | `ReportController@store` (`image` nullable) | C-24 |
| P6 | The server may accept a report for an **inactive city**, or a pin **outside Morocco**, if the form is bypassed | `ReportController@store` | S-12, S-13 |
| P7 | Arabic counts like `:count بلاغ` are wrong for 3 to 10 (Arabic needs بلاغات), and the same for حساب, حي, مقاطعة | `ar.json` | T-10 |
| P8 | Case files can't be created from any screen | no admin page | noted only |

---

## 2. Language, direction and global layout

| ID | Check | Expected |
|---|---|---|
| L-01 | Switch to العربية with the globe button, from the home page | Page reloads in Arabic, `dir="rtl"`, choice remembered across pages |
| L-02 | Switch while on a deep page (report details, admin report) | Stays on the same page, now in Arabic |
| L-03 | The language stays after logout and login | Arabic kept (or a clearly defined rule) |
| L-04 | Arabic font loads and stays readable at all sizes; no squares or fallback glyphs | Clean Arabic glyphs |
| L-05 | Mirroring on every page: menus, arrows, back links, chevrons, icons with direction, toggles, drawers, toasts | Everything faces the right way |
| L-06 | Mixed-direction content: emails, URLs, `#000032`, dates, phone numbers, coordinates, "11–15 من 15" | Each reads correctly, nothing jumbled |
| L-07 | Numbers: Western digits (0–9) used consistently everywhere | Consistent |
| L-08 | Dates and relative times ("منذ 3 أيام") in Arabic | Arabic month names and phrasing |
| L-09 | Browser tab titles in Arabic on every page | Arabic titles |
| L-10 | Switch back to French and English once, to confirm nothing stays stuck in RTL | Clean LTR |

---

## 3. Visitor (not signed in)

| ID | Journey | Expected |
|---|---|---|
| V-01 | Home page: hero, categories, how it works, latest reports, call to action, footer | All in Arabic, links work |
| V-02 | Home page ad banner (after the admin creates one in A-70) | Shows, the click opens the link, the click is counted |
| V-03 | Reports list: browse, filters (category, city, district, quartier, status), search, sorting, pagination | Correct results, filters combine, empty state clear |
| V-04 | Report details page: photo, map, status, confirmations count, location names in Arabic | Correct and readable |
| V-05 | Confirm button while signed out | Asks to sign in and returns to the same report afterwards |
| V-06 | "New report" while signed out | Sends to sign in, then back to the form |
| V-07 | Help page: every tab (citizens, authorities, conditions, privacy, contact) | Content in Arabic, tabs work on phone |
| V-08 | Contact form: all topics, required fields, honeypot, success message, the email received | Message arrives with the right content, reply goes to the sender |
| V-09 | Contact form sent 6 times in a minute | Polite limit message in Arabic, not an error page |
| V-10 | About, terms, privacy pages | Arabic content, readable |
| V-11 | A link to a report that doesn't exist, and a random wrong URL | Friendly 404 in Arabic |
| V-12 | `/admin` while signed out | Login page, then back to admin only for an admin |

---

## 4. Citizen

### 4.1 Account
| ID | Journey | Expected |
|---|---|---|
| C-01 | Register Citizen C with valid data | Account created, "check your email" page in Arabic |
| C-02 | Register with an email already used | Clear Arabic error |
| C-03 | Register with empty fields, an invalid email, a short password, mismatched passwords | Arabic errors next to each field **(P2)** |
| C-04 | Unverified Citizen C tries to create a report or confirm one | Blocked with an explanation and a resend option |
| C-05 | "Resend verification email" | New email arrives; repeated clicks are limited |
| C-06 | Open the verification email | Subject, text and button in Arabic, the link works **(P1)** |
| C-07 | Click the verification link while signed out, and again a second time | Sensible result both times, no crash |
| C-08 | Login with correct details | Lands on "my reports" |
| C-09 | Login with a wrong password 5+ times | Arabic lockout message with waiting time |
| C-10 | "Remember me" | Still signed in after closing the browser |
| C-11 | Logout from the header and from the phone menu | Signed out, back to the home page |
| C-12 | Form left open for over 2 hours, then submitted (expired session) | Friendly Arabic notice, no 419 page |
| C-13 | Forgot password: request, email, reset, login with the new password | Works end to end |
| C-14 | The password reset email | Arabic **(P1)** |
| C-15 | Reset link used twice, or after it expires | Clear Arabic message |

### 4.2 Profile
| ID | Journey | Expected |
|---|---|---|
| C-16 | Change name | Saved, visible in the header |
| C-17 | Change email | Requires verifying again; the new email is sent |
| C-18 | Change password: wrong current password, mismatch, success | Correct messages |
| C-19 | Delete account: wrong password, then the right one | Deleted, signed out; their reports stay, shown as anonymous |

### 4.3 Creating a report (the core journey)
| ID | Journey | Expected |
|---|---|---|
| C-20 | Photo with GPS from Salé | Map pin, city, district and quartier filled automatically |
| C-21 | Photo without GPS, then "use my location" | Browser asks; location filled |
| C-22 | Location permission refused | Clear Arabic message, the map can still be used by hand |
| C-23 | Place the pin by tapping the map; move it | City, district and quartier update |
| C-24 | No photo at all | Decide: allowed or not **(P5)**; the behaviour must match the decision |
| C-25 | PNG, file over 6 MB, HEIC, a PDF renamed to `.jpg` | Accepted / refused with Arabic messages; HEIC behaviour noted |
| C-26 | Photo from Rabat (outside the active area) | Clear explanation of where Madinup works |
| C-27 | Required fields missing (title, category, location) | Arabic errors, nothing lost from the form |
| C-28 | Long Arabic title, mixed Arabic and French, emoji, `<b>` text | Saved and shown exactly as typed, no HTML executed |
| C-29 | Send twice quickly (double tap) | Only one report created |
| C-30 | More than 10 reports in a minute | Arabic limit message |
| C-31 | After sending: success message, report shows with status "مفتوح" | Correct |
| C-32 | The new report appears in the public list, on the home page and in "my reports" | Visible everywhere it should be |

### 4.4 My reports, editing, deleting
| ID | Journey | Expected |
|---|---|---|
| C-33 | "My reports" page: list, statuses, counts, empty state for a new account | Correct |
| C-34 | Edit title, description, category | Saved |
| C-35 | Edit photo and location | Saved; location re-resolved |
| C-36 | Edit a report that belongs to a case file | Location and photo locked, with an explanation |
| C-37 | Delete own report (with confirmation) | Deleted, photo removed |
| C-38 | Delete a report that belongs to a case file | Refused with an Arabic explanation |
| C-39 | Edit or delete after the admin changed the status | Rule defined and respected |

### 4.5 Confirming (neighbours)
| ID | Journey | Expected |
|---|---|---|
| C-40 | Citizen A tries to confirm their **own** report | Should not be possible **(P3)** |
| C-41 | Citizen B confirms A's report, then cancels | Count goes up then down; persists after reload |
| C-42 | Confirm with JavaScript off | Works; message in Arabic **(P4)** |
| C-43 | Count shown everywhere (card, details, admin) | Same number everywhere |

### 4.6 Other citizen pages
| ID | Journey | Expected |
|---|---|---|
| C-44 | Bottom navigation on phone: every tab, the active tab highlighted | Works |
| C-45 | Header and menu on desktop: every link | Works |
| C-46 | Comments are off: no comment form anywhere, and posting directly is refused | Hidden and refused |

---

## 5. Admin

### 5.1 Access and layout
| ID | Journey | Expected |
|---|---|---|
| A-01 | Login as admin | Goes straight to the dashboard, no 419 |
| A-02 | Admin opens "new report" or another citizen-only page | Polite redirect |
| A-03 | Sidebar on desktop (right side), drawer on phone (opens from the right, closes on tap outside) | Works |
| A-04 | "View site" link and language button in the header | Work |

### 5.2 Dashboard
| ID | Journey | Expected |
|---|---|---|
| A-05 | Each number checked against the database: users, reports, confirmations, resolution rate | Exact |
| A-06 | Trend badges ("جديد", percentages) | Mathematically right |
| A-07 | Daily chart: bars on the right days, whole numbers, Arabic day labels | Correct |
| A-08 | Status donut and list; clicking a status opens the filtered list | Correct |
| A-09 | Categories chart, latest reports, newest members, "see all" links | Correct |

### 5.3 Reports
| ID | Journey | Expected |
|---|---|---|
| A-10 | Status filters, search by title / `#ref` / user, category and city filters, sorting, combinations | Correct results |
| A-11 | Change one status from the list; the toast; undo | Saved, undone, history recorded |
| A-12 | Select several, apply a status (with confirmation) | All changed, one history line each |
| A-13 | Report page: photo full size, map, details, confirmations list, history | Correct |
| A-14 | Change status from the report page | Saved, history shows "OPEN → RESOLVED" |
| A-15 | Delete a report: confirmation, photo removed, history | Deleted |
| A-16 | Delete a report in a case file | Refused, explained |
| A-17 | Report from a deleted user | Shows "حساب محذوف" |

### 5.4 Users
| ID | Journey | Expected |
|---|---|---|
| A-18 | Search by name and email | Correct |
| A-19 | Deactivate a citizen (toggle, toast, undo) | Saved, undone |
| A-20 | Admin accounts and own account show "محمي" and can't be changed | Protected |
| A-21 | Delete a citizen with reports | Confirmation mentions the reports; reports remain, anonymous |
| A-22 | User details page | Correct, Arabic |

### 5.5 Categories
| ID | Journey | Expected |
|---|---|---|
| A-23 | Create (French and Arabic names), edit, delete an unused one | Works |
| A-24 | Try to delete a category in use | Refused, explained |
| A-25 | Duplicate name, empty name | Arabic errors |
| A-26 | New category appears in the citizen form and filters, in Arabic | Visible |

### 5.6 Cities, districts, quartiers
| ID | Journey | Expected |
|---|---|---|
| A-27 | Add a city, rename it, deactivate and reactivate it (toggle and undo) | Works |
| A-28 | Delete a city in use | Refused |
| A-29 | Districts page: list, report counts, "no map point" warnings | Correct |
| A-30 | Add a district with the map picker; edit; remove its point | Saved |
| A-31 | Add a quartier with the map picker; edit; delete an unused one | Saved |
| A-32 | Delete a district with quartiers, or a quartier in use | Refused, explained |
| A-33 | Point outside Morocco, only one coordinate filled | Arabic errors |
| A-34 | Review the Salé quartier points that sit on the Rabat side of the river | Decide and correct |

### 5.7 Ads
| ID | Journey | Expected |
|---|---|---|
| A-35 | Create a house banner with an image, a link, dates, a weight | Saved |
| A-36 | Edit, deactivate, delete | Works |
| A-37 | Wrong image type or size, missing link | Arabic errors |
| A-38 | Banner dates: not started, running, ended | Shown only while running |

### 5.8 General admin behaviour
| ID | Journey | Expected |
|---|---|---|
| A-39 | Every table on phone: stacked rows readable | Readable |
| A-40 | Pagination on every list | Correct, Arabic |
| A-41 | Success and error messages (toasts) | Arabic, readable, dismiss by themselves |
| A-42 | Comments page reachable by URL only while the feature is off | Works, not in the menu |

---

## 6. Cross-profile journeys (the most important end-to-end loops)

| ID | Story | Check on each side |
|---|---|---|
| X-01 | Citizen A reports → admin sees it at the top of the list and in the dashboard numbers → admin sets "قيد المعالجة" → A sees the new status on their report and in "my reports" → admin sets "تم الحل" | Status and counts consistent everywhere |
| X-02 | Citizen B confirms A's report → admin sees the confirmation count and B's name | Same number on both sides |
| X-03 | Admin deactivates Citizen A while A is signed in → A's next click signs them out with an Arabic message → A can't sign in → admin reactivates → A can sign in | Deactivation real and immediate |
| X-04 | Admin deletes Citizen B → B's confirmations and reports remain correct; B can't sign in | No broken pages |
| X-05 | Admin deactivates Salé → the citizen form no longer offers it, and location detection doesn't pick it → reactivate | Respected |
| X-06 | Admin adds a quartier point → a new citizen report placed there is filed under that quartier | Detection follows the admin's change |
| X-07 | Admin creates a category → citizen uses it → admin can't delete it while it's used | Guard works |
| X-08 | Citizen edits a report after the admin rejected it | Rule respected, admin sees the change |
| X-09 | Contact form sent by a citizen → email arrives → Reply goes to the citizen | Works |
| X-10 | Admin rejects a report; the reporter can still see it with a clear "مرفوض" status | Clear to the reporter |

---

## 7. Security and permissions

| ID | Attempt | Expected |
|---|---|---|
| S-01 | Citizen opens any `/admin/...` URL | 403 page in Arabic |
| S-02 | Citizen edits or deletes another citizen's report by changing the URL | Refused |
| S-03 | Unverified citizen posts a report or confirmation directly | Refused |
| S-04 | Admin posts a report directly | Refused |
| S-05 | Deactivated user keeps an old session open | Signed out on the next request |
| S-06 | Title or name containing `<script>` shown in the lists, the admin confirmation dialogs and the toasts | Shown as text, never executed |
| S-07 | Form submitted with an old CSRF token | Friendly notice |
| S-08 | Uploaded file that is not really an image | Refused |
| S-09 | Admin or owner confirms a report **(P3)** | Refused |
| S-10 | Brute force on login, contact, report sending, confirmations | Rate limits apply with Arabic messages |
| S-11 | `login-redirect` with an external URL | Ignored |
| S-12 | Report posted directly with an **inactive city** **(P6)** | Refused |
| S-13 | Report posted directly with a pin in another country **(P6)** | Refused |
| S-14 | Photos: the stored file keeps no GPS data that the public can download (privacy) | Checked and noted |

---

## 8. Edge cases and robustness

| ID | Case | Expected |
|---|---|---|
| E-01 | Empty states everywhere: no reports, no confirmations, no history, no districts | Clear Arabic messages |
| E-02 | Very long Arabic names and titles in cards, tables and the admin on phone | No overflow, sensible truncation |
| E-03 | Slow network (throttled): uploading a photo, map tiles, charts | Loading feedback, no double submit |
| E-04 | Browser back button after sending a form | No resending, no broken page |
| E-05 | Reload in the middle of the report form | Clear behaviour |
| E-06 | Map tiles fail to load (offline) | The form stays usable |
| E-07 | Zoom 200% and a large system font | Still usable |
| E-08 | Keyboard only: forms, menus, the admin toggles | Usable, visible focus |
| E-09 | Screen reader labels on icon buttons (edit, delete, close) | Arabic labels |

---

## 9. Translation review (after the functional pass)

Every page and every email, read in full in Arabic, on desktop and phone. Each finding is recorded in this format:

| ID | Page / element | Current text | Problem | Suggested text | Type |
|---|---|---|---|---|---|

**Types:** wrong · untranslated · unnatural · inconsistent term · grammar (gender, plural, agreement) · punctuation · too long for the space · tone.

**What we look for**
- **T-01 Missing text:** English or French text anywhere, raw keys like `admin.something`, and placeholders such as `:count` showing.
- **T-02 Meaning:** the text says what the button or page actually does.
- **T-03 Consistent terms:** one word per idea across the site and the admin, collected into a small glossary:
  - report = بلاغ / بلاغات; to report = الإبلاغ
  - confirmation = تأكيد; to confirm = يؤكد
  - neighbourhood = حي / أحياء; district = مقاطعة / مقاطعات
  - case file = ملف; status names; open / in progress / resolved / rejected
- **T-04 Register:** clear Modern Standard Arabic, friendly and simple, never bureaucratic. Note where a citizen would phrase it differently.
- **T-05 Gender and agreement:** messages about cities (feminine), users, reports.
- **T-10 Plurals with numbers (P7):** Arabic needs different forms for 1, 2, 3–10 and 11+ (بلاغ واحد، بلاغان، 3 بلاغات، 11 بلاغًا). Check every count on the site and in the admin.
- **T-11 Punctuation:** Arabic comma ، and question mark ؟, guillemets « », no stray Latin punctuation.
- **T-12 Text length:** buttons and badges where the Arabic is too long or wraps badly.
- **T-20 Emails (P1):** verification, password reset, contact form. Subject, body, button, signature.
- **T-21 Form errors (P2):** every validation message seen during sections 4 and 5.
- **T-22 Error pages:** 403, 404, 419, 429, 500.
- **T-23 Page titles and alt text.**
- **T-30 Content pages:** help tabs, terms, privacy, about. Read for meaning, not just grammar.

The result is a **translation sheet** ready to apply, plus a recommendation on whether a native speaker should review before launch.

---

## 10. Real phone pass

On your own phone, once on a private network or the live site:
- Register → verify from the phone's mail app → report with the **camera directly** (GPS from the photo) → confirm from a second account → admin changes the status from the phone.
- Check: camera and photo-library upload, HEIC photos from an iPhone, the location permission prompt, the Arabic keyboard in forms, bottom navigation, the admin drawer.

---

## 11. Deliverables

1. **Results sheet:** every case ID with Pass / Fail / Blocked and a short note.
2. **Findings list:** sorted by severity, each with steps, expected and actual results, and a screenshot.
3. **Translation sheet:** current and suggested Arabic for every issue, plus the glossary.
4. **Fix plan:** the findings grouped into pull requests (S1 and S2 first), for your approval before any change.

## 12. Order and timing

| Phase | Content | Rough size |
|---|---|---|
| 1 | Setup: backup, accounts, test files, seeded data | short |
| 2 | Suspicions P1–P7, so the big unknowns are known early | short |
| 3 | Sections 2–4: language, visitor, citizen | long |
| 4 | Section 5: admin | long |
| 5 | Sections 6–8: cross-profile, security, edge cases | long |
| 6 | Section 9: translation review | medium |
| 7 | Report: results, findings, fix plan | short |
| 8 | Section 10: real phone, when available | separate session |

A checkpoint with you after each phase, with screenshots of the important results. Database restored at the end.
