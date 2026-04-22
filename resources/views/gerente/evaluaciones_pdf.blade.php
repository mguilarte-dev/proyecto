@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Evaluaciones Resueltas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f3f3f3; }
        h1, h2 { margin-bottom: 0; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Evaluaciones Resueltas</h1>
        <h2>Área: {{ $area->name }}</h2>
        <p>Generado por: {{ $gerente->name }} | Fecha: {{ Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Empleado</th>
                <th>Curso</th>
                <th>Evaluación</th>
                <th>Puntaje</th>
                <th>Aprobado</th>
                <th>Intentos</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
        @foreach($resultados as $res)
            <tr>
                <td>{{ $res->user->name }}</td>
                <td>{{ $res->evaluation->course->title }}</td>
                <td>{{ $res->evaluation->title }}</td>
                <td>{{ $res->score }}</td>
                <td>{{ $res->passed ? 'Sí' : 'No' }}</td>
                <td>{{ $res->attempt_number ?? '-' }}</td>
                <td>{{ $res->attempted_at ? Carbon::parse($res->attempted_at)->format('d/m/Y H:i') : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
