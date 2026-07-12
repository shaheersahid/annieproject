@extends('admin.layouts.master')

@section('page-title', 'Edit Seller')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Edit Seller" :items="[
            ['label' => 'Sellers', 'url' => route('admin.sellers.index')],
            ['label' => 'Edit']
        ]" />

        <form action="{{ route('admin.sellers.update', $seller) }}" method="POST" enctype="multipart/form-data">
            @include('admin.content.sellers.form')
        </form>
    </div>
</div>
@endsection
