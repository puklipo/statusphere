@props(['status'])

<span class="text-sm text-gray-500 dark:text-gray-300">
    @if(data_get($status, 'value.createdAt')->gte(today()))
        is feeling {{ data_get($status, 'value.status') }} today
    @else
        was feeling {{ data_get($status, 'value.status') }}
        on {{ data_get($status, 'value.createdAt')->diffForHumans() }}
    @endif
</span>
