<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CV - {{ $admin->pseudo }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11pt; color: #1f2937; line-height: 1.5; padding: 40px; }
        h1 { font-size: 24pt; font-weight: 700; color: #111827; }
        h2 { font-size: 14pt; font-weight: 700; color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 4px; margin-top: 24px; margin-bottom: 12px; }
        h3 { font-size: 12pt; font-weight: 600; color: #1f2937; }
        .headline { font-size: 12pt; color: #6b7280; margin-top: 2px; }
        .bio { margin-top: 12px; font-size: 10pt; color: #4b5563; }
        .social { font-size: 9pt; color: #6b7280; margin-top: 8px; }
        .social a { color: #4f46e5; text-decoration: none; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .skill-cat { margin-bottom: 10px; }
        .skill-cat h3 { font-size: 10pt; font-weight: 600; color: #374151; margin-bottom: 2px; }
        .skill-cat .skills { font-size: 9pt; color: #6b7280; }
        .formation { margin-bottom: 8px; }
        .formation h3 { font-size: 11pt; }
        .formation .meta { font-size: 9pt; color: #6b7280; }
        .formation .badge { display: inline-block; font-size: 8pt; padding: 1px 6px; border-radius: 999px; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-progress { background: #fef3c7; color: #92400e; }
        .project { margin-bottom: 6px; }
        .project h3 { font-size: 10pt; }
        .project .meta { font-size: 8pt; color: #9ca3af; }
        .project p { font-size: 9pt; color: #4b5563; }
        .footer { position: fixed; bottom: 20px; left: 0; right: 0; text-align: center; font-size: 8pt; color: #9ca3af; }
        @page { margin: 30px; }
    </style>
</head>
<body>
    <h1>{{ $admin->pseudo ?? 'Alex Mbe' }}</h1>
    <p class="headline">{{ $admin->headline ?? 'Développeur Full Stack' }}</p>

    @if($admin->social_links)
        <p class="social">
            @foreach($admin->social_links as $link)
                <strong>{{ $link['platform'] }} :</strong> {{ $link['url'] }}@if(!$loop->last) &nbsp;|&nbsp; @endif
            @endforeach
        </p>
    @endif

    @if($admin->bio)
        <p class="bio">{{ $admin->bio }}</p>
    @endif

    <h2>Compétences</h2>
    @foreach($skills as $category => $items)
        <div class="skill-cat">
            <h3>{{ $category }}</h3>
            <p class="skills">{{ $items->pluck('name')->implode(', ') }}</p>
        </div>
    @endforeach

    <h2>Formations</h2>
    @forelse($formations as $formation)
        <div class="formation">
            <h3>{{ $formation->name }}</h3>
            <p class="meta">{{ $formation->institution }} &middot; {{ $formation->year }}
                <span class="badge badge-{{ $formation->status === 'completed' ? 'completed' : 'progress' }}">
                    {{ $formation->status === 'completed' ? 'Terminé' : 'En cours' }}
                </span>
            </p>
        </div>
    @empty
        <p style="font-size: 10pt; color: #9ca3af;">Aucune formation renseignée.</p>
    @endforelse

    <h2>Projets</h2>
    @forelse($projects as $project)
        <div class="project">
            <h3>{{ $project->title }}</h3>
            <p class="meta">{{ $project->type }}@if($project->created_at) &middot; {{ \Carbon\Carbon::parse($project->created_at)->format('d/m/Y') }}@endif</p>
            @if($project->description)
                <p>{{ Str::limit($project->description, 150) }}</p>
            @endif
        </div>
    @empty
        <p style="font-size: 10pt; color: #9ca3af;">Aucun projet renseigné.</p>
    @endforelse

    <div class="footer">CV généré depuis MbeAlexPortfolio</div>
</body>
</html>
