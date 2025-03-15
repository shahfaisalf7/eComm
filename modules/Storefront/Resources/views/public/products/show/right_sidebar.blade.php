<aside class="right-sidebar">
    <div class="feature-list">
        @foreach ($features as $feature)
            <div class="single-feature">
                <div class="feature-icon">
                    <i class="{{ $feature->icon }}"></i>
                </div>

                <div class="feature-details">
                    <p>{{ $feature->title }}</p>

                    <span>{{ $feature->subtitle }}</span>
                </div>
            </div>
        @endforeach
    </div>
</aside>
