@extends ('layouts.admin')
@section ('contenido')
    <html>
    <head>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
        </script>
    </head>
    <body>
    <section class="content-header">
        <h1>
            Dashboard
            <small>Control panel</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                        <h3><span id="sin_stock"></span></h3>

                        <p>Sin stock</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="grafico/detallestock" class="small-box-footer">Más Información <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3><sup style="font-size: 20px">$</sup><span id="caja_del_dia"></span></h3>
                        <p>Caja actual</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#myModal" >Más Información <i class="fa fa-arrow-circle-right"></i></a>
                    <!-- Modal -->
                    <div id="myModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead style="background-color: #a94442">
                                            <th>Nombre</th>
                                            <th>Código</th>
                                            <th>Cantidad</th>
                                            <th>Precio Venta</th>
                                            </thead>
                                            <tfoot>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th style="color: black;">
                                                <?php $a = 0; ?>
                                                @foreach($detalle_venta_hoy as $vent)
                                                        <?php $a = $a + ($vent->precio_venta * $vent->cantidad) ?>
                                                @endforeach
                                                    ${{$a}}
                                            </th>
                                            </tfoot>
                                            <tbody>
                                            @foreach($detalle_venta_hoy as $vent)
                                                <tr>
                                                    <td style="color: black;">{{$vent->nombre}}</td>
                                                    <td style="color: black;">{{$vent->codigo}}</td>
                                                    <td style="color: black;">{{$vent->cantidad}}</td>
                                                    <td style="color: black;">{{$vent->precio_venta}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3><sup style="font-size: 20px">$</sup><span id="caja_de_ayer"></span></h3>

                        <p>Caja de ayer</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="" class="small-box-footer">Más Información <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-xs-6">
                <!-- small box -->
                <div class="small-box bg-red">
                    <div class="inner">
                        {{--<div class="row">--}}
                            {{--<div class="col-lg-5">--}}
                                <h3><sup style="font-size: 20px">$</sup><span id="ganancias"></span></h3>
                            {{--</div>--}}
                            {{--<div class="col-lg-7">--}}
                                {{--<input type="text" class="form-control" id="daterange" name="daterange"/>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <p>Ganancias del mes</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="grafico/detalleganacias" class="small-box-footer">Más Información <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
            <div class="container col-lg-12 ">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <a href="{{URL::action('ReportesController@exportProveedorQueMasProductosVende')}}"><button class="btn btn-success">Exportar<i class="fa fa-file-excel-o"></i></button></a>
                        <div id="sales-chart" style="position: relative; height: 300px;"></div>
                    </div>
                </div>
            </div>
            <div class="container col-lg-12 ">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <a href="{{URL::action('ReportesController@exportVentasPorProducto')}}"><button class="btn btn-success">Exportar<i class="fa fa-file-excel-o"></i></button></a>
                        <div id="revenue-chart" style="position: relative; height: 300px;"></div>
                    </div>
                </div>
            </div>
            <!-- Left col -->
            {{--<section class="col-lg-12 connectedSortable">--}}
                {{--<!-- Custom tabs (Charts with tabs)-->--}}
                {{--<div class="nav-tabs-custom">--}}
                    {{--<!-- Tabs within a box -->--}}
                    {{--<ul class="nav nav-tabs pull-right">--}}
                        {{--<li class="active"><a href="#revenue-chart" data-toggle="tab">Area</a></li>--}}
                        {{--<li><a href="#sales-chart" data-toggle="tab">Donut</a></li>--}}
                        {{--<li class="pull-left header"><i class="fa fa-inbox"></i> Sales</li>--}}
                    {{--</ul>--}}
                    {{--<div class="tab-content no-padding">--}}
                        {{--<!-- Morris chart - Sales -->--}}
                        {{--<div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;"></div>--}}
                        {{--<div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;"></div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<!-- /.nav-tabs-custom -->--}}


            {{--</section>--}}
            <!-- /.Left col -->
            <!-- right col (We are only adding the ID to make the widgets sortable)-->
            <!-- right col -->
        </div>
        <!-- /.row (main row) -->

    </section>
    </body>
    </html>

@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $.ajax({
            type: 'get',
            url: '{!!URL::to('articulosSinStock')!!}',
            success: function (data) {
                //console.log('success');
                $("#sin_stock").text(data[0].cantidad);

            },
            error: function () {

            }
        });

        $.ajax({
            type: 'get',
            url: '{!!URL::to('cajaDelDiaReportes')!!}',
            success: function (data) {
                //console.log('success');
                $("#caja_del_dia").text(data[0].total);
            },
            error: function () {

            }
        });

        $.ajax({
            type: 'get',
            url: '{!!URL::to('cajaDeAyer')!!}',
            success: function (data) {
                //console.log('success');
                $("#caja_de_ayer").text(data[0].total);
            },
            error: function () {

            }
        });

        $.ajax({
            type: 'get',
            url: '{!!URL::to('ventasPorProductos')!!}',
            success: function (data) {
                //console.log('success');
                google.charts.load('current', {'packages':['corechart','bar']});

                google.charts.setOnLoadCallback(function(){ drawBar(data) });



                function drawBar(data) {
                    var output =    data.map(function(obj) {
                        return Object.keys(obj).sort().map(function(key) {
                            return obj[key];
                        });
                    });
//                    output.forEach(function(element) {
//                        element[0] = parseInt(element[0]);
////                        element.reverse();
//                    });
                    output.reverse();

                    output.unshift(['Articulo','Cantidad']);
                    var datav = new google.visualization.arrayToDataTable(output);

                    var options = {
                        title: 'Productos más vendidos',
                        legend: { position: 'none' },
//                        chart: { subtitle: 'popularity by percentage' },
                        axes: {
                            x: {
//                                0: { side: 'top', label: 'Productos más vendidos'} // Top x-axis.
                            }
                        },
                        bar: { groupWidth: "100%" }
                    };

                    var chart = new google.charts.Bar(document.getElementById('revenue-chart'));
                    // Convert the Classic options to Material options.
                    chart.draw(datav, google.charts.Bar.convertOptions(options));
                };

            },
            error: function () {

            }
        });

        $.ajax({
            type: 'get',
            url: '{!!URL::to('proveedorQueMasProductosVende')!!}',
            success: function (data) {
                //console.log('success');
                google.charts.load('current', {'packages':['corechart','bar']});

                google.charts.setOnLoadCallback(function(){ drawBar(data) });



                function drawBar(data) {
                    var output =    data.map(function(obj) {
                        return Object.keys(obj).sort().map(function(key) {
                            return obj[key];
                        });
                    });
//                    output.forEach(function(element) {
//                        element[0] = parseInt(element[0]);
//                    });
                    output.reverse();

                    output.unshift(['Cantidad','Proveedor']);
                    var datav = new google.visualization.arrayToDataTable(output);

                    var options = {
                        title: 'Proveedor que más productos vende',
                        legend: { position: 'none' },
//                        chart: { subtitle: 'popularity by percentage' },
                        axes: {
                            x: {
//                                0: { side: 'top', label: 'Productos más vendidos'} // Top x-axis.
                            }
                        },
                        bar: { groupWidth: "100%" }
                    };

                    var chart = new google.charts.Bar(document.getElementById('sales-chart'));
                    // Convert the Classic options to Material options.
                    chart.draw(datav, google.charts.Bar.convertOptions(options));
                };

            },
            error: function () {

            }
        });

        $.ajax({

            type:'get',
            url:'{!!URL::to('ganancias')!!}',
            success:function(result) {
                $("#ganancias").text(result[0].ganancia);

            },
            error: function(a){
            }
        });


    });

</script>
@endpush
