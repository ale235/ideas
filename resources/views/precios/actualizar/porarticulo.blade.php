@extends ('layouts.admin')
@section ('contenido')
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Cambiar Precio por Artículo</h3>
        </div>
        <div class="box-body">
            {!! Form::open(array('url'=>'precios/porarticulo', 'method'=>'POST', 'autocomplete'=>'off'))!!}
            {{Form::token()}}
            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                <div class="from-group">
                    <label for="proveedor">Proveedor</label>
                    <select name="idproveedor" id="idproveedor" class="lista-proveedores form-control selectpicker" data-live-search="true">
                        <option value="0" disabled="true" selected="true">Seleccione el Proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{$proveedor->idpersona}}+{{$proveedor->codigo}}">{{$proveedor->codigo}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
                <div class="form-group">
                    <label>Artículo</label>
                    <select name="pidarticulo" id="pidarticulo" class="selectpicker form-control" data-live-search="true">
                        <option value="0" disabled="true" selected="true">Artículos</option>
                    </select>
                </div>
            </div>
            <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
                <table id="detalles1" class="table table-bordered table-hover">
                    <thead>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Porcentaje de venta actual</th>
                    <th>Costo Actual</th>
                    <th>Nuevo porcentaje</th>
                    <th>Precio de venta actual</th>
                    </thead>
                    <tfoot>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    </tfoot>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <input name="_token" value="{{csrf_token()}}" type="hidden">
            <button class="btn btn-primary" type="submit">Guardar</button>
            {!! Form::close()!!}
        </div>
        <!-- /.box-body -->
    </div>
@endsection
@push ('scripts')
<script>

    $(document).ready(function () {

        $(document).on('change', '.selectpicker', function () {
            $('.selectpicker').selectpicker('refresh');
        });
        $(document).on('change', '#detalles1', function () {
            $('#detalles1 > tbody  > tr').each(function(data){
                var costo = parseInt($($(this).find('td').eq(3)[0]).children().val());
                var porcentaje_venta = $($(this).find('td').eq(4)[0]).children().val();
                $($(this).find('td').eq(5)[0]).children().text('$' + Math.round(costo + costo*porcentaje_venta/100))
            });
        });
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
                    if(data.length !=0){
                        //console.log(data.length);
                        op+='<option value="0" selected disabled>Elegí un artículo</option>';
                        for(var i=0;i<data.length;i++){
                            op+='<option value="'+data[i].idarticulo+'">'+data[i].nombre+'</option>';
                        }
                        $('#pidarticulo').html(" ");
                        $('#pidarticulo').append(op);
                        $('#pidarticulo').selectpicker('refresh');
                    }

                },
                error:function(){

                }
            });
        });
        $(document).on('change','#pidarticulo',function(){
            var cat_id=$(this).val();
            var div=$(this).parent();

            var op=" ";

            $.ajax({
                type:'get',
                url:'{!!URL::to('buscarPrecioArticulo')!!}',
                data:{'id':cat_id},
                success:function(data){

                    var fila = '<tr class="selected">' +
                        '<td><label type="text">'+data[0].codigo+'</label></td>' +
                        '<td><label type="text">'+data[0].nombre+'</label></td>' +
                        '<td><label type="number">%'+data[0].porcentaje+'</label></td>' +
                        '<td><input type="number" name="nuevo_precio_compra" value="'+data[0].precio_compra+'"></td>' +
                        '<td><input type="number" id="nuevo_porcentaje1" name="nuevo_porcentaje1" value='+data[0].porcentaje+'></td>' +
                        '<td><label type="number">$'+data[0].precio_venta+'</label></td>' +
                        '</tr>';

                    $('#detalles1').append(fila);
                    //$('#pidarticulo').attr('disabled',true);
                    $('.lista-proveedores').attr('disabled',true);
                },
                error:function(){

                }
            });
        });
    });
</script>
@endpush