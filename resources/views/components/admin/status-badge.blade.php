@props([
    'status' => true,
    'trueLabel' => 'Active',
    'falseLabel' => 'Inactive',
    'trueClass' => 'bg-success',
    'falseClass' => 'bg-danger',
    'type' => null,
    'label' => null
])

@php
    $class = $type ? "bg-$type" : ($status ? $trueClass : $falseClass);
    $displayLabel = $label ?? ($status ? $trueLabel : $falseLabel);
@endphp

<span class="badge {{ $class }}">
    {{ $displayLabel }}
</span>
