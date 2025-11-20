@props([
    'rows' => 3,
    'columns' => 4,
    'showHeader' => true
])

<div class="animate-pulse">
    @if($showHeader)
        <!-- Table Header Skeleton -->
        <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-b border-gray-200 dark:border-gray-700">
            <div class="flex space-x-4">
                @for($i = 0; $i < $columns; $i++)
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded flex-1"></div>
                @endfor
            </div>
        </div>
    @endif
    
    <!-- Table Body Skeleton -->
    <div class="divide-y divide-gray-200 dark:divide-gray-700">
        @for($row = 0; $row < $rows; $row++)
            <div class="px-6 py-4">
                <div class="flex space-x-4">
                    @for($col = 0; $col < $columns; $col++)
                        <div class="flex-1">
                            @if($col === 0)
                                <!-- First column usually has more content -->
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-2"></div>
                                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                            @elseif($col === $columns - 1)
                                <!-- Last column usually has actions -->
                                <div class="flex space-x-2">
                                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                    <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-16"></div>
                                </div>
                            @else
                                <!-- Regular columns -->
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-full"></div>
                            @endif
                        </div>
                    @endfor
                </div>
            </div>
        @endfor
    </div>
</div>
