@extends("components.layout")

@section("content")
@component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
@endcomponent

<div class="container my-4">
    <h2>Abonos del Préstamo {{ $prestamo->id_prestamo }}</h2>

    <div class="card p-3">
        <div class="row">
            <div class="col-md-6">
                <p><strong>EMPLEADO:</strong> {{ $prestamo->nombre }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>ID PRÉSTAMO:</strong> {{ $prestamo->id_prestamo }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>FECHA APROBACIÓN:</strong> {{ $prestamo->fecha_aprob }}</p>
            </div>
            <div class="col-md-3">
                <p><strong>MONTO PRESTADO:</strong> {{ number_format($prestamo->monto, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center my-3">
        <h4>Abonos</h4>
        <a class="btn btn-primary" href="{{ url('/prestamos/' . $prestamo->id_prestamo . '/abonos/agregar') }}">Agregar</a>
    </div>

    <div class="table-responsive">
        <table class="table" id="maintable">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">NUM DE ABONO</th>
                    <th scope="col">FECHA</th>
                    <th scope="col">MONTO CAPITAL</th>
                    <th scope="col">MONTO INTERÉS</th>
                    <th scope="col">MONTO COBRADO</th>
                    <th scope="col">SALDO PENDIENTE</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalCapital = 0;
                    $totalInteres = 0;
                    $totalCobrado = 0;
                @endphp
                @foreach($abonos as $abono)
                    <tr>
                        <td class="text-center">{{ $abono->id }}</td>
                        <td class="text-center">{{ $abono->num_abono }}</td>
                        <td class="text-center">{{ $abono->fecha }}</td>
                        <td class="text-end">{{ number_format($abono->monto_capital, 2) }}</td>
                        <td class="text-end">{{ number_format($abono->monto_interes, 2) }}</td>
                        <td class="text-end">{{ number_format($abono->monto_cobrado, 2) }}</td>
                        <td class="text-end">{{ number_format($abono->saldo_pendiente, 2) }}</td>
                    </tr>
                    @php
                        $totalCapital += $abono->monto_capital;
                        $totalInteres += $abono->monto_interes;
                        $totalCobrado += $abono->monto_cobrado;
                    @endphp
                @endforeach
                <tr class="fw-bold">
                    <td colspan="3" class="text-end">TOTAL</td>
                    <td class="text-end">{{ number_format($totalCapital, 2) }}</td>
                    <td class="text-end">{{ number_format($totalInteres, 2) }}</td>
                    <td class="text-end">{{ number_format($totalCobrado, 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection