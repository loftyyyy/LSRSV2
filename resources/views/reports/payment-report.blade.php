<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Report</title>
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
        
        .summary-card.completed {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }
        
        .summary-card.pending {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .summary-card.collected {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
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
        
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-failed {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .method-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
            background-color: #e7f3ff;
            color: #0066cc;
        }
        
        .text-right {
            text-align: right;
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
            <h1>Love & Styles Payment Report</h1>
            <p>Comprehensive Payment Activity Report</p>
        </div>
        
        <div class="meta-info">
            <div class="meta-item">
                <strong>Report Period:</strong>
                {{ $start_date->format('M d, Y') }} - {{ $end_date->format('M d, Y') }}
            </div>
            <div class="meta-item">
                <strong>Report Type:</strong>
                {{ ucfirst($report_type) }}
            </div>
            <div class="meta-item">
                <strong>Generated:</strong>
                {{ $generated_at->format('M d, Y H:i:s') }}
            </div>
        </div>
        
        <div class="summary-section">
            <div class="summary-card">
                <div class="summary-label">Total Payments</div>
                <div class="summary-value">{{ count($payments) }}</div>
            </div>
            <div class="summary-card completed">
                <div class="summary-label">Completed Payments</div>
                <div class="summary-value">{{ $payments->where('status.status_name', 'completed')->count() ?? 0 }}</div>
            </div>
            <div class="summary-card pending">
                <div class="summary-label">Pending Payments</div>
                <div class="summary-value">{{ $payments->where('status.status_name', 'pending')->count() ?? 0 }}</div>
            </div>
            <div class="summary-card collected">
                <div class="summary-label">Amount Collected</div>
                <div class="summary-value">₱{{ number_format($summary['total_amount_collected'], 2) }}</div>
            </div>
        </div>
        
        @if($payment_method_breakdown && count($payment_method_breakdown) > 0)
        <div class="breakdown-section">
            <div class="breakdown-card">
                <div class="breakdown-title">Payment Method Breakdown</div>
                @foreach($payment_method_breakdown as $method => $data)
                    <div class="breakdown-item">
                        <span>{{ ucfirst($method) }}:</span>
                        <strong>₱{{ number_format($data['total'] ?? 0, 2) }} ({{ $data['count'] ?? 0 }} payments)</strong>
                    </div>
                @endforeach
            </div>
            
            <div class="breakdown-card">
                <div class="breakdown-title">Payment Status Breakdown</div>
                <div class="breakdown-item">
                    <span>Completed:</span>
                    <strong>{{ $payments->where('status.status_name', 'completed')->count() ?? 0 }}</strong>
                </div>
                <div class="breakdown-item">
                    <span>Pending:</span>
                    <strong>{{ $payments->where('status.status_name', 'pending')->count() ?? 0 }}</strong>
                </div>
                <div class="breakdown-item">
                    <span>Failed:</span>
                    <strong>{{ $payments->where('status.status_name', 'failed')->count() ?? 0 }}</strong>
                </div>
            </div>
        </div>
        @endif
        
        <div class="section-title">Payment Details</div>
        <table>
            <thead>
                <tr>
                    <th>Payment #</th>
                    <th>Customer Name</th>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Reference</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>#{{ $payment->payment_id }}</td>
                        <td>{{ $payment->invoice->customer->first_name ?? 'N/A' }} {{ $payment->invoice->customer->last_name ?? '' }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                        <td class="text-right">₱{{ number_format($payment->amount ?? 0, 2) }}</td>
                        <td>
                            <span class="method-badge">{{ ucfirst($payment->payment_method ?? 'N/A') }}</span>
                        </td>
                        <td>{{ $payment->reference_number ?? '-' }}</td>
                        <td>
                            @php
                                $statusClass = 'status-' . strtolower($payment->status->status_name ?? 'pending');
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $payment->status->status_name ?? 'Unknown' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999;">No payments found for the selected period</td>
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
