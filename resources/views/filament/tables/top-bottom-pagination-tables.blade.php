<div class="table-header">
    @if (isset($heading) && $heading)
        <div class="px-3 py-3 sm:px-6">
            <h2 class="text-md font-medium text-gray-900 dark:text-white">
                {{ $heading }}
            </h2>
        </div>
    @endif
    
    @php
    $records = $table->getRecords()
    @endphp
    @if ($records instanceof \Illuminate\Contracts\Pagination\Paginator)
        <x-filament::pagination
            :extremeLinks="false"
            :page-options="[10, 20, 50, 100]"
            :paginator="$records"
            class="fi-ta-pagination px-3 py-3 sm:px-6"
        />
    @endif
</div>