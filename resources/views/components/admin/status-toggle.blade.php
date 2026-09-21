@props([
    // PATCH endpoint that flips the record.
    'action',
    // Current state.
    'active' => false,
    // Field name the endpoint validates (cities and users both use "active").
    'field' => 'active',
    // Used in the toast: "Casablanca activée".
    'label' => '',
    // Grammatical agreement for the French toast ("activé" / "activée").
    'feminine' => false,
])

{{--
    Replaces the old "select + confirm() + full page reload" pattern.

    The switch flips immediately, the request goes out in the background, and a
    toast offers an undo. If the request fails the switch rolls back on its own,
    so the UI never claims a change the server did not accept.

    Inside a <form> so it still works without JavaScript: admin.js intercepts
    the submit, and without JS the hidden input posts the flipped value normally.
--}}
<form method="POST"
      action="{{ $action }}"
      data-status-toggle
      data-label="{{ $label }}"
      data-feminine="{{ $feminine ? '1' : '0' }}"
      class="inline-flex items-center gap-3">
    @csrf
    @method('PATCH')
    <input type="hidden" name="{{ $field }}" value="{{ $active ? 0 : 1 }}" data-status-value>

    <button type="submit"
            role="switch"
            aria-checked="{{ $active ? 'true' : 'false' }}"
            data-status-switch
            class="group relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 disabled:cursor-wait disabled:opacity-60 {{ $active ? 'bg-emerald-500' : 'bg-slate-300' }}">
        <span class="sr-only">{{ $label ? __('Changer le statut de') . ' ' . $label : __('Changer le statut') }}</span>
        <span aria-hidden="true"
              data-status-knob
              class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $active ? 'translate-x-5' : 'translate-x-0' }}"></span>
    </button>

    <span data-status-text
          class="text-sm font-medium tabular-nums {{ $active ? 'text-emerald-700' : 'text-slate-500' }}">
        {{ $active ? 'Actif' : 'Inactif' }}
    </span>
</form>
