<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; background: #f4f4f4; margin: 0; padding: 0; }
        .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; border: 1px solid #e0e0e0; }
        .header { background: #2d7a3a; padding: 24px 32px; color: #fff; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 28px 32px; }
        .greeting { font-size: 15px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { text-align: left; font-size: 12px; text-transform: uppercase; color: #666; padding: 6px 10px; border-bottom: 2px solid #eee; }
        td { padding: 10px; border-bottom: 1px solid #f0f0f0; font-size: 13px; vertical-align: top; }
        .overdue { color: #c0392b; font-weight: bold; }
        .badge-hoog { background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 99px; font-size: 11px; }
        .badge-normaal { background: #fef9c3; color: #92400e; padding: 2px 8px; border-radius: 99px; font-size: 11px; }
        .badge-laag { background: #f3f4f6; color: #6b7280; padding: 2px 8px; border-radius: 99px; font-size: 11px; }
        .btn { display: inline-block; margin-top: 20px; background: #2d7a3a; color: #fff; padding: 10px 22px; border-radius: 6px; text-decoration: none; font-size: 14px; }
        .footer { padding: 16px 32px; background: #f9f9f9; font-size: 12px; color: #999; border-top: 1px solid #eee; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>BABB Portaal &mdash; Taakherinnering</h1>
    </div>
    <div class="body">
        <p class="greeting">Hoi {{ $user->name }},</p>
        <p>Je hebt <strong>{{ $tasks->count() }} verlopen {{ $tasks->count() === 1 ? 'taak' : 'taken' }}</strong> die aandacht nodig {{ $tasks->count() === 1 ? 'heeft' : 'hebben' }}:</p>

        <table>
            <thead>
                <tr>
                    <th>Taak</th>
                    <th>Deadline</th>
                    <th>Prioriteit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks->sortBy('due_date') as $task)
                <tr>
                    <td>
                        <strong>{{ $task->title }}</strong>
                        @if ($task->meeting)
                            <br><span style="font-size:11px;color:#666;">{{ $task->meeting->title }}</span>
                        @endif
                    </td>
                    <td class="overdue">{{ $task->due_date->format('d-m-Y') }}</td>
                    <td>
                        @if ($task->priority === 'hoog')
                            <span class="badge-hoog">Hoog</span>
                        @elseif ($task->priority === 'normaal')
                            <span class="badge-normaal">Normaal</span>
                        @else
                            <span class="badge-laag">Laag</span>
                        @endif
                    </td>
                    <td>{{ ucfirst($task->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <a href="{{ config('app.url') }}/tasks?user_id={{ $user->id }}" class="btn">Bekijk mijn taken</a>
    </div>
    <div class="footer">
        Dit is een automatische herinnering vanuit BABB Portaal. Je ontvangt dit bericht omdat er taken aan jou zijn toegewezen met een verlopen deadline.
    </div>
</div>
</body>
</html>
