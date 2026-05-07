@extends('layouts.admin', ['title' => 'Edit Product | Afayar'])

@section('content')
<section class="admin-panel-card form-page-head">
    <div>
        <span class="eyebrow">Edit</span>
        <h1>Edit {{ $product['name'] }}</h1>
        <p>Update the product details, replace pictures, change reviews, and save the product directly to the live website catalog.</p>
    </div>
    <div class="dashboard-actions">
        <a href="{{ route('products.show', $product['slug']) }}" class="admin-link-button" target="_blank" rel="noopener">Preview product</a>
        <a href="{{ route('admin.products.index') }}" class="admin-link-button">Back to dashboard</a>
    </div>
</section>

@include('admin.products._form', [
    'submitRoute' => route('admin.products.update', $product['slug']),
    'submitMethod' => 'PUT',
    'submitLabel' => 'Save changes',
])
@endsection
