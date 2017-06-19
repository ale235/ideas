<?php

namespace ideas\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use ideas\Http\Requests\ArticuloFormRequest;
use ideas\Articulo;
use ideas\Ingreso;
use ideas\DetalleIngreso;
use ideas\Precio;
use Carbon\Carbon;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class ArticuloController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if($request)
        {
            $query = trim($request->get('searchText'));
            $query2 = trim($request->get('searchText2'));
            $categorias=DB::table('categoria')
                ->where('condicion','=','1')
                ->get();
            $estados = DB::table('articulo')
                ->select('estado')
                ->distinct('estado')->get();

            if($request->get('selectText')== null ){
                $query3 = 'Activo';
            }
            else $query3 = trim($request->get('selectText'));
            $articulos = DB::table('articulo as art')
                ->join('categoria as cat', 'art.idcategoria', '=', 'cat.idcategoria')
//                ->join('precio as p', 'art.idarticulo', '=', 'p.idarticulo')
                ->select('art.idarticulo','art.nombre', 'art.codigo', 'art.stock', 'cat.nombre as categoria', 'art.descripcion', 'art.imagen', 'art.estado', 'art.proveedor','art.ultimoprecio')
                ->where('art.estado','=',$query3)
                ->where([
                    ['art.nombre','LIKE','%'.$query.'%'],
                    ['art.codigo','LIKE','%'.$query2.'%'],
                    ['art.estado', '=', $query3],
                ])
                ->orderBy('art.idarticulo','desc')
//                ->orderBy('p.idprecio','desc')
                ->paginate('30');

            return view('almacen.articulo.index', ['articulos'=>$articulos,'searchText'=>$query, 'searchText2'=>$query2, 'estados'=>$estados, 'selectText'=>$query3, 'categorias'=>$categorias]);
        }
    }

    public function create()
    {
        $articulos = DB::table('articulo as art')
            ->select('art.idarticulo','art.codigo','art.proveedor')
            ->get();


        $proveedores = DB::table('persona')
            ->where('tipo_persona','=','Proveedor')
            ->where('estado','=','Activo')
            ->orderBy('codigo','desc')
            ->get();
        $categorias=DB::table('categoria')
            ->where('condicion','=','1')
            ->get();
        return view('almacen.articulo.create', ['categorias'=>$categorias, 'proveedores'=>$proveedores, 'articulos'=>$articulos]);
    }

    public function store(ArticuloFormRequest $request)
    {
        try
        {
            DB::beginTransaction();
        $articulo = new Articulo;
        $articulo->idcategoria = $request->get('idcategoria');
        $articulo->codigo = $request->get('idproveedores') . $request->get('codigo');
        $articulo->proveedor = $request->get('idproveedores');
        $articulo->nombre = $request->get('nombre');
        $articulo->stock = 0;
        $articulo->descripcion = $request->get('descripcion');
        $articulo->estado = 'Activo';

        if(Input::hasFile('imagen'))
        {
            $file=Input::file('imagen');
            $file->move(public_path().'imagenes/articulos/', $file->getClientOriginalName());
            $articulo->imagen = $file->getClientOriginalName();
        }
        $articulo->save();

        $ingreso = new Ingreso;
//            $pieces = explode("+", $request->get('idproveedor'));
//            $ingreso->idproveedor = $pieces[0];
        $ingreso->idproveedor = $request->get('idproveedorsolo');
        $mytime= Carbon::now('America/Argentina/Buenos_Aires');

        $ingreso->fecha_hora=$mytime->toDateTimeString();
        $ingreso->estado='Activo';
        $ingreso->save();

        $idarticulo = $articulo->idarticulo;
        $cantidad = $request->get('pcantidad');
        $precio_compra_costo = $request->get('pprecio_compra_costo');
        $porcentaje_venta = $request->get('pporcentaje_venta');

        $detalle = new DetalleIngreso();
        $detalle->idingreso = $ingreso->idingreso;
        $detalle->idarticulo = $idarticulo;
        $detalle->cantidad = $cantidad;
        $detalle->precio_compra_costo = $precio_compra_costo;
        $detalle->porcentaje_venta = $porcentaje_venta;
        $detalle->save();

        $precio = new Precio();
        $precio->idarticulo = $idarticulo;
        $precio->porcentaje = $porcentaje_venta;
        $precio->fecha = $mytime->toDateTimeString();
        $precio->precio_compra = $precio_compra_costo;
        $precio->precio_venta = (($porcentaje_venta / 100) + 1) * $precio_compra_costo;
        $precio->save();

        $articulo = Articulo::findOrFail($idarticulo);
        $articulo->ultimoprecio = (($porcentaje_venta / 100) + 1) * $precio_compra_costo;
        $articulo->stock = $articulo->stock + $cantidad;
        $articulo->update();

        DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollback();
        }



            return Redirect::to('almacen/articulo?selectText=Activo');
    }

    public function show($id)
    {
        return view('almacen.articulo.show',['articulo'=>Articulo::findOrFail($id)]);
    }

    public function edit($id)
    {
        $articulo=Articulo::findOrFail($id);
        $proveedores = DB::table('persona')
            ->where('tipo_persona','=','Proveedor')->get();
        $categorias=DB::table('categoria')
            ->where('condicion', '=', '1')
            ->get();
        return view('almacen.articulo.edit',['articulo'=>$articulo,'categorias'=>$categorias,'proveedores'=>$proveedores]);
    }

    public function update(ArticuloFormRequest $request,$id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->idcategoria = $request->get('idcategoria');
        $articulo->codigo = $request->get('codigo');
        $articulo->nombre = $request->get('nombre');
        $articulo->stock = 0;
        $articulo->descripcion = $request->get('descripcion');
        $articulo->estado = 'Activo';

        if(Input::hasFile('imagen'))
        {
            $file=Input::file('imagen');
            $file->move(public_path().'imagenes/articulos/', $file->getClientOriginalName());
            $articulo->imagen = $file->getClientOriginalName();
        }
        $articulo->update();
        return Redirect::to('almacen/articulo');
    }

    public function destroy($id)
    {
        $articulo = Articulo::findOrFail($id);
        $articulo->delete();
        return Redirect::to('almacen/articulo');
    }

    public static  function test()
    {

        return "hola";
    }

    public function prodfunct(){

        $prod=DB::table('articulo as art')->all();//get data from table
        return view('almacen/articulo',compact('articulosnuevo'));//sent data to view

    }

    public function findProductName(Request $request){


        //if our chosen id and products table prod_cat_id col match the get first 100 data

        //$request->id here is the id of our chosen option id
        $data=DB::table('articulo as art')->select('idarticulo','nombre')->get();
        //$data=Product::select('productname','id')->where('prod_cat_id',$request->id)->take(100)->get();
        return response()->json($data);//then sent this data to ajax success
    }


    public function findPrice(Request $request){

        //it will get price if its id match with product id
        //$p=Product::select('price')->where('id',$request->id)->first();
        $p=DB::table('articulo as art')->select('idarticulo','nombre')->where('idarticulo','=',$request->idarticulo)->get();
        return response()->json($p);
    }

    public function cambiarEstadoArticulo($id){

        $articulo = Articulo::findOrFail($id);
        echo $articulo;
        if($articulo->estado ==  'Inactivo')
            $articulo->estado = 'Activo';
        else $articulo->estado = 'Inactivo';

        $articulo->update();
        return Redirect::to('almacen/articulo');
    }


    public function mostrarPrecio($id){

        $precio= DB::table('precio')
            ->where('idarticulo','=',$id)
            ->orderBy('idarticulo','desc')
            ->orderBy('idprecio','desc')
            ->get();
        $precio = $precio->unique('idarticulo');
        return response()->json($precio);
    }


    public function exportArticulo($selectText)
    {
        //$articulos= Articulo::where('estado',$selectText)->get();
        $articulos= Articulo::join('precio','articulo.idarticulo', '=', 'precio.idarticulo')
            ->join('categoria','categoria.idcategoria','=','articulo.idcategoria')
//            ->select('precio.idprecio','articulo.idarticulo','precio.fecha','precio.precio_venta','articulo.codigo','articulo.nombre','precio.porcentaje','precio.precio_compra')
            ->select('articulo.nombre as articulo','articulo.codigo','articulo.stock','categoria.nombre','articulo.estado','precio.precio_venta','precio.fecha as último_precio','precio.porcentaje','precio.precio_compra')
            ->where('articulo.estado',$selectText)
            //->groupBy('precio.idprecio','articulo.idarticulo','precio.fecha','precio.precio_venta','articulo.codigo','articulo.nombre','precio.porcentaje','precio.precio_compra')
            ->orderBy('articulo.idarticulo','desc')
            ->orderBy('precio.idprecio','desc')

            ->get();

        $articulos = $articulos->unique('codigo');

        Excel::create('Laravel Excel', function($excel) use ($articulos) {

            $excel->sheet('Excel sheet', function($sheet) use ($articulos){

               // $sheet->setOrientation('landscape');
                $sheet->fromArray($articulos);

            });

        })->export('xls');
    }

    public function buscarProveedor(Request $request){

        //it will get price if its id match with product id
        //$p=Product::select('price')->where('id',$request->id)->first();
        $p=DB::table('persona as p')->select('p.idpersona')->where('p.codigo','=', $request->codigo)->get();
        return response()->json($p);
    }

    public function buscarUltimoId(Request $request){

              //it will get price if its id match with product id
              //$p=Product::select('price')->where('id',$request->id)->first();
              $p=DB::table('articulo as art')
                     ->select('art.idarticulo')
                  ->where('art.proveedor','=', $request->codigo)
                  ->orderBy('art.idarticulo','desc')
                  ->get();
       return response()->json($p);
   }

// ->orderBy('p.idprecio','desc')
//->select(DB::raw('max(p.idprecio) as elprecio'),'p.idprecio','art.idarticulo','p.fecha','p.precio_venta','art.codigo','art.nombre','p.porcentaje','p.precio_compra')
}
