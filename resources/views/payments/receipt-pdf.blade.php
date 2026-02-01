<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
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
        
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 20px;
        }
        
        .receipt-header h1 {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 5px;
        }
        
        .receipt-header .subtitle {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        .receipt-number {
            background-color: #ecf0f1;
            padding: 10px 20px;
            border-radius: 4px;
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .receipt-meta {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
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
            font-weight: 600;
        }
        
        .section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
        
        .info-block {
            font-size: 13px;
            margin-bottom: 15px;
        }
        
        .info-block p {
            margin: 3px 0;
        }
        
        .info-block strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .payment-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-left: 4px solid #27ae60;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .payment-details-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            font-size: 13px;
        }
        
        .payment-details-label {
            font-weight: 500;
            color: #2c3e50;
            flex: 1;
        }
        
        .payment-details-value {
            font-weight: 600;
            color: #27ae60;
            text-align: right;
            flex: 1;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
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
        
        .amount-due {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: center;
        }
        
        .amount-due-label {
            font-size: 12px;
            color: #856404;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .amount-due-value {
            font-size: 32px;
            font-weight: bold;
            color: #856404;
        }
        
        .notes {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #2c3e50;
            margin: 20px 0;
            font-size: 12px;
        }
        
        .notes strong {
            display: block;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            text-align: center;
            font-size: 11px;
            color: #7f8c8d;
        }
        
        .thank-you {
            font-size: 16px;
            font-weight: 600;
            color: #27ae60;
            margin-bottom: 10px;
        }
        
        .text-right {
            text-align: right;
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
        <div class="receipt-header">
            <h1>Payment Receipt</h1>
            <div class="subtitle">Love & Styles Rental System</div>
            <div class="receipt-number">Receipt #{{ $payment->payment_id }}</div>
        </div>
        
        <div class="receipt-meta">
            <div class="meta-item">
                <strong>Payment Date:</strong>
                {{ \Carbon\Carbon::parse($payment->payment_date)->format('F d, Y H:i A') }}
            </div>
            <div class="meta-item">
                <strong>Processed By:</strong>
                {{ $payment->processedBy->name ?? 'System' }}
            </div>
            <div class="meta-item">
                <strong>Status:</strong>
                <span class="status-badge status-{{ strtolower($payment->status->status_name ?? 'pending') }}">
                    {{ $payment->status->status_name ?? 'Pending' }}
                </span>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Payment From</div>
            <div class="info-block">
                <strong>{{ $payment->invoice->customer->first_name }} {{ $payment->invoice->customer->last_name }}</strong>
                <p>Email: {{ $payment->invoice->customer->email }}</p>
                <p>Phone: {{ $payment->invoice->customer->phone ?? 'N/A' }}</p>
                <p>Customer ID: #{{ $payment->invoice->customer->customer_id }}</p>
            </div>
        </div>
        
        <div class="section">
            <div class="section-title">Invoice Details</div>
            <div class="info-block">
                <p><strong>Invoice Number:</strong> #{{ $payment->invoice->invoice_number }}</p>
                <p><strong>Invoice Date:</strong> {{ \Carbon\Carbon::parse($payment->invoice->invoice_date)->format('F d, Y') }}</p>
                <p><strong>Invoice Total:</strong> ₱{{ number_format($payment->invoice->total_amount ?? 0, 2) }}</p>
            </div>
        </div>
        
        <div class="payment-details">
            <div class="payment-details-row">
                <span class="payment-details-label">Payment Method:</span>
                <span class="payment-details-value">{{ ucfirst($payment->payment_method ?? 'N/A') }}</span>
            </div>
            <div class="payment-details-row">
                <span class="payment-details-label">Amount Paid:</span>
                <span class="payment-details-value">₱{{ number_format($payment->amount ?? 0, 2) }}</span>
            </div>
            @if($payment->reference_number)
            <div class="payment-details-row">
                <span class="payment-details-label">Reference Number:</span>
                <span class="payment-details-value">{{ $payment->reference_number }}</span>
            </div>
            @endif
        </div>
        
        @if($payment->invoice->balance_due > 0)
        <div class="amount-due">
            <div class="amount-due-label">Remaining Balance Due</div>
            <div class="amount-due-value">₱{{ number_format($payment->invoice->balance_due, 2) }}</div>
        </div>
        @else
        <div style="background-color: #d4edda; padding: 15px; border-radius: 4px; text-align: center; margin: 20px 0;">
            <div style="color: #155724; font-size: 14px; font-weight: 600;">✓ Invoice Fully Paid</div>
        </div>
        @endif
        
        @if($payment->notes)
        <div class="notes">
            <strong>Notes:</strong>
            {{ $payment->notes }}
        </div>
        @endif
        
        <div class="footer">
            <div class="thank-you">Thank You For Your Payment!</div>
            <p>This receipt serves as proof of payment for the transaction above.</p>
            <p style="margin-top: 10px; font-size: 10px;">Generated on {{ now()->format('F d, Y H:i:s') }}</p>
            <p style="margin-top: 15px; border-top: 1px solid #ecf0f1; padding-top: 10px;">For inquiries, please contact our office at {{ $payment->invoice->customer->phone ?? 'provided number' }}</p>
        </div>
    </div>
</body>
</html>
