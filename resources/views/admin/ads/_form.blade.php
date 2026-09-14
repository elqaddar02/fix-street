@php
    $ad = $ad ?? null;
    $isHouse = old('provider', $ad->provider ?? \App\Models\Ad::PROVIDER_HOUSE) === \App\Models\Ad::PROVIDER_HOUSE;
@endphp

<div x-data="{ provider: '{{ old('provider', $ad->provider ?? \App\Models\Ad::PROVIDER_HOUSE) }}' }" class="space-y-6">
    <!-- Placement -->
    <div>
        <label for="ad_placement_id" class="block text-sm font-medium text-gray-700 mb-2">Emplacement</label>
        <select id="ad_placement_id" name="ad_placement_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('ad_placement_id') border-red-500 @enderror">
            @foreach($placements as $placement)
                <option value="{{ $placement->id }}" {{ (int) old('ad_placement_id', $ad->ad_placement_id ?? null) === $placement->id ? 'selected' : '' }}>
                    {{ $placement->name }} ({{ $placement->slug }})
                </option>
            @endforeach
        </select>
        @error('ad_placement_id')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Title -->
    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Titre</label>
        <input type="text" id="title" name="title" required value="{{ old('title', $ad->title ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('title') border-red-500 @enderror">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Provider -->
    <div>
        <label for="provider" class="block text-sm font-medium text-gray-700 mb-2">Fournisseur</label>
        <select id="provider" name="provider" x-model="provider" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('provider') border-red-500 @enderror">
            <option value="{{ \App\Models\Ad::PROVIDER_HOUSE }}">Bannière maison (image + lien)</option>
            <option value="{{ \App\Models\Ad::PROVIDER_ADSENSE }}">Google AdSense (à venir)</option>
        </select>
        @error('provider')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- House provider fields -->
    <div x-show="provider === '{{ \App\Models\Ad::PROVIDER_HOUSE }}'" class="space-y-6">
        <div>
            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Image / bannière</label>
            @if(($ad->image ?? null))
                <img src="{{ asset('storage/' . $ad->image) }}" alt="{{ $ad->title }}" class="mb-3 h-16 rounded border border-gray-200 object-contain">
            @endif
            <input type="file" id="image" name="image" accept="image/jpeg,image/png" class="w-full px-4 py-2 border border-gray-300 rounded-lg @error('image') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-500">JPG ou PNG, 2 Mo maximum.</p>
            @error('image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="target_url" class="block text-sm font-medium text-gray-700 mb-2">Lien de destination</label>
            <input type="url" id="target_url" name="target_url" value="{{ old('target_url', $ad->target_url ?? '') }}" placeholder="https://exemple.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('target_url') border-red-500 @enderror">
            @error('target_url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- AdSense provider fields -->
    <div x-show="provider === '{{ \App\Models\Ad::PROVIDER_ADSENSE }}'" class="space-y-6">
        <div>
            <label for="external_slot_id" class="block text-sm font-medium text-gray-700 mb-2">Identifiant du bloc AdSense</label>
            <input type="text" id="external_slot_id" name="external_slot_id" value="{{ old('external_slot_id', $ad->external_slot_id ?? '') }}" placeholder="ca-pub-XXXXXXXXXX / slot ID" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('external_slot_id') border-red-500 @enderror">
            <p class="mt-1 text-xs text-gray-500">Le rendu AdSense n'est pas encore actif sur le site — ce champ prépare l'intégration future.</p>
            @error('external_slot_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Scheduling -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
            <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">Date de début (optionnel)</label>
            <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', optional($ad->starts_at ?? null)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('starts_at') border-red-500 @enderror">
            @error('starts_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2">Date de fin (optionnel)</label>
            <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', optional($ad->ends_at ?? null)->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('ends_at') border-red-500 @enderror">
            @error('ends_at')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <!-- Weight -->
    <div>
        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Poids (rotation entre plusieurs pubs actives)</label>
        <input type="number" id="weight" name="weight" min="1" max="100" value="{{ old('weight', $ad->weight ?? 1) }}" class="w-full max-w-xs px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent @error('weight') border-red-500 @enderror">
        @error('weight')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Active -->
    <div class="flex items-center gap-3">
        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $ad->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500">
        <label for="is_active" class="text-sm font-medium text-gray-700">Publicité active</label>
    </div>
</div>
