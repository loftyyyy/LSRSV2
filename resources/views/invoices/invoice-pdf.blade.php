<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
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
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 20px;
        }
        
        .company-info h1 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        
        .company-info p {
            color: #7f8c8d;
            font-size: 13px;
            margin: 3px 0;
        }
        
        .invoice-meta {
            text-align: right;
            font-size: 13px;
        }
        
        .invoice-meta-label {
            color: #7f8c8d;
            font-weight: 500;
        }
        
        .invoice-meta-value {
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
        }
        
        .invoice-meta .row {
            margin: 5px 0;
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
        
        .billing-info {
            display: flex;
            gap: 40px;
        }
        
        .info-block {
            flex: 1;
            font-size: 13px;
        }
        
        .info-block p {
            margin: 3px 0;
        }
        
        .info-block strong {
            color: #2c3e50;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
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
        
        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin: 30px 0;
        }
        
        .summary-table {
            width: 350px;
            border: 1px solid #34495e;
            border-collapse: collapse;
            font-size: 12px;
        }
        
        .summary-table td {
            padding: 10px 15px;
            border: none;
        }
        
        .summary-label {
            width: 60%;
            font-weight: 500;
            color: #2c3e50;
        }
        
        .summary-value {
            width: 40%;
            text-align: right;
            color: #333;
        }
        
        .summary-table tr:nth-child(n+3) {
            border-top: 1px solid #ecf0f1;
        }
        
        .summary-total {
            background-color: #34495e;
            color: white;
            font-weight: 600;
        }
        
        .summary-total .summary-label,
        .summary-total .summary-value {
            color: white;
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
        
        .notes {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #2c3e50;
            margin: 30px 0;
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
        <div class="invoice-header">
            <div class="company-info">
                <h1>{{ $company['name'] }}</h1>
                <p>{{ $company['address'] }}</p>
                <p>{{ $company['phone'] }}</p>
                <p>{{ $company['email'] }}</p>
            </div>
            <div class="invoice-meta">
                <div class="row">
                    <span class="invoice-meta-label">Invoice Number:</span>
                    <span class="invoice-meta-value">#{{ $invoice->invoice_number }}</span>
                </div>
                <div class="row">
                    <span class="invoice-meta-label">Invoice Date:</span>
                    <span class="invoice-meta-value">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</span>
                </div>
                <div class="row">
                    <span class="invoice-meta-label">Due Date:</span>
                    <span class="invoice-meta-value">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="row" style="margin-top: 10px;">
                    <span class="status-badge status-{{ strtolower($invoice->status->status_name ?? 'pending') }}">
                        {{ $invoice->status->status_name ?? 'Pending' }}
                    </span>
                </div>
            </div>
        </div>
        
        <div class="section">
            <div class="billing-info">
                <div class="info-block">
                    <div class="section-title">Bill To:</div>
                    <strong>{{ $invoice->customer->first_name }} {{ $invoice->customer->last_name }}</strong>
                    <p>Email: {{ $invoice->customer->email }}</p>
                    <p>Phone: {{ $invoice->customer->phone ?? 'N/A' }}</p>
                    <p>Address: {{ $invoice->customer->address ?? 'N/A' }}</p>
                </div>
                <div class="info-block">
                    <div class="section-title">Invoice Details:</div>
                    <p><strong>Invoice Type:</strong> {{ ucfirst($invoice->invoice_type ?? 'N/A') }}</p>
                    @if($invoice->rental)
                        <p><strong>Rental ID:</strong> #{{ $invoice->rental->rental_id }}</p>
                    @endif
                    @if($invoice->reservation)
                        <p><strong>Reservation ID:</strong> #{{ $invoice->reservation->reservation_id }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        @if($invoice->invoiceItems && $invoice->invoiceItems->count() > 0)
        <div class="section">
            <div class="section-title">Invoice Items</div>
            <table>
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Item Type</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->invoiceItems as $item)
                    <tr>
                        <td>{{ $item->description ?? 'N/A' }}</td>
                        <td>{{ ucfirst($item->item_type ?? 'N/A') }}</td>
                        <td>{{ $item->quantity ?? 1 }}</td>
                        <td>₱{{ number_format($item->unit_price ?? 0, 2) }}</td>
                        <td class="text-right">₱{{ number_format($item->total_price ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td class="summary-label">Subtotal:</td>
                    <td class="summary-value">₱{{ number_format($invoice->total_amount ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="summary-label">Amount Paid:</td>
                    <td class="summary-value">₱{{ number_format($invoice->amount_paid ?? 0, 2) }}</td>
                </tr>
                <tr class="summary-total">
                    <td class="summary-label">Balance Due:</td>
                    <td class="summary-value">₱{{ number_format($invoice->balance_due ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>
        
        @if($invoice->payments && $invoice->payments->count() > 0)
        <div class="section">
            <div class="section-title">Payment History</div>
            <table>
                <thead>
                    <tr>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                        <td>₱{{ number_format($payment->amount ?? 0, 2) }}</td>
                        <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                        <td>{{ $payment->reference_number ?? '-' }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower($payment->status->status_name ?? 'pending') }}">
                                {{ $payment->status->status_name ?? 'Pending' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if($invoice->notes)
        <div class="notes">
            <strong>Notes:</strong>
            {{ $invoice->notes }}
        </div>
        @endif
        
        <div class="footer">
            <p>Thank you for your business! Payment is due by the date specified above.</p>
            <p style="margin-top: 10px; font-size: 10px;">Generated on {{ now()->format('F d, Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
