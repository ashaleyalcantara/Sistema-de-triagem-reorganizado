@props(['label', 'value', 'compact' => false])

<div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
    <p class="text-xs font-bold text-cyan-600 uppercase tracking-wider">{{ $label }}</p>
    <p class="font-extrabold mt-2 text-slate-800 {{ $compact ? 'text-3xl' : 'text-4xl' }}">
        {{ $value }}
    </p>
</div>
