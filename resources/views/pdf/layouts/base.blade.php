<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Reporte') - Solupatch</title>
    <style>
        /* ===========================
           ESTILOS GLOBALES BASE
        =========================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #2c3e50;
            background-color: #ffffff;
        }
        
        /* ===========================
           VARIABLES CSS (COLORES)
        =========================== */
        :root {
            --color-primary: #f39c12;
            --color-primary-dark: #e67e22;
            --color-secondary: #2c3e50;
            --color-text: #2c3e50;
            --color-text-light: #7f8c8d;
            --color-text-muted: #95a5a6;
            --color-border: #dee2e6;
            --color-background: #f8f9fa;
            --color-success: #27ae60;
            --color-warning: #f39c12;
            --color-danger: #e74c3c;
            --color-info: #3498db;
        }
        
        /* ===========================
           HEADER UNIFICADO
        =========================== */
        .pdf-header {
            border-bottom: 3px solid var(--color-primary);
            padding-bottom: 20px;
            margin-bottom: 25px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }
        
        .header-logo-section {
            display: table-cell;
            width: 120px;
            vertical-align: middle;
            padding-right: 20px;
        }
        
        .header-logo {
            width: 150px; /* Aumentamos el tamaño un poco */
            height: auto;
            display: block;
            max-width: 100%;
            object-fit: contain;
        }
        
        .header-info-section {
            display: table-cell;
            vertical-align: middle;
            text-align: left;
        }
        
        .header-company-name {
            font-size: 22px;
            font-weight: bold;
            color: var(--color-secondary);
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .header-report-title {
            font-size: 16px;
            color: var(--color-primary-dark);
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        .header-report-date {
            font-size: 10px;
            color: var(--color-text-light);
            font-style: italic;
        }
        
        .header-report-subtitle {
            font-size: 12px;
            color: var(--color-text-muted);
            margin-top: 3px;
        }
        
        /* ===========================
           SECCIÓN DE ESTADÍSTICAS
        =========================== */
        .pdf-stats-section {
            background-color: var(--color-background);
            border: 1px solid var(--color-border);
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        
        .stats-title {
            color: var(--color-secondary);
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 12px;
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        
        .stats-row {
            display: table-row;
        }
        
        .stat-item {
            display: table-cell;
            text-align: center;
            padding: 10px 8px;
            border-right: 1px solid var(--color-border);
            vertical-align: top;
        }
        
        .stat-item:last-child {
            border-right: none;
        }
        
        .stat-number {
            display: block;
            font-size: 16px;
            font-weight: bold;
            color: var(--color-primary);
            margin-bottom: 3px;
        }
        
        .stat-label {
            font-size: 9px;
            color: var(--color-text-light);
            text-transform: uppercase;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }
        
        /* ===========================
           INFORMACIÓN DEL REPORTE
        =========================== */
        .pdf-info-section {
            background-color: #ecf0f1;
            border: 1px solid var(--color-border);
            border-radius: 4px;
            padding: 12px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .info-grid {
            display: table;
            width: 100%;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            color: var(--color-secondary);
            padding: 3px 15px 3px 0;
            width: 25%;
            vertical-align: top;
        }
        
        .info-value {
            display: table-cell;
            color: var(--color-text);
            padding: 3px 0;
            vertical-align: top;
        }
        
        /* ===========================
           TABLAS PRINCIPALES
        =========================== */
        .pdf-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .pdf-table th {
            background-color: var(--color-primary);
            color: white;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            padding: 8px 6px;
            text-align: left;
            border: 1px solid var(--color-primary-dark);
        }
        
        .pdf-table td {
            padding: 6px;
            border: 1px solid var(--color-border);
            vertical-align: top;
        }
        
        .pdf-table tr:nth-child(even) {
            background-color: #fafbfc;
        }
        
        .pdf-table tr:hover {
            background-color: #f1f3f5;
        }
        
        /* ===========================
           BADGES DE ESTADO
        =========================== */
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }
        
        .status-disponible {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-asignado {
            background-color: #f4c430;
            color: #8b6914;
            border: 1px solid #e6b800;
        }
        
        .status-mantenimiento {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-fuera-servicio {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-baja {
            background-color: #f5c6cb;
            color: #721c24;
            border: 1px solid #f1b0b7;
        }
        
        .status-activo {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-finalizado {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }
        
        .status-pendiente {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-critico {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-alta {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-normal {
            background-color: #f4c430;
            color: #8b6914;
            border: 1px solid #e6b800;
        }
        
        .status-info {
            background-color: #f4c430;
            color: #8b6914;
            border: 1px solid #e6b800;
        }
        
        /* ===========================
           ESTILOS PARA ESTADOS DE OBRAS
        =========================== */
        .status-activa {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status-en-progreso {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        
        .status-completada {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }
        
        .status-suspendida {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-cancelada {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .status-planificada {
            background-color: #cce5ff;
            color: #004085;
            border: 1px solid #b3d9ff;
        }
        
        /* ===========================
           UTILIDADES
        =========================== */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-muted { color: var(--color-text-muted); }
        .text-success { color: var(--color-success); }
        .text-warning { color: var(--color-warning); }
        .text-danger { color: var(--color-danger); }
        .text-info { color: var(--color-info); }
        
        .font-small { font-size: 9px; }
        .font-medium { font-size: 11px; }
        .font-large { font-size: 13px; }
        
        .no-wrap { white-space: nowrap; }
        .break-word { word-break: break-word; }
        
        .mb-5 { margin-bottom: 5px; }
        .mb-10 { margin-bottom: 10px; }
        .mb-15 { margin-bottom: 15px; }
        .mb-20 { margin-bottom: 20px; }
        
        .mt-5 { margin-top: 5px; }
        .mt-10 { margin-top: 10px; }
        .mt-15 { margin-top: 15px; }
        .mt-20 { margin-top: 20px; }
        
        .p-5 { padding: 5px; }
        .p-10 { padding: 10px; }
        .p-15 { padding: 15px; }
        
        /* ===========================
           FOOTER
        =========================== */
        .pdf-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid var(--color-border);
            text-align: center;
            color: var(--color-text-light);
            font-size: 9px;
            page-break-inside: avoid;
        }
        
        .footer-info {
            margin-bottom: 5px;
        }
        
        .footer-page {
            font-style: italic;
        }
        
        /* ===========================
           CONFIGURACIÓN DE PÁGINA
        =========================== */
        @page {
            margin: 1.5cm 1cm 2cm 1cm;
            size: A4;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .no-page-break {
            page-break-inside: avoid;
        }
        
        /* ===========================
           ESTILOS ESPECÍFICOS ADICIONALES
        =========================== */
        @yield('additional-styles')
    </style>
</head>
<body>
    <!-- Header del PDF -->
    <div class="pdf-header">
        <!-- Header con logo -->
        <div class="header-logo-section">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logo-solupatch.png'))) }}" alt="Logo Solupatch" class="header-logo">
        </div>
        <div class="header-info-section">
            <div class="header-company-name">Solupatch S.A. de C.V.</div>
            <div class="header-report-title">@yield('report-title', 'Reporte del Sistema')</div>
            <div class="header-report-date">Generado el: {{ now()->format('d/m/Y H:i:s') }}</div>
            @hasSection('report-subtitle')
                <div class="header-report-subtitle">@yield('report-subtitle')</div>
            @endif
        </div>
    </div>

    <!-- Contenido específico del reporte -->
    @yield('content')

    <!-- Footer del PDF -->
    <div class="pdf-footer">
        <div class="footer-info">
            <strong>Solupatch S.A. de C.V.</strong> - Sistema de Control Interno
        </div>
        <div class="footer-page">
            @yield('footer-info', 'Documento generado automáticamente por el sistema')
        </div>
    </div>
</body>
</html>
