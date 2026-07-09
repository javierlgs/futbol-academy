<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { background: #0EA5E9; color: white; padding: 20px; text-align: center; }
        .logo { width: 80px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .bar { background: #e5e7eb; height: 20px; border-radius: 10px; overflow: hidden; }
        .bar-fill { background: #0EA5E9; height: 100%; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ACADEMIA DE FÚTBOL</h1>
        <h2>Reporte de Progreso</h2>
    </div>

    <div class="section">
        <h3>{{ $jugador->nombre_completo }}</h3>
        <p>Categoría: {{ $jugador->categoria->nombre }} | Edad: {{ $jugador->edad }} años</p>
        <p>Posición: {{ $jugador->posicion }} | Estado: {{ $jugador->estado }}</p>
    </div>

    <div class="section">
        <h3>Evaluación Técnica</h3>
        @foreach($jugador->evaluaciones->groupBy('habilidad.nombre') as $habilidad => $evals)
            @php $promedio = $evals->avg('puntaje'); @endphp
            <p><strong>{{ $habilidad }}:</strong> {{ number_format($promedio, 1) }}/5</p>
            <div class="bar"><div class="bar-fill" style="width: {{ $promedio * 20 }}%"></div></div>
        @endforeach
    </div>

    <div class="section">
        <h3>Evaluación Psicológica</h3>
        @forelse($jugador->evaluacionesPsicologicas as $eval)
            <p><strong>{{ ucfirst($eval->tipo) }}:</strong> {{ $eval->puntaje }}/10 - {{ ucfirst($eval->nivel) }}</p>
            <p style="font-size: 10px; color: #666;">{{ $eval->recomendaciones }}</p>
        @empty
            <p>Sin evaluaciones psicológicas</p>
        @endforelse
    </div>
</body>
</html>