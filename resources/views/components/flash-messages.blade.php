@if (session('success'))
    <div
        class="p-4 bg-green-100 text-green-800 rounded-2xl text-sm font-semibold border border-green-200 shadow-xs flex items-center">
        <i class="fa-solid fa-circle-check text-lg mr-2 text-green-600"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('sucesso'))
    <div
        class="p-4 bg-green-100 text-green-800 rounded-2xl text-sm font-semibold border border-green-200 shadow-xs flex items-center">
        <i class="fa-solid fa-circle-check text-lg mr-2 text-green-600"></i>
        <span>{{ session('sucesso') }}</span>
    </div>
@endif

@if (session('error'))
    <div
        class="p-4 bg-amber-100 text-amber-800 rounded-2xl text-sm font-semibold border border-amber-200 shadow-xs flex items-center">
        <i class="fa-solid fa-triangle-exclamation text-lg mr-2 text-amber-600"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div
        class="p-4 bg-red-50 text-red-700 rounded-2xl text-sm font-semibold border border-red-100 shadow-xs">
        <div class="flex items-center mb-1">
            <i class="fa-solid fa-triangle-exclamation text-lg mr-2 text-red-500"></i>
            <span>Não foi possível concluir a operação:</span>
        </div>
        <ul class="list-disc list-inside space-y-1 font-medium">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
