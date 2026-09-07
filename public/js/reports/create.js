// ========== IMAGE UPLOAD HANDLING ==========
const imageInput = document.getElementById('image');
const imageUploadArea = document.getElementById('image-upload-area');
const imagePreview = document.getElementById('image-preview');
const previewImage = document.getElementById('preview-image');

if (imageUploadArea) {
    imageUploadArea.addEventListener('click', (event) => {
        if (event.target !== imageInput) {
            imageInput.click();
        }
    });

    imageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUploadArea.classList.add('border-red-500', 'bg-red-50');
    });

    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('border-red-500', 'bg-red-50');
    });

    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('border-red-500', 'bg-red-50');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            showImagePreview();
        }
    });
}

if (imageInput) {
    imageInput.addEventListener('change', showImagePreview);
}

function showImagePreview() {
    if (imageInput.files && imageInput.files[0]) {
        const file = imageInput.files[0];
        const allowedTypes = ['image/jpeg', 'image/png'];
        const maxSize = 6 * 1024 * 1024;

        if (!allowedTypes.includes(file.type)) {
            showImageAlert(window.reportConfig.translations.imageTypes);
            imageInput.value = '';
            imagePreview.classList.add('hidden');
            return;
        }

        if (file.size > maxSize) {
            showImageAlert(window.reportConfig.translations.imageSize);
            imageInput.value = '';
            imagePreview.classList.add('hidden');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            previewImage.src = e.target.result;
            imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);

        readExifLocation(file);
    }
}

const imageAlert = document.getElementById('image-alert');
const imageAlertText = document.getElementById('image-alert-text');
let imageAlertTimeout;

function showImageAlert(message) {
    if (!imageAlert || !imageAlertText) return;
    imageAlertText.textContent = message;
    imageAlert.classList.remove('hidden');
    clearTimeout(imageAlertTimeout);
    imageAlertTimeout = setTimeout(() => {
        imageAlert.classList.add('hidden');
    }, 4000);
}

// ========== LOCATION STATUS ==========
// Every location attempt reports back to the user. Silence was the previous
// failure mode: when a photo had no GPS tag nothing happened on screen, which
// is indistinguishable from "the feature is broken".
const LOCATION_STATE = {
    photo: { icon: '📷', tone: 'green' },
    gps: { icon: '📍', tone: 'green' },
    manual: { icon: '📍', tone: 'blue' },
    locating: { icon: '⏳', tone: 'blue' },
    nophoto: { icon: 'ℹ️', tone: 'amber' },
    error: { icon: '⚠️', tone: 'amber' },
};

const TONE_CLASSES = {
    green: 'border-green-200 bg-green-50 text-green-800',
    blue: 'border-blue-200 bg-blue-50 text-blue-800',
    amber: 'border-amber-200 bg-amber-50 text-amber-800',
};

function setLocationStatus(state, message) {
    const box = document.getElementById('location-status');
    const text = document.getElementById('location-status-text');
    if (!box || !text) return;

    const { icon, tone } = LOCATION_STATE[state] || LOCATION_STATE.manual;
    box.className = 'mt-3 rounded-2xl border px-4 py-3 text-sm flex items-start gap-3 ' + TONE_CLASSES[tone];
    text.innerHTML = `<span class="mr-1">${icon}</span>${message}`;
    box.classList.remove('hidden');
}

// ========== LOCATION MODE: auto-locked (from photo GPS) vs manual cascade ==========
// "auto" = a photo's EXIF GPS resolved City/District/Quartier; those fields are
// shown locked (read-only) and the map stays the only editable input, per the
// "Modifier la localisation" escape hatch back to "manual".
// "manual" is the default and the reliable fallback: the existing City ->
// District -> Quartier cascade, exactly as it worked before this feature —
// device GPS / map clicks only *suggest* values there, never lock anything.
let locationMode = 'manual';
let userEditedManualSelects = false;
let lastResolved = { city_id: null, district_id: null, quartier_id: null };

const autoPanel = document.getElementById('location-auto-panel');
const manualFieldset = document.getElementById('location-manual-fieldset');
const autoCityIdInput = document.getElementById('auto-city-id');
const autoDistrictIdInput = document.getElementById('auto-district-id');
const autoQuartierIdInput = document.getElementById('auto-quartier-id');

function enterAutoMode(resolved, cityName, districtName, quartierName) {
    locationMode = 'auto';
    lastResolved = resolved;

    updateAutoPanelValues(resolved, cityName, districtName, quartierName);

    if (autoPanel) autoPanel.classList.remove('hidden');
    if (manualFieldset) {
        manualFieldset.classList.add('hidden');
        manualFieldset.disabled = true;
    }
    [autoCityIdInput, autoDistrictIdInput, autoQuartierIdInput].forEach((el) => {
        if (el) el.disabled = false;
    });

    const areaEl = document.getElementById('location-area');
    if (areaEl) areaEl.classList.add('hidden');
}

// Updates the locked display + hidden inputs without changing mode — used when
// the citizen drags/clicks the marker while already in auto mode (the
// "Manual correction" case: GPS placed the pin, the exact spot needs a nudge,
// and that nudge can land in a different quartier/district).
function updateAutoPanelValues(resolved, cityName, districtName, quartierName) {
    lastResolved = resolved;

    const notAvailable = window.reportConfig.translations.notAvailable;
    const cityEl = document.getElementById('auto-city-name');
    const districtEl = document.getElementById('auto-district-name');
    const quartierEl = document.getElementById('auto-quartier-name');
    if (cityEl) cityEl.textContent = cityName || notAvailable;
    if (districtEl) districtEl.textContent = districtName || notAvailable;
    if (quartierEl) quartierEl.textContent = quartierName || notAvailable;

    if (autoCityIdInput) autoCityIdInput.value = resolved.city_id ?? '';
    if (autoDistrictIdInput) autoDistrictIdInput.value = resolved.district_id ?? '';
    if (autoQuartierIdInput) autoQuartierIdInput.value = resolved.quartier_id ?? '';
}

function enterManualMode() {
    locationMode = 'manual';

    if (autoPanel) autoPanel.classList.add('hidden');
    [autoCityIdInput, autoDistrictIdInput, autoQuartierIdInput].forEach((el) => {
        if (el) el.disabled = true;
    });

    if (manualFieldset) {
        manualFieldset.classList.remove('hidden');
        manualFieldset.disabled = false;
    }

    if (!userEditedManualSelects && lastResolved.city_id) {
        preFillManualSelects(lastResolved);
    }
}

const editLocationBtn = document.getElementById('edit-location-btn');
if (editLocationBtn) {
    editLocationBtn.addEventListener('click', enterManualMode);
}

// Central dispatcher: called whenever the pin moves, from whatever source
// (photo EXIF, device GPS, a map click, or a marker drag). Resolves the pin
// against the server (same resolver store() uses) and routes the result to
// wherever it belongs depending on both the source and the current mode.
let resolveRequestId = 0;
function applyResolvedLocation(lat, lng, source) {
    const requestId = ++resolveRequestId;
    const areaEl = document.getElementById('location-area');

    if (locationMode === 'manual' && areaEl) {
        areaEl.textContent = window.reportConfig.translations.checkingArea;
        areaEl.classList.remove('hidden');
    }

    fetch(`${window.reportConfig.resolveUrl}?lat=${lat}&lng=${lng}`, {
        headers: { 'Accept': 'application/json' },
    })
        .then((res) => (res.ok ? res.json() : Promise.reject(res.status)))
        .then((data) => {
            if (requestId !== resolveRequestId) return; // a newer pin won

            if (data.outside_area) {
                if (source === 'photo') {
                    setLocationStatus('nophoto', window.reportConfig.translations.outsideArea);
                }
                if (areaEl) {
                    areaEl.textContent = window.reportConfig.translations.outsideArea;
                    areaEl.classList.remove('hidden');
                }
                return;
            }

            const resolved = {
                city_id: data.city_id,
                district_id: data.district_id,
                quartier_id: data.quartier_id,
            };

            if (source === 'photo') {
                enterAutoMode(resolved, data.city, data.district, data.quartier);
                return;
            }

            if (locationMode === 'auto') {
                // The pin moved while already locked (drag/click correction):
                // stay locked, just correct what it's locked to.
                updateAutoPanelValues(resolved, data.city, data.district, data.quartier);
                return;
            }

            // Manual mode: suggest, never force. The suggestion line always
            // reflects the pin; the selects only follow it if the citizen
            // hasn't already made their own choice.
            lastResolved = resolved;
            if (areaEl) {
                const parts = [data.city, data.district, data.quartier].filter(Boolean);
                if (parts.length === 0) {
                    areaEl.textContent = window.reportConfig.translations.areaUnknown;
                } else {
                    let label = parts.join(' · ');
                    if (!data.district) {
                        label += ` — ${window.reportConfig.translations.cityLevelOnly}`;
                    }
                    areaEl.textContent = label;
                }
            }
            if (!userEditedManualSelects) {
                preFillManualSelects(resolved);
            }
        })
        .catch(() => {
            if (requestId !== resolveRequestId) return;
            if (areaEl) areaEl.classList.add('hidden');
        });
}

// ========== EXIF GPS AUTO-PIN ==========
// Photos only sometimes carry GPS: WhatsApp, screenshots, downloaded images and
// captures taken with location services off all have no GPS tag. So this is one
// of three inputs, not the mechanism — and when it comes up empty we say so and
// fall through to device GPS rather than leaving the user guessing. When it IS
// present, it is authoritative: it always overwrites whatever pin is already on
// the map (device GPS from page load, an earlier manual pin), because the photo
// was taken when and where the problem actually was — the device's location
// right now, possibly reporting hours later from home, is not that.
function readExifLocation(file) {
    if (typeof EXIF === 'undefined' || !file.type.includes('jpeg')) {
        // A PNG can't carry EXIF at all, and exif-js may have failed to load.
        fallbackToDeviceLocation(window.reportConfig.translations.photoNoGpsPng);
        return;
    }

    // exif-js parses the file inside its own async reader and can throw
    // (e.g. RangeError on a truncated or oddly-structured JPEG) where we
    // cannot catch it — which would leave the callback below unreachable and
    // the user staring at an unchanged form. So the fallback is armed on a
    // timer up front, and the success path disarms it.
    let settled = false;
    const settle = (fn) => {
        if (settled) return;
        settled = true;
        clearTimeout(guard);
        fn();
    };

    const guard = setTimeout(() => {
        settle(() => fallbackToDeviceLocation(window.reportConfig.translations.photoNoGps));
    }, 2000);

    try {
        EXIF.getData(file, function () {
            let decimalLat = NaN;
            let decimalLng = NaN;

            try {
                const lat = EXIF.getTag(this, 'GPSLatitude');
                const latRef = EXIF.getTag(this, 'GPSLatitudeRef');
                const lng = EXIF.getTag(this, 'GPSLongitude');
                const lngRef = EXIF.getTag(this, 'GPSLongitudeRef');

                if (lat && lng) {
                    decimalLat = exifDmsToDecimal(lat, latRef);
                    decimalLng = exifDmsToDecimal(lng, lngRef);
                }
            } catch (e) {
                console.warn('EXIF parse failed:', e);
            }

            const validRange = !Number.isNaN(decimalLat) && !Number.isNaN(decimalLng)
                && decimalLat >= -90 && decimalLat <= 90
                && decimalLng >= -180 && decimalLng <= 180;

            if (!validRange) {
                settle(() => fallbackToDeviceLocation(window.reportConfig.translations.photoNoGps));
                return;
            }

            settle(() => {
                setLocation(decimalLat, decimalLng, 'photo');
                setLocationStatus('photo', window.reportConfig.translations.locatedFromPhoto);
            });
        });
    } catch (e) {
        console.warn('EXIF read failed:', e);
        settle(() => fallbackToDeviceLocation(window.reportConfig.translations.photoNoGps));
    }
}

// The photo had nothing usable. If we already have a pin (device GPS on load,
// or a manual click) keep it and just explain; otherwise ask the device. Either
// way this never enters auto/locked mode — only a photo with real GPS does.
function fallbackToDeviceLocation(reason) {
    const latInput = document.getElementById('latitude');
    if (latInput && latInput.value) {
        setLocationStatus('nophoto', reason + ' ' + window.reportConfig.translations.keepingCurrentPin);
        return;
    }

    setLocationStatus('nophoto', reason + ' ' + window.reportConfig.translations.tryingDevice);
    requestDeviceLocation({ silent: true });
}

function exifDmsToDecimal(dms, ref) {
    // exif-js returns each DMS component as a {numerator, denominator} rational, not a plain number.
    const toNumber = (part) => (typeof part === 'object' ? part.numerator / part.denominator : part);

    const degrees = toNumber(dms[0]);
    const minutes = toNumber(dms[1]);
    const seconds = toNumber(dms[2]);

    let decimal = degrees + minutes / 60 + seconds / 3600;
    if (ref === 'S' || ref === 'W') {
        decimal *= -1;
    }
    return decimal;
}

// ========== MAP ==========
let map;
let marker;

function initMap() {
    if (!document.getElementById('map')) return;

    delete L.Icon.Default.prototype._getIconUrl;
    L.Icon.Default.mergeOptions({
        iconRetinaUrl: '/images/marker-icon-2x.png',
        iconUrl: '/images/marker-icon.png',
        shadowUrl: '/images/marker-shadow.png',
    });

    const defaultLat = 31.7917;
    const defaultLng = -7.0926;
    const zoom = 6;

    map = L.map('map').setView([defaultLat, defaultLng], zoom);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    map.on('click', function (e) {
        setLocation(e.latlng.lat, e.latlng.lng, 'click');
        setLocationStatus(
            'manual',
            locationMode === 'auto'
                ? window.reportConfig.translations.correctedManually
                : window.reportConfig.translations.pinnedManually
        );
    });

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    if (latInput && lngInput && latInput.value && lngInput.value) {
        // Restored after a validation error — keep whatever the user had.
        setLocation(parseFloat(latInput.value), parseFloat(lngInput.value), 'restore');
        setLocationStatus('manual', window.reportConfig.translations.pinnedManually);
    }
}

// `source` flows through to applyResolvedLocation so it can tell a photo pin
// (authoritative, enters auto mode) apart from every other kind (suggestions
// only). 'restore' (a validation-error redisplay) resolves like any manual
// pin but the mode itself is left as the template already rendered it.
function setLocation(lat, lng, source) {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    if (latInput) latInput.value = lat.toFixed(6);
    if (lngInput) lngInput.value = lng.toFixed(6);

    if (marker) {
        map.removeLayer(marker);
    }

    marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        if (latInput) latInput.value = pos.lat.toFixed(6);
        if (lngInput) lngInput.value = pos.lng.toFixed(6);
        setLocationStatus(
            'manual',
            locationMode === 'auto'
                ? window.reportConfig.translations.correctedManually
                : window.reportConfig.translations.pinnedManually
        );
        applyResolvedLocation(pos.lat, pos.lng, 'drag');
    });

    map.setView([lat, lng], 16);
    applyResolvedLocation(lat, lng, source);
}

const USE_LOCATION_ICON = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
const SPINNER_ICON = `<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>`;

// Asks the device for its position. `silent` requests (page load, or an EXIF
// miss) report failures into the status box instead of an alert() — only an
// explicit button press is worth interrupting someone over. This is always a
// suggestion (source: 'gps'), never authoritative the way photo EXIF is — see
// readExifLocation's comment on why a photo's own GPS always wins instead.
function requestDeviceLocation({ silent = false } = {}) {
    const btn = document.getElementById('use-location-btn');

    if (!navigator.geolocation) {
        setLocationStatus('error', window.reportConfig.translations.geolocationNotSupported);
        return;
    }

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = SPINNER_ICON + window.reportConfig.translations.gettingLocation;
    }
    setLocationStatus('locating', window.reportConfig.translations.gettingLocation);

    const restoreButton = () => {
        if (!btn) return;
        btn.disabled = false;
        btn.innerHTML = USE_LOCATION_ICON + window.reportConfig.translations.useMyLocation;
    };

    navigator.geolocation.getCurrentPosition(
        function (position) {
            setLocation(position.coords.latitude, position.coords.longitude, 'gps');
            setLocationStatus('gps', window.reportConfig.translations.locatedFromDevice);
            restoreButton();
        },
        function (error) {
            console.error('Geolocation error:', error);
            restoreButton();

            const denied = error.code === error.PERMISSION_DENIED;
            setLocationStatus(
                'error',
                denied
                    ? window.reportConfig.translations.geolocationDenied
                    : window.reportConfig.translations.geolocationError
            );

            if (!silent && !denied) {
                alert(window.reportConfig.translations.geolocationError);
            }
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 300000
        }
    );
}

const useLocationBtn = document.getElementById('use-location-btn');
if (useLocationBtn) {
    useLocationBtn.addEventListener('click', () => requestDeviceLocation({ silent: false }));
}

// ========== MANUAL CASCADE: City -> District -> Quartier ==========
// Restored as-is from the original manual flow (this is the fallback the
// citizen falls back to when no photo GPS is available, and it must keep
// working exactly like it did before this feature existed) — with one
// addition: picking anything by hand sets userEditedManualSelects so a
// later device-GPS/map suggestion doesn't clobber a deliberate choice.
const citySelect = document.getElementById('city_id');
const districtSelect = document.getElementById('district_id');
const quartierSelect = document.getElementById('quartier_id');

const districtCoordinates = {};
let allDistrictOptions = [];
if (districtSelect) {
    allDistrictOptions = Array.from(districtSelect.options);
    allDistrictOptions.forEach((option) => {
        if (option.value) {
            districtCoordinates[option.value] = {
                lat: parseFloat(option.dataset.lat) || 33.5731,
                lng: parseFloat(option.dataset.lng) || -7.5898,
            };
        }
    });
}

const quartierCoordinates = {};
let allQuartierOptions = [];
if (quartierSelect) {
    allQuartierOptions = Array.from(quartierSelect.options);
    allQuartierOptions.forEach((option) => {
        if (option.value) {
            quartierCoordinates[option.value] = {
                districtId: option.dataset.districtId,
                lat: parseFloat(option.dataset.lat) || 33.5731,
                lng: parseFloat(option.dataset.lng) || -7.5898,
            };
        }
    });
}

function refreshQuartierOptions(districtId, selectedQuartierId = '') {
    if (!quartierSelect) return;
    quartierSelect.innerHTML = '';
    const emptyOption = document.createElement('option');
    emptyOption.value = '';
    emptyOption.textContent = window.reportConfig.translations.selectQuartier;
    quartierSelect.appendChild(emptyOption);

    allQuartierOptions.forEach((option) => {
        if (!option.value) return;
        if (districtId && option.dataset.districtId === districtId) {
            quartierSelect.appendChild(option.cloneNode(true));
        }
    });

    quartierSelect.value = selectedQuartierId
        && Array.from(quartierSelect.options).some((opt) => opt.value === selectedQuartierId)
        ? selectedQuartierId
        : '';
    quartierSelect.disabled = !districtId;
}

function refreshDistrictOptions(cityId, selectedDistrictId = '', selectedQuartierId = '') {
    if (!districtSelect) return;
    districtSelect.innerHTML = '';
    const emptyOption = document.createElement('option');
    emptyOption.value = '';
    emptyOption.textContent = window.reportConfig.translations.selectDistrict;
    districtSelect.appendChild(emptyOption);

    allDistrictOptions.forEach((option) => {
        if (!option.value) return;
        if (!cityId || option.dataset.cityId === cityId) {
            districtSelect.appendChild(option.cloneNode(true));
        }
    });

    districtSelect.value = selectedDistrictId
        && Array.from(districtSelect.options).some((opt) => opt.value === selectedDistrictId)
        ? selectedDistrictId
        : '';
    districtSelect.disabled = !cityId;
    refreshQuartierOptions(districtSelect.value, selectedQuartierId);
}

// Suggests a city/district/quartier without overriding a deliberate pick —
// used both when entering manual mode with a prior resolution in hand, and
// whenever the pin moves in manual mode (device GPS, map click, drag).
function preFillManualSelects(resolved) {
    if (!citySelect || !resolved.city_id) return;

    citySelect.value = String(resolved.city_id);
    refreshDistrictOptions(
        String(resolved.city_id),
        resolved.district_id ? String(resolved.district_id) : '',
        resolved.quartier_id ? String(resolved.quartier_id) : ''
    );
}

if (quartierSelect) {
    quartierSelect.addEventListener('change', function () {
        userEditedManualSelects = true;
        if (this.value && quartierCoordinates[this.value]) {
            const coords = quartierCoordinates[this.value];
            setLocation(coords.lat, coords.lng, 'select');
        } else if (districtSelect.value && districtCoordinates[districtSelect.value]) {
            const coords = districtCoordinates[districtSelect.value];
            setLocation(coords.lat, coords.lng, 'select');
        }
    });
}

if (districtSelect) {
    districtSelect.addEventListener('change', function () {
        userEditedManualSelects = true;
        refreshQuartierOptions(this.value);
        if (this.value && districtCoordinates[this.value]) {
            const coords = districtCoordinates[this.value];
            setLocation(coords.lat, coords.lng, 'select');
        }
    });
}

if (citySelect) {
    citySelect.addEventListener('change', function () {
        userEditedManualSelects = true;
        if (districtSelect) districtSelect.value = '';
        if (quartierSelect) quartierSelect.value = '';
        refreshDistrictOptions(this.value);

        const selectedOption = this.options[this.selectedIndex];
        if (this.value && selectedOption.dataset.lat && selectedOption.dataset.lng) {
            setLocation(parseFloat(selectedOption.dataset.lat), parseFloat(selectedOption.dataset.lng), 'select');
        }
    });
}

// Restore cascade state after a validation-error redisplay (old() values are
// already baked into the rendered <option selected>, this just re-derives the
// enabled/disabled state and filtered option lists to match).
function initializeSelectors() {
    if (!citySelect) return;

    const initialCityId = citySelect.value;
    const initialDistrictId = districtSelect ? districtSelect.value : '';
    const initialQuartierId = quartierSelect ? quartierSelect.value : '';

    if (initialCityId) {
        refreshDistrictOptions(initialCityId, initialDistrictId, initialQuartierId);
    } else if (districtSelect) {
        districtSelect.disabled = true;
        if (quartierSelect) quartierSelect.disabled = true;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initMap();
    initializeSelectors();

    // Try the device straight away so the common case (reporting a problem
    // while standing next to it) needs no interaction at all. This only ever
    // suggests a manual-mode pin/selects — it can never lock anything, and a
    // photo with real GPS (processed afterwards, on file selection) always
    // overwrites it. Browsers only surface the permission prompt on a secure
    // origin, and a refusal just leaves the map ready for a manual pin.
    const latInput = document.getElementById('latitude');
    if (!latInput || !latInput.value) {
        requestDeviceLocation({ silent: true });
    }
});
