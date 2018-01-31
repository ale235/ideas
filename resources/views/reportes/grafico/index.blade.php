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
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3><span id="stocknegativo"></span></h3>
                        <p>Stock Negativo</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="{{URL::action('ReportesController@getDetalleStockNegativo')}}" class="small-box-footer">Más Información <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
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
                    <a href="{{URL::action('ReportesController@getDetalleStock')}}" class="small-box-footer">Más Información <i class="fa fa-arrow-circle-right"></i></a>
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

                                            </tfoot>
                                            <tbody>

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
            <div style="display: none" class="col-lg-3 col-xs-6">
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
        <div class="row">
            <div class="col-md-12">
                <!-- Application buttons -->
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Reportes</h3>
                    </div>
                    <div class="box-body">
                        <a href="{{URL::action('ReportesController@verstock')}}" class="btn btn-app">
                            <i class="fa fa-barcode"></i> Exportar Stock
                        </a>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
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
                        <div id="myChart"></div>
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
    <input id="daterange" />
    <div id="ventachart"></div>
    <div style="text-align:center;">
        <label for="from">From</label>
        <input type="text" id="from" name="from" readonly="readonly" />
        <label for="to">to</label>
        <input type="text" id="to" name="to" readonly="readonly"  />
        <input type="button" id="btnShow" value="Show" />
    </div>
    </html>

@endsection
@push('scripts')
<script src="https://cdn.zingchart.com/zingchart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.js"></script>
<script>
    $.ajax({
        type: 'get',
        url: '{!!URL::to('ventasPorProductos')!!}',
        success: function (data) {
            console.log(data);
            var labels = data.map(function (x) {
                return x.nombre;
            });
            var dataChart = data.map(function (x) {
                return parseInt(x.cantidadTotal);
            });
            zingchart.render({
                id: 'myChart',
                data: {
                    type: "bar",
                    scaleX: {
                        label: {
                            text: "Productos más vendidos"
                        },
                        labels: labels
                    },
                    series: [{
                        values: dataChart
                    }]
                }
            });
        },
        error: function () {

        }
    });

    $(function () {

        var start = moment().subtract(29, 'days');
        var end = moment();

        $('#daterange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });
    });
    $('#daterange').on('apply.daterangepicker', function (ev, picker) {
        console.log(picker.startDate.format('YYYY-MM-DD'));
        console.log(picker.endDate.format('YYYY-MM-DD'));
        $.ajax({
            type: 'get',
            url: '{!!URL::to('ventasPorProductosPorFecha')!!}',
            data: {'startDate': picker.startDate.format('YYYY-MM-DD'), 'endDate': picker.endDate.format('YYYY-MM-DD')},
            success: function (data) {
                console.log(data);
                var labels = data.map(function (x) {
                    return x.nombre;
                });
                var dataChart = data.map(function (x) {
                    return parseInt(x.cantidadTotal);
                });
//                zingchart.render({
//                    id: 'myChart',
//                    data: {
//                        type: "bar",
//                        scaleX: {
//                            label: {
//                                text: "Productos más vendidos"
//                            },
//                            labels: labels
//                        },
//                        series: [{
//                            values: dataChart
//                        }]
//                    }
//                });


                zingchart.exec('myChart', 'setseriesvalues', {
                    plotindex: 0,
                    values: dataChart
                });
            },
            error: function () {

            }
        });
    });
</script>
<style>
    .ranges li:last-child { display: none; }

    .ui-datepicker-calendar {
        display: none;
    }
</style>
<script>
    $("#from, #to").datepicker({
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'yy-m-d',
        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).datepicker('setDate', new Date(year, month, 1));
        },
        beforeShow: function(input, inst) {
            if ((datestr = $(this).val()).length > 0) {
                year = datestr.substring(datestr.length - 4, datestr.length);
                month = jQuery.inArray(datestr.substring(0, datestr.length - 5), $(this).datepicker('option', 'monthNames'));
                $(this).datepicker('option', 'defaultDate', new Date(year, month, 1));
                $(this).datepicker('setDate', new Date(year, month, 1));
            }
            var other = this.id == "from" ? "#to" : "#from";
            var option = this.id == "from" ? "maxDate" : "minDate";
            if ((selectedDate = $(other).val()).length > 0) {
                year = selectedDate.substring(selectedDate.length - 4, selectedDate.length);
                month = jQuery.inArray(selectedDate.substring(0, selectedDate.length - 5), $(this).datepicker('option', 'monthNames'));
                $(this).datepicker("option", option, new Date(year, month, 1));
            }
        }
    });
    $("#btnShow").click(function() {
        if ($("#from").val().length == 0 || $("#to").val().length == 0) {
            alert('All fields are required');
        } else {
            $.ajax({
                type: 'get',
                url: '{!!URL::to('ventasPorDias')!!}',
                data:{'startDate':$("#from").val(),'endDate':$("#to").val()},
                success: function (data) {
                    console.log(data);
                    var arr = [];
                    for (var key in data) {
                        if (data.hasOwnProperty(key)) {
                            console.log(key + " -> " + data[key]);
                        }
                    }
//                    data.forEach(function(item) {
//                        var fecha = item.fecha_hora.split(' ');
//                        arr.push({
//                            values: fecha[0]
//                        });
//                    });
                    zingchart.render({
                        id: 'ventachart',
                        data: {
                            type: "line",
                            scaleX: {
                                label: {
                                    text: "rgssg"
                                },
                                labels: [1,2,3,
                                    4,
                                    5,
                                    6,
                                    7,
                                    8,
                                    9,
                                    10,
                                    11,
                                    12,
                                    13,
                                    14,
                                    15,
                                    16,
                                    17,
                                    18,
                                    19,
                                    20,
                                    21,
                                    22,
                                    23,
                                    24,
                                    25,
                                    26,
                                    27,
                                    28,
                                    29,
                                    30,
                                    31
                                ]
                            },
                            series: arr
                        }
                    });
                },
                error: function () {

                }
            });
            alert('Selected Month Range :' + $("#from").val() + ' to ' + $("#to").val());
        }
    });
    $(function() {

        var start = moment().subtract(29, 'days');
        var end = moment();

        $('#daterange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });
    });
    $('#daterange').on('apply.daterangepicker', function(ev, picker) {
        console.log(picker.startDate.format('YYYY-MM-DD'));
        console.log(picker.endDate.format('YYYY-MM-DD'));
        $.ajax({
            type: 'get',
            url: '{!!URL::to('ventasPorDias')!!}',
            data:{'startDate':picker.startDate.format('YYYY-MM-DD'),'endDate':picker.endDate.format('YYYY-MM-DD')},
            success: function (data) {
                console.log(data);
                var arr = [];
                var len = data.length;
                for (var i = 0; i < len; i++) {
                    arr.push({
                        values: data[i]
                    });
                }
                zingchart.render({
                    id: 'ventachart',
                    data: {
                        type: "line",
                        scaleX: {
                            label: {
                                text: "rgssg"
                            },
                            labels: [1,2,3,
                                4,
                                5,
                                6,
                                7,
                                8,
                                9,
                                10,
                                11,
                                12,
                                13,
                                14,
                                15,
                                16,
                                17,
                                18,
                                19,
                                20,
                                21,
                                22,
                                23,
                                24,
                                25,
                                26,
                                27,
                                28,
                                29,
                                30,
                                31
                            ]
                        },
                        series: arr
                    }
                });
            },
            error: function () {

            }
        });
    });
</script>
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
            url: '{!!URL::to('articulosStockNegativo')!!}',
            success: function (data) {
                //console.log('success');
                $("#stocknegativo").text(data[0].cantidad);

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

//                    var chart = new google.charts.Bar(document.getElementById('revenue-chart'));
//                    // Convert the Classic options to Material options.
//                    chart.draw(datav, google.charts.Bar.convertOptions(options));
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
