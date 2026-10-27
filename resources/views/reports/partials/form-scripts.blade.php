<!-- exif-js: reads GPS tags embedded in the uploaded photo, when present, to pre-place the map pin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/exif-js/2.3.0/exif.min.js"></script>

<script>
@php
    // While Madinup covers a single city, open the map on it rather than on all of Morocco.
    $coveredCities = ($cities ?? collect())->filter(fn ($city) => $city->latitude && $city->longitude);
    $defaultCenter = $coveredCities->count() === 1
        ? [(float) $coveredCities->first()->latitude, (float) $coveredCities->first()->longitude, 13]
        : null;
@endphp
    window.reportConfig = {
        resolveUrl: "{{ route('location.resolve') }}",
        markerBase: "{{ asset('images') }}",
        // A new report needs a photo; an edit keeps the current one.
        photoRequired: @json(! isset($report)),
        defaultCenter: @json($defaultCenter),
        translations: {
            photoMissing: @json(__('validation.image_missing')),
            imageTypes: @json(__('validation.image_types')),
            imageSize: @json(__('validation.image_size')),
            imageUnreadable: @json(__('validation.image_unreadable')),
            gettingLocation: "{{ __('Getting location...') }}",
            useMyLocation: "{{ __('Use My Location') }}",
            geolocationError: "{{ __('Unable to get your location. Please click on the map to set the location manually.') }}",
            geolocationDenied: "{{ __('Location access is blocked. Allow it in your browser, or tap the map to place the pin yourself.') }}",
            geolocationNotSupported: "{{ __('Geolocation is not supported by this browser.') }}",
            locatedFromPhoto: "{{ __('Location read from the photo. Drag the pin to fine-tune.') }}",
            locatedFromDevice: "{{ __('Using your current location. Drag the pin to fine-tune.') }}",
            pinnedManually: "{{ __('Pin placed manually.') }}",
        placedFromArea: "{{ __('Pin placed in the selected area. Tap the map to place it exactly.') }}",
            photoNoGps: "{{ __('This photo has no GPS data (common for shared or screenshot images).') }}",
            photoNoGpsPng: "{{ __('PNG images cannot carry GPS data.') }}",
            tryingDevice: "{{ __('Trying your device location instead...') }}",
            keepingCurrentPin: "{{ __('Keeping the pin already set.') }}",
            checkingArea: "{{ __('Checking area...') }}",
            areaUnknown: "{{ __('Area could not be determined — drag the pin closer to the street.') }}",
            cityLevelOnly: "{{ __('city level only, no district data for this area yet') }}",
            outsideArea: "{{ __('This location is outside our coverage area. Please set it manually.') }}",
            notAvailable: "{{ __('Not available') }}",
            correctedManually: "{{ __('Pin corrected. Location updated.') }}",
            selectDistrict: "{{ __('-- Select a district --') }}",
            selectQuartier: "{{ __('-- Select a quartier --') }}"
        }
    };
</script>
{{-- create.js calls window.madinupImage.compress(); without this the call
     throws and every photo is rejected as unreadable. --}}
<script src="{{ asset('js/reports/image-compress.js') }}?v={{ filemtime(public_path('js/reports/image-compress.js')) }}"></script>
<script src="{{ asset('js/reports/create.js') }}?v={{ filemtime(public_path('js/reports/create.js')) }}"></script>
