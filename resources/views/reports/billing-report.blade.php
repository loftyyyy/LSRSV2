<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billing Report</title>
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
        
        .summary-card.pending {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .summary-card.paid {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .summary-card.balance {
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
        
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
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
            <h1>Love & Styles Billing Report</h1>
            <p>Comprehensive Invoice and Payment Report</p>
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
                <div class="summary-label">Total Invoices</div>
                <div class="summary-value">{{ $summary['total_invoices'] }}</div>
            </div>
            <div class="summary-card paid">
                <div class="summary-label">Amount Paid</div>
                <div class="summary-value">₱{{ number_format($summary['total_paid'], 2) }}</div>
            </div>
            <div class="summary-card balance">
                <div class="summary-label">Balance Due</div>
                <div class="summary-value">₱{{ number_format($summary['total_balance_due'], 2) }}</div>
            </div>
            <div class="summary-card" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                <div class="summary-label">Total Amount</div>
                <div class="summary-value">₱{{ number_format($summary['total_amount'], 2) }}</div>
            </div>
        </div>
        
        <div class="summary-section">
            <div class="summary-card">
                <div class="summary-label">Fully Paid</div>
                <div class="summary-value">{{ $summary['fully_paid_count'] }}</div>
            </div>
            <div class="summary-card pending">
                <div class="summary-label">Pending Payment</div>
                <div class="summary-value">{{ $summary['pending_payment_count'] }}</div>
            </div>
        </div>
        
        <div class="section-title">Invoice Details</div>
        <table>
            <thead>
                <tr>
                    <th>Invoice #</th>
                    <th>Customer Name</th>
                    <th>Invoice Date</th>
                    <th>Total Amount</th>
                    <th>Amount Paid</th>
                    <th>Balance Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>#{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                        <td class="text-right">₱{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                        <td class="text-right">₱{{ number_format($invoice->amount_paid ?? 0, 2) }}</td>
                        <td class="text-right">₱{{ number_format($invoice->balance_due ?? 0, 2) }}</td>
                        <td>
                            @php
                                $statusClass = 'status-';
                                if ($invoice->balance_due <= 0) {
                                    $statusClass .= 'paid';
                                    $statusText = 'Paid';
                                } elseif ($invoice->due_date && \Carbon\Carbon::parse($invoice->due_date)->isPast()) {
                                    $statusClass .= 'overdue';
                                    $statusText = 'Overdue';
                                } else {
                                    $statusClass .= 'pending';
                                    $statusText = 'Pending';
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999;">No invoices found for the selected period</td>
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
