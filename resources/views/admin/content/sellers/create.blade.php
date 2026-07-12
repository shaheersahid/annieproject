@extends('admin.layouts.master')

@section('page-title', 'Create Seller')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Create Seller" :items="[
            ['label' => 'Sellers', 'url' => route('admin.sellers.index')],
            ['label' => 'Create']
        ]" />

        <form action="{{ route('admin.sellers.store') }}" method="POST" enctype="multipart/form-data">
            @include('admin.content.sellers.form')
        </form>
    </div>
</div>
@endsection
