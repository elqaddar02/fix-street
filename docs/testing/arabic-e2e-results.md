# Arabic end-to-end test: results

Plan: [arabic-e2e-test-plan.md](arabic-e2e-test-plan.md). Language: العربية. Decision recorded: **a photo is required** for every report.

## Progress

| Phase | Status |
|---|---|
| 1. Setup | Done 2026-10-22: database and photos backed up; QA admin, Citizen A, Citizen B created; test photo set built |
| 2. Suspicions P1–P8 | Done 2026-10-22: P1–P7 confirmed, P8 noted |
| 3. Language, visitor, citizen | Done 2026-10-22 (desktop 1366 px and phone 375 px) |
| 4. Admin | Done 2026-10-22 (desktop 1366 px; phone 375 px for all lists) |
| 5. Cross-profile, security, edge cases | Done 2026-10-27 |
| 6. Translation review | Done 2026-10-27: see [arabic-translation-sheet.md](arabic-translation-sheet.md) |
| 7. Report and fix plan | Done 2026-10-27: see the end of this file |

**Test data created** (all removed by the database restore at the end): reports #92–#97 and #99–#108 (probes, C-30 rate-limit batch), #95 and #96 by Citizen B, test case file #1 (QA) linked to #96. Citizen C registered then deleted their account. Citizen A and B passwords changed through the real flows; Citizen B's email changed to `+citizenb2`. Temporary photo folder `public/qa-tmp/` (deleted at the end).

**Emails sent to your inbox during the run** (to check by eye: they should arrive, and per F-01 they will be in English): verification for `+citizenb2`, verification for `+citizenc`, password reset for `+citizena`.

---

## Findings

Severity: **S1** blocker · **S2** major · **S3** minor · **S4** suggestion.

### Functional

| ID | Sev. | Case | Finding |
|---|---|---|---|
| F-31 | S2 | A-12 | **Bulk status change never does anything**, and tells the admin it worked. Each row's own status menu is also named `status` inside the bulk form, so the last row's value ("OPEN") replaces the chosen one. Result: green toast "تم تطبيق الحالة على 0 بلاغ." |
| F-32 | S2 | A-35 · V-02 | **Ads created in the admin are never shown.** The home banner is a fixed "Ad Placeholder" component that never reads the ads table, and no click-tracking address exists, so impressions and clicks stay at 0. A-38 (dates) can't be tested. |
| F-33 | S2 | A-39 | **The admin cities page breaks on a phone in Arabic**: the page is 434 px on a 375 px screen, shifted so city names and the add form are cut off at the right; "غير نشط" wraps onto two lines. |
| F-39 | S2 | E-02 | **The admin dashboard goes blank on a phone when a report has a long title**: the "latest reports" card can't shrink below the title (the grid items lack `min-w-0`), so the page becomes 1,679 px wide on a 375 px screen. Desktop is unaffected. |
| F-16 | S2 | C-12 | **"419 Page Expired" is still shown raw.** Laravel turns the token error into a generic 419 *before* custom handlers run, so the friendly handler added in PR #21 never fires (tests don't catch it because tests switch the check off). This is the 419 you saw after logging in. |
| F-17 | S2 | C-26 | **A photo taken in Rabat is filed as Salé / Bab Lamrissa / Marina**, with a green "location read from the photo" message. Nearest-point matching within 15 km covers all of Rabat; no "outside the service area" message exists. |
| F-18 | S2 | C-23 | **A pin placed by hand saves no district or quartier**, although the form displays them ("سلا · باب لمريسة · المدينة القديمة"). Report #96: district and quartier empty; the server's own resolver gives Bab Lamrissa / Médina. Such reports escape district and quartier filters and statistics. |
| F-05 | S2 | P5 · C-24 | **A report is saved without a photo**, through the real form (#97) and directly (#92). |
| F-03 | S2 | P3 · C-40 · S-09 | **A citizen can confirm their own report, and an admin can confirm any report.** Both inflate the neighbours' count. |
| F-38 | S3 | S-01 · X-03 | **A citizen can land on an admin page after signing in**: if they opened an admin link while signed out, sign-in sends them back to it and they get the bare English "403 \| UNAUTHORIZED ACCESS." |
| F-06 | S2 | P6 · S-12 | The server accepts a report for an **inactive city** (#93, Rabat). |
| F-07 | S2 | P6 · S-13 | The server accepts a **pin outside Morocco** (#94, Paris → Salé) and never works out district or quartier itself. |
| F-10 | S2 | L-03 | **Signing out resets the language to English** (the choice lives in the session, which logout clears). A visitor whose session expires is also back in English. The default language is English. |
| F-19 | S3 | C-38 | On a report in a case file, the **"حذف" button is still shown**; pressing it opens a bare English 403 page ("This report is part of a case and can no longer be deleted."). Deletion itself is correctly refused. |
| F-20 | S3 | C-23 | Choosing Salé puts the pin at Salé's stored centre, which is **in the river**. The map opens zoomed out over the whole region instead of on Salé. |
| F-21 | S3 | C-13 | **Forgot password reveals whether an email is registered** ("We can't find a user with that email address"). |
| F-22 | S3 | C-07 | After clicking the verification link, a person being sent on to the page they wanted (e.g. the report form) gets **no "email confirmed" message**. |
| F-23 | S4 | C-17 | After changing email, the new verification email isn't sent automatically; the person must click "re-send". |
| F-09 | S4 | setup | Dozens of empty 0-byte files in `storage/app/public/reports`, written by earlier automated test runs. |
| F-28 | S4 | C-07 · S-03 | **Server settings to get right at launch**: `APP_URL` is `http://localhost` instead of the real address (links built outside a page request would break), and `APP_DEBUG` must be `false` (today error responses include internal file paths and exception names). |
| F-40 | S4 | X-01 · X-08 · X-10 | **Decisions for you on the report lifecycle**: citizens get no notification when the status changes; a rejected report shows "مرفوض" with no reason (the admin can't give one); a citizen can still edit a rejected or resolved report and the admin isn't told. |
| F-41 | S4 | E-09 | The admin menu's open and close buttons have no label for screen readers. |
| F-30 | S4 | C-37 | The delete confirmation on "my reports" sits inside single quotes; a translation containing an apostrophe would break the button (none does today). |

### Language and content (functional impact: people can't read them)

| ID | Sev. | Case | Finding |
|---|---|---|---|
| F-01 | S2 | P1 · C-06 · C-14 | Verification and password reset **emails are entirely in English**. |
| F-02 | S2 | P2 · C-03 · V-08 · C-18 | **Every form error is in English**: sign-up, contact form, password change ("The email field is required.", "The password is incorrect.") |
| F-14 | S2 | C-09 · C-13 · C-15 · C-16 · C-17 | **Account messages in English**: wrong login ("These credentials do not match our records."), lockout ("Too many login attempts…"), password reset messages, "Saved.", "Your email address is unverified…". |
| F-13 | S2 | C-16 | **The profile page is mostly English** (11 strings: sections, buttons, explanations). Its deletion text is also **wrong**: it says all data will be deleted, but reports are kept as anonymous. |
| F-11 | S2 | V-10 | **Terms and Privacy pages (footer links) are entirely English** (0 Arabic words). |
| F-15 | S2 | V-09 · V-11 · C-12 · C-30 | **Error pages are Laravel's bare defaults**, in English, without the site menu or a way back: 403, 404, 419, 429. |
| F-42 | S2 | T-30 | **The help page describes the platform wrongly** in places visitors read: a location search that doesn't exist, photos "if possible" (a photo is required), "wait for confirmation" (reports publish at once), commenting (switched off), and a privacy text that omits that the reporter's name is public. Details in the translation sheet, section 4. |
| F-43 | S4 | T-30 | The admin ad form says images are limited to 2 MB; the real limit is 6 MB. |
| F-08 | S3 | P7 · T-10 | **Arabic plurals after numbers are wrong**: "2 تأكيدات" (تأكيدان), "11 تأكيدات" (11 تأكيدًا), "13 بلاغ" (13 بلاغًا), and about 15 admin texts. |
| F-12 | S3 | V-01 | A grey box **"Ad Placeholder (Horizontal)"** is shown to every visitor on the home page when no ad exists. |
| F-04 | S3 | P4 · C-42 | Without JavaScript, confirming shows "Report liked!" / "Report unliked!" in English. |
| F-24 | S3 | L-09 | **Browser tab titles** exist only on sign-in, sign-up and forgot-password; every other page, including each report, is just "Madinup". |
| F-34 | S3 | A-05 · A-23 · A-26 | **Category names**: the field the admin form calls "French name" holds **English** names for the 8 existing categories, so the **French site shows them in English** (Pothole, Garbage…). In Arabic mode the admin dashboard chart and admin messages also use them ("لا يمكن حذف «Pothole»…"). |
| F-35 | S3 | A-05 · A-27 · A-32 | **Place names in the admin**: a city can only be given one name (no Arabic field), so a city added from the admin shows its Latin name to Arabic visitors. The dashboard's latest reports and several admin messages quote Latin names ("Salé", "Bettana") inside Arabic text. |
| F-36 | S3 | A-13 | The admin **report history shows raw status codes** ("REJECTED → RESOLVED") instead of "مرفوض → تم الحل". |
| F-37 | S4 | A-22 | The admin **user page is thin**: no list of the person's reports, no join date, no "email verified" status. |
| F-26 | S4 | V-04 | **The reporter's full name is public** on every report ("أبلغ عنه: Test User"). A privacy decision for you. |
| F-27 | S4 | V-03 | The public list has **no status filter and no search** (e.g. "show resolved ones"). |
| F-25 | S4 | V-01 | An unused English "Terms of Service" pop-up is loaded on every page (dead code). |
| F-29 | S4 | V-01 · C-20 | English screen-reader labels: map buttons ("Zoom in", "Marker"), hero image alt text. |

P8 (case files can't be created from any screen) is noted, not counted.

---

## Passed

**Language and layout:** L-01, L-02, L-04, L-05 (pages checked so far mirror correctly), L-07 (Western digits), L-08 (Arabic dates and relative times), L-10.

**Visitor:** V-01 (home works, all 17 links resolve), V-03 (every filter, combination, empty result and sorting correct against the database; picking a district narrows quartiers), V-04, V-05 (confirm while signed out → sign in → back to the same report), V-07 (all help tabs in Arabic), V-08 (contact form validates, all five fields), V-09 (rate limit works), V-11 (404 status correct, page itself F-15), V-12.

**Citizen:**
- Account: C-01, C-02, C-03, C-04 (unverified: report form and confirm both lead to the Arabic verify page), C-05 (resend works, Arabic notice), C-07 (link verifies; second use handled), C-08, C-09 (lockout after 5 tries), C-13, C-15, C-19 (wrong password refused; right one deletes and signs out).
- Profile: C-16, C-17 (email change → unverified), C-18 (wrong current password, mismatch, too short all refused; valid change works and the new password signs in).
- Reporting: C-20 (GPS photo → Salé / Tabriquet / Hay Karima, all saved), C-22 (location blocked → clear Arabic notice), C-25 (7 MB photo compressed to 602 KB in the browser; PNG converted; fake PDF refused by the server with Arabic messages; form keeps title, category and location after an error), C-27, C-28 (HTML and script in a title shown as text on 4 pages), C-29 (double click → one report), C-30 (limit after 10 per minute), C-31, C-32 (new report visible in my reports, public list, home, quartier filter).
- My reports: C-33 (counts match the database), C-34, C-35, C-36 (case-file report: photo and location locked on screen and on the server), C-37 (report and photo file deleted, Arabic message), C-38 (refused; see F-19).
- Confirming: C-41 (confirm and cancel, persists after reload).
- Navigation: C-44 (phone bottom bar, all 5 links, current tab marked), C-45 (phone menu: links, profile, logout, language).
- C-46 (comments: no form, direct post refused).
- **Privacy:** S-14 passes early: the GPS data is stripped from stored photos.

**Admin:**
- A-01 (sign-in goes straight to the dashboard, Arabic, no 419).
- Dashboard: A-05 (every number matches the database: 6 users, 18 reports, 1 confirmation, 0/18 resolved, trends +100% and +1600% correct), A-06, A-07 (bars on the right days: 1 + 1 + 15 = 17), A-08, A-09 (20 links all work).
- Reports: A-10 (search by Arabic title, `#95`, `000095`, name, email; status, category, city, sort, page all correct), A-11 (status change, Arabic toast, undo, colour, every step in the history), A-13, A-14, A-15 (Arabic confirmation with the title, deleted), A-16 (case-file report: no delete button, explanation, direct delete refused in Arabic), A-17 ("حساب محذوف").
- Users: A-18, A-19 (deactivate, Arabic toast, undo, logged), A-20 (admins "محمي", no switch, no delete), A-21 (confirmation mentions the 2 reports; account removed, reports kept without owner), A-22.
- Categories: A-23, A-24 ("in use" refused, disabled button with reason), A-25 (empty and duplicate refused), A-26 (new category appears for citizens in Arabic).
- Cities and places: A-27, A-28 (Salé refused: 16 reports, 1 case file), A-29–A-33 (Paris point refused, district with quartiers refused, unused quartier deleted). **X-06 passes**: a quartier added in the admin is picked by the citizen-side location detection immediately.
- Ads: A-35, A-36, A-37 (admin side only; see F-32).
- A-40 (Arabic pagination), A-41 (toasts), A-42 (comments page reachable by URL, hidden from the menu).
- Phone: reports, users, dashboard and districts fit 375 px exactly; ads and categories scroll inside their table box.

**Cross-profile:**
- X-01 (admin sets "in progress", the citizen sees it in my reports, on the report page and in the counter), X-02 and C-43 (citizen A's confirmation: same count on the public card, the admin page, which names "مواطن أ", and the dashboard), X-03 (deactivated citizen can't sign in, clear Arabic message; reactivated, signs in), X-04 (via A-21), X-05 (city switched off: gone from the report form and location detection; existing reports stay public), X-06 (via phase 4), X-07 (via A-24/A-26), X-08 (works; see F-40), X-09 (contact email received, reply goes to the sender), X-10 ("مرفوض" shown to the reporter; see F-40).

**Security:**
- S-01 (citizen blocked from admin pages; page itself F-15/F-38), S-02 (another person's report: view edit page, change and delete all 403, report untouched), S-03 (via C-04), S-04 (admin can't file a report), S-05 (signed-in user deactivated → signed out on the next click with the Arabic message; page around it English, F-10), S-06 (script and quotes in a title: plain text on 4 public and 2 admin pages, and in the admin delete confirmation and toast), S-07 (F-16), S-08 (via C-25), S-10 (login, contact and report limits), S-11 (outside address ignored), S-14 (GPS stripped). **Failed:** S-09 (F-03), S-12 (F-06), S-13 (F-07).

**Edge cases:** E-01 (empty states clear in Arabic: new citizen, admin comments, empty history), E-02 (255-character Arabic title fits on every public page and the admin list and report page at 375 px; fails on the admin dashboard, F-39), E-04 (every form redirects after sending, so Back never re-sends), E-09 (F-41). **Moved to the real-phone pass:** E-03 slow network, E-06 offline map tiles, E-07 browser zoom, E-08 keyboard use.

Citizens A and B were reactivated after X-03 and S-05.

**Moved:** C-10 (remember me) to the real-phone pass; C-39 and C-43 to the cross-profile section.

---

## Translation notes (fed into section 9)

| ID | Where | Current | Problem | Suggestion |
|---|---|---|---|---|
| TN-01 | Site-wide | تقاريري · تقاريرك · إبلاغاتك · التقارير · البلاغات · إجمالي التقارير · "حذف هذا التقرير" | Several words for "report" | One term: **بلاغ / بلاغات** (بلاغاتي، جميع البلاغات، إجمالي البلاغات، حذف هذا البلاغ) |
| TN-02 | My reports | "قيد التنفيذ" (card) / "قيد المعالجة" (subtitle, admin) | Two terms for "in progress" | **قيد المعالجة** everywhere |
| TN-03 | Report page, form | "المقاطعة / العمالة" | العمالة means prefecture, a different level | **المقاطعة** |
| TN-04 | Report page | "21 سبتمبر 2026, 13:20" | Latin comma | "21 سبتمبر 2026، 13:20" |
| TN-05 | Relative times | "منذ 6 ثواني" | Grammar | "منذ 6 ثوانٍ" |
| TN-07 | Admin reports bulk bar | "3 محدد" | Agreement | "3 محددة" or "تم تحديد 3" |
| TN-08 | Admin messages | "حذف حساب Test User نهائيًا؟ سيتم الاحتفاظ بـ 2 بلاغ", "6 حي", "14 بلاغ" | Plurals (F-08) | بلاغين، 6 أحياء، 14 بلاغًا |
| TN-06 | Home, report cards | "QA P6b باريس" style titles fine; category names (حفرة، قمامة، إضاءة مكسورة…) | "إضاءة مكسورة" is literal | "إنارة معطلة" (matches the hero text) |

---

## Summary

- **Cases run:** about 150 across 7 phases; roughly 120 passed, 26 failed, 7 moved to the real-phone pass (C-10, E-03, E-06, E-07, E-08, HEIC photos, A-38 cannot run until ads display).
- **Findings:** 43 in total: **0 blockers**, **19 major**, 12 minor, 12 suggestions.
- **Strongest areas:** the GPS photo journey, filters and search (checked against the database), the admin dashboard numbers, deletion protections, deactivation, access control and script-injection protection.
- **Weakest areas:** server-side checks on new reports (photo, city, location, area), the language coverage of everything Laravel provides (emails, form errors, sign-in, error pages, profile), and three admin features that look like they work but don't (bulk status, ads, the 419 notice).

## Proposed fix plan

Each group becomes one pull request, in this order. Nothing is changed until you approve.

### PR 1 · Reports: correct data from the start (major)
F-05 photo required (form and server) · F-06 refuse inactive cities · F-07 refuse points outside Morocco · **F-18 the server works out district and quartier itself** (fixes hand-placed pins) · F-17 "outside the area" message for points like Rabat, plus a stricter distance · F-03 no confirming your own report, no confirming as admin · F-19 hide "delete" on case-file reports · F-20 map opens on Salé, city pin on land.

### PR 2 · Admin features that silently fail (major)
F-31 bulk status change · F-16 the 419 "session expired" notice actually shown · F-38 citizens never sent to admin pages after sign-in · F-39 and F-33 admin dashboard and cities page on phones · F-36 history in Arabic · F-41 menu button labels.

### PR 3 · Everything Laravel says, in Arabic and French (major)
F-01 emails · F-02 form errors · F-14 sign-in and reset messages · F-13 profile page (and the corrected deletion text) · F-11 Terms and Privacy pages · F-15 site-styled error pages (403, 404, 419, 429, 500) · F-04 · F-12 hide the ad placeholder · F-24 page titles · F-29 labels.

### PR 4 · Language that sticks (major)
F-10 remember the chosen language after logout and session expiry (cookie), and choose a default language for first visits.

### PR 5 · Translation sheet (minor)
Glossary, plurals (F-08), wording, help content (F-42), category names (F-34, also fixes the French site), Arabic city names (F-35), F-43.

### Needs your decision first
1. **Default language** for first-time visitors (today English): Arabic, French, or the browser's language?
2. **Ads (F-32):** build the display on the home page, or hide the ads section until it's needed?
3. **Reporter's name (F-26):** keep it public, show initials only, or hide it?
4. **Report lifecycle (F-40):** email citizens when the status changes? Let the admin give a reason when rejecting? Allow edits after "rejected" / "resolved"?
5. **Salé points on the Rabat side (A-34):** I can propose corrected positions for you to approve on the map.
6. **Public list (F-27):** add a status filter and a search box?

### Later, before going online
F-28 server settings (`APP_URL`, `APP_DEBUG=false`) · F-21 neutral "forgot password" message · F-22, F-23 small verification messages · F-37 richer admin user page · F-09 clean the empty test files · F-30.
