@extends('admin.layouts.app')

@php
    $isDistrict = $kind === 'district';
    $editing = $place->exists;
    $title = $isDistrict
        ? __($editing ? 'admin.districts_edit_title' : 'admin.districts_new')
        : __($editing ? 'admin.quartiers_edit_title' : 'admin.quartiers_new');
    $lat = old('latitude', $isDistrict ? $place->lat : $place->latitude);
    $lng = old('longitude', $isDistrict ? $place->lng : $place->longitude);
    $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-500 focus:ring-1 focus:ring-red-500';
    $label = 'mb-1.5 block text-sm font-medium text-slate-700';
@endphp

@section('title', $title)

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('admin.cities.places', $city) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-slate-900">
            <svg class="h-4 w-4 rtl:rotate-180" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
            {{ $city->name }}@isset($district) · {{ $district->name_fr }}@endisset
        </a>
        <h2 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ $title }}</h2>
    </div>

    <form method="POST" action="{{ $action }}" class="grid gap-6 lg:grid-cols-5">
        @csrf
        @if($editing) @method('PATCH') @endif

        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-5 lg:col-span-2">
            <div>
                <label for="name_fr" class="{{ $label }}">{{ __('admin.name_fr') }}</label>
                <input id="name_fr" name="name_fr" required maxlength="255" value="{{ old('name_fr', $place->name_fr) }}" class="{{ $input }}">
                @error('name_fr')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="name_ar" class="{{ $label }}">
                    {{ __('admin.name_ar') }}
                    @unless($isDistrict)<span class="font-normal text-slate-400">· {{ __('admin.optional') }}</span>@endunless
                </label>
                <input id="name_ar" name="name_ar" dir="rtl" maxlength="255" @if($isDistrict) required @endif value="{{ old('name_ar', $place->name_ar) }}" class="{{ $input }}">
                @error('name_ar')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>

            <fieldset>
                <legend class="{{ $label }}">{{ __('admin.position') }}</legend>
                <div class="grid grid-cols-2 gap-2" dir="ltr">
                    <input id="latitude" name="latitude" inputmode="decimal" value="{{ $lat }}" placeholder="{{ __('admin.latitude') }}" aria-label="{{ __('admin.latitude') }}" class="{{ $input }} tabular-nums">
                    <input id="longitude" name="longitude" inputmode="decimal" value="{{ $lng }}" placeholder="{{ __('admin.longitude') }}" aria-label="{{ __('admin.longitude') }}" class="{{ $input }} tabular-nums">
                </div>
                @error('latitude')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                @error('longitude')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                <p class="mt-2 text-xs leading-5 text-slate-500">{{ __('admin.position_hint') }}</p>
                <button type="button" id="clear-point" class="mt-2 text-xs font-medium text-slate-500 underline-offset-2 hover:text-slate-800 hover:underline">{{ __('admin.position_clear') }}</button>
            </fieldset>

            <div class="flex gap-2 border-t border-slate-100 pt-4">
                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-red-700">{{ __('admin.save') }}</button>
                <a href="{{ route('admin.cities.places', $city) }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">{{ __('admin.cancel') }}</a>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white lg:col-span-3">
            <div id="place-map" class="h-80 lg:h-full lg:min-h-[28rem]" dir="ltr"
                 data-center='@json($center)'
                 data-neighbours='@json($neighbours)'
                 data-boundary='@json($city->boundary)'
                 data-zoom="{{ $isDistrict ? 12 : 14 }}"></div>
            <p class="flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-slate-200 px-4 py-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-2">
                    <span class="inline-block h-2.5 w-2.5 rounded-full border-2 border-slate-700 bg-white"></span>
                    {{ $isDistrict ? __('admin.map_other_districts') : __('admin.map_other_quartiers') }}
                </span>
                @if($city->boundary)
                    <span class="inline-flex items-center gap-2">
                        <span class="inline-block h-0 w-4 border-t-2 border-dashed border-red-600"></span>
                        {{ __('admin.map_city_boundary', ['city' => $city->display_name]) }}
                    </span>
                @endif
            </p>
        </div>
    </form>
</div>

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function () {
            const el = document.getElementById('place-map');
            if (!el || typeof L === 'undefined') return;

            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const center = JSON.parse(el.dataset.center || 'null');
            const neighbours = JSON.parse(el.dataset.neighbours || '[]');
            const current = () => {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                return Number.isFinite(lat) && Number.isFinite(lng) ? [lat, lng] : null;
            };

            const map = L.map(el).setView(current() || center || [31.8, -7.1], current() || center ? Number(el.dataset.zoom) : 6);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; OpenStreetMap' }).addTo(map);

            // The city's boundary: points outside it are refused, and reports outside it don't belong to the city.
            const boundary = JSON.parse(el.dataset.boundary || 'null');
            if (boundary) {
                L.geoJSON(boundary, { interactive: false, style: { color: '#dc2626', weight: 2, dashArray: '6 4', fillColor: '#dc2626', fillOpacity: 0.04 } }).addTo(map);
            }

            // The other places: the automatic location picks whichever point is nearest to a report.
            neighbours.forEach((place) => {
                L.circleMarker([place.lat, place.lng], { radius: 7, color: '#334155', weight: 2.5, fillColor: '#ffffff', fillOpacity: 1 })
                    .bindTooltip(place.name, { direction: 'top', offset: [0, -6] })
                    .addTo(map);
            });

            const icon = L.icon({
                iconUrl: @js(asset('images/marker-icon.png')),
                iconRetinaUrl: @js(asset('images/marker-icon-2x.png')),
                shadowUrl: @js(asset('images/marker-shadow.png')),
                iconSize: [25, 41], iconAnchor: [12, 41], shadowSize: [41, 41],
            });
            let marker = null;

            function place(latlng, { pan = false } = {}) {
                latInput.value = latlng[0].toFixed(6);
                lngInput.value = latlng[1].toFixed(6);
                if (!marker) {
                    marker = L.marker(latlng, { icon, draggable: true }).addTo(map);
                    marker.on('dragend', () => {
                        const { lat, lng } = marker.getLatLng();
                        place([lat, lng]);
                    });
                } else {
                    marker.setLatLng(latlng);
                }
                if (pan) map.panTo(latlng);
            }

            if (current()) place(current());
            map.on('click', (event) => place([event.latlng.lat, event.latlng.lng]));

            // Typing coordinates moves the pin too.
            [latInput, lngInput].forEach((input) => input.addEventListener('change', () => {
                if (current()) place(current(), { pan: true });
            }));

            document.getElementById('clear-point').addEventListener('click', () => {
                latInput.value = '';
                lngInput.value = '';
                if (marker) { marker.remove(); marker = null; }
            });

            // The map sits in a grid cell whose height settles after layout.
            setTimeout(() => map.invalidateSize(), 100);
        })();
    </script>
@endpush
@endsection
