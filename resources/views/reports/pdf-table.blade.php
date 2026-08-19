<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 8px;
        }
        .meta {
            margin-bottom: 12px;
            color: #4b5563;
        }
        .summary {
            margin: 10px 0 14px;
        }
        .summary-item {
            display: inline-block;
            margin: 0 14px 8px 0;
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #f9fafb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 7px;
            vertical-align: top;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">
        Generated: {{ $generatedAt->format('Y-m-d H:i:s') }}
        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            | Date Range: {{ $filters['date_from'] ?? 'Any' }} to {{ $filters['date_to'] ?? 'Any' }}
        @endif
    </div>

    @if(!empty($summary))
        <div class="summary">
            @foreach($summary as $stat)
                <span class="summary-item">{{ $stat['label'] }}: {{ $stat['value'] }}</span>
            @endforeach
        </div>
    @endif

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($headers as $header)
                        <td>{{ $row[$header] ?? '' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}">No data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
