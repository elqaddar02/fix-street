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

// ========== MAP AND LOCATION HANDLING ==========
const citySelect = document.getElementById('city_id');
const districtSelect = document.getElementById('district_id');
const quartierSelect = document.getElementById('quartier_id');

// Geolocation data for districts
const districtCoordinates = {};
if (districtSelect) {
    Array.from(districtSelect.options).forEach(option => {
        if (option.value) {
            districtCoordinates[option.value] = {
                lat: parseFloat(option.dataset.lat) || 33.5731,
                lng: parseFloat(option.dataset.lng) || -7.5898,
                name: option.textContent
            };
        }
    });
}

// Geolocation data for quartiers
const quartierCoordinates = {};
let allQuartierOptions = [];
if (quartierSelect) {
    allQuartierOptions = Array.from(quartierSelect.options);
    allQuartierOptions.forEach(option => {
        if (option.value) {
            quartierCoordinates[option.value] = {
                districtId: option.dataset.districtId,
                lat: parseFloat(option.dataset.lat) || 33.5731,
                lng: parseFloat(option.dataset.lng) || -7.5898,
                name: option.textContent
            };
        }
    });
}

let allDistrictOptions = [];
if (districtSelect) {
    allDistrictOptions = Array.from(districtSelect.options);
}

function refreshQuartierOptions(districtId, selectedQuartierId = '') {
    if (!quartierSelect) return;
    quartierSelect.innerHTML = '';
    const emptyOption = document.createElement('option');
    emptyOption.value = '';
    emptyOption.textContent = window.reportConfig.translations.selectQuartier;
    quartierSelect.appendChild(emptyOption);

    allQuartierOptions.forEach(option => {
        if (!option.value) {
            return;
        }
        if (districtId && option.dataset.districtId === districtId) {
            quartierSelect.appendChild(option.cloneNode(true));
        }
    });

    if (selectedQuartierId) {
        quartierSelect.value = selectedQuartierId;
    } else {
        quartierSelect.value = '';
    }
    quartierSelect.disabled = !districtId;
}

function refreshDistrictOptions(cityId, selectedDistrictId = '') {
    if (!districtSelect) return;
    districtSelect.innerHTML = '';
    const emptyOption = document.createElement('option');
    emptyOption.value = '';
    emptyOption.textContent = window.reportConfig.translations.selectDistrict;
    districtSelect.appendChild(emptyOption);

    allDistrictOptions.forEach(option => {
        if (!option.value) {
            return;
        }
        if (!cityId || option.dataset.cityId === cityId) {
            districtSelect.appendChild(option.cloneNode(true));
        }
    });

    if (selectedDistrictId && Array.from(districtSelect.options).some(opt => opt.value === selectedDistrictId)) {
        districtSelect.value = selectedDistrictId;
    } else {
        districtSelect.value = '';
    }
    districtSelect.disabled = !cityId;
    refreshQuartierOptions(districtSelect.value);
}

// Update map when quartier changes (HIGHEST PRIORITY)
if (quartierSelect) {
    quartierSelect.addEventListener('change', function() {
        if (this.value && quartierCoordinates[this.value]) {
            const coords = quartierCoordinates[this.value];
            updateMapLocation(coords.lat, coords.lng, 16);
        } else if (districtSelect.value && districtCoordinates[districtSelect.value]) {
            const coords = districtCoordinates[districtSelect.value];
            updateMapLocation(coords.lat, coords.lng, 14);
        }
    });
}

// Update map when district changes
if (districtSelect) {
    districtSelect.addEventListener('change', function() {
        refreshQuartierOptions(this.value);
        if (this.value && districtCoordinates[this.value]) {
            const coords = districtCoordinates[this.value];
            updateMapLocation(coords.lat, coords.lng, 14);
        }
    });
}

// Update map when city changes
if (citySelect) {
    citySelect.addEventListener('change', function() {
        if (districtSelect) districtSelect.value = '';
        if (quartierSelect) quartierSelect.value = '';
        refreshDistrictOptions(this.value);
        const selectedOption = this.options[this.selectedIndex];
        if (this.value && selectedOption.dataset.lat && selectedOption.dataset.lng) {
            updateMapLocation(parseFloat(selectedOption.dataset.lat), parseFloat(selectedOption.dataset.lng), 12);
        } else if (this.value) {
            updateMapLocation(33.5731, -7.5898, 12); // default center if no specific city coordinates
        } else {
            updateMapLocation(31.7917, -7.0926, 6); // Morocco center if no city selected
        }
    });
}

function updateMapLocation(lat, lng, zoom = 15) {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    
    if (latInput) latInput.value = lat.toFixed(6);
    if (lngInput) lngInput.value = lng.toFixed(6);
    
    if (typeof marker !== 'undefined' && marker) {
        map.removeLayer(marker);
    }
    if (typeof map !== 'undefined') {
        marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], zoom);
    }
}

// Initialize on page load if location was previously selected
function initializeSelectors() {
    if (!citySelect) return;
    
    const initialCityId = citySelect.value;
    const initialDistrictId = districtSelect.value;
    const initialQuartierId = quartierSelect.value;

    if (initialCityId) {
        refreshDistrictOptions(initialCityId, initialDistrictId);
    } else {
        if (districtSelect) districtSelect.disabled = true;
        if (quartierSelect) quartierSelect.disabled = true;
    }

    if (initialDistrictId) {
        refreshQuartierOptions(initialDistrictId, initialQuartierId);
    }

    if (initialQuartierId && quartierCoordinates[initialQuartierId]) {
        const coords = quartierCoordinates[initialQuartierId];
        if (document.getElementById('latitude')) document.getElementById('latitude').value = coords.lat.toFixed(6);
        if (document.getElementById('longitude')) document.getElementById('longitude').value = coords.lng.toFixed(6);
    } else if (initialDistrictId && districtCoordinates[initialDistrictId]) {
        const coords = districtCoordinates[initialDistrictId];
        if (document.getElementById('latitude')) document.getElementById('latitude').value = coords.lat.toFixed(6);
        if (document.getElementById('longitude')) document.getElementById('longitude').value = coords.lng.toFixed(6);
    } else if (initialCityId) {
        const selectedOption = citySelect.options[citySelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.lat && selectedOption.dataset.lng) {
            if (document.getElementById('latitude')) document.getElementById('latitude').value = parseFloat(selectedOption.dataset.lat).toFixed(6);
            if (document.getElementById('longitude')) document.getElementById('longitude').value = parseFloat(selectedOption.dataset.lng).toFixed(6);
        }
    }
}

// Initialize map
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

    map.on('click', function(e) {
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
    useLocationBtn.addEventListener('click', function() {
        if (navigator.geolocation) {
            this.disabled = true;
            this.innerHTML = `<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>${window.reportConfig.translations.gettingLocation}`;

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    setLocation(lat, lng);
                    useLocationBtn.disabled = false;
                    useLocationBtn.innerHTML = `<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${window.reportConfig.translations.useMyLocation}`;
                },
                function(error) {
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
    initializeSelectors();
    initMap();
});
