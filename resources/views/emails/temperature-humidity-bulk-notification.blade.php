{{-- filepath: c:\laragon\www\scm\resources\views\emails\temperature-humidity-bulk-notification.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Temperature & Humidity Summary</title>
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

        .count-badge {
            display: inline-block;
            background: #f8f9fa;
            color: #28a745;
            font-size: 48px;
            font-weight: bold;
        }

        .alert {
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-left-color: #2196f3;
            color: #0d47a1;
        }

        .alert-warning {
            background-color: #fff8e1;
            border-left-color: #ff9800;
            color: #e65100;
        }

        .cta-button {
            display: inline-block;
            background: #101399;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
            transition: transform 0.2s;
        }

        .cta-button:hover {
            background-color: #0d0d0d;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e1e5e9;
            font-size: 14px;
            color: #6c757d;
            text-align: center;
        }

        .summary-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .box-content{
            padding: 20px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="box-content">
        <div class="header">
            <img src="{{ asset('assets/images/LOGO-MEDQUEST-HD.png') }}" alt="Medquest Logo" class="logo">
        </div>

        <div class="content">
            <div class="summary-box">
                <h2 style="margin: 0;">
                    @if ($notificationType === 'review')
                        Total Pending Review
                    @else
                        TotalPending Acknowledgment
                    @endif
                </h2>
                <div class="count-badge">{{ $count }}</div>
                <p style="font-size: 18px; margin: 0;">
                    @if ($notificationType === 'review')
                        {{ $count === 1 ? 'record' : 'records' }}
                    @else
                        {{ $count === 1 ? 'record' : 'records' }}
                    @endif
                </p>
            </div>

            <div style="text-align: center;">
                <p style="font-size: 18px; margin: 0;">
                    @if ($notificationType === 'review')
                        Please log into the system to review the Temperature & Humidity records.
                    @else
                        Please log into the system to acknowledge the Temperature & Humidity records.
                    @endif
                </p>

                <a href="@if ($notificationType === 'review') {{ route('filament.dashboard.resources.temperature-humidities.reviewed') }} @else {{ route('filament.dashboard.resources.temperature-humidities.acknowledged') }} @endif" class="cta-button">
                    Login to System
                </a>
            </div>
        </div>

        <div class="footer">
            <p><strong>📧 Automated Notification</strong></p>
            <p>This is an automated summary from <br> Medquest Jaya Global Supply Chain Management System.</p>
            <p>Please do not reply to this email.</p>
        </div>
    </div>
</body>

</html>
