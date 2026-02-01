<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
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
        
        .summary-card.available {
            background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        }
        
        .summary-card.rented {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .summary-card.damaged {
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
        
        .status-available {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rented {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-damaged {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-maintenance {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .condition-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
        }
        
        .condition-excellent {
            background-color: #d4edda;
            color: #155724;
        }
        
        .condition-good {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        
        .condition-fair {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .condition-poor {
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
            <h1>Love & Styles Inventory Report</h1>
            <p>Comprehensive Inventory Status Report</p>
        </div>
        
        <div class="meta-info">
            <div class="meta-item">
                <strong>Report Type:</strong>
                {{ str_replace('_', ' ', ucfirst($reportType ?? 'inventory_summary')) }}
            </div>
            <div class="meta-item">
                <strong>Generated:</strong>
                {{ $generatedAt }}
            </div>
        </div>
        
        <div class="summary-section">
            <div class="summary-card">
                <div class="summary-label">Total Items</div>
                <div class="summary-value">{{ isset($data['total_items']) ? $data['total_items'] : 0 }}</div>
            </div>
            <div class="summary-card available">
                <div class="summary-label">Available</div>
                <div class="summary-value">{{ isset($data['available_items']) ? $data['available_items'] : 0 }}</div>
            </div>
            <div class="summary-card rented">
                <div class="summary-label">Rented</div>
                <div class="summary-value">{{ isset($data['rented_items']) ? $data['rented_items'] : 0 }}</div>
            </div>
            <div class="summary-card damaged">
                <div class="summary-label">Damaged</div>
                <div class="summary-value">{{ isset($data['damaged_items']) ? $data['damaged_items'] : 0 }}</div>
            </div>
        </div>
        
        @if(isset($data['total_value']))
        <div class="section-title">Inventory Valuation</div>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="font-weight: 600; color: #2c3e50;">Total Inventory Value:</span>
                <span style="font-size: 24px; font-weight: bold; color: #27ae60;">₱{{ number_format($data['total_value'] ?? 0, 2) }}</span>
            </div>
        </div>
        @endif
        
        @if(isset($data['inventory_items']) && count($data['inventory_items']) > 0)
        <div class="section-title">Inventory Items</div>
        <table>
            <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Purchase Price</th>
                    <th>Rental Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['inventory_items'] as $item)
                    <tr>
                        <td>#{{ $item['item_id'] ?? 'N/A' }}</td>
                        <td>{{ $item['item_name'] ?? 'N/A' }}</td>
                        <td>{{ $item['category'] ?? 'N/A' }}</td>
                        <td>
                            @php
                                $conditionClass = 'condition-' . strtolower($item['condition'] ?? 'fair');
                            @endphp
                            <span class="condition-badge {{ $conditionClass }}">
                                {{ ucfirst($item['condition'] ?? 'Unknown') }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusClass = 'status-' . strtolower($item['status'] ?? 'available');
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($item['status'] ?? 'Unknown') }}
                            </span>
                        </td>
                        <td class="text-right">₱{{ number_format($item['purchase_price'] ?? 0, 2) }}</td>
                        <td class="text-right">₱{{ number_format($item['rental_price'] ?? 0, 2) }}/day</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #999;">No inventory items found</td>
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
