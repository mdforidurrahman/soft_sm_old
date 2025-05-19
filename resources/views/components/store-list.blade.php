
@if($stores->count() > 0)
    @foreach($stores as $store)
        <div class="store-item">
            {{ $store->name }}
        </div>
    @endforeach
@else
    <span class="text-muted">No stores assigned</span>
@endif