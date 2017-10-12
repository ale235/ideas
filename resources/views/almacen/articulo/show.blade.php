@extends ('layouts.admin')
@section ('contenido')

    <!-- Input addon -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Artículo {{$articulo->codigo}}: {{$articulo->nombre}}</h3>
        </div>
        <div class="box box-body">
            <div class="input-group">
                <span class="input-group-addon">Nombre</span>
                <input type="text" name="nombre" id="nombre" value="{{$articulo->nombre}}" class="form-control" placeholder="Nombre" readonly>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">Código</span>
                <input type="text" name="codigo" id="codigo" value="{{$articulo->codigo}}" class="form-control" readonly>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">Artículos en Stock</span>
                <input type="number" name="pcantidad" id="pcantidad" class="form-control" value="{{$articulo->stock}}">
            </div>
            <br>

            <hr size="60" />

            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="number" name="pprecio_compra_costo" id="pprecio_compra_costo" class="form-control" value="{{$precio->precio_compra}}">
                <span class="input-group-addon">Costo del Artículo</span>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">%</span>
                <input type="number" name="pporcentaje_venta" id="pporcentaje_venta" class="form-control" value="{{$precio->porcentaje}}">
                <span class="input-group-addon">Porcentaje de Venta</span>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="number" name="pprecio_venta_esperado" id="pprecio_venta_esperado"  class="form-control" value="{{$articulo->ultimoprecio}}">
                <span class="input-group-addon">Precio de Venta</span>
            </div>
            <br>
            <!-- /input-group -->
        </div>
    </div>
    <!-- /.box -->
@endsection