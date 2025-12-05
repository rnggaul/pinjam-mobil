<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Booking HVBS</title>
</head>
<body>
    <h2>Halo Admin,</h2>
    <p>Ada beberapa booking status <b>Pending</b> yang akan dimulai dalam 3 hari kedepan. Mohon segera ditindaklanjuti</p>

    <table border="1" cellpadding="10" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #f2f2f2f2;">
                <th>ID</th>
                <th>Pemesan</th>
                <th>Status</th>
                <th>Tanggal Mulai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $booking)
                <tr>
                    <td>{{ $booking->booking_id }}</td>
                    <td>{{ $booking->user->name }}</td>
                    <td>{{ $booking->status }}</td>
                    <td style="color: orange; font-weight: bold;">{{ $booking->tanggal_mulai->format('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Silahkan melakukan login untuk melakukan Approve/Reject</p>
    <p><i>- HVBS System</i></p>
</body>
</html>