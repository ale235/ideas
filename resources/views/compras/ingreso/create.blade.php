@extends ('layouts.admin')
@section ('contenido')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Nuevo Ingreso por Proveedor</h3>
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
        <!-- /.box-header -->
        <!-- form start -->
        {!! Form::open(array('url'=>'compras/ingreso', 'method'=>'POST', 'autocomplete'=>'off', 'id'=>'myForm'))!!}
        {{Form::token()}}
            <div class="box-body">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <select name="idproveedor" id="idproveedor"
                                class="lista-proveedores form-control selectpicker"  data-live-search="true">
                            <option value="0" disabled="true" selected="true">
                                Seleccioné el Proveedor
                            </option>
                            @foreach($personas as $persona)
                                <option value="{{$persona->idpersona}}+{{$persona->codigo}}">{{$persona->codigo}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group has-success">
                                <div style="display: none" id="textoAgregarArticuloCuidado"  class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-warning"></i> El Artículo NO Existe</h4>
                                    Si desea cargarlo haga Click en el Botón de "Agregar Preducto" de la derecha
                                </div>
                                <div style="display: none" id="textoAgregarArticuloOk" class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-check"></i> El Artículo es correcto</h4>
                                </div>
                                <div style="display: none" id="textoAgregarArticuloCargadoPerfecto" class="alert alert-success alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <h4><i class="icon fa fa-check"></i> El Artículo fue cargado correctamente</h4>
                                </div>
                                <label class="control-label" for="inputSuccess">
                                    <i class="fa fa-check"></i> Nombre del artículo
                                </label>
                                <div class="form-group">
                                    <input type="text" class="form-control" id="inputSuccess" placeholder="Nombre del artículo" disabled>
                                    <button type="button" id="botonmodal" class="btn btn-warning" data-toggle="modal" data-target="#modal-default" style="display: none">
                                       Agregar Producto
                                    </button>
                                    {{--<span id="bt_add_product" class="input-group-addon btn" disabled>Agregar Producto</span>--}}
                                </div>

                                <div class="modal fade" id="modal-default">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span></button>
                                                <h4 class="modal-title">Atención</h4>
                                            </div>
                                            <div class="modal-body">
                                                <p>¿Está seguro que desea agregar este producto?</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cerrar</button>
                                                <button id="bt_add_product" type="button" class="btn btn-primary" data-dismiss="modal">Sì</button>
                                            </div>
                                        </div>
                                        <!-- /.modal-content -->
                                    </div>
                                    <!-- /.modal-dialog -->
                                </div>
                            </div>
                                <div class="form-group">
                                    <label>Código del artículo</label>
                                    <div class="input-group">
                                        {{--<select name="pidarticulo" id="pidarticulo" class="selectpicker" data-live-search="true">--}}
                                            {{--<option value="0" selected disabled>Elegí un artículo</option>--}}
                                        {{--</select>--}}
                                        {{--<input type="text" class="form-control" name="pidarticulo" id="pidarticulo"/>--}}
                                        <input type="text" class="form-control" name="codigodelarticuloacargar" id="codigodelarticuloacargar" readonly/>
                                        <input type="hidden" class="form-control" name="pidarticulonombre" id="pidarticulonombre"/>
                                        <input type="hidden" class="form-control" name="pidarticuloidarticulo" id="pidarticuloidarticulo"/>
                                        <input type="hidden" class="form-control" name="pidproveedor" id="pidproveedor"/>
                                    </div>
                                </div>

                            <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label for="cantidad">Cantidad</label>
                                    <input type="number" name="pcantidad" id="pcantidad" class="form-control" onkeyup="actualizar()" placeholder="Cantidad">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label for="precio_compra_costo">Costo</label>
                                    <input type="number" name="pprecio_compra_costo" id="pprecio_compra_costo" class="form-control" onkeyup="actualizar()" placeholder="Costo">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label for="porcentaje_venta">Porcentaje de venta</label>
                                    <input type="number" name="pporcentaje_venta" id="pporcentaje_venta" class="form-control" onkeypress="return valida(event)" onkeyup="actualizar()" placeholder="Porcentaje de Venta">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label for="precio_venta">Esperado</label>
                                    <input type="number" name="pprecio_venta_esperado" id="pprecio_venta_esperado" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
                                <div class="form-group">
                                    <label>Agregar Producto</label>
                                    <button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                                <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                                    <thead style="background-color: #a94442">
                                    <th>Opciones</th>
                                    <th>Código</th>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Compra</th>
                                    <th>Porcentaje</th>
                                    <th>Subtotal</th>
                                    </thead>
                                    <tfoot>
                                    <th>TOTAL</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th><h4 id="total">$ 0.00</h4></th>
                                    </tfoot>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->

            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Agregar Ingreso</button>
            </div>
        {!! Form::close()!!}
    </div>
@push ('scripts')
<script>
    $(document).ready(function () {
       $('#bt_add').click(function () {
           agregar();
       });
        $("#myform").keypress(function(e) {
            if (e.which == 13 || e.which == '10') {
                event.preventDefault();
            }
        });

        $(document).on('change','.lista-proveedores',function(data){
            // console.log("hmm its change");
            $(this).attr('readonly', true);
            var cat_id=$("#idproveedor option:selected").val();
            var input = cat_id.split('+')
            cat_id = input[1];
            $('#pidproveedor').val(input[0]);

            $('#inputSuccess').attr('disabled',false);
        });

        var path ="{{ route('autocompleteIngresoPorProveedor') }}";
        $("#inputSuccess").typeahead({
            minLength: 3,
            autoSelect: true,
            dataType: 'json',
            source: function (query, process) {

                return $.get(path, {query:query, prov: $(".lista-proveedores option:selected" ).text()}, function (data) {
                    if(data.length == 0 ){
                        $('#textoAgregarArticuloCuidado').css('display','block');
                        $('#pidarticuloidarticulo').val('');
                        $('#textoAgregarArticuloOk').css('display','none');
                        //$('#bt_add_product').attr('disabled',false);
                        $('#botonmodal').css('display','block');
                    }else{
                        $('#textoAgregarArticuloCuidado').css('display','none');
                        //$('#bt_add_product').attr('disabled',true);
                        $('#botonmodal').css('display','none');
                    }

                    var nombres = data.map(function (item) {

                        return item.codigo + ' ' + item.nombre
                    });
                    return process(nombres);
                })
            },
            updater:function (item,data) {
                $('#textoAgregarArticuloOk').css('display','block');

                $('#textoAgregarArticuloCargadoPerfecto').css('display','none');
                var input = item.split(' ');
                $.ajax({
                    type:'get',
                    url:'{!!URL::to('buscarPrecioArticuloIngresosPorCodigo')!!}',
                    data:{'codigo':input[0]},
                    success:function(data){
                        //console.log('success');
                        if(jQuery.isEmptyObject(data)){
                            $('#pidarticulo').val("");
                            alert('NO')
                        }
                        else if($('#pidproveedor').val() != "" && $('#pidproveedor').val() != data.idpersona){
                            $('#pidarticulo').val("");
                            alert('El artículo no pertenece al primer proveedor cargado')
                        } else{
                            $('#inputSuccess').val(data.nombre);
                            $('#pidarticuloidarticulo').val(data.idarticulo);
                            $('#pidarticulonombre').val(data.nombre);
                            $('#pidproveedor').val(data.idpersona);
                            $('#pprecio_compra_costo').val(data.precio_compra);
                            $('#pporcentaje_venta').val(data.porcentaje);
                            $('#codigodelarticuloacargar').val(data.codigo)
                        }

                    },
                    error:function(){

                    }
                });

            }
        });

        $(document).on('click','#bt_add_product',function(){
            // console.log("hmm its change");

            var cat_prov=$('.lista-proveedores').find("option:selected").text();
            var nombre_prov=$('#inputSuccess').val();

            $.ajax({
                type:'get',
                url:'{!!URL::to('agregarArticuloParaIngreso')!!}',
                data:{'prov':cat_prov, 'nombre':nombre_prov},
                success:function(data){
                    $('#inputSuccess').val(data.nombre);
                    $('#codigodelarticuloacargar').val(data.codigo);
                    $('#pidarticuloidarticulo').val(data.idarticulo);
                    $('#pidarticulonombre').val(data.nombre);
                    $('#textoAgregarArticuloCuidado').css('display','none');
                    $('#textoAgregarArticuloCargadoPerfecto').css('display','block');
                    $('#botonmodal').css('display','none');

                },
                error:function(){

                }
            });
        });

        $(document).on('change','#pidarticulo',function(){
            // console.log("hmm its change");

            var cat_prov=$('.lista-proveedores').find("option:selected").text();
            var cat_codigo=$(this).val();
            var div=$(this).parent();
            var codigo = cat_prov + cat_codigo;

            var op=" ";

            $.ajax({
                type:'get',
                url:'{!!URL::to('buscarPrecioArticuloIngresosPorCodigo')!!}',
                data:{'codigo':cat_codigo},
                success:function(data){
                    //console.log('success');
                    if(jQuery.isEmptyObject(data)){
                        $('#pidarticulo').val("");
                        alert('NO')
                    }
                    else if($('#pidproveedor').val() != "" && $('#pidproveedor').val() != data.idpersona){
                        $('#pidarticulo').val("");
                        alert('El artículo no pertenece al primer proveedor cargado')
                    } else{
                        $('#pidarticuloidarticulo').val(data.idarticulo);
                        $('#pidarticulonombre').val(data.nombre);
                        $('#pidproveedor').val(data.idpersona);
                    }

                },
                error:function(){

                }
            });
        });

        $(document).on('change','.nombre-articulo',function(){
            // console.log("hmm its change");

            var cat_id=$(this).val();
            var div=$(this).parent();

            var op=" ";

            $.ajax({
                type:'get',
                url:'{!!URL::to('buscarArticuloParaIngreso')!!}',
                data:{'id':cat_id},
                success:function(data){
                    //console.log('success');

                    if($('#pidproveedor').val() != "" && $('#pidproveedor').val() != data.idpersona){
                        $('#pidarticulo').val("");
                        alert('El artículo no pertenece al primer proveedor cargado')
                    } else{

                        $('#pidarticulo').val(data.codigo);
                        $('#pidarticuloidarticulo').val(data.idarticulo);
                        $('#pidarticulonombre').val(data.nombre);
                        $('#pidproveedor').val(data.idpersona);

                    }

                },
                error:function(){
                    alert('NO')
                }
            });
        });


    });
    var cont = 0;
    total = 0;
    subtotal=[];
    $('#guardar').hide();

    function actualizar() {
        var a =  $('#pprecio_compra_costo').val();
        var b =  $('#pporcentaje_venta').val()/100 + 1;
        var cantidad =  $('#pcantidad').val();
        $('#pprecio_venta_esperado').val(a*b);
    }

    function limpiar(){
        $('#pcantidad').val("");
        $('#pprecio_compra_costo').val("");
        $('#pporcentaje_venta').val("");
        $('#pprecio_venta_esperado').val("");
        $('#inputSuccess').val("");

    }

    function evaluar(){
        if(total>0){
            $('#guardar').show();
        }
        else{
            $('#guardar').hide();
        }
    }
    
    function agregar() {
        datosArticulo = document.getElementById('pidarticuloidarticulo').value.split('_');
        idarticulo=datosArticulo[0];
        //articulo = $('#pidarticulo option:selected').val();
        articulo = $('#pidarticulonombre').val();
        codigo = $('#codigodelarticuloacargar').val();
        cantidad = $('#pcantidad').val();
        precio_compra_costo = $('#pprecio_compra_costo').val();
        porcentaje_venta = $('#pporcentaje_venta').val();

        if(idarticulo!='' && cantidad!='' && cantidad>0 && precio_compra_costo!='' && porcentaje_venta!=''){
            subtotal[cont] = (cantidad*precio_compra_costo);
            total = total+subtotal[cont];
            var fila = '<tr class="selected" id="fila'+cont+'">' +
                '<td><button type="button" class="btn btn-warning" onclick="eliminar('+cont+');">X</button></td>' +
                '<td><input type="hidden" name="codigoarticulo[]" value="'+codigo+'">'+codigo+'</td>' +
                '<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td>' +
                '<td><input type="number" name="cantidad[]" value="'+cantidad+'" readonly></td>' +
                '<td><input type="number" name="precio_compra_costo[]" value="'+precio_compra_costo+'" readonly></td>' +
                '<td><input type="number" name="porcentaje_venta[]" value="'+porcentaje_venta+'" readonly></td>' +
                '<td>'+subtotal[cont]+'</td></tr>';
            cont++;
            limpiar();
            $('#total').html('$' + total);
            evaluar();
            $('#detalles').append(fila);
        }
        else{
            alert("Haga click en Agregar Producto a la derecha del nombre así queda guardado en la Base de Datos o complete las cantidades y precios");
        }
        $('#textoAgregarArticuloOk').css('display','none');
        $('#textoAgregarArticuloCargadoPerfecto').css('display','none');
        $('#codigodelarticuloacargar').val('');
        $('#inputSuccess').focus();
        $('#pidarticuloidarticulo').val('');
//        $("#pidarticulo")[0].selectedIndex = 0;
    }

    function eliminar(index) {
        total=total - subtotal[index];
        $('#total').html("S/. " + total);
        $('#fila' + index).remove();
        evaluar();
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
@endsection