@foreach ($plans as $plan)
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">{{ $plan->plan_name }}</h5>
            </div>
            <div class="card-body">
                <p class="card-text">Price: {{ $plan->plan_price }}</p>
            </div>
            <div class="card-footer">
                <a href="{{ route('subscription.create', ['plan_id' => $plan->id]) }}" class="btn btn-primary">Select
                    Plan</a>
            </div>
        </div>
    </div>
@endforeach
