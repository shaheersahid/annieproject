@extends('admin.layouts.master')

@section('page-title', 'Create Tag')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Create Tag" :items="[
            ['label' => 'Tags', 'url' => route('admin.attributes.index')],
            ['label' => 'Create']
        ]" />

        <form action="{{ route('admin.attributes.store') }}" method="POST">
            @include('admin.content.product-management.tags.form')
        </form>
    </div>
</div>
@endsection
