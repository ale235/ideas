<?php

namespace ideas\Http\Controllers;

use Illuminate\Http\Request;

use ideas\Articulo;
use ideas\Http\Requests;
use Illuminate\Support\Facades\Redirect;
use ideas\Http\Requests\PersonaFormRequest;
use Carbon\Carbon;
use ideas\DetalleVenta;
use Maatwebsite\Excel\Facades\Excel;

use DB;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = DB::table('articulo')
            ->select('proveedor',DB::raw('count(*) as total'))
            ->groupBy('proveedor')
            ->get();

        $articulos =  DB::table('articulo as a')
            ->join('detalle_venta as dv','a.idarticulo','=','dv.idarticulo')
            ->get();

        $venta = DB::table('venta')
            ->select('fecha_hora', DB::raw('sum(total_venta) as total_venta'))
            ->whereBetween('fecha_hora',['2016-02-02 00:00:00','2017-02-05 00:00:00'])
            ->groupBy('fecha_hora')
            ->get();

        $mytime= Carbon::now('America/Argentina/Buenos_Aires');

        $detalle_venta_hoy = DB::table('detalle_venta as dv')
            ->join('venta as v','dv.idventa','=','v.idventa')
            ->join('articulo as art','art.idarticulo','=','dv.idarticulo')
            ->whereDay('v.fecha_hora',$mytime->day)
            ->whereMonth('v.fecha_hora',$mytime->month)
            ->whereYear('v.fecha_hora',$mytime->year)
            ->get();
        return view('reportes.grafico.index', ['proveedores'=> $proveedores,'articulos'=>$articulos, 'detalle_venta_hoy'=> $detalle_venta_hoy]);
        //return view('home');
    }

    public function projectsChartData()
    {
//        $devlist =  DB::table('articulo as a')
//            ->join('detalle_venta as dv','a.idarticulo','=','dv.idarticulo')
//            ->get();

        $start = Carbon::parse('2017-02-01 00:00:00')->startOfDay();  //2016-09-29 00:00:00.000000
        $end = Carbon::parse('2017-02-03 00:00:00')->endOfDay(); //2016-09-29 23:59:59.000000

        $devlist = DB::table('venta')
            ->select('total_venta','fecha_hora')
//            ->whereBetween('fecha_hora',[new Carbon('2017-02-02 00:00:00'),new Carbon('2017-02-03 00:00:00')])
            ->whereBetween('fecha_hora',[$start, $end])
            ->groupBy('total_venta','fecha_hora')
            ->distinct('fecha_hora')->get();

        //Articulo mçàs vendido

        $collection = DB::table('detalle_venta as dv')
            ->join('articulo as art','art.idarticulo','=','dv.idarticulo')
            ->select('dv.idarticulo',DB::raw('sum(dv.cantidad) as lacantidad'),'art.proveedor')
            ->groupBy('dv.idarticulo','art.proveedor')
            ->orderBy('lacantidad','desc')
            ->get();

        return $collection;
    }

    public function articulosSinStock()
    {
        //Articulo mçàs vendido

        $collection = DB::table('articulo')
            ->select(DB::raw('COUNT(*) as cantidad'))
            ->where('stock','=','0')
            ->where('estado','=','Activo')
            ->get();

        return $collection;
    }

    public function getDetalleStock(Request $request) {
        if($request){
            $query = trim($request->get('searchText'));
            $stock = DB::table('articulo')
                ->where([
                    ['codigo','LIKE','%'.$query.'%'],
                    ['estado','=','Activo'],
                    ['stock','=','0']])
                ->orWhere([
                    ['nombre','LIKE','%'.$query.'%'],
                    ['estado','=','Activo'],
                    ['stock','=','0']])
                ->paginate(30);
        }


        return view('reportes.grafico.detallestock', ['stock'=> $stock,'searchText'=>$query]);
    }

    public function articulosStockNegativo()
    {
        //Articulo mçàs vendido

        $collection = DB::table('articulo')
            ->select(DB::raw('COUNT(*) as cantidad'))
            ->where('stock','<','0')
            ->where('estado','=','Activo')
            ->get();

        return $collection;
    }

    public function getDetalleStockNegativo(Request $request) {
        if($request){
            $query = trim($request->get('searchText'));
            $stock = DB::table('articulo')
                ->where([
                    ['codigo','LIKE','%'.$query.'%'],
                    ['estado','=','Activo'],
                    ['stock','<','0']])
                ->orwhere([
                    ['nombre','LIKE','%'.$query.'%'],
                    ['estado','=','Activo'],
                    ['stock','<','0']])
                ->paginate(30);
        }


        return view('reportes.grafico.detallestocknegativo', ['stock'=> $stock,'searchText'=>$query]);
    }

    public function cajaDelDiaReportes()
    {
        $mytime= Carbon::now('America/Argentina/Buenos_Aires');

        //$date=$mytime->toDateTimeString();

        $collection = DB::table('venta')
            ->select(DB::raw('SUM(total_venta_real) as total'))
            ->whereDay('fecha_hora',$mytime->day)
            ->whereMonth('fecha_hora',$mytime->month)
            ->whereYear('fecha_hora',$mytime->year)
            ->get();

        return $collection;
    }

    public function cajaDeAyer()
    {
        $mytime= Carbon::now('America/Argentina/Buenos_Aires')->yesterday();

        //$date=$mytime->toDateTimeString();

        $collection = DB::table('venta')
            ->select(DB::raw('SUM(total_venta_real) as total'))
            ->whereDay('fecha_hora',$mytime->day)
            ->whereMonth('fecha_hora',$mytime->month)
            ->whereYear('fecha_hora',$mytime->year)
            ->get();

        return $collection;
    }

    public function exportVentasPorProducto()
    {

        $collection = DB::table('detalle_venta as v')
            ->join('articulo as a','a.idarticulo','=','v.idarticulo')
            ->select('a.nombre',DB::raw('SUM(v.cantidad) as cantidadTotal'))
            ->where('a.estado','=','Activo')
            ->groupBy('a.nombre')
            ->orderBy('cantidadTotal','desc')
//            ->orderBy('desc')
            ->limit(10)
            ->get();

        $columna = [];
        $cont2 = 1;
        $fila0 = [];
        $fila0[0] = 'Nombre';
        $fila0[1] = 'Cantidad Total';

        $columna[0] = $fila0;

        foreach ($collection as $a) {
            $fila = [];

            $fila[0] = $a->nombre;
            $fila[1] = $a->cantidadTotal;
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
        }

        Excel::create('Laravel Excel', function ($excel) use ($columna) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna) {

                $sheet->fromArray($columna);

            });

        })->download('xls');

//        return $collection;
    }

    public function ventasPorProductos()
    {

        $collection = DB::table('detalle_venta as v')
            ->join('articulo as a','a.idarticulo','=','v.idarticulo')
            ->select('a.nombre',DB::raw('SUM(v.cantidad) as cantidadTotal'))
            ->where('a.estado','=','Activo')
            ->groupBy('a.nombre')
            ->orderBy('cantidadTotal','desc')
//            ->orderBy('desc')
            ->limit(10)
            ->get();

        return $collection;
    }

    public function proveedorQueMasProductosVende()
    {

        $collection = DB::table('detalle_venta as v')
            ->join('articulo as a','a.idarticulo','=','v.idarticulo')
            ->join('persona as p','a.proveedor','=','p.codigo')
            ->select('a.proveedor',DB::raw('SUM(v.cantidad) as cantidadTotal'))
            ->where('p.estado','=','Activo')
            ->groupBy('a.proveedor')
            ->orderBy('cantidadTotal','desc')
//            ->orderBy('desc')
            ->limit(10)
            ->get();

        return $collection;
    }

    public function exportProveedorQueMasProductosVende()
    {

        $collection = DB::table('detalle_venta as v')
            ->join('articulo as a','a.idarticulo','=','v.idarticulo')
            ->join('persona as p','a.proveedor','=','p.codigo')
            ->select('a.proveedor',DB::raw('SUM(v.cantidad) as cantidadTotal'))
            ->where('p.estado','=','Activo')
            ->groupBy('a.proveedor')
            ->orderBy('cantidadTotal','desc')
//            ->orderBy('desc')
            ->limit(10)
            ->get();

        $columna = [];
        $cont2 = 1;
        $fila0 = [];
        $fila0[0] = 'Proveedor';
        $fila0[1] = 'Cantidad Total';

        $columna[0] = $fila0;

        foreach ($collection as $a) {
            $fila = [];

            $fila[0] = $a->proveedor;
            $fila[1] = $a->cantidadTotal;
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
        }

        Excel::create('Laravel Excel', function ($excel) use ($columna) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna) {

                $sheet->fromArray($columna);

            });

        })->download('xls');

    }

    public function ganancias(){
        $today = Carbon::now('America/Argentina/Buenos_Aires');
        $firstDay = Carbon::now('America/Argentina/Buenos_Aires');
        $firstDay->day = 1;
        $firstDay->hour = 0;
        $firstDay->minute = 0;
        $firstDay->second = 0;
        $firstDay->toDateTimeString();
        $today->toDateTimeString();
        $collection = DB::table('venta as v')
//            ->join('detalle_venta as dv','v.idventa','=','dv.idventa')
//            ->join('precio as p','p.idarticulo','=','dv.idarticulo')
//            ->select('v.idventa', 'p.idprecio', 'p.fecha as fechaprecio', 'v.fecha_hora as fechaventa', 'dv.precio_venta','dv.idarticulo','p.precio_compra','dv.cantidad')
            ->select(DB::raw('SUM(v.ganancia) as ganancia'))
            ->whereBetween('v.fecha_hora', array($firstDay, $today))
//            ->orderBy('v.idventa','desc')
//            ->orderBy('dv.idarticulo','desc')
            ->get();

//        Excel::create('Laravel Excel', function ($excel) use ($columna) {
//
//            $excel->sheet('Excel sheet', function ($sheet) use ($columna) {
//
//                $sheet->row(1, ['Fecha', 'Cliente', 'Total']);
//                $sheet->fromArray($columna, null, 'A1', false, false);
//
//            });
//
//        })->download('xls');



        return response()->json($collection);
        //return $collection
    }

    public function show(Request $request)
    {
    }

    public function volveracerotodos(){

        try {
            DB::beginTransaction();

            DB::table('articulo')
                ->where('stock','<',0)
                ->update(['stock' => 0]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
        return Redirect::to('reportes/detallestock');
    }

    public function ventasPorDias(Request $request)
    {

        $collection = DB::table('venta as v')
            ->whereBetween('v.fecha_hora', array($request->get('startDate'), $request->get('endDate')))
//            ->orderBy('desc')
            ->limit(10)
            ->get();

        $something = DB::table('venta as v')
            ->whereBetween('v.fecha_hora', array($request->get('startDate'), $request->get('endDate')))
            ->get(['idventa','fecha_hora','total_venta_real'])
            ->groupBy(function($date) {
            return Carbon::parse($date->fecha_hora)->format('y-m-d');
        });

        $test =[[50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60],
                [60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,50,70,60,50,70,60,34],
                [70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,50,70,60,70]] ;

        return $something;
    }

    public function ventasPorProductosPorFecha(Request $request)
    {

        $collection = DB::table('detalle_venta as v')
            ->join('articulo as a','a.idarticulo','=','v.idarticulo')
            ->join('venta as vv','vv.idventa','=','v.idventa')
            ->select('a.nombre',DB::raw('SUM(v.cantidad) as cantidadTotal'))
            ->where('a.estado','=','Activo')
            ->whereBetween('vv.fecha_hora', array($request->get('startDate'), $request->get('endDate')))
            ->groupBy('a.nombre')
            ->orderBy('cantidadTotal','desc')
//            ->orderBy('desc')
            ->limit(10)
            ->get();

        return $collection;
    }

    public function verstock(Request $request)
    {


        $mytime = Carbon::now('America/Argentina/Buenos_Aires');
        $mytime2 = Carbon::now('America/Argentina/Buenos_Aires');
        $mytime2->hour = 0;
        $mytime2->minute = 0;
        $mytime2->second = 0;
        $yesterday = $mytime2->toDateTimeString();
        $today = $mytime->toDateTimeString();

        $stock = DB::table('articulo as a')
            //->select('a.codigo','a.nombre','a.proveedor','p.precio_compra','p.precio_venta')
            ->join('precio as p', 'p.idarticulo', '=', 'a.idarticulo')
            ->where('a.stock','>=','1')
            ->orderby('a.codigo','asc')
            ->get();

        $proveedores = DB::table('articulo as a')
            ->select('a.proveedor')
            ->where('a.stock','>=','1')
            ->distinct()
            ->orderby('a.codigo','asc')
            ->get();

        $cont2 = 1;
        $columna = [];
        $totalCosto = 0;
        $totalVenta = 0;
        //dd($stock);
        foreach ($stock as $a) {
            $fila = [];

            $fila[0] = $a->codigo;
            $fila[1] = $a->nombre;
            $fila[2] = $a->proveedor;
            $fila[3] = $a->precio_compra;
            $fila[4] = $a->precio_venta;
            $fila[5] = $a->stock;
            $fila[6] = $a->stock * $a->precio_compra;
            $fila[7] = $a->stock * $a->precio_venta;
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
            $totalCosto = $totalCosto + $fila[6];

            $totalVenta = $totalVenta + $fila[7];
        }
//        usort($columna, function ($item1, $item2) {
//            if ($item1[5] == $item2[5]) return 0;
//            return $item1[5] < $item2[5] ? -1 : 1;
//        });
        $aux = array();
        $final = array();
        foreach ($columna as $arr)
            if (!in_array($arr[0], $aux)){
                $aux[] = $arr[0];
                $final[] = $arr;
            }

        $filaVacia = [];
        $filaVacia[0] = ' ';
        $filaVacia[1] = ' ';
        $filaVacia[2] = ' ';
        $filaVacia[3] = ' ';
        $filaVacia[4] = ' ';
        $final[$cont2+2] = $filaVacia;

        $filanueva = [];
        $filanueva[0] = ' ';
        $filanueva[1] = 'Sumatoria Costo: $' . $totalCosto;
        $filanueva[2] = ' ';
        $filanueva[3] = ' ';
        $filanueva[4] = ' ';
        $final[$cont2+3] = $filanueva;

        $filanueva2 = [];
        $filanueva2[0] = ' ';
        $filanueva2[1] ='Sumatoria Ventas: $' . $totalVenta;
        $filanueva2[2] = ' ';
        $filanueva2[3] = ' ';
        $filanueva2[4] = ' ';
        $final[$cont2+4] = $filanueva2;

        //dd($proveedores);
        Excel::create('Resultado entre: ', function ($excel) use ($final,$proveedores,$totalCosto,$totalVenta) {

            $excel->sheet('Excel sheet', function ($sheet) use ($final,$proveedores,$totalCosto,$totalVenta) {

                $sheet->row(1, ['Codigo', 'Nombre', 'Proveedor','Costo/U','PdV/U','Cantidad','Costo Total','PdV Total']);
                $sheet->fromArray($final, null, 'A2', false, false);

            });

        })->download('xls');
    }

}

//public function scopeBirthdays($query)
//{
//    return $query-> User::whereMonth('DOB' , Carbon::today()->month);