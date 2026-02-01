<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reservation Report</title>
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
        
        .summary-card.confirmed {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }
        
        .summary-card.pending {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .summary-card.cancelled {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .breakdown-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin: 30px 0;
        }
        
        .breakdown-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2c3e50;
        }
        
        .breakdown-title {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .breakdown-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 12px;
        }
        
        .text-right {
            text-align: right;
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
            <h1>Love & Styles Reservation Report</h1>
            <p>Comprehensive Reservation Activity Report</p>
        </div>
        
        <div class="meta-info">
            <div class="meta-item">
                <strong>Generated:</strong>
                {{ now()->format('M d, Y H:i:s') }}
            </div>
            <div class="meta-item">
                <strong>Total Reservations:</strong>
                {{ isset($summary['total_reservations']) ? $summary['total_reservations'] : 0 }}
            </div>
        </div>
        
        <div class="summary-section">
            <div class="summary-card">
                <div class="summary-label">Total Reservations</div>
                <div class="summary-value">{{ isset($summary['total_reservations']) ? $summary['total_reservations'] : 0 }}</div>
            </div>
            <div class="summary-card confirmed">
                <div class="summary-label">Confirmed</div>
                <div class="summary-value">{{ isset($summary['by_status']['confirmed']) ? $summary['by_status']['confirmed'] : 0 }}</div>
            </div>
            <div class="summary-card pending">
                <div class="summary-label">Pending</div>
                <div class="summary-value">{{ isset($summary['by_status']['pending']) ? $summary['by_status']['pending'] : 0 }}</div>
            </div>
            <div class="summary-card cancelled">
                <div class="summary-label">Cancelled</div>
                <div class="summary-value">{{ isset($summary['by_status']['cancelled']) ? $summary['by_status']['cancelled'] : 0 }}</div>
            </div>
        </div>
        
        @if(isset($summary) && count($summary) > 0)
        <div class="breakdown-section">
            <div class="breakdown-card">
                <div class="breakdown-title">Summary Statistics</div>
                <div class="breakdown-item">
                    <span>Total Items Reserved:</span>
                    <strong>{{ isset($summary['total_items_reserved']) ? $summary['total_items_reserved'] : 0 }}</strong>
                </div>
                <div class="breakdown-item">
                    <span>Total Revenue:</span>
                    <strong>₱{{ isset($summary['total_revenue']) ? number_format($summary['total_revenue'], 2) : '0.00' }}</strong>
                </div>
                <div class="breakdown-item">
                    <span>Avg Items per Reservation:</span>
                    <strong>{{ isset($summary['average_items_per_reservation']) ? $summary['average_items_per_reservation'] : 0 }}</strong>
                </div>
            </div>
            
            @if(isset($summary['by_month']) && count($summary['by_month']) > 0)
            <div class="breakdown-card">
                <div class="breakdown-title">Reservations by Month</div>
                @foreach($summary['by_month'] as $month => $count)
                    <div class="breakdown-item">
                        <span>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y') }}:</span>
                        <strong>{{ $count }}</strong>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
        @endif
        
        @if(isset($reservations) && count($reservations) > 0)
        <div class="section-title">Reservation Details</div>
        <table>
            <thead>
                <tr>
                    <th>Reservation #</th>
                    <th>Customer</th>
                    <th>Reserved By</th>
                    <th>Reservation Date</th>
                    <th>Items Count</th>
                    <th>Expected Revenue</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                    <tr>
                        <td>#{{ $reservation->reservation_id }}</td>
                        <td>{{ $reservation->customer->first_name ?? 'N/A' }} {{ $reservation->customer->last_name ?? '' }}</td>
                        <td>{{ $reservation->reservedBy->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('M d, Y') }}</td>
                        <td class="text-right">{{ $reservation->items->count() ?? 0 }}</td>
                        <td class="text-right">
                            @php
                                $revenue = 0;
                                foreach($reservation->items as $item) {
                                    $revenue += ($item->rental_price ?? 0) * ($item->quantity ?? 1);
                                }
                            @endphp
                            ₱{{ number_format($revenue, 2) }}
                        </td>
                        <td>
                            @php
                                $statusClass = 'status-' . strtolower($reservation->status->status_name ?? 'pending');
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($reservation->status->status_name ?? 'Unknown') }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999;">No reservations found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @endif
        
        <div class="footer">
            <p>This is an automated report generated by Love & Styles Rental System. All information contained herein is confidential and proprietary.</p>
        </div>
    </div>
</body>
</html>
