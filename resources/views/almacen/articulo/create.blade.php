@extends ('layouts.admin')
@section ('contenido')

    <!-- Input addon -->
    <div class="box box-info">
        <div class="box-header with-border">
            <h3 class="box-title">Nuevo Artículo</h3>
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
        {!! Form::open(array('url'=>'almacen/articulo', 'method'=>'POST', 'autocomplete'=>'off', 'files'=>'true', 'novalidate' => 'novalidate'))!!}
        {{Form::token()}}
        <div class="box box-body">
            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">Proveedor</span>
                    <select  name="idproveedores" id="idproveedores"  class="form-control selectpicker" data-live-search="true">
                        <option selected>Seleccione el Proveedor</option>
                        @foreach($proveedores as $prov)
                            <option value="{{$prov->codigo}}">{{$prov->codigo}}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="idproveedorsolo" id="idproveedorsolo" value="{{old('idproveedorsolo')}}">
                    <span class="input-group-btn">
                        <a href="{{ url('compras/proveedor/create?lastPage=art') }}"><button type="button" class="btn btn-info btn-flat">Nuevo Proveedor</button></a>
                    </span>
                </div>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">Código</span>
                <input type="text" name="codigo" id="codigo" value="{{old('codigo')}}" class="form-control" placeholder="Código...">
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">Nombre</span>
                <input type="text" name="nombre" id="nombre" value="{{old('nombre')}}" class="form-control" placeholder="Nombre">
            </div>
            <br>

            <div class="form-group">
                <div class="input-group">
                    <span class="input-group-addon">Categoría</span>
                    <select name="idcategoria" id="idcategoria" class="form-control selectpicker" data-live-search="true">
                        @foreach($categorias as $cat)
                            <option value="{{$cat->idcategoria}}">{{$cat->nombre}}</option>
                        @endforeach
                    </select>
                    <span class="input-group-btn">
                        <a href="{{ url('almacen/categoria/create?lastPage=art') }}"><button type="button" class="btn btn-info btn-flat">Nueva Categoría</button></a>
                    </span>
                </div>
            </div>
            <br>

            <hr size="60" />

            <div class="input-group">
                <span id="inputdelexistencia" style="display: none" class="input-group-addon">Hay <span id="existencia"></span> artículos en Stock</span>
                <input type="number" name="pcantidad" id="pcantidad" class="form-control" onkeyup="actualizar()" placeholder="Cantidad">
                <span class="input-group-addon">Cantidad de Artículos a Ingresar al Stock</span>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="number" name="pprecio_compra_costo" id="pprecio_compra_costo" class="form-control" onkeyup="actualizar()" placeholder="Costo">
                <span class="input-group-addon">Costo del Artículo</span>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">%</span>
                <input type="number" name="pporcentaje_venta" id="pporcentaje_venta" class="form-control" onkeypress="return valida(event)" onkeyup="actualizar()" placeholder="Porcentaje de Venta">
                <span class="input-group-addon">Porcentaje de Venta del Artículo</span>
            </div>
            <br>

            <div class="input-group">
                <span class="input-group-addon">$</span>
                <input type="number" name="pprecio_venta_esperado" id="pprecio_venta_esperado"  class="form-control" placeholder="Precio Esperado">
                <span class="input-group-addon">Precio Esperado (Es el cálculo del Costo x el Porcentaje de Venta)</span>
            </div>
            <br>
            <!-- /input-group -->
        </div>

        <!-- /.box-body -->
        <div class="box box-footer">
            <button type="reset" class="btn btn-default">Cancelar</button>
            <button type="submit" class="btn btn-info pull-right">Cargar Artículo</button>
        </div>
        {!! Form::close()!!}
    </div>
    <!-- /.box -->
@endsection

@push ('scripts')
<script>
    var art = {!! json_encode($articulos->toArray()) !!};
    var casa = '<?php echo $articulos ?>';
    var ultimoid = null;
    $(document).ready(function () {
 /*       articulo = $('#idproveedores option:selected').text();
        $('#idproveedores').click(function () {
            agregarprov();
        })
        */
 /*       $(document).on('change','#idproveedores',function(){
            // console.log("hmm its change");

            var cat_id=$(this).val();

            $.ajax({
                type:'get',

                data:{'codigo':cat_id},
                success:function(data){
                    $('#idproveedorsolo').val(data[0].idpersona);

                },
                error:function(){

                }
            });
        });
*/

        $(document).on('change','#idproveedores',function(){
            // console.log("hmm its change");

            var cat_id=$(this).val();

            $.ajax({
                type:'get',
                url:'{!!URL::to('buscarProveedor')!!}',
                data:{'codigo':cat_id},
                success:function(data){
                    $('#idproveedorsolo').val(data[0].idpersona);

                },
                error:function(){

                }
            });
            $.ajax({
                type:'get',
                url:'{!!URL::to('buscarUltimoId')!!}',
                data:{'codigo':cat_id},
                success:function(data){
                    //$('#idproveedorsolo').val(data[0].idpersona);
                    if(data.length==0){
                        ultimoid = 0;
                    }else{
                        ultimoid = data[0].idarticulo;
                        console.log(data)
                    }

                    agregarprov()

                },
                error:function(){

                }
            });

        });

        $(document).on('change','#codigo',function(){
            var cod = $('#idproveedores').val()+$('#codigo').val();
           for(var i = 0; i<art.length;i++){
               if(art[i].codigo == cod){
                   alert('El código ya existe');
                   $('#codigo').val(' ');
               }
           }

        });
    });

    function agregarprov() {
        var proveedorselected = $('#idproveedores option:selected').text();
        if (ultimoid.length == 0) {
            var d = ajustar(5, 1);
            $('#codigo').val(d);
        } else {
            var a = ultimoid;
            var b = parseInt(a) + 1;
            var c = ajustar(5, b);
            $('#codigo').val(c);
        }
    }
    function ajustar(tam, num) {
        if (num.toString().length < tam) return ajustar(tam, "0" + num)
        else return num;
    }

    function actualizar() {
        var a =  $('#pprecio_compra_costo').val();
        var b =  $('#pporcentaje_venta').val()/100 + 1;
        var cantidad =  $('#pcantidad').val();
        $('#pprecio_venta_esperado').val(a*b);
    }
    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }


</script>
@endpush