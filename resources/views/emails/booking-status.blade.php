<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status Update - UX Serve</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #1E88E5 0%, #1565C0 100%);
            color: #ffffff;
            text-align: center;
            padding: 30px 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .status-box {
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .status-pending {
            background: linear-gradient(135deg, #FFF3E0 0%, #FFE0B2 100%);
            border: 2px solid #FF9800;
        }
        .status-accepted {
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            border: 2px solid #1E88E5;
        }
        .status-ongoing {
            background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
            border: 2px solid #4CAF50;
        }
        .status-completed {
            background: linear-gradient(135deg, #E8F5E9 0%, #A5D6A7 100%);
            border: 2px solid #2E7D32;
        }
        .status-cancelled {
            background: linear-gradient(135deg, #FFEBEE 0%, #FFCDD2 100%);
            border: 2px solid #F44336;
        }
        .status-label {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
            color: #666666;
        }
        .status-value {
            font-size: 24px;
            font-weight: 700;
        }
        .status-pending .status-value { color: #E65100; }
        .status-accepted .status-value { color: #1565C0; }
        .status-ongoing .status-value { color: #388E3C; }
        .status-completed .status-value { color: #2E7D32; }
        .status-cancelled .status-value { color: #C62828; }
        .booking-details {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }
        .booking-details h3 {
            margin: 0 0 15px 0;
            color: #1E88E5;
            font-size: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eeeeee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #666666;
            font-size: 14px;
        }
        .detail-value {
            color: #333333;
            font-size: 14px;
            font-weight: 500;
        }
        .info-box {
            background-color: #E3F2FD;
            border-left: 4px solid #1E88E5;
            padding: 15px;
            margin: 25px 0;
            font-size: 14px;
            color: #1565C0;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-top: 1px solid #eeeeee;
        }
        .footer p {
            margin: 5px 0;
            font-size: 13px;
            color: #999999;
        }
        .footer a {
            color: #1E88E5;
            text-decoration: none;
        }
        .brand {
            color: #1E88E5;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>UX Serve</h1>
            <p>Cool Comfort at Your Doorstep</p>
        </div>

        <div class="content">
            <p class="greeting">Hello, <strong>{{ $customerName }}</strong>!</p>

            <p class="message">
                Your booking status has been updated. Here are the details:
            </p>

            @php
                $statusClass = 'status-pending';
                $statusText = 'Pending';

                if ($status == 'accept' || $status == 'accepted') {
                    $statusClass = 'status-accepted';
                    $statusText = 'Accepted';
                } elseif ($status == 'on_going' || $status == 'ongoing') {
                    $statusClass = 'status-ongoing';
                    $statusText = 'In Progress';
                } elseif ($status == 'completed') {
                    $statusClass = 'status-completed';
                    $statusText = 'Completed';
                } elseif ($status == 'cancelled' || $status == 'rejected') {
                    $statusClass = 'status-cancelled';
                    $statusText = 'Cancelled';
                } elseif ($status == 'pending') {
                    $statusClass = 'status-pending';
                    $statusText = 'Pending';
                }
            @endphp

            <div class="status-box {{ $statusClass }}">
                <div class="status-label">Booking Status</div>
                <div class="status-value">{{ $statusText }}</div>
            </div>

            <div class="booking-details">
                <h3>Booking Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Booking ID</span>
                    <span class="detail-value">#{{ $booking->id }}</span>
                </div>
                @if(isset($booking->service) && $booking->service)
                <div class="detail-row">
                    <span class="detail-label">Service</span>
                    <span class="detail-value">{{ $booking->service->name ?? 'AC Service' }}</span>
                </div>
                @endif
                @if(isset($booking->date))
                <div class="detail-row">
                    <span class="detail-label">Scheduled Date</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</span>
                </div>
                @endif
                @if(isset($booking->total_amount))
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value">${{ number_format($booking->total_amount, 2) }}</span>
                </div>
                @endif
            </div>

            @if($status == 'accept' || $status == 'accepted')
            <div class="info-box">
                <strong>Great news!</strong> Your booking has been accepted. Our technician will arrive at the scheduled time.
            </div>
            @elseif($status == 'on_going' || $status == 'ongoing')
            <div class="info-box">
                <strong>Service in Progress!</strong> Our technician is currently working on your AC service.
            </div>
            @elseif($status == 'completed')
            <div class="info-box">
                <strong>Service Completed!</strong> Thank you for choosing UX Serve. We hope you're satisfied with our service.
            </div>
            @endif
        </div>

        <div class="footer">
            <p>If you have any questions, please contact us.</p>
            <p>Email: <a href="mailto:support@acchill.com">support@acchill.com</a></p>
            <p>&copy; {{ date('Y') }} UX Serve. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
