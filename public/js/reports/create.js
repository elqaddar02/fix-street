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

// Shows which city/district/quartier the pin actually resolves to, using the
// same server-side resolver that store() will use — so what the user sees here
// is what gets filed.
let resolveRequestId = 0;
function showResolvedArea(lat, lng) {
    const areaEl = document.getElementById('location-area');
    if (!areaEl) return;

    const requestId = ++resolveRequestId;
    areaEl.textContent = window.reportConfig.translations.checkingArea;
    areaEl.classList.remove('hidden');

    fetch(`${window.reportConfig.resolveUrl}?lat=${lat}&lng=${lng}`, {
        headers: { 'Accept': 'application/json' },
    })
        .then((res) => (res.ok ? res.json() : Promise.reject(res.status)))
        .then((data) => {
            if (requestId !== resolveRequestId) return; // a newer pin won

            const parts = [data.city, data.district, data.quartier].filter(Boolean);
            if (parts.length === 0) {
                areaEl.textContent = window.reportConfig.translations.areaUnknown;
                return;
            }

            let label = parts.join(' · ');
            if (!data.district) {
                label += ` — ${window.reportConfig.translations.cityLevelOnly}`;
            }
            areaEl.textContent = label;
        })
        .catch(() => {
            if (requestId !== resolveRequestId) return;
            areaEl.classList.add('hidden');
        });
}

// ========== EXIF GPS AUTO-PIN ==========
// Photos only sometimes carry GPS: WhatsApp, screenshots, downloaded images and
// captures taken with location services off all have no GPS tag. So this is one
// of three inputs, not the mechanism — and when it comes up empty we say so and
// fall through to device GPS rather than leaving the user guessing.
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

            if (Number.isNaN(decimalLat) || Number.isNaN(decimalLng)) {
                settle(() => fallbackToDeviceLocation(window.reportConfig.translations.photoNoGps));
                return;
            }

            settle(() => {
                setLocation(decimalLat, decimalLng);
                setLocationStatus('photo', window.reportConfig.translations.locatedFromPhoto);
            });
        });
    } catch (e) {
        console.warn('EXIF read failed:', e);
        settle(() => fallbackToDeviceLocation(window.reportConfig.translations.photoNoGps));
    }
}

// The photo had nothing usable. If we already have a pin (device GPS on load,
// or a manual click) keep it and just explain; otherwise ask the device.
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

// ========== MAP AND LOCATION HANDLING ==========
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
        setLocation(e.latlng.lat, e.latlng.lng);
        setLocationStatus('manual', window.reportConfig.translations.pinnedManually);
    });

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    if (latInput && lngInput && latInput.value && lngInput.value) {
        // Restored after a validation error — keep whatever the user had.
        setLocation(parseFloat(latInput.value), parseFloat(lngInput.value));
        setLocationStatus('manual', window.reportConfig.translations.pinnedManually);
    }
}

function setLocation(lat, lng) {
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
        setLocationStatus('manual', window.reportConfig.translations.pinnedManually);
        showResolvedArea(pos.lat, pos.lng);
    });

    map.setView([lat, lng], 16);
    showResolvedArea(lat, lng);
}

const USE_LOCATION_ICON = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>`;
const SPINNER_ICON = `<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>`;

// Asks the device for its position. `silent` requests (page load, or an EXIF
// miss) report failures into the status box instead of an alert() — only an
// explicit button press is worth interrupting someone over.
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
            setLocation(position.coords.latitude, position.coords.longitude);
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

document.addEventListener('DOMContentLoaded', () => {
    initMap();

    // Try the device straight away so the common case (reporting a problem
    // while standing next to it) needs no interaction at all. Browsers only
    // surface the permission prompt on a secure origin, and a refusal just
    // leaves the map ready for a manual pin.
    const latInput = document.getElementById('latitude');
    if (!latInput || !latInput.value) {
        requestDeviceLocation({ silent: true });
    }
});
