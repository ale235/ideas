@extends ('layouts.admin')
@section ('contenido')
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <h3>
            Listado de Ventas
        </h3>
        {{--<a href="venta/create"><button class="btn btn-success pull-left">Nueva Venta</button></a>--}}
        @if (Auth::user()->role == 1)
        <a href="{{URL::action('VentaController@exportResultado',$date)}}"><button class="btn btn-success">Exportar Resultado Con Ganancia<i class="fa fa-file-excel-o"></i></button></a>
        <a href="{{URL::action('VentaController@exportDetalle',$date)}}"><button class="btn btn-success">Exportar Resultado con Detalle<i class="fa fa-file-excel-o"></i></button></a>
        <a href="{{URL::action('VentaController@exportReducido',$date)}}"><button class="btn btn-success">Exportar Resultado Reducido<i class="fa fa-file-excel-o"></i></button></a>
        <a href="{{URL::action('VentaController@cajaDelDia')}}"><button class="btn btn-success">Caja del día de hoy<i class="fa fa-file-excel-o"></i></button></a>
        <a href="{{URL::action('VentaController@verstock')}}"><button class="btn btn-success">verstock<i class="fa fa-file-excel-o"></i></button></a>
        <a href="{{URL::action('VentaController@productoMasVendido',$date)}}"><button class="btn btn-success">Producto más vendido<i class="fa fa-file-excel-o"></i></button></a>
        <a href="{{URL::action('VentaController@proveedorQueMasVende',$date)}}"><button class="btn btn-success">Proveedor que más vende<i class="fa fa-file-excel-o"></i></button></a>


        @endif

    </div>
    @include('ventas.venta.search')
</div>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
                <thead>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Opciones</th>
                </thead>
                @foreach($ventas as $vent)
                <tr>
                    <td>{{$vent->fecha_hora}}</td>
                    <td>{{$vent->nombre}}</td>
                    <td>{{$vent->total_venta_real}}</td>
                    <td>{{$vent->estado}}</td>
                    <td>
                        <a href="{{URL::action('VentaController@show',$vent->idventa)}}"><button class="btn btn-primary">Detalles</button></a>
                        {{--<a href="{{URL::action('VentaController@edit',$vent->idventa)}}"><button class="btn btn-info">Editar</button></a>--}}
                        <a href="" data-target="#modal-delete-{{$vent->idventa}}" data-toggle="modal"><button class="btn btn-danger">Anular</button></a>
                    </td>
                </tr>
                @include('ventas.venta.modal')
                @endforeach
            </table>
        </div>
        {{--{{$ventas->render()}}--}}
        {{$ventas->appends(['daterange' => $date])->render()}} 
        {{--{!! $articulos->appends(['selectText' => $selectText, 'searchText' => $searchText, 'searchText2' => $searchText2])->render() !!}--}}
    </div>
</div>
@endsection
@push ('scripts')
<script>
    $(document).ready(function () {
        $('input[name="daterange"]').daterangepicker(
            {

                locale: {
//                    useCurrent: false,
                    format: 'YYYY-MM-DD',
//                    defaultDate: d
                },

        }
        );

    });
    var val = getURLParameter('daterange');
    $('#daterange').val(val);

    function getURLParameter(name) {
        return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null
    }



</script>
@endpush
