# Madinup: Arabic translation sheet

Phase 6 of the [Arabic test plan](arabic-e2e-test-plan.md). Source: every page and message seen during phases 3–5, plus a full read of `resources/lang/ar.json` (the 633 texts the code uses).

**Mechanical check:** 605 of 633 texts have an Arabic version. The 28 without one are the profile page, the Terms and Privacy pages and three sign-in messages (findings F-11, F-13, F-14). Laravel's own messages (form errors, password reset, emails) have no Arabic file at all (F-01, F-02).

**Overall:** the Arabic is understandable and mostly correct Modern Standard Arabic. The three things that make it feel unfinished are: several words for the same idea, wrong plurals after numbers, and a few literal translations from English. None of the existing texts is wrong enough to mislead, **except the content issues in section 4**.

Recommendation: apply this sheet, then have a native speaker read the site once before launch (about one hour, using the glossary below).

---

## 1. Glossary (one word per idea, everywhere)

| Idea | Use | Stop using |
|---|---|---|
| a report (the thing a citizen posts) | **بلاغ**، الجمع **بلاغات** | تقرير، تقارير، إبلاغ (as a noun for the report), إبلاغات |
| to report / the act of reporting | **أبلِغ**، **الإبلاغ** | — |
| my reports | **بلاغاتي** | تقاريري، إبلاغاتك |
| all reports | **جميع البلاغات** | جميع التقارير، جميع الإبلاغات |
| new report | **بلاغ جديد** | إبلاغ جديد، تقرير جديد |
| send the report | **إرسال البلاغ** | إرسال الإبلاغ |
| to confirm (neighbours) | **أكِّد** / **تأكيد**، الجمع **تأكيدات** | دعم (in "يمكن للجيران دعم البلاغ") |
| status: open | **مفتوح** | — |
| status: in progress | **قيد المعالجة** | قيد التنفيذ |
| status: resolved | **تم الحل** | — |
| status: rejected | **مرفوض** | — |
| district | **مقاطعة**، الجمع **مقاطعات** | العمالة (that is a prefecture) |
| neighbourhood | **حي**، الجمع **أحياء** | — |
| case file | **ملف** | — |
| filter / filters | **التصفية** | الفلاتر، الفلتر |
| website | **الموقع الإلكتروني** or the name **Madinup** when next to a map | الموقع alone (it also means "location") |
| megabyte | **ميغابايت** | ميجابايت |
| graffiti | **كتابة على الجدران** | الجرافيتي |
| citizen (adjective) | **مواطِنة** (منصة مواطنة) | مواطنية |

---

## 2. Plurals after numbers (F-08)

Arabic needs different forms for 0, 1, 2, 3–10 and 11+. Laravel supports this with `trans_choice` and ranges. Every count on the site and in the admin should use it:

| Key | Current | Proposed |
|---|---|---|
| `report.confirmations_count` | `{0} لا توجد تأكيدات\|{1} تأكيد واحد\|[2,*] :count تأكيدات` | `{0} لا توجد تأكيدات\|{1} تأكيد واحد\|{2} تأكيدان\|[3,10] :count تأكيدات\|[11,*] :count تأكيدًا` |
| `reports.summary` | `{1} بلاغ واحد\|[0,*] :count بلاغ` | `{0} لا توجد بلاغات\|{1} بلاغ واحد\|{2} بلاغان\|[3,10] :count بلاغات\|[11,*] :count بلاغًا` |
| `reports.new_today` | `:count جديد خلال آخر 24 ساعة` | `{1} بلاغ جديد خلال آخر 24 ساعة\|{2} بلاغان جديدان…\|[3,10] :count بلاغات جديدة…\|[11,*] :count بلاغًا جديدًا…` |
| `dashboard.breakdown.count` (under the donut) | `بلاغ` | plural by count, as above |
| `admin.reports_count`, `admin.used_by_reports`, `admin.city_in_use`, `admin.confirm_bulk`, `admin.flash_bulk_status`, `admin.flash_category_in_use`, `admin.confirm_delete_user_reports`, `admin.flash_user_deleted_reports` | `:count بلاغ` | same five-form pattern for بلاغ |
| `admin.accounts_count` | `:count حساب` | `{1} حساب واحد\|{2} حسابان\|[3,10] :count حسابات\|[11,*] :count حسابًا` |
| `admin.cities_intro` | `:count مدينة` | `… مدينتان \| :count مدن \| :count مدينةً` |
| `admin.districts_count` | `:count مقاطعة` | `… مقاطعتان \| :count مقاطعات \| :count مقاطعةً` |
| `admin.quartiers_count`, `admin.flash_district_in_use` | `:count حي` | `… حيّان \| :count أحياء \| :count حيًّا` |
| `admin.selected` (bulk bar) | `محدد` after the number | `تم تحديد :count` |
| Relative time (Laravel/Carbon) | `منذ 6 ثواني` | `منذ 6 ثوانٍ` (Carbon's Arabic file; minor) |

---

## 3. Wording corrections

| Where | Current | Problem | Proposed |
|---|---|---|---|
| Report form, report page | المقاطعة / العمالة | Wrong level | المقاطعة |
| Report form | تم تصحيح الموقع. تم تحديث الموقع. | Says the same thing twice | تم تحديث موقع العلامة. |
| Report form validation | لا يجب أن يتجاوز حجم الصورة 6 ميجابايت. | "لا يجب" means "it's not required"; spelling | يجب ألّا يتجاوز حجم الصورة 6 ميغابايت. |
| Home, help | أضواء الشوارع المعطلة… / الإضاءة العامة المعطلة | Literal | مصابيح الإنارة العمومية المعطلة |
| Home "Infrastructure" card | والأرصفة والأرصفة الجانبية | Same word twice | الأرصفة وحوافها |
| Category name (database) | إضاءة مكسورة | Literal ("broken light") | إنارة معطلة |
| Category name (database) | تلف الطريق | The home page says أضرار الطريق | Pick one: أضرار الطريق |
| Category name (database) | فيضان | Too strong for a street puddle | تجمّع المياه |
| Home | ساهم في إحداث فرق اليوم | Calque of "make a difference" | ساهم في تحسين حيّك اليوم |
| Home section label | العملية | Calque of "Process" | الخطوات |
| Home | كيف يعمل | Missing subject | كيف تعمل المنصة؟ |
| Menu, footer | حول | Calque of "About" | من نحن |
| Footer | اتصل | Incomplete | اتصل بنا |
| Footer | ملفي | Incomplete | ملفي الشخصي |
| Home "Follow up" step | يمكن للجيران دعم البلاغ | Glossary | يمكن للجيران تأكيد البلاغ |
| Report page | 21 سبتمبر 2026, 13:20 | Latin comma | 21 سبتمبر 2026، 13:20 |
| Report page | أكد أن هذه المشكلة… | Missing shadda | أكِّد أن هذه المشكلة… |
| Help, technical problems | إذا استمر المشكلة | Gender agreement | إذا استمرت المشكلة |
| Help contact topics | سؤال سلطة | Unclear | سؤال من جهة رسمية |
| Help contact topics | المشكلة غير محلولة | Stiff | مشكلة لم تُحلّ بعد |
| Admin header | عرض الموقع | "الموقع" also means location | زيارة الموقع الإلكتروني |
| Admin report list | إزالة الفلاتر / لهذا الفلتر | Loanword | إزالة التصفية / لهذه التصفية |
| Admin ad form | JPG أو PNG، بحد أقصى 2 ميغابايت | Wrong: the limit is 6 MB | JPG أو PNG، بحد أقصى 6 ميغابايت |
| Admin messages with names | لا يمكن حذف «Pothole»… / «Salé» / «Bettana» | Latin names inside Arabic (F-34, F-35) | Use the Arabic name when one exists |
| Admin report history | REJECTED → RESOLVED | Raw codes (F-36) | مرفوض ← تم الحل |
| Emails, form errors, sign-in, profile, Terms, Privacy | English | Not translated (F-01, F-02, F-11, F-13, F-14) | Full Arabic versions needed |

---

## 4. Content that says something untrue (fix before launch)

These are correct Arabic, but they describe the platform wrongly. Visitors read them in the help page.

| Where | What it says | Reality | Proposed |
|---|---|---|---|
| Help › how to report, step 1 | "Go to the home page and choose your location on the map **or search your area**" | There is no search | "اضغط على «بلاغ جديد» والتقط صورة للمشكلة أو اخترها من هاتفك." |
| Help › how to report, step 4 | "Add **photos if possible**" | One photo is **required** | "أرفق صورة واضحة للمشكلة (إلزامية)." |
| Help › how to report, step 5 | "Send your report **and wait for confirmation**" | It's published at once | "أرسل البلاغ: يُنشر فورًا ويمكن لجيرانك تأكيده." |
| Help › accounts | "An account is needed to report, support a report **or comment**" | Comments are off | Remove "أو التعليق" |
| Help › tracking (if shown later) | Stages "open, in progress, resolved" | "Rejected" exists too | Add "أو مرفوض" |
| Help › privacy › report data | "Your sensitive personal information (email, phone) is never shown publicly" | True, but **the reporter's full name is shown** on every report (F-26) | Either hide the name, or say "يظهر اسمك مع البلاغ" |
| Help › contact | "We usually reply within 24–48 hours" | A promise | Keep only if you can hold it |
| Profile › delete account | "All of its data will be permanently deleted" (English today) | Reports are kept as anonymous | "سيتم حذف حسابك نهائيًا. تبقى بلاغاتك منشورة دون اسمك." |
| Help › authorities (not shown today) | Authorities can "assign reports to teams, talk to citizens, generate reports" | None of this exists, and Madinup isn't linked to authorities | Delete this section, or rewrite before ever showing it |

---

## 5. Already good

- Hero, call-to-action and footer independence notice ("Madinup مبادرة مواطنة مستقلة، وليست خدمة حكومية…"): clear and natural.
- Report form guidance (GPS notice, "location access blocked", "location read from the photo"): clear.
- Sign-in, sign-up, forgot and reset password pages (the Arabic parts): natural.
- Help › terms and privacy tabs: correct, readable legal register.
- Admin interface: consistent and clear, apart from plurals and the items above.
- Dates and relative times: correct Arabic month names ("22 أكتوبر 2026", "منذ 3 أيام").
