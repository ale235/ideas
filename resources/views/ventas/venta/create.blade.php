@extends ('layouts.admin')
@section ('contenido')

    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <h3>
                Nuevo Venta
            </h3>
            @if(count($errors)>0)
            <div class="alert alert-danger">
                <u>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </u>
            </div>
            @endif
        </div>
    </div>
            {!! Form::open(array('url'=>'ventas/venta', 'method'=>'POST', 'autocomplete'=>'off', 'id'=>'myForm'))!!}
            {{Form::token()}}
    <div class="row">
        <div class="col-lg-3 col-sm-3 col-md-3 col-xs-12">
        <div class="form-group">
            <label>Código del artículo</label>
            <select name="pidarticulo" id="pidarticulo" class="form-control selectpicker"  data-live-search="true">
                <option>Seleccione un artículo</option>
                @foreach($articulos as $articulo)
                    <option value="{{$articulo->idarticulo}}">{{$articulo->codigo}} {{$articulo->nombre}}</option>
                @endforeach
            </select>
            <input type="hidden" class="form-control" name="pidarticulonombre" id="pidarticulonombre"/>
            <input type="hidden" class="form-control" name="pidarticuloidarticulo" id="pidarticuloidarticulo"/>
        </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label for="cantidad">Cantidad</label>
                <input type="number" name="pcantidad" id="pcantidad" class="form-control" onkeyup="actualizar()"
                       placeholder="Cantidad">
            </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label for="precio_venta">Precio por Unidad</label>
                <input type="number" name="pprecio_venta" id="pprecio_venta" class="form-control"
                       onkeypress="return valida(event)" onkeyup="actualizar()" placeholder="Precio de Venta" disabled>
            </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <label for="pprecio_venta_cantidad">Precio * Cantidad</label>
                <input type="number" name="pprecio_venta_cantidad" id="pprecio_venta_cantidad" class="form-control"
                       placeholder="Precio * Cantidad" disabled>
            </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-12">
            <div class="form-group">
                <button type="button" id="bt_add" class="btn btn-primary">Agregar</button>
            </div>
        </div>
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <table id="detalles" class="table table-striped table-bordered table-condensed table-hover">
                <thead style="background-color: #a94442">
                <th>Opciones</th>
                <th>Artículo</th>
                <th>Cantidad</th>
                <th>Precio Venta</th>
                <th>Subtotal</th>
                </thead>
                <tfoot>
                <th>TOTAL</th>
                <th></th>
                <th></th>
                <th></th>
                <th><h4 id="total">$ 0.00</h4> <input type="hidden" name="total_venta" id="total_venta"></th>
                </tfoot>
                <tbody>

                </tbody>
            </table>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
            <div class="from-group" id="elcliente">
                <label for="proveedor">Cliente</label>
                <select name="idcliente" id="idcliente" class="lista-clientes form-control selectpicker"  data-live-search="true">
                    @foreach($personas as $persona)
                        @if($loop->index == 0){
                        <option selected="true" value="{{$persona->idpersona}}">{{$persona->nombre}}</option>
                        }
                        @endif
                        <option value="{{$persona->idpersona}}">{{$persona->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
            <div class="from-group">
                <label for="pventareal">Venta real</label>
                <input type="number" name="pventa_real" id="pventa_real" class="form-control"
                       placeholder="Venta Real">
            </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
            <div class="from-group">
                <div class="checkbox">
                    <label><input type="checkbox" id="checkCliente" name="checkCliente" value="false">Agregar cliente</label>
                </div>
            </div>
        </div>
        <div id="checkClienteInputs">
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <div class="from-group">
                <div class="row">
                    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="from-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" id='nombreCliente' name="nombre" class="form-control" placeholder="Nombre...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="from-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" placeholder="Teléfono...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="from-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" class="form-control" placeholder="Direccion...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="from-group">
                            <label for="num_documento">Numero de Documento</label>
                            <input type="text" name="num_documento" class="form-control" placeholder="Número de documento...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="from-group">
                            <label for="facebook">Facebook</label>
                            <input type="text" name="facebook" class="form-control" placeholder="Facebook...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="from-group">
                            <label for="instagram">Instagram</label>
                            <input type="text" name="instagram" class="form-control" placeholder="Instagram...">
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                        <div class="from-group">
                            <label for="email">E-mail</label>
                            <input type="text" name="email" class="form-control" placeholder="E-mail...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
            <div class="from-group">
                <div class="checkbox">
                    <label><input type="checkbox" id="checkOtraFecha" name="checkOtraFecha" value="false">Ingresar otra fecha</label>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
            <div class="from-group">
                <div class="checkbox">
                    <label><input type="checkbox" id="checkTarjetaDebito" name="checkTarjetaDebito" value="false">Tarjeta de Débito</label>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-sm-2 col-md-2 col-xs-2">
            <div class="from-group">
                <div class="checkbox">
                    <label><input type="checkbox" id="checkTarjetaCredito" name="checkTarjetaCredito" value="false">Tarjeta de Crédito</label>
                </div>
            </div>
        </div>
        <div id="checkOtraFechaInputs"  class="container">
            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
                <div class="from-group">
                        <div class="col-lg-7 col-sm-7 col-md-7 col-xs-12 inputGroupContainer">
                            <div class="from-group ">
                                <input type="text" class="form-control" id="daterange" name="daterange"/>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12" id="guardar">
            <div class="form-group">
                <input name="_token" value="{{csrf_token()}}" type="hidden">
                <button class="btn btn-primary" type="submit">Guardar</button>
                <button class="btn btn-danger" type="reset">Reset</button>
            </div>
        </div>
    </div>

            {!! Form::close()!!}
@push ('scripts')
<script>

    var form = $('#myForm'),
        checkbox = $('#checkCliente'),
        chShipBlock = $('#checkClienteInputs');
        inputCliente = $('#elcliente');
        nombreCliente = $('#nombreCliente');
    chShipBlock.hide();

    checkbox.on('click', function() {
        if($(this).is(':checked')) {
            chShipBlock.show();
            nombreCliente.attr('required', true);
            inputCliente.hide();
            checkbox.attr('value','true');
        } else {
            chShipBlock.hide();
            nombreCliente.attr('required', false);
            inputCliente.show();
            checkbox.attr('value','false');
        }
    });

    var form = $('#myForm'),
        checkOtraFecha = $('#checkOtraFecha'),
        chOtraFechaShipBlock = $('#checkOtraFechaInputs');
        chOtraFechaShipBlock.hide();
        daterangeOtraFecha = $('#daterange');
    checkOtraFecha.on('click', function() {
        if($(this).is(':checked')) {
            chOtraFechaShipBlock.show();
            daterangeOtraFecha.attr('required', true);
            checkOtraFecha.attr('value','true');
        } else {
            chOtraFechaShipBlock.hide();
            daterangeOtraFecha.attr('required', false);
            checkOtraFecha.attr('value','false');
        }
    });

    checkboxTarjetaDebito = $('#checkTarjetaDebito');
    checkboxTarjetaDebito.on('click',function () {
        if($(this).is(':checked')) {
            checkboxTarjetaDebito.attr('value','true');
        } else {
            checkboxTarjetaDebito.attr('value','false');
        }
    });

    checkboxTarjetaCredito = $('#checkTarjetaCredito');
    checkboxTarjetaCredito.on('click',function () {
        if($(this).is(':checked')) {
            checkboxTarjetaCredito.attr('value','true');
        } else {
            checkboxTarjetaCredito.attr('value','false');
        }
    });

    $(document).ready(function () {
        $('input[name="daterange"]').daterangepicker(
            {
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY',
                    cancelLabel: 'Clear',
                }
            }
        );
       $('#bt_add').click(function () {
           agregar();
       });
        $("#pidarticulo").keypress(function(event){
            if (event.which == '10' || event.which == '13') {
                event.preventDefault();
            }
        });
        $('#pidarticulo').focus();

        $(document).on('change','.lista-proveedores',function(){
            // console.log("hmm its change");

            var cat_id=$(this).val();
            var input = cat_id.split('+')
            cat_id = input[1];
            // console.log(cat_id);
            var div=$(this).parent();

            var op=" ";

            $.ajax({
                type:'get',
                url:'{!!URL::to('buscarArticuloPorProveedor')!!}',
                data:{'codigo':cat_id},
                success:function(data){
                    //console.log('success');

                    console.log(data);

                    //console.log(data.length);
                    op+='<option value="0" selected disabled>Elegí un artículo</option>';
                    for(var i=0;i<data.length;i++){
                        op+='<option value="'+data[i].idarticulo+'">'+data[i].codigo+ ' ' +data[i].nombre+'</option>';
                    }
                    div.parent().parent().parent().parent().parent().find('.nombre-articulo').html(" ");
                    div.parent().parent().parent().parent().parent().find('.nombre-articulo').append(op);
//                    div.find('.nombre-articulo').html(" ");
//                    div.find('.nombre-articulo').append(op);
//                    alert($(".lista-proveedores").prop('selectedIndex'));
                    //div.parent().parent().parent().parent().parent().find('.nombre-articulo').unbind('change')
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
                url:'{!!URL::to('buscarPrecioArticuloVentas')!!}',
                data:{'id':cat_id},
                success:function(data){
                    //console.log('success');

                    console.log(data);

                    $('#pprecio_venta').val(data[0].precio_venta);
                    $('#pidarticulo').val(data.codigo);
                    $('#pidarticuloidarticulo').val(data.idarticulo);
                    $('#pidarticulonombre').val(data.nombre);

                },
                error:function(){

                }
            });
        });

        $(document).on('change','#pidarticulo',function(){
            // console.log("hmm its change");

            var cat_codigo=$(this).val();
            var div=$(this).parent();

            var op=" ";

            $.ajax({
                type:'get',
                url:'{!!URL::to('buscarPrecioArticuloVentasPorCodigo')!!}',
                data:{'id':cat_codigo},
                success:function(data){
                    //console.log('success');

                    console.log(data);

                    $('#pprecio_venta').val(data.ultimoprecio);
                    $('#pidarticulo').val(data.codigo);
                    $('#pidarticuloidarticulo').val(data.idarticulo);
                    $('#pidarticulonombre').val(data.nombre);
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
        var b =  $('#pprecio_venta').val();
        var cantidad =  $('#pcantidad').val();
        $('#pprecio_venta_cantidad').val(cantidad *b);
    }
    
    function limpiar(){
        $('#pcantidad').val("");
        $('#pprecio_venta').val("");
        $('#pidarticulo').val("");
        $('#pidarticulonombre').val("");
        $('#pprecio_venta_cantidad').val("")
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
        cantidad = $('#pcantidad').val();

        precio_venta = $('#pprecio_venta').val();

        if(idarticulo!='' && cantidad!='' && cantidad>0 && precio_venta!=''){

                subtotal[cont] = (cantidad*precio_venta);
                total = total+subtotal[cont];
                var fila = '<tr class="selected" id="fila'+cont+'">' +
                    '<td><button type="button" class="btn btn-warning" onclick="eliminar('+cont+');">X</button></td>' +
                    '<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</td>' +
                    '<td><input type="number" name="cantidad[]" value="'+cantidad+'"></td><td><input type="number" name="precio_venta[]" value="'+precio_venta+'"></td>' +
                    '<td>'+subtotal[cont]+'</td>' +
                    '</tr>';
                cont++;
                limpiar();
                $('#total').html('$: ' + total);
                $('#total_venta').val(total);
                evaluar();
                $('#detalles').append(fila);


        }
        else{
            alert("error al ingresar uun detalle de la venta, revise los datos del articulo");
        }
    }

    function eliminar(index) {
        total=total - subtotal[index];
        $('#total').html("S/. " + total);
        $('#total_venta').val(total);
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