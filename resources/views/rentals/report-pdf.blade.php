<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rental Report</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            max-width: 100%;
            padding: 40px;
            background-color: #fff;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            font-size: 13px;
            color: #555;
        }
        
        .meta-item {
            flex: 1;
        }
        
        .meta-item strong {
            display: block;
            color: #2c3e50;
            margin-bottom: 3px;
        }
        
        .summary-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 40px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .summary-card.active {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .summary-card.returned {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .summary-card.overdue {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .summary-value {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .summary-label {
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.9;
            letter-spacing: 1px;
        }
        
        .section-title {
            font-size: 16px;
            color: #2c3e50;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #ecf0f1;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        
        thead {
            background-color: #34495e;
            color: white;
        }
        
        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border: 1px solid #34495e;
        }
        
        td {
            padding: 10px 12px;
            border: 1px solid #ecf0f1;
        }
        
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tbody tr:hover {
            background-color: #ecf0f1;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-returned {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-cancelled {
            background-color: #e2e3e5;
            color: #383d41;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Love & Styles Rental Report</h1>
            <p>Comprehensive Rental Activity Report</p>
        </div>
        
        <div class="meta-info">
            <div class="meta-item">
                <strong>Report Period:</strong>
                @if($summary['date_from'] !== 'All')
                    {{ \Carbon\Carbon::parse($summary['date_from'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($summary['date_to'])->format('M d, Y') }}
                @else
                    All Time
                @endif
            </div>
            <div class="meta-item">
                <strong>Generated:</strong>
                {{ $generated_at }}
            </div>
        </div>
        
        <div class="summary-section">
            <div class="summary-card">
                <div class="summary-label">Total Rentals</div>
                <div class="summary-value">{{ $summary['total_rentals'] }}</div>
            </div>
            <div class="summary-card active">
                <div class="summary-label">Active Rentals</div>
                <div class="summary-value">{{ $summary['active_rentals'] }}</div>
            </div>
            <div class="summary-card returned">
                <div class="summary-label">Returned</div>
                <div class="summary-value">{{ $summary['returned_rentals'] }}</div>
            </div>
            <div class="summary-card overdue">
                <div class="summary-label">Overdue</div>
                <div class="summary-value">{{ $summary['overdue_rentals'] }}</div>
            </div>
        </div>
        
        <div class="summary-section">
            <div class="summary-card" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
                <div class="summary-label">Returned On Time</div>
                <div class="summary-value">{{ $summary['returned_on_time'] }}</div>
            </div>
            <div class="summary-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="summary-label">Returned Late</div>
                <div class="summary-value">{{ $summary['returned_overdue'] }}</div>
            </div>
            <div class="summary-card" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                <div class="summary-label">Total Penalties</div>
                <div class="summary-value">₱{{ number_format($summary['total_penalties'], 2) }}</div>
            </div>
            <div class="summary-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                <div class="summary-label">Total Revenue</div>
                <div class="summary-value">₱{{ number_format($summary['total_revenue'], 2) }}</div>
            </div>
        </div>
        
        <div class="section-title">Rental Details</div>
        <table>
            <thead>
                <tr>
                    <th>Rental ID</th>
                    <th>Customer</th>
                    <th>Item</th>
                    <th>Released Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rentals as $rental)
                    <tr>
                        <td>#{{ $rental->rental_id }}</td>
                        <td>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</td>
                        <td>{{ $rental->item->item_name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($rental->released_date)->format('M d, Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($rental->due_date)->format('M d, Y') }}</td>
                        <td>
                            @if($rental->return_date)
                                {{ \Carbon\Carbon::parse($rental->return_date)->format('M d, Y') }}
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusClass = 'status-' . strtolower($rental->status->status_name ?? 'unknown');
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $rental->status->status_name ?? 'Unknown' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999;">No rental records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="footer">
            <p>This is an automated report generated by Love & Styles Rental System. All information contained herein is confidential and proprietary.</p>
        </div>
    </div>
</body>
</html>
