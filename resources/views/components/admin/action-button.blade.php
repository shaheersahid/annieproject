@props([
    'type' => 'edit', // edit, delete, view
    'url' => '#',
    'id' => null,
    'tooltip' => null
])

@php
    $configs = [
        'edit' => [
            'icon' => 'fas fa-edit',
            'class' => 'btn-outline-primary',
            'defaultTooltip' => 'Edit'
        ],
        'seo' => [
            'icon' => 'fas fa-globe',
            'class' => 'btn-outline-secondary',
            'defaultTooltip' => 'SEO'
        ],
        'sort' => [
            'icon' => 'fas fa-list',
            'class' => 'btn-outline-secondary',
            'defaultTooltip' => 'Sort'
        ],
        'delete' => [
            'icon' => 'fas fa-trash-alt',
            'class' => 'btn-outline-danger del_confirm',
            'defaultTooltip' => 'Delete'
        ],
        'view' => [
            'icon' => 'fas fa-eye',
            'class' => 'btn-outline-info',
            'defaultTooltip' => 'View'
        ]
    ];
    $typeConfig = $configs[$type] ?? $configs['edit'];
@endphp


@if($type === 'delete')
    <form action="{{ $url }}" method="POST" class="d-inline del_confirm">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm {{ $typeConfig['class'] }}" 
            data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltip ?? $typeConfig['defaultTooltip'] }}">
            <i class="{{ $typeConfig['icon'] }}"></i>
        </button>
    </form>
@else
    <a href="{{ $url }}" class="btn btn-sm {{ $typeConfig['class'] }}"
       data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltip ?? $typeConfig['defaultTooltip'] }}">
        <i class="{{ $typeConfig['icon'] }}"></i>
    </a>
@endif
