<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Customer Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
            font-size: 28px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 12px;
        }
        .info-section {
            margin-bottom: 20px;
            background: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: #f9f9f9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            border-radius: 4px;
        }
        .stat-card h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2c3e50;
            margin: 20px 0 10px 0;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th {
            background: #4CAF50;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10px;
            color: #666;
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Customer Report</h1>
        <p>Love & Styles</p>
        <p>Generated on {{ $generated_at }}</p>
    </div>

    <!-- Filter Information -->
    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Report Type:</span>
            <span>Filtered Customer Report</span>
        </div>
        @if($date_range['start'] || $date_range['end'])
        <div class="info-row">
            <span class="info-label">Date Range:</span>
            <span>
                @if($date_range['start'])
                    {{ \Carbon\Carbon::parse($date_range['start'])->format('M d, Y') }}
                @endif
                @if($date_range['start'] && $date_range['end'])
                    to
                @endif
                @if($date_range['end'])
                    {{ \Carbon\Carbon::parse($date_range['end'])->format('M d, Y') }}
                @endif
            </span>
        </div>
        @endif
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Customers</h3>
            <div class="stat-value">{{ $statistics['total_customers'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Active Customers</h3>
            <div class="stat-value">{{ $statistics['active_customers'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Inactive Customers</h3>
            <div class="stat-value">{{ $statistics['inactive_customers'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Total Rentals</h3>
            <div class="stat-value">{{ $statistics['total_rentals'] }}</div>
        </div>
    </div>

    <!-- Customer Details Table -->
    <div class="section-title">Customer Details</div>
    
    @if($customers->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Status</th>
                    <th>Total Rentals</th>
                    <th>Registration Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customers as $customer)
                    <tr>
                        <td>{{ $customer['name'] }}</td>
                        <td>{{ $customer['email'] }}</td>
                        <td>{{ $customer['contact_number'] }}</td>
                        <td>{{ ucfirst($customer['status']) }}</td>
                        <td>{{ $customer['total_rentals'] }}</td>
                        <td>{{ $customer['registration_date'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color: #999; text-align: center; padding: 20px;">No customers found for the specified filters.</p>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This report was automatically generated and contains confidential information.</p>
        <p>&copy; {{ date('Y') }} Love & Styles. All rights reserved.</p>
    </div>
</body>
</html>
