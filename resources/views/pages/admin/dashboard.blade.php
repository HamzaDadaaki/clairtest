@extends('layouts.admin', ['title' => 'Claire Stefanich Arts | Admin Dashboard'])

@section('content')
@php
    $commissionMessages = array_values(array_filter($messages, fn($message) => ($message['subject'] ?? '') === 'Commission request' || !empty($message['commission_type'])));
    $contactMessages = array_values(array_filter($messages, fn($message) => !(($message['subject'] ?? '') === 'Commission request' || !empty($message['commission_type']))));
    $stripeReady = ($settings['stripe_status'] ?? 'missing_key') === 'configured';
    $printfulReady = ($settings['printful_status'] ?? 'missing_key') === 'configured';
@endphp

<section class="kimi-admin-app">
    <aside class="kimi-admin-sidebar">
        <div class="kimi-admin-brand">
            <div class="kimi-brand-mark">CS</div>
            <div>
                <h2>Claire Studio</h2>
                <p>Admin Panel</p>
            </div>
        </div>

        <nav class="kimi-admin-tabs" aria-label="Admin sections">
            <button type="button" class="active" data-admin-tab="originals"><span>🎨</span>Originals</button>
            <button type="button" data-admin-tab="prints"><span>🖼️</span>Printful Prints</button>
            <button type="button" data-admin-tab="commissions"><span>💬</span>Commissions</button>
            <button type="button" data-admin-tab="orders"><span>💳</span>Orders</button>
            <button type="button" data-admin-tab="testimonials"><span>⭐</span>Testimonials</button>
            <button type="button" data-admin-tab="subscribers"><span>✉️</span>Email List</button>
            <button type="button" data-admin-tab="messages"><span>👥</span>Messages</button>
            <button type="button" data-admin-tab="settings"><span>⚙️</span>Settings</button>
        </nav>

        <div class="kimi-admin-sidebar-footer">
            <a href="{{ route('home') }}" target="_blank" rel="noopener">Open website</a>
            <form method="post" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"><span>↩</span> Sign Out</button>
            </form>
        </div>
    </aside>

    <main class="kimi-admin-main">
        <header class="kimi-admin-topbar">
            <div>
                <span class="kimi-eyebrow">Studio dashboard</span>
                <h1>Claire Stefanich Arts control panel</h1>
                <p>Manage artworks, Printful prints, Stripe orders, commissions, testimonials, messages, and email subscribers.</p>
            </div>
        </header>

        <div class="kimi-stat-grid">
            <article><span>Products</span><strong>{{ $stats['products'] }}</strong><small>Original artworks</small></article>
            <article><span>Printful</span><strong>{{ $stats['printful'] }}</strong><small>Synced print items</small></article>
            <article><span>Orders</span><strong>{{ $stats['orders'] }}</strong><small>Stripe/manual orders</small></article>
            <article><span>Commissions</span><strong>{{ count($commissionMessages) }}</strong><small>Client requests</small></article>
            <article><span>Email List</span><strong>{{ $stats['subscribers'] }}</strong><small>Subscribers</small></article>
        </div>

        <section class="kimi-admin-panel active" data-admin-panel="originals">
            <div class="kimi-panel-card">
                <div class="kimi-card-head">
                    <div>
                        <span class="kimi-eyebrow">Admin dashboard</span>
                        <h2>{{ $editing ? 'Edit Artwork' : 'Add Artwork' }}</h2>
                    </div>
                    <div class="kimi-muted-badge">{{ count($products) }} artwork{{ count($products) === 1 ? '' : 's' }} in catalog</div>
                </div>

                @if($editing)
                    <div class="kimi-note">Editing: <strong>{{ $editing['name'] }}</strong></div>
                @endif

                <form class="kimi-admin-form" method="post" enctype="multipart/form-data" action="{{ $editing ? route('admin.products.update', $editing['slug']) : route('admin.products.store') }}">
                    @csrf
                    @if($editing) @method('PUT') @endif

                    <div class="kimi-form-grid four">
                        <div>
                            <label>Title</label>
                            <input type="text" name="name" placeholder="Artwork title" value="{{ old('name', $editing['name'] ?? '') }}" required>
                            @error('name')<small class="field-error">{{ $message }}</small>@enderror
                        </div>
                        <div>
                            <label>Category</label>
                            <select name="category" required>
                                @foreach($categoryLabels as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category', $editing['category'] ?? 'original') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Price</label>
                            <input type="text" name="price" placeholder="Example: $120" value="{{ old('price', $editing['price'] ?? '') }}" required>
                            @error('price')<small class="field-error">{{ $message }}</small>@enderror
                        </div>
                        <div>
                            <label>Status</label>
                            <select name="status" required>
                                @foreach($statusLabels as $key => $label)
                                    <option value="{{ $key }}" @selected(old('status', $editing['status'] ?? 'for_sale') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="kimi-form-grid three">
                        <div>
                            <label>Medium / Tag</label>
                            <input type="text" name="tag" placeholder="Watercolor on paper" value="{{ old('tag', $editing['tag'] ?? '') }}">
                        </div>
                        <div>
                            <label>Dimensions</label>
                            <input type="text" name="size" placeholder="20 x 14 cm" value="{{ old('size', $editing['size'] ?? '') }}">
                        </div>
                        <div>
                            <label>Button label</label>
                            <input type="text" name="button_label" placeholder="Ask to buy" value="{{ old('button_label', $editing['button_label'] ?? '') }}">
                        </div>
                    </div>

                    <div class="kimi-form-grid two">
                        <div>
                            <label>Description</label>
                            <textarea name="description" rows="5" placeholder="Short description" required>{{ old('description', $editing['description'] ?? '') }}</textarea>
                        </div>
                        <div>
                            <label>Story / Inspiration</label>
                            <textarea name="story" rows="5" placeholder="Story or inspiration">{{ old('story', $editing['story'] ?? '') }}</textarea>
                        </div>
                    </div>

                    @if($editing && !empty($editing['images']))
                        <div class="kimi-upload-box">
                            <span class="kimi-eyebrow">Current images</span>
                            <div class="kimi-image-grid">
                                @foreach($editing['images'] as $image)
                                    <label>
                                        <input type="checkbox" name="existing_images[]" value="{{ $image }}" checked>
                                        <img src="{{ asset($image) }}" alt="{{ $editing['name'] }} image">
                                        <small>Keep this image</small>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="kimi-upload-box">
                        <label>Upload image</label>
                        <input type="file" name="new_images[]" accept="image/*" multiple {{ $editing ? '' : 'required' }}>
                        <small>Add multiple images to create a gallery on the product page.</small>
                    </div>

                    <label class="kimi-check-line">
                        <input type="checkbox" name="featured" value="1" @checked(old('featured', $editing['featured'] ?? true))>
                        Feature on homepage
                    </label>

                    <div class="kimi-form-actions">
                        <button class="kimi-gradient-button" type="submit">{{ $editing ? 'Save changes' : 'Create product' }}</button>
                        @if($editing)
                            <a class="kimi-soft-button" href="{{ route('admin.dashboard') }}">Cancel edit</a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="kimi-product-admin-grid">
                @forelse($products as $product)
                    <article class="kimi-product-admin-card">
                        <img src="{{ asset($product['images'][0] ?? 'assets/images/10.png') }}" alt="{{ $product['name'] }}">
                        <div>
                            <span>{{ $categoryLabels[$product['category']] ?? $product['category'] }}</span>
                            <h3>{{ $product['name'] }}</h3>
                            <p>{{ $product['price'] }} · {{ $statusLabels[$product['status']] ?? $product['status'] }}</p>
                        </div>
                        <div class="kimi-card-actions">
                            <a href="{{ route('products.show', $product['slug']) }}" target="_blank" rel="noopener">View</a>
                            <a href="{{ route('admin.products.edit', $product['slug']) }}">Edit</a>
                            <form method="post" action="{{ route('admin.products.delete', $product['slug']) }}" onsubmit="return confirm('Delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <div class="kimi-empty">No products yet.</div>
                @endforelse
            </div>
        </section>

        <section class="kimi-admin-panel" data-admin-panel="prints">
            <div class="kimi-panel-card">
                <div class="kimi-card-head">
                    <div>
                        <span class="kimi-eyebrow">Printful</span>
                        <h2>Print-on-demand sync</h2>
                        <p>Sync products from Printful so they appear on the Prints page.</p>
                    </div>
                    <form method="post" action="{{ route('admin.printful.sync') }}">
                        @csrf
                        <button class="kimi-gradient-button" type="submit">Sync Printful</button>
                    </form>
                </div>
                <div class="kimi-integration-status {{ $printfulReady ? 'ready' : 'missing' }}">
                    <strong>{{ $printfulReady ? 'API key configured' : 'Missing API key' }}</strong>
                    <span>{{ $printfulReady ? 'PRINTFUL_API_KEY found in .env.' : 'Add PRINTFUL_API_KEY to .env, then sync again.' }}</span>
                </div>
            </div>

            <div class="kimi-product-admin-grid">
                @forelse($printfulProducts as $product)
                    <article class="kimi-product-admin-card">
                        @if(!empty($product['thumbnail_url']))
                            <img src="{{ $product['thumbnail_url'] }}" alt="{{ $product['name'] }}">
                        @else
                            <div class="kimi-product-placeholder">Print</div>
                        @endif
                        <div>
                            <span>Printful ID: {{ $product['id'] }}</span>
                            <h3>{{ $product['name'] }}</h3>
                            <p>{{ $product['price'] }} · {{ $product['status'] }} · {{ $product['synced_at'] }}</p>
                            @if(!empty($product['default_variant_id']))<small>Default variant: {{ $product['default_variant_id'] }}</small>@endif
                        </div>
                    </article>
                @empty
                    <div class="kimi-empty">No Printful products synced yet. Press Sync Printful after adding the API key.</div>
                @endforelse
            </div>
        </section>

        <section class="kimi-admin-panel" data-admin-panel="commissions">
            <div class="kimi-panel-card">
                <div class="kimi-card-head"><div><span class="kimi-eyebrow">Commissions</span><h2>Commission requests</h2></div></div>
                <div class="kimi-list-stack">
                    @forelse($commissionMessages as $message)
                        <article class="kimi-message-card">
                            <div>
                                <strong>{{ $message['name'] }}</strong>
                                <small>{{ $message['email'] }} @if(!empty($message['phone'])) · {{ $message['phone'] }} @endif</small>
                            </div>
                            <p>{{ $message['message'] }}</p>
                            <small>Type: {{ $message['commission_type'] ?? 'Commission' }} @if(!empty($message['budget'])) · Budget: {{ $message['budget'] }} @endif @if(!empty($message['deadline'])) · Deadline: {{ $message['deadline'] }} @endif</small>
                        </article>
                    @empty
                        <p class="kimi-empty">No commission requests yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="kimi-admin-panel" data-admin-panel="orders">
            <div class="kimi-panel-card">
                <div class="kimi-card-head"><div><span class="kimi-eyebrow">Orders</span><h2>Stripe / Printful orders</h2></div></div>
                <div class="kimi-list-stack">
                    @forelse($orders as $order)
                        <article class="kimi-order-card">
                            <div class="kimi-order-head">
                                <div>
                                    <strong>{{ $order['id'] }}</strong>
                                    <small>{{ $order['customer']['first_name'] ?? 'Customer' }} {{ $order['customer']['last_name'] ?? '' }} · {{ $order['customer']['email'] ?? '' }}</small>
                                </div>
                                <span>{{ $order['subtotal'] ?? 0 }} {{ strtoupper($settings['currency'] ?? 'USD') }}</span>
                            </div>
                            <ul>
                                @foreach($order['items'] ?? [] as $item)
                                    <li>{{ $item['name'] ?? 'Item' }} <small>· {{ $item['price'] ?? '' }}</small></li>
                                @endforeach
                            </ul>
                            <form method="post" action="{{ route('admin.orders.update', $order['id']) }}" class="kimi-mini-form">
                                @csrf
                                @method('PUT')
                                <input name="payment_status" value="{{ $order['payment_status'] ?? 'pending' }}" placeholder="Payment status">
                                <input name="fulfillment_status" value="{{ $order['fulfillment_status'] ?? 'not_sent' }}" placeholder="Fulfillment status">
                                <input name="admin_note" value="{{ $order['admin_note'] ?? '' }}" placeholder="Admin note">
                                <button type="submit">Update</button>
                            </form>
                            <form method="post" action="{{ route('admin.orders.delete', $order['id']) }}" onsubmit="return confirm('Delete this order?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="kimi-delete-link kimi-danger-action">Remove order</button>
                            </form>
                        </article>
                    @empty
                        <p class="kimi-empty">No orders yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="kimi-admin-panel" data-admin-panel="testimonials">
            <div class="kimi-panel-card">
                <div class="kimi-card-head"><div><span class="kimi-eyebrow">Testimonials</span><h2>Add testimonial</h2></div></div>
                <form class="kimi-admin-form" method="post" enctype="multipart/form-data" action="{{ route('admin.testimonials.store') }}">
                    @csrf
                    <div class="kimi-form-grid two">
                        <div><label>Name</label><input name="name" required placeholder="Client name"></div>
                        <div><label>Context</label><input name="context" placeholder="Collector / commission client"></div>
                    </div>
                    <div><label>Testimonial</label><textarea name="body" rows="4" required></textarea></div>
                    <div class="kimi-form-grid two">
                        <div><label>Client image</label><input type="file" name="image" accept="image/*"></div>
                        <label class="kimi-check-line"><input type="checkbox" name="featured" value="1"> Feature on homepage</label>
                    </div>
                    <button class="kimi-gradient-button" type="submit">Add testimonial</button>
                </form>
            </div>
            <div class="kimi-list-stack">
                @foreach($testimonials as $testimonial)
                    <article class="kimi-message-card">
                        <div><strong>{{ $testimonial['name'] }}</strong><small>{{ $testimonial['context'] }}</small></div>
                        <p>{{ $testimonial['body'] }}</p>
                        <form method="post" action="{{ route('admin.testimonials.delete', $testimonial['id']) }}" onsubmit="return confirm('Delete this testimonial?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="kimi-delete-link">Delete</button>
                        </form>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="kimi-admin-panel" data-admin-panel="subscribers">
            <div class="kimi-panel-card">
                <div class="kimi-card-head">
                    <div><span class="kimi-eyebrow">Email list</span><h2>Subscribers</h2></div>
                    <a class="kimi-soft-button" href="{{ route('admin.subscribers.export') }}">Export CSV</a>
                </div>
                <div class="kimi-table-wrap">
                    <table>
                        <thead><tr><th>Name</th><th>Email</th><th>Date</th><th></th></tr></thead>
                        <tbody>
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td>{{ $subscriber['name'] ?: 'No name' }}</td>
                                <td>{{ $subscriber['email'] }}</td>
                                <td>{{ $subscriber['created_at'] }}</td>
                                <td>
                                    <form method="post" action="{{ route('admin.subscribers.delete', $subscriber['id']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">No subscribers yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <section class="kimi-admin-panel" data-admin-panel="messages">
            <div class="kimi-panel-card">
                <div class="kimi-card-head"><div><span class="kimi-eyebrow">Messages</span><h2>Contact inbox</h2></div></div>
                <div class="kimi-list-stack">
                    @forelse($contactMessages as $message)
                        <article class="kimi-message-card">
                            <div><strong>{{ $message['name'] }}</strong><small>{{ $message['email'] }} @if(!empty($message['phone'])) · {{ $message['phone'] }} @endif</small></div>
                            <h3>{{ $message['subject'] ?: 'Website message' }}</h3>
                            <p>{{ $message['message'] }}</p>
                            <a href="mailto:{{ $message['email'] }}?subject={{ rawurlencode('Re: '.($message['subject'] ?: 'Website message')) }}">Reply by email</a>
                        </article>
                    @empty
                        <p class="kimi-empty">No contact messages yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="kimi-admin-panel" data-admin-panel="settings">
            <div class="kimi-panel-card">
                <div class="kimi-card-head"><div><span class="kimi-eyebrow">Settings</span><h2>Website settings</h2></div></div>
                <form class="kimi-admin-form" method="post" action="{{ route('admin.settings.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="kimi-form-grid two">
                        <div><label>Store email</label><input type="email" name="store_email" value="{{ $settings['store_email'] ?? '' }}" required></div>
                        <div><label>Currency</label><input type="text" name="currency" value="{{ $settings['currency'] ?? 'usd' }}" required></div>
                        <div><label>Instagram URL</label><input type="url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}"></div>
                        <div><label>TikTok URL</label><input type="url" name="tiktok_url" value="{{ $settings['tiktok_url'] ?? '' }}"></div>
                    </div>
                    <button class="kimi-gradient-button" type="submit">Save settings</button>
                </form>
            </div>

            <div class="kimi-form-grid two">
                <article class="kimi-panel-card">
                    <span class="kimi-eyebrow">Stripe</span>
                    <h2>{{ $stripeReady ? 'Stripe connected' : 'Stripe missing' }}</h2>
                    <p>{{ $stripeReady ? 'STRIPE_SECRET_KEY is configured.' : 'Add STRIPE_SECRET_KEY in .env to enable Stripe checkout.' }}</p>
                </article>
                <article class="kimi-panel-card">
                    <span class="kimi-eyebrow">Printful SSL</span>
                    <h2>Local certificate fix included</h2>
                    <p>If Windows/PHP has no CA bundle, set <code>PRINTFUL_SSL_VERIFY=false</code> for local testing only. Use true on real hosting.</p>
                </article>
            </div>
        </section>
    </main>
</section>
@endsection
