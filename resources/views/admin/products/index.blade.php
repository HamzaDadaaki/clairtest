@extends('layouts.admin', ['title' => 'Manage Products | Afayar'])

@section('content')
<section class="admin-panel-card dashboard-header-card">
    <div>
        <span class="eyebrow">Product management</span>
        <h1>Products dashboard</h1>
        <p>You can add new products, edit descriptions and reviews, upload new pictures, or delete products from the live website.</p>
    </div>
    <div class="dashboard-actions">
        <a href="{{ route('admin.products.create') }}" class="admin-primary-button">Add new product</a>
    </div>
</section>

<section class="admin-stats-grid three">
    <article class="admin-stat-card">
        <strong>{{ count($products) }}</strong>
        <span>Total products</span>
    </article>
    <article class="admin-stat-card">
        <strong>{{ count(array_filter($products, fn ($item) => $item['featured'])) }}</strong>
        <span>Featured</span>
    </article>
    <article class="admin-stat-card">
        <strong>{{ count(array_filter($products, fn ($item) => $item['latest'])) }}</strong>
        <span>Latest</span>
    </article>
</section>

<section class="admin-panel-card">
    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }}" class="table-image">
                        </td>
                        <td>
                            <strong>{{ $product['name'] }}</strong>
                            <div class="table-meta">{{ $product['badge'] }}</div>
                        </td>
                        <td>{{ $product['type'] }}</td>
                        <td>{{ $product['price_label'] }}</td>
                        <td><code>{{ $product['slug'] }}</code></td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('products.show', $product['slug']) }}" class="admin-link-button" target="_blank" rel="noopener">View</a>
                                <a href="{{ route('admin.products.edit', $product['slug']) }}" class="admin-primary-button small">Edit</a>
                                <form action="{{ route('admin.products.destroy', $product['slug']) }}" method="POST" onsubmit="return confirm('Delete this product permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-danger-button small">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
