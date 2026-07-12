@extends('admin.layouts.master')

@section('page-title', 'Edit Tag')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Edit Tag" :items="[
            ['label' => 'Tags', 'url' => route('admin.attributes.index')],
            ['label' => 'Edit']
        ]" />

        <form action="{{ route('admin.attributes.update', $tag) }}" method="POST">
            @include('admin.content.product-management.tags.form')
        </form>
    </div>
</div>
@endsection
