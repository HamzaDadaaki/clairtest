@extends('layouts.admin', ['title' => 'Add Product | Afayar'])

@section('content')
<section class="admin-panel-card form-page-head">
    <div>
        <span class="eyebrow">Create</span>
        <h1>Add a new product</h1>
        <p>Fill in the product information, upload product images, and add reviews that will appear on the website.</p>
    </div>
    <a href="{{ route('admin.products.index') }}" class="admin-link-button">Back to dashboard</a>
</section>

@include('admin.products._form', [
    'submitRoute' => route('admin.products.store'),
    'submitMethod' => 'POST',
    'submitLabel' => 'Create product',
])
@endsection
