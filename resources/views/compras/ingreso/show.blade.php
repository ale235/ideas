@extends ('layouts.admin')
@section ('contenido')

    <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="from-group">
                <label for="proveedor">Proveedor</label>
                <p>{{$ingreso->nombre}}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <div class="form-group">
                <label>Tipo de Comprobante</label>
                <p>{{$ingreso->tipo_comprobante}}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <div class="from-group">
                <label>Serie Comprobante</label>
                <p>{{$ingreso->serie_comprobante}}</p>
            </div>
        </div>
        <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
            <div class="from-group">
                <label for="numero_comprobante">Numero Comprobante</label>
                <p>{{$ingreso->numero_comprobante}}</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                    <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                        <thead style="background-color: #a94442">
                            <th>Artículo</th>
                            <th>Cantidad</th>
                            <th>Precio compra</th>
                            <th>Porcentaje venta</th>
                            <th>Subtotal</th>
                        </thead>
                        <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><h4 id="total">{{$ingreso->total}}</h4></th>
                        </tfoot>
                        <tbody>
                            @foreach($detalles as $det)
                                <tr>
                                    <td>{{$det->articulo}}</td>
                                    <td>{{$det->cantidad}}</td>
                                    <td>${{$det->precio_compra_costo}}</td>
                                    <td>%{{$det->porcentaje_venta}}</td>
                                    <td>{{$det->cantidad*$det->precio_compra_costo}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="from-group">
                {{--<a href="{{URL::action('IngresoController@edit',$ingreso->idingreso)}}"><button class="btn btn-info">Editar</button></a>--}}
            </div>
        </div>
    </div>
@endsection