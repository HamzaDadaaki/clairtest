@if (! empty($partners))
    <div class="partners-marquee reveal-on-scroll">
        <div class="partners-track">
            @for ($i = 0; $i < 2; $i++)
                @foreach ($partners as $partner)
                    <div class="partner-card">
                        @if (! empty($partner['website']))
                            <a href="{{ $partner['website'] }}" target="_blank" rel="noopener" aria-label="{{ $partner['name'] }}">
                                <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }} logo">
                            </a>
                        @else
                            <img src="{{ asset($partner['logo']) }}" alt="{{ $partner['name'] }} logo">
                        @endif
                    </div>
                @endforeach
            @endfor
        </div>
    </div>
@else
    <div class="glass-card reveal-on-scroll">
        <p>No partners added yet.</p>
    </div>
@endif
