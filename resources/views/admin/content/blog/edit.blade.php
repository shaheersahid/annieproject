@extends('admin.layouts.master')
@section('page-title', 'Edit Blog Post')

@section('admin-content')
    <div class="page-content blog-editor-page">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Edit Post" :items="[
                ['label' => 'Blog', 'url' => route('admin.blog.index')],
                ['label' => $blog->title],
            ]" />

            @include('admin.content.blog.form', [
                'formAction' => route('admin.blog.update', $blog),
                'formMethod' => 'PUT',
                'submitLabel' => 'Update Post',
            ])
        </div>
    </div>
@endsection

@include('admin.content.blog.editor')
