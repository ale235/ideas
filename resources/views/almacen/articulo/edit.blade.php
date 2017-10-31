@extends ('layouts.admin')
@section ('contenido')

    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Editar Artículo: {{$articulo->nombre}}</h3>
        </div>
        @if(count($errors)>0)
            <div class="alert alert-danger">
                <u>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </u>
            </div>
        @endif
        {!! Form::model($articulo, ['method'=>'PATCH','route'=>['articulo.update',$articulo->idarticulo], 'files'=>'true'])!!}
        {{Form::token()}}

        <div class="box-body">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">Proveedor</span>
                    <select  name="idproveedores" id="idproveedores"  class="form-control selectpicker" data-live-search="true">
                        <option value="{{$articulo->proveedor}}" selected>{{$articulo->proveedor}}</option>
                        @foreach($proveedores as $prov)
                            <option value="{{$prov->codigo}}">{{$prov->codigo}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="idproveedorsolo" id="idproveedorsolo" value="{{old('idproveedorsolo')}}">
                    <input type="hidden" name="idproveedor" id="idproveedor" value="{{old('idproveedor')}}">
                    <span class="input-group-btn">
                        <a href="{{ url('compras/proveedor/create?lastPage=art') }}"><button type="button" class="btn btn-info btn-flat">Nuevo Proveedor</button></a>
                    </span>
                </div>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">Código</span>
                <input type="text" name="codigo" id="codigo" value="{{$articulo->codigo}}" class="form-control" placeholder="Código...">
                <input type="hidden" name="codigooculto" id="codigooculto" value="{{$articulo->codigo}}" class="form-control" placeholder="Código...">
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">Nombre</span>
                <input type="text" name="nombre" id="nombre" value="{{$articulo->nombre}}" class="form-control" placeholder="Nombre">
            </div>
            <br>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">Categoría</span>
                    <select name="idcategoria" class="form-control">
                        @foreach($categorias as $cat)
                            @if ($cat->idcategoria == $articulo->idcategoria)
                                <option value="{{$cat->idcategoria}}" selected>{{$cat->nombre}}</option>
                            @else
                                <option value="{{$cat->idcategoria}}">{{$cat->nombre}}</option>
                            @endif
                        @endforeach
                    </select>
                    <span class="input-group-btn">
                        <a href="{{ url('almacen/categoria/create?lastPage=art') }}"><button type="button" class="btn btn-info btn-flat">Nueva Categoría</button></a>
                    </span>
                </div>
            </div>
            <br>


            <br>

            <hr size="60" />
            @if (Auth::user()->role == 1)
            <div class="input-group">
                <span id="inputdelexistencia" style="display: none" class="input-group-addon">Hay <span id="existencia"></span> artículos en Stock</span>
                <input type="number" name="pcantidad" id="pcantidad" value="{{$articulo->stock}}" class="form-control" placeholder="Cantidad">
                <span class="input-group-addon">Cantidad de árticulos en existencia (Stock)</span>
            </div>
            @endif
            <br>
            <!-- /input-group -->
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <button type="reset" class="btn btn-default">Cancelar</button>
            <button type="submit" class="btn btn-info pull-right">Cargar Artículo</button>
        </div>
        {!! Form::close()!!}
    </div>
@endsection

@push ('scripts')
<script>
    $(document).on('change','#codigo',function(){
        var cod = $('#codigo').val();
        $.ajax({
            type:'get',
            url:'{!!URL::to('verificarCodigo')!!}',
            data:{'codigo':cod},
            success:function(data){
                if(data.codigo != null){
                    alert('El código ya existe')
                    $('#codigo').val($('#codigooculto').val());
                }else{
                    $('#codigooculto').val($('#codigo').val())
                }

            },
            error:function(){

            }
        });
    });
</script>
@endpush