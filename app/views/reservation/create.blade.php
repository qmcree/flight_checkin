<form method="post">
    <input type="date" name="date" id="date" />
    <input type="text" name="confirmation_number" id="confirmation-number" />
    <input type="text" name="first_name" id="first-name" />
    <input type="text" name="last_name" id="last-name" />
    <input type="email" name="email" id="email" />
    <select name="airport_id" id="airport">
        @foreach ($airports as $airport)
        <option value="{{ $airport['attributes']['id'] }}">{{{ $airport['attributes']['abbreviation'] }}} - {{{ $airport['attributes']['name'] }}}</option>
        @endforeach
    </select>
</form>