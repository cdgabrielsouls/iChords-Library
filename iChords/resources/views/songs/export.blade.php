<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $song->title }}</title>
    <style>
        body { color: #20231f; font-family: DejaVu Sans, sans-serif; }
        h1 { margin-bottom: 3px; font-size: 24px; }
        .meta { color: #77786f; font-size: 11px; margin-bottom: 24px; }
        .section { border-bottom: 1px solid #d7ae31; color: #9b7611; font-size: 11px; font-weight: bold; letter-spacing: 1px; margin-top: 20px; padding-bottom: 5px; text-transform: uppercase; }
        .line { margin: 9px 0; }
        .chord { color: #9b7611; font-family: DejaVu Sans Mono, monospace; font-size: 11px; white-space: pre; }
        .lyric { font-family: DejaVu Sans Mono, monospace; font-size: 12px; white-space: pre; }
    </style>
</head>
<body>
    <h1>{{ $song->title }}</h1>
    <div class="meta">{{ $song->artist ?: 'Unknown artist' }} · Key {{ $song->original_key ?: 'C' }}</div>
    @foreach($song->content ?? [] as $line)
        @if(isset($line['section']))
            <div class="section">{{ $line['section'] }}</div>
        @elseif($type === 'lyrics')
            <div class="line lyric">{{ $line[1] ?? '' }}</div>
        @elseif($type === 'chords')
            <div class="line chord">{{ $line[0] ?? '' }}</div>
        @else
            <div class="line"><div class="chord">{{ $line[0] ?? '' }}</div><div class="lyric">{{ $line[1] ?? '' }}</div></div>
        @endif
    @endforeach
</body>
</html>