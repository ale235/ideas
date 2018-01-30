<?php

namespace ideas\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Redirect;
use ideas\Http\Requests;
use ideas\Http\Requests\VentaFormRequest;
use Illuminate\Support\Facades\Input;
use ideas\Venta;
use ideas\Articulo;
use ideas\DetalleVenta;
use ideas\Precio;
use ideas\Persona;
use DB;

use Carbon\Carbon;
use Response;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

class VentaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request) {
            if ($request->get('daterange') == null || $request->get('daterange') == '') {
                $mytime = Carbon::now('America/Argentina/Buenos_Aires');
                $date = $mytime->toDateTimeString();
                $ventas = DB::table('venta as v')
                    ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
                    ->join('detalle_venta as dv', 'v.idventa', '=', 'dv.idventa')
                    ->select('v.idventa', 'v.fecha_hora', 'p.nombre', 'v.tipo_comprobante', 'v.serie_comprobante', 'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta','v.total_venta_real')
                   // ->whereBetween('v.fecha_hora', array(new Carbon($pieces[1]), new Carbon($pieces[0])))
//                ->where('v.num_comprobante', 'LIKE', '%'.$query.'%')
                    ->whereDay('fecha_hora',$mytime->day)
                    ->whereMonth('fecha_hora',$mytime->month)
                    ->whereYear('fecha_hora',$mytime->year)
                    ->orderBy('v.idventa', 'desc')
                    ->groupBy('v.idventa', 'v.fecha_hora', 'p.nombre', 'v.tipo_comprobante', 'v.serie_comprobante', 'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta','v.total_venta_real')
                    ->paginate(20);
            } else {

                $date = $request->get('daterange');
                $pieces = explode(" - ", $date);
                $pieces[0]=$pieces[0] . ' 00:00:00';
                $pieces[1]=$pieces[1] . ' 23:59:00';

                $query = trim($request->get('searchText'));
                $ventas = DB::table('venta as v')
                    ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
                    ->join('detalle_venta as dv', 'v.idventa', '=', 'dv.idventa')
                    ->select('v.idventa', 'v.fecha_hora', 'p.nombre', 'v.tipo_comprobante', 'v.serie_comprobante', 'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta','v.total_venta_real')
                    ->whereBetween('v.fecha_hora',[$pieces[0],$pieces[1]])
                    ->orderBy('v.fecha_hora', 'desc')
                    ->groupBy('v.idventa', 'v.fecha_hora', 'p.nombre', 'v.tipo_comprobante', 'v.serie_comprobante', 'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta','v.total_venta_real')
                    ->paginate(20);
            }
//            echo $pieces[0];

            return view('ventas.venta.index', ['ventas' => $ventas, 'date' => $date]);
        }
    }

    public function create()
    {
        $personas = DB::table('persona')->where('tipo_persona', '=', 'Cliente')->get();
        $proveedores = DB::table('persona')->where('tipo_persona', '=', 'Proveedor')->where('estado', '=', 'Activo')->get();
        $articulos = Articulo::where('estado', '=', 'Activo')->get();
        return view('ventas.venta.create', ['personas' => $personas, 'articulos' => $articulos, 'proveedores' => $proveedores]);
    }

    public function edit($id)
    {
        $venta = Venta::findOrFail($id);

        $detalles = DB::table('detalle_venta')->where('idventa', '=', $id)->get();

        $personas = DB::table('persona')->where('tipo_persona', '=', 'Cliente')->get();
        $proveedores = DB::table('persona')->where('tipo_persona', '=', 'Proveedor')->get();
        $articulos = DB::table('articulo as art')
            ->select(DB::raw('CONCAT(art.codigo, " ", art.nombre) AS articulo'), 'art.idarticulo')
//            ->where('art.estado', '=', 'Activo')
            ->get();
        return view('ventas.venta.edit', ['venta' => $venta, 'personas' => $personas, 'articulos' => $articulos, 'proveedores' => $proveedores, 'detalles' => $detalles]);
    }

    public function store(VentaFormRequest $request)
    {

            $venta = new Venta;
            if($request->get('checkCliente')=='true'){
                $persona = new Persona;
                $persona->tipo_persona = 'Cliente';
                $persona->nombre = $request->get('nombre');
                $persona->num_documento = $request->get('num_documento');
                $persona->direccion = $request->get('direccion');
                $persona->telefono = $request->get('telefono');
                $persona->email = $request->get('email');
                $persona->instagram = $request->get('instagram');
                $persona->facebook = $request->get('facebook');
                $persona->save();
                $venta->idcliente = $persona->idpersona;
            }
            else{
                $venta->idcliente = $request->get('idcliente');
            }
            if($request->get('checkOtraFecha')=='true'){
                $venta->fecha_hora = new Carbon($request->daterange);
            }
            else {
                $mytime = Carbon::now('America/Argentina/Buenos_Aires');

                $venta->fecha_hora = $mytime->toDateTimeString();
            }
            if($request->get('checkTarjetaDebito')=='true' && $request->get('checkTarjetaCredito')=='true')
                $venta->tarjeta = null;
            else if($request->get('checkTarjetaDebito')=='true')
                $venta->tarjeta = "Debito";
            else if($request->get('checkTarjetaCredito')=='true')
                $venta->tarjeta = "Credito";
            $venta->tipo_comprobante = $request->get('tipo_comprobante');
            $venta->serie_comprobante = $request->get('serie_comprobante');
            $venta->num_comprobante = $request->get('num_comprobante');
            $venta->total_venta = $request->get('total_venta');
            $venta->idvendedor = auth()->user()->id;
            $venta->impuesto = '0';
            $venta->estado = 'Activo';
            $venta->save();

            $idarticulo = $request->get('idarticulo');
            $cantidad = $request->get('cantidad');
            $precio_venta = $request->get('precio_venta');

            $cont = 0;
            $totalcompra = 0;
            while ($cont < count($idarticulo)) {
                $detalle = new DetalleVenta();
                $precio_compra = DB::table('precio')->where('idarticulo', '=', $idarticulo[$cont])->orderBy('idprecio','desc')->first();
                $totalcompra = $totalcompra + $precio_compra->precio_compra * $cantidad[$cont];
                $detalle->idventa = $venta->idventa;
                $detalle->idarticulo = $idarticulo[$cont];
                $detalle->cantidad = $cantidad[$cont];
//                $detalle->descuento = $descuento[$cont];
                $detalle->precio_venta = $precio_venta[$cont];
                $detalle->save();

                $articulo = Articulo::findOrFail($idarticulo[$cont]);
                $articulo->stock = $articulo->stock - $cantidad[$cont];
                $articulo->update();

                $cont = $cont + 1;
            }
            if($request->get('pventa_real') == ''){
                $venta->total_venta_real = $venta->total_venta;
                $ganancia = $venta->total_venta_real - $totalcompra;
            } else{
                $venta->total_venta_real = $request->get('pventa_real');
                $ganancia = $venta->total_venta_real - $totalcompra;
            }
            //$venta = Venta::findOrFail($venta->idventa);
            $venta->total_compra = $totalcompra;
            $venta->ganancia = $ganancia;
            $venta->save();



        return Redirect::to('ventas/venta');
    }

    public function update(VentaFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            DB::table('detalle_venta')->where('idventa', $id)->delete();

            $venta = Venta::findOrFail($id);
            $venta->idcliente = $request->get('idcliente');
            $venta->tipo_comprobante = $request->get('tipo_comprobante');
            $venta->serie_comprobante = $request->get('serie_comprobante');
            $venta->num_comprobante = $request->get('num_comprobante');
            $venta->total_venta = $request->get('total_venta');
            $venta->idvendedor = auth()->user()->id;
            $mytime = Carbon::now('America/Argentina/Buenos_Aires');

            $venta->fecha_hora = $mytime->toDateTimeString();
            $venta->impuesto = '0';
            $venta->estado = 'Activo';
            $venta->save();

            $idarticulo = $request->get('idarticulo');
            $cantidad = $request->get('cantidad');
//            $descuento = $request->get('descuento');
            $precio_venta = $request->get('precio_venta');

            $cont = 0;

            while ($cont < count($idarticulo)) {
                $detalle = new DetalleVenta();
                $detalle->idventa = $venta->idventa;
                $detalle->idarticulo = $idarticulo[$cont];
                $detalle->cantidad = $cantidad[$cont];
//                $detalle->descuento = $descuento[$cont];
                $detalle->precio_venta = $precio_venta[$cont];
                $detalle->save();
                $cont = $cont + 1;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        return Redirect::to('ventas/venta');
    }

    public function show($id)
    {
        $venta = DB::table('venta as v')
            ->join('persona as p', 'v.idcliente', '=', 'p.idpersona')
            ->join('detalle_venta as dv', 'v.idventa', '=', 'dv.idventa')
            ->select('v.idventa', 'v.fecha_hora', 'p.nombre', 'v.tipo_comprobante', 'v.serie_comprobante', 'v.num_comprobante', 'v.impuesto', 'v.estado', 'v.total_venta')
            ->where('v.idventa', '=', $id)
            ->first();

        $detalles = DB::table('detalle_venta as d')
            ->join('articulo as a', 'd.idarticulo', '=', 'a.idarticulo')
            ->select('a.nombre as articulo', 'd.cantidad', 'd.descuento', 'd.precio_venta')
            ->where('d.idventa', '=', $id)->get();


        return view('ventas.venta.show', ['venta' => $venta, 'detalles' => $detalles]);
    }

    public function destroy($id)
    {
//        $venta = Venta::findOrFail($id);
//        $venta->estado = 'Cancelado';
//        $venta->update();


        try{
            DB::beginTransaction();
            $venta = Venta::findOrFail($id);
            $fecha = new Carbon($venta->fecha_hora);
            $detalle_venta= DetalleVenta::where('idventa',$id)->get();
            foreach ($detalle_venta as $di){
                $precios = Precio::where('fecha', $fecha->format('Y-m-d'))->where('idarticulo', $di->idarticulo)->get();
                foreach ($precios as $p){
                    $p->delete();
                }

                $di->delete();
            }

            $venta->delete();
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }

        return Redirect::to('ventas/venta');

    }

    public function buscarArticuloPorProveedor(Request $request)
    {


        //if our chosen id and products table prod_cat_id col match the get first 100 data

        //$request->id here is the id of our chosen option id
        $data = DB::table('articulo as art')->select('art.idarticulo', 'art.nombre', 'art.codigo')->where('art.proveedor', '=', $request->codigo)->get();
        //$data= DB::table('articulo as art')->join('persona as p', 'p.codigo' , '=', 'art.proveedor')->select('art.idarticulo','art.nombre','art.codigo','id.persona')->where('p.codigo','=',$request->codigo)->get();

        // $data= DB::table('articulo as art')->where('idarticulo','=',$request->id);
        //$data=Product::select('productname','id')->where('prod_cat_id',$request->id)->take(100)->get();
        return response()->json($data);//then sent this data to ajax success

//        $request->id here is the id of our chosen option id
//        $data=DB::table('articulo as art')->select('idarticulo','nombre')->get();
//        //$data=Product::select('productname','id')->where('prod_cat_id',$request->id)->take(100)->get();
//        return response()->json($data);//then sent this data to ajax success
    }

    public function buscarPrecioArticuloVentas(Request $request)
    {


        //if our chosen id and products table prod_cat_id col match the get first 100 data
        //$request->id here is the id of our chosen option id
        $precio = DB::table('precio')
            ->where('idarticulo', '=', $request->id)
            ->orderBy('idarticulo', 'desc')
            ->orderBy('idprecio', 'desc')
            ->get();
        $precio = $precio->unique('idarticulo');
        $articulo = Articulo::findOrFail($request->id);
        //$elprecio = DB::table('articulo')join('precio','idarticulo','=',$precio->idarticulo)->get();
        //$data= DB::table('articulo as art')->join('persona as p', 'p.codigo' , '=', 'art.proveedor')->select('art.idarticulo','art.nombre','art.codigo','id.persona')->where('p.codigo','=',$request->codigo)->get();
        $precio = $precio->merge($articulo);

        // $data= DB::table('articulo as art')->where('idarticulo','=',$request->id);
        //$data=Product::select('productname','id')->where('prod_cat_id',$request->id)->take(100)->get();
        return response()->json($precio);//then sent this data to ajax success

//        $request->id here is the id of our chosen option id
//        $data=DB::table('articulo as art')->select('idarticulo','nombre')->get();
//        //$data=Product::select('productname','id')->where('prod_cat_id',$request->id)->take(100)->get();
//        return response()->json($data);//then sent this data to ajax success
    }

    public function buscarPrecioArticuloVentasPorCodigo(Request $request)
    {


        //if our chosen id and products table prod_cat_id col match the get first 100 data
        //$request->id here is the id of our chosen option id
        $articulo = DB::table('articulo')->where('codigo', '=', $request->codigo)->first();

        $precio = DB::table('precio')
            ->where('idarticulo', '=', $articulo->idarticulo)
            ->orderBy('idarticulo', 'desc')
            ->orderBy('idprecio', 'desc')
            ->get();
        $precio = $precio->unique('idarticulo');
        //$elprecio = DB::table('articulo')join('precio','idarticulo','=',$precio->idarticulo)->get();
        //$data= DB::table('articulo as art')->join('persona as p', 'p.codigo' , '=', 'art.proveedor')->select('art.idarticulo','art.nombre','art.codigo','id.persona')->where('p.codigo','=',$request->codigo)->get();
        $precio = $precio->merge($articulo);

        // $data= DB::table('articulo as art')->where('idarticulo','=',$request->id);
        //$data=Product::select('productname','id')->where('prod_cat_id',$request->id)->take(100)->get();
        return response()->json($precio);//then sent this data to ajax success

//        $request->id here is the id of our chosen option id
//        $data=DB::table('articulo as art')->select('idarticulo','nombre')->get();
//        //$data=Product::select('productname','id')->where('prod_cat_id',$request->id)->take(100)->get();
//        return response()->json($data);//then sent this data to ajax success
    }

    public function exportDetalle(Request $request, $date)
    {

        if ($date != null && $date != '' && strtotime($date)) {
            $this->cajaDelDia($request);


        } else {
            $pieces = explode(" - ", $date);
            $pieces[0]=$pieces[0] . ' 00:00:00';
            $pieces[1]=$pieces[1] . ' 23:59:00';

            $lafechachica = Carbon::parse($pieces[0]);//2017-08-11
            $lafechagrande = Carbon::parse($pieces[1]);//2017-10-12
            $pibot = Carbon::parse('2017-09-30 00:00:00');
            if($lafechagrande->lt($pibot)){
                $aux = DB::table('articulo as a')
                    ->join('detalle_venta as dv', 'dv.idarticulo', '=', 'a.idarticulo')
                    ->join('venta as v', 'v.idventa', '=', 'dv.idventa')
                    ->select('a.nombre', 'dv.precio_venta', 'v.fecha_hora', 'dv.cantidad', DB::raw('SUM(dv.precio_venta/dv.cantidad) AS precio_total'))
                    ->whereBetween('v.fecha_hora',[$pieces[0],$pieces[1]])
                    ->groupBy('a.nombre', 'dv.precio_venta', 'v.fecha_hora','dv.cantidad')
                    ->orderBy('v.fecha_hora', 'desc')
                    ->get();
            }
            else if($lafechachica->lt($pibot) && $lafechagrande->gt($pibot)){
                $auxviejo = DB::table('articulo as a')
                    ->join('detalle_venta as dv', 'dv.idarticulo', '=', 'a.idarticulo')
                    ->join('venta as v', 'v.idventa', '=', 'dv.idventa')
                    ->select('a.nombre', 'dv.precio_venta as precio_total', 'v.fecha_hora', 'dv.cantidad', DB::raw('SUM(dv.precio_venta/dv.cantidad) AS precio_venta'))
                    ->whereBetween('v.fecha_hora',[$pieces[0],'2017-09-30 23:59:00'])
                    ->groupBy('a.nombre', 'dv.precio_venta', 'v.fecha_hora','dv.cantidad')
                    ->orderBy('v.fecha_hora', 'desc')
                    ->get();

                $auxnuevo = DB::table('articulo as a')
                    ->join('detalle_venta as dv', 'dv.idarticulo', '=', 'a.idarticulo')
                    ->join('venta as v', 'v.idventa', '=', 'dv.idventa')
                    ->select('a.nombre', 'dv.precio_venta', 'v.fecha_hora', 'dv.cantidad', DB::raw('SUM(dv.precio_venta*dv.cantidad) AS precio_total'))
                    ->whereBetween('v.fecha_hora',['2017-09-30 23:59:00',$pieces[1]])
                    ->groupBy('a.nombre', 'dv.precio_venta', 'v.fecha_hora','dv.cantidad')
                    ->orderBy('v.fecha_hora', 'desc')
                    ->get();

                $aux = $auxviejo->merge($auxnuevo);

            } else {
                $aux = DB::table('articulo as a')
                    ->join('detalle_venta as dv', 'dv.idarticulo', '=', 'a.idarticulo')
                    ->join('venta as v', 'v.idventa', '=', 'dv.idventa')
                    ->select('a.nombre', 'dv.precio_venta', 'v.fecha_hora', 'dv.cantidad', DB::raw('SUM(dv.precio_venta*dv.cantidad) AS precio_total'))
                    ->whereBetween('v.fecha_hora',[$pieces[0],$pieces[1]])
                    ->groupBy('a.nombre', 'dv.precio_venta', 'v.fecha_hora','dv.cantidad')
                    ->orderBy('v.fecha_hora', 'desc')
                    ->get();
            }

//            $aux = DB::table('articulo as a')
//                ->join('detalle_venta as dv', 'dv.idarticulo', '=', 'a.idarticulo')
//                ->join('venta as v', 'v.idventa', '=', 'dv.idventa')
//                ->select('a.nombre', 'dv.precio_venta', 'v.fecha_hora', 'dv.cantidad', DB::raw('SUM(dv.precio_venta/dv.cantidad) AS precio_total'))
//                ->whereBetween('v.fecha_hora',[$pieces[0],$pieces[1]])
//                ->groupBy('a.nombre', 'dv.precio_venta', 'v.fecha_hora','dv.cantidad')
//                ->orderBy('dv.precio_venta', 'desc')
//                ->get();
        }

        $columna = [];
        $cont2 = 1;
        $total = 0;
        $totalPromedioTentativo = 0;
        $cantidadDeProductos = 0;
        $fila0 = [];
        $fila0[0] = 'Nombre';
        $fila0[1] = 'Precio Venta';
        $fila0[2] = 'Cantidad';
        $fila0[3] = 'Precio total';
        $fila0[4] = 'Promedio';
        $fila0[5] = 'Fecha';
        $columna[0] = $fila0;

        if($lafechagrande->lt($pibot)){
            foreach ($aux as $a) {
                if($a->cantidad>0){
                    $fila = [];

                    $fila[0] = $a->nombre;
                    $fila[1] = $a->precio_venta;
                    $fila[2] = $a->cantidad;
                    $fila[3] = $a->precio_total;
                    $fila[4] = $a->precio_total * $a->cantidad;
                    $fila[5] = $a->fecha_hora;
                    $total = $total + $fila[4];
                    $totalPromedioTentativo = $totalPromedioTentativo + $fila[4];

                    $cantidadDeProductos = $cantidadDeProductos + $fila[2];
                    $columna[$cont2] = $fila;
                    $cont2 = $cont2 + 1;
                }

            }
        }
        else if($lafechachica->lt($pibot) && $lafechagrande->gt($pibot)){
            foreach ($aux as $a) {
                if($a->cantidad>0){
                    $fila = [];

                    $fila[0] = $a->nombre;
                    $fila[1] = $a->precio_venta;
                    $fila[2] = $a->cantidad;
                    $fila[3] = $a->precio_total;
                    $fila[4] = $a->precio_total / $a->cantidad;
                    $fila[5] = $a->fecha_hora;
                    $total = $total + $fila[3];
                    $totalPromedioTentativo = $totalPromedioTentativo + $fila[4];

                    $cantidadDeProductos = $cantidadDeProductos + $fila[2];
                    $columna[$cont2] = $fila;
                    $cont2 = $cont2 + 1;
                }

            }
        } else {
            foreach ($aux as $a) {
                if($a->cantidad>0){
                    $fila = [];

                    $fila[0] = $a->nombre;
                    $fila[1] = $a->precio_venta;
                    $fila[2] = $a->cantidad;
                    $fila[3] = $a->precio_total;
                    $fila[4] = $a->precio_total / $a->cantidad;
                    $fila[5] = $a->fecha_hora;
                    $total = $total + $fila[3];
                    $totalPromedioTentativo = $totalPromedioTentativo + $fila[4];

                    $cantidadDeProductos = $cantidadDeProductos + $fila[2];
                    $columna[$cont2] = $fila;
                    $cont2 = $cont2 + 1;
                }

            }
        }

//        foreach ($aux as $a) {
//            if($a->cantidad>0){
//                $fila = [];
//
//                $fila[0] = $a->nombre;
//                $fila[1] = $a->precio_venta;
//                $fila[2] = $a->cantidad;
//                $fila[3] = $a->precio_total;
//                $fila[4] = $a->precio_total * $a->cantidad;
//                $fila[5] = $a->fecha_hora;
//                $total = $total + $fila[4];
//                $totalPromedioTentativo = $totalPromedioTentativo + $fila[4];
//
//                $cantidadDeProductos = $cantidadDeProductos + $fila[2];
//                $columna[$cont2] = $fila;
//                $cont2 = $cont2 + 1;
//            }
//
//        }
        $pieces = explode(" - ", $date);
        Excel::create('Detalle entre: '.$pieces[0].' a '.$pieces[1], function ($excel) use ($columna,$total,$totalPromedioTentativo, $cantidadDeProductos) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna,$total,$totalPromedioTentativo, $cantidadDeProductos) {

                $row = 1;
                $sheet->row($row, ['Nombre', 'Precio venta', 'Cantidad', 'Precio total','Promedio', 'Fecha']);
//                $sheet->fromArray($columna, null, 'A1', false, false);
                $i = 1;
                while(count($columna)> $i) {
                    $row++;
                    $sheet->row($row, $columna[$i]);
                    $i++;

                }
                $row = $row + 2;
                $sheet->row($row+1, ['Total',$total]);
                //$sheet->row($row+2, ['Total Promedio',$total]);
                $sheet->row($row+3, ['Cantidad Productos Vendidos',$cantidadDeProductos]);            });

        })->download('xls');
        //return $merged;
    }

    public function exportResultado(Request $request, $date)
    {

        //dd($date);
        if (strtotime($date)) {
            $this->cajaDelDia($request);
        } else {
            $pieces = explode(" - ", $date);
            $pieces[0]=$pieces[0] . ' 00:00:00';
            $pieces[1]=$pieces[1] . ' 23:59:00';
            $aux = DB::table('venta as v')
                ->join('persona as p', 'p.idpersona', '=', 'v.idcliente')
                ->select('v.fecha_hora', 'p.nombre', 'v.total_venta','v.total_compra')
                ->whereBetween('v.fecha_hora',[$pieces[0],$pieces[1]])
                ->orderBy('v.fecha_hora','desc')
                ->get();
        }

        $columna = [];
        $cont2 = 1;
        $total = 0;
        $totalCosto = 0;
        $fila0 = [];
        $fila0[0] = 'Fecha';
        $fila0[1] = 'Cliente';
        $fila0[2] = 'Total venta';
        $fila0[3] = 'Total Costo';
        $columna[0] = $fila0;

        foreach ($aux as $a) {
            $fila = [];

            $fila[0] = $a->fecha_hora;
            $fila[1] = $a->nombre;
            $fila[2] = $a->total_venta;
            $fila[3] = $a->total_compra;
            $total = $total + $fila[2];
            $totalCosto = $totalCosto + $fila[3];
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
        }
        $filanueva = [];
        $filanueva[0] = ' ';
        $filanueva[1] = ' ';
        $filanueva[2] = $total;
        $columna[$cont2] = $filanueva;

        $pieces = explode(" - ", $date);
        Excel::create('Resultado entre: '.$pieces[0].' a '.$pieces[1], function ($excel) use ($columna,$total,$totalCosto) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna,$total,$totalCosto) {

                $sheet->row(1, ['Fecha', 'Cliente', 'Total Venta', 'Total Costo']);
                $sheet->fromArray($columna, null, 'A1', false, false);

                $row = 1;
                $sheet->row($row, ['Fecha', 'Cliente', 'Total Venta', 'Total Costo']);
//                $sheet->fromArray($columna, null, 'A1', false, false);
                $i = 1;
                while(count($columna)> $i) {
                    $row++;
                    $sheet->row($row, $columna[$i]);
                    $i++;

                }
                $row = $row + 2;
                $sheet->row($row+1, ['Total Venta',$total]);
                $sheet->row($row+2, ['Total Costo',$totalCosto]);
                $sheet->row($row+3, ['Ganancia',$total - $totalCosto]);

            });

        })->download('xls');
    }

    public function exportReducido(Request $request, $date)
    {


        //dd($date);
        if (strtotime($date)) {
            $this->cajaDelDia($request);
        } else {
            $pieces = explode(" - ", $date);
            $pieces[0]=$pieces[0] . ' 00:00:00';
            $pieces[1]=$pieces[1] . ' 23:59:00';
            $aux = DB::table('venta')
                ->select('fecha_hora','total_venta')
                ->whereBetween('fecha_hora',[$pieces[0],$pieces[1]])
                ->orderBy('fecha_hora','desc')
                ->get(['fecha_hora'])
                ->groupBy(function($date) {
                    return Carbon::parse($date->fecha_hora)->format('y-m-d');
                });
        }

        $columna = [];
        $cont2 = 1;
        $total = 0;
        $totalCosto = 0;
        $fila0 = [];
        $fila0[0] = 'Fecha';
        $fila0[1] = 'Total venta día';
        $columna[0] = $fila0;

        foreach ($aux as $a) {
            $pieces = explode(" ", $a[0]->fecha_hora);
            $fila = [];
            $fila[0] = $pieces[0] ;
            $fila[1] = 0;
                foreach ($a as $b){

                    $fila[1] = $fila[1] + $b->total_venta;
                }

            //$fila[1] = $fila[1] +  $a->total_venta;
            $total = $total + $fila[1];
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
        }

        $pieces = explode(" - ", $date);
        Excel::create('Resultado entre: '.$pieces[0].' a '.$pieces[1], function ($excel) use ($columna,$total,$totalCosto) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna,$total,$totalCosto) {

                $sheet->row(1, ['Fecha', 'Total Venta del Día']);
                $sheet->fromArray($columna, null, 'A1', false, false);

                $row = 1;
                $sheet->row($row, ['Fecha','Total Venta Dia']);
//                $sheet->fromArray($columna, null, 'A1', false, false);
                $i = 1;
                while(count($columna)> $i) {
                    $row++;
                    $sheet->row($row, $columna[$i]);
                    $i++;

                }
                $row = $row + 2;
                $sheet->row($row+1, ['Total Venta',$total]);

            });

        })->download('xls');
    }

    public function cajaDelDia(Request $request)
    {


        $mytime = Carbon::now('America/Argentina/Buenos_Aires');
        $mytime2 = Carbon::now('America/Argentina/Buenos_Aires');
        $mytime2->hour = 0;
        $mytime2->minute = 0;
        $mytime2->second = 0;
        $yesterday = $mytime2->toDateTimeString();
        $today = $mytime->toDateTimeString();


        $aux = DB::table('articulo as a')
            ->join('detalle_venta as dv', 'dv.idarticulo', '=', 'a.idarticulo')
            ->join('venta as v', 'v.idventa', '=', 'dv.idventa')
            ->select('a.nombre','a.codigo' ,'dv.precio_venta', 'v.fecha_hora', DB::raw('SUM(dv.cantidad) AS cantidad'), DB::raw('SUM(dv.precio_venta*dv.cantidad) AS precio_total'))
            ->whereBetween('v.fecha_hora', array($yesterday, $today))
            ->groupBy('a.nombre','a.codigo' ,'dv.precio_venta', 'v.fecha_hora')
            ->orderBy('v.fecha_hora', 'desc')
            ->get();

        $columna = [];
        $cont2 = 1;
        $total = 0;
        $totalPromedioTentativo = 0;
        $cantidadDeProductos = 0;
        $fila0 = [];
        $fila0[0] = 'Nombre';
        $fila0[1] = 'Codigo';
        $fila0[2] = 'Precio Venta';
        $fila0[3] = 'Cantidad';
        $fila0[4] = 'Precio total';
        $fila0[5] = 'Promedio';
        $fila0[6] = 'Fecha';
        $columna[0] = $fila0;

        foreach ($aux as $a) {
            $fila = [];

            $fila[0] = $a->nombre;
            $fila[1] = $a->codigo;
            $fila[2] =$a->precio_venta;
            $fila[3] =$a->cantidad;
            $fila[4] =$a->precio_total;
            $fila[5] =$a->precio_total/$a->cantidad;
            $fila[6] =$a->fecha_hora;
            $total = $total + $fila[4];
            $totalPromedioTentativo = $totalPromedioTentativo + $fila[5];
            $cantidadDeProductos = $cantidadDeProductos + $fila[3];
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
        }

        Excel::create('Caja ' . $mytime->format('Y-m-d'), function ($excel) use ($columna,$total,$totalPromedioTentativo, $cantidadDeProductos) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna,$total,$totalPromedioTentativo, $cantidadDeProductos) {
                $row = 1;
                $sheet->row($row, ['Nombre', 'Codigo', 'Precio venta', 'Cantidad', 'Precio total','Promedio', 'Fecha']);
//                $sheet->fromArray($columna, null, 'A1', false, false);
                $i = 1;
                while(count($columna)> $i) {
                    $row++;
                    $sheet->row($row, $columna[$i]);
                    $i++;

                }
                $row = $row + 2;
                $sheet->row($row+1, ['Total',$total]);
                $sheet->row($row+2, ['Total Promedio',$total/$cantidadDeProductos]);
                $sheet->row($row+3, ['Cantidad Productos Vendidos',$cantidadDeProductos]);
            });

        })->download('xls');
    }

    public function productoMasVendido($date)
    {

        if ($date != null && $date != '' && strtotime($date)) {
            $mytime = Carbon::now('America/Argentina/Buenos_Aires');

            $collection = DB::table('detalle_venta as dv')
                ->join('venta as v','v.idventa','=','dv.idventa')
                ->join('articulo as a','a.idarticulo','=','dv.idarticulo')
                ->join('persona as p','a.proveedor','=','p.codigo')
                ->select('a.idarticulo',DB::raw('SUM(dv.cantidad) as cantidadTotal'),'a.codigo','dv.precio_venta','a.nombre')
                ->where('p.estado','=','Activo')
                ->whereDay('fecha_hora',$mytime->day)
                ->whereMonth('fecha_hora',$mytime->month)
                ->whereYear('fecha_hora',$mytime->year)
                ->groupBy('a.idarticulo','a.codigo','a.nombre','dv.precio_venta')
                ->orderBy('cantidadTotal','desc')
                ->limit(10)
                ->get();
        } else {
            $pieces = explode(" - ", $date);
            $collection = DB::table('detalle_venta as dv')
                ->join('venta as v','v.idventa','=','dv.idventa')
                ->join('articulo as a','a.idarticulo','=','dv.idarticulo')
                ->join('persona as p','a.proveedor','=','p.codigo')
                ->select('a.idarticulo',DB::raw('SUM(dv.cantidad) as cantidadTotal'),'a.nombre','a.codigo','dv.precio_venta')
                ->where('p.estado','=','Activo')
                ->whereBetween('v.fecha_hora', array(new Carbon($pieces[0]), new Carbon($pieces[1])))
                ->groupBy('a.idarticulo','a.codigo','a.nombre','dv.precio_venta')
                ->orderBy('cantidadTotal','desc')
                ->limit(10)
                ->get();
        }
        //dd($collection);
        $columna = [];
        $cont2 = 1;
        $fila0 = [];
        $fila0[0] = 'Codigo';
        $fila0[1] = 'Nombre';
        $fila0[2] = 'Precio de Venta';
        $fila0[3] = 'Cantidad Total';

        $columna[0] = $fila0;

        foreach ($collection as $a) {
            $fila = [];

            $fila[0] = $a->codigo;
            $fila[1] = $a->nombre;
            $fila[2] = $a->precio_venta;
            $fila[3] = $a->cantidadTotal;
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
        }

        Excel::create('Laravel Excel', function ($excel) use ($columna) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna) {

                $sheet->fromArray($columna);

            });

        })->download('xls');

    }

    public function proveedorQueMasVende($date)
    {

        if ($date != null && $date != '' && strtotime($date)) {
            $mytime = Carbon::now('America/Argentina/Buenos_Aires');

            $collection = DB::table('detalle_venta as dv')
                ->join('articulo as a','a.idarticulo','=','dv.idarticulo')
                ->join('persona as p','a.proveedor','=','p.codigo')
                ->join('venta as v', 'v.idventa','=','dv.idventa')
                ->select('a.proveedor',DB::raw('SUM(dv.cantidad) as cantidadTotal'))
                ->where('p.estado','=','Activo')
                ->whereDay('v.fecha_hora',$mytime->day)
                ->whereMonth('v.fecha_hora',$mytime->month)
                ->whereYear('v.fecha_hora',$mytime->year)
                ->groupBy('a.proveedor')
                ->orderBy('cantidadTotal','desc')
//            ->orderBy('desc')
                ->limit(10)
                ->get();
        } else {
            $pieces = explode(" - ", $date);
            $collection = DB::table('detalle_venta as dv')
                ->join('articulo as a','a.idarticulo','=','dv.idarticulo')
                ->join('persona as p','a.proveedor','=','p.codigo')
                ->join('venta as v', 'v.idventa','=','dv.idventa')
                ->select('a.proveedor',DB::raw('SUM(dv.cantidad) as cantidadTotal'))
                ->where('p.estado','=','Activo')
                ->whereBetween('v.fecha_hora', array(new Carbon($pieces[0]), new Carbon($pieces[1])))
                ->groupBy('a.proveedor')
                ->orderBy('cantidadTotal','desc')
//            ->orderBy('desc')
                ->limit(10)
                ->get();
        }
        //dd($collection);
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

    public function autocomplete(Request $request)
    {
        $data = Articulo::select('nombre','codigo','idarticulo','ultimoprecio')
            ->where('nombre','LIKE','%'.$request->get('query').'%')
            ->where('estado','=','Activo')
            ->orwhere('codigo','LIKE','%'.$request->get('query').'%')
            ->get();
        return response()->json($data);
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
            ->join('precio as p', 'p.idarticulo', '=', 'a.idarticulo')
            ->where('a.stock','>=','1')
            ->orderby('a.codigo','asc')
            ->get();

        $cont2 = 1;
        $columna = [];
        //dd($stock);
        foreach ($stock as $a) {
            $fila = [];

            $fila[0] = $a->codigo;
            $fila[1] = $a->nombre;
            $fila[2] = $a->proveedor;
            $fila[3] = $a->precio_compra;
            $fila[4] = $a->precio_venta;
            $columna[$cont2] = $fila;
            $cont2 = $cont2 + 1;
        }

        Excel::create('Resultado entre: ', function ($excel) use ($columna) {

            $excel->sheet('Excel sheet', function ($sheet) use ($columna) {

                $sheet->row(1, ['Fecha', 'Cliente', 'Total Venta', 'Total Costo']);
                $sheet->fromArray($columna, null, 'A1', false, false);

            });

        })->download('xls');
    }
}
