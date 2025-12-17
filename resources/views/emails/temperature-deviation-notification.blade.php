{{-- resources/views/emails/temperature-deviation-notification.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temperature Deviation Alert</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            color: black;
            text-align: center;
        }

        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 15px;
        }

        .content {
            background-color: #fff;
            padding: 30px;
            border: 1px solid #e1e5e9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .deviation-badge {
            display: inline-block;
            background: #f8d7da;
            color: #721c24;
            font-size: 32px;
            font-weight: bold;
            padding: 15px 25px;
            border-radius: 8px;
            margin: 20px 0;
        }

        .alert {
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-left-color: #ffc107;
            color: #856404;
        }

        .cta-button {
            display: inline-block;
            background: #dc3545;
            color: #fff;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }

        .cta-button:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e1e5e9;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }

        .deviation-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .box-content{
            padding: 20px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
        }

        .priority-label {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="box-content">
        <div class="header">
            <img src="https://medquest.co.id/wp-content/uploads/2023/02/LOGO-MEDQUEST-HD-2020-11-27-14_56_44.png" alt="Medquest Logo" class="logo" style="width: 70%; height: 70%; text-align: center;">
        </div>

        <div class="content">
            @if($isHighPriority)
                <div class="priority-label">HIGH PRIORITY ALERT</div>
            @endif

            <h2 style="color: #dc3545; text-align: center;">Temperature Deviation Detected</h2>

            <p style="font-size: 16px; text-align: center;">A temperature deviation has been recorded that falls outside the acceptable range. <br>
            Please review the deviation details below :</p>

            <div class="deviation-details">
                <h3 style="text-align: center">Deviation Details</h3>
                
                <div class="detail-row">
                    <span><strong>Location:</strong> </span>
                    <span> {{ $temperatureDeviation->location->location_name ?? 'N/A' }}</span>
                </div>

                <div class="detail-row">
                    <span><strong>Room:</strong> </span>
                    <span> {{ $temperatureDeviation->room->room_name ?? 'N/A' }}</span>
                </div>

                <div class="detail-row">
                    <span><strong>Serial Number:</strong> </span>
                    <span> {{ $temperatureDeviation->serialNumber->serial_number ?? 'N/A' }}</span>
                </div>

                <div class="detail-row">
                    <span><strong>Date:</strong> </span>
                    <span> {{ date('d F Y', strtotime($temperatureDeviation->date)) . ' ' . $temperatureDeviation->time ?? 'N/A' }}</span>
                </div>

                <div class="detail-row">
                    <span><strong>Temperature Deviation:</strong> </span>
                    <span style="color: #dc3545; font-weight: bold;"> {{ $temperatureDeviation->temperature_deviation }}°C</span>
                </div>

                <div class="detail-row">
                    <span><strong>Temperature Range:</strong> </span>
                    <span> 
                        @if($temperatureDeviation->roomTemperature)
                            {{ $temperatureDeviation->roomTemperature->temperature_start }}°C to 
                            {{ $temperatureDeviation->roomTemperature->temperature_end }}°C
                        @else
                            N/A
                        @endif
                    </span>
                </div>

                <div class="detail-row">
                    <span><strong>Reason for Deviation:</strong> </span>
                    <span> {{ $temperatureDeviation->deviation_reason ?? 'N/A' }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('filament.dashboard.resources.temperature-deviations.view', ['record' => $temperatureDeviation->id]) }}" 
                class="cta-button">
                    View Deviation Details
                </a>
            </div>
        </div>

        <div class="footer">
            <p><strong>🌡️ Automated Temperature Deviation Alert</strong></p>
            <p>This is an automated alert from <br> Supply Chain Management System of PT. Medquest Jaya Global</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>