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
        const maxSize = 2 * 1024 * 1024;

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

// ========== EXIF GPS AUTO-PIN ==========
// Best effort only: most shared/re-compressed photos (WhatsApp, screenshots,
// downloaded images, or photos taken with location services off) carry no
// GPS EXIF tag at all. When one is present we use it to save the user a tap;
// when it isn't, the map silently falls back to "Use My Location" / a manual
// click, which is why the pin itself (not the photo) stays the source of truth.
function readExifLocation(file) {
    const notice = document.getElementById('exif-location-notice');
    if (notice) notice.classList.add('hidden');

    if (typeof EXIF === 'undefined' || !file.type.includes('jpeg')) {
        // exif-js failed to load, or the file is a PNG (no EXIF support) — skip silently.
        return;
    }

    EXIF.getData(file, function () {
        const lat = EXIF.getTag(this, 'GPSLatitude');
        const latRef = EXIF.getTag(this, 'GPSLatitudeRef');
        const lng = EXIF.getTag(this, 'GPSLongitude');
        const lngRef = EXIF.getTag(this, 'GPSLongitudeRef');

        if (!lat || !lng) {
            return; // no GPS tag in this photo — expected most of the time
        }

        const decimalLat = exifDmsToDecimal(lat, latRef);
        const decimalLng = exifDmsToDecimal(lng, lngRef);

        if (Number.isNaN(decimalLat) || Number.isNaN(decimalLng)) {
            return;
        }

        setLocation(decimalLat, decimalLng);
        if (notice) notice.classList.remove('hidden');
    });
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
    });

    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    if (latInput && lngInput && latInput.value && lngInput.value) {
        setLocation(parseFloat(latInput.value), parseFloat(lngInput.value));
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

    marker = L.marker([lat, lng]).addTo(map);
    map.setView([lat, lng], 15);
}

const useLocationBtn = document.getElementById('use-location-btn');
if (useLocationBtn) {
    useLocationBtn.addEventListener('click', function () {
        if (navigator.geolocation) {
            this.disabled = true;
            this.innerHTML = `<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>${window.reportConfig.translations.gettingLocation}`;

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    setLocation(lat, lng);
                    useLocationBtn.disabled = false;
                    useLocationBtn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${window.reportConfig.translations.useMyLocation}`;
                },
                function (error) {
                    console.error('Geolocation error:', error);
                    alert(window.reportConfig.translations.geolocationError);
                    useLocationBtn.disabled = false;
                    useLocationBtn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${window.reportConfig.translations.useMyLocation}`;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000
                }
            );
        } else {
            alert(window.reportConfig.translations.geolocationNotSupported);
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initMap();
});
