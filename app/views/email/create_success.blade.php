<h2>You're all set, {{{ $reservation->first_name }}}</h2>

<p>We'll automatically check you in 24 hours before your Southwest flight reservation, so you get to <strong>reap the benefits of boarding early</strong>!</p>

<p>Here's what you gave us to check you in with:</p>

<table border="0">
    <tbody>
    <tr>
        <td>Date of Flight</td>
        <td>{{{ $flight->date }}}</td>
    </tr>
    <tr>
        <td>Confirmation Number</td>
        <td>{{{ $reservation->confirmation_number }}}</td>
    </tr>
    <tr>
        <td>First Name</td>
        <td>{{{ $reservation->first_name }}}</td>
    </tr>
    <tr>
        <td>Last Name</td>
        <td>{{{ $reservation->last_name }}}</td>
    </tr>
    </tbody>
</table>