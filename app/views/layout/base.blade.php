@if (isset($_success) && !is_null($_success))
<div class="alert alert-success">
    {{ $_success }}
</div>
@endif

@if (isset($_info) && !is_null($_info))
<div class="alert alert-info">
    {{ $_info }}
</div>
@endif

@if (isset($_warning) && !is_null($_warning))
<div class="alert alert-warning">
    {{ $_warning }}
</div>
@endif

@if (isset($_danger) && !is_null($_danger))
<div class="alert alert-danger">
    {{ $_danger }}
</div>
@endif

@yield('content')