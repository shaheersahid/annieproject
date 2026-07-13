@extends('admin.layouts.master')
@section('page-title', 'Write Blog Post')

@section('admin-content')
    <div class="page-content blog-editor-page">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Write Post" :items="[
                ['label' => 'Blog', 'url' => route('admin.blog.index')],
                ['label' => 'New Post'],
            ]" />

            @include('admin.content.blog.form', [
                'formAction' => route('admin.blog.store'),
                'formMethod' => 'POST',
                'submitLabel' => 'Save Post',
            ])
        </div>
    </div>
@endsection

@include('admin.content.blog.editor')
