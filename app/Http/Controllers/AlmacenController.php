<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AlmacenController extends Controller
{
    // Métodos para Categorías
    public function categoriaIndex()
    {
        $categorias = Categoria::orderBy('id', 'DESC')->get();
        return view('almacen.categoria.index', compact('categorias'));
    }

    public function categoriaCreate()
    {
        return view('almacen.categoria.create');
    }

    public function categoriaStore(Request $request)
    {
        $request->validate([
            'denominacion' => 'required|string|max:50|unique:categorias,denominacion',
        ]);

        $categoria = new Categoria();
        $categoria->denominacion = $request->denominacion;
        $categoria->save();

        return redirect()->route('almacen.categoria')
            ->with('success', 'Se registró la categoría satisfactoriamente.');
    }

    public function categoriaShow($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('almacen.categoria.show', compact('categoria'));
    }

    public function categoriaEdit($id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('almacen.categoria.edit', compact('categoria'));
    }

    public function categoriaUpdate(Request $request, $id)
    {
        $request->validate([
            'denominacion' => 'required|string|max:50|unique:categorias,denominacion,' . $id,
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->denominacion = $request->denominacion;
        $categoria->save();

        return redirect()->route('almacen.categoria')
            ->with('success', 'Se actualizó la categoría satisfactoriamente.');
    }

    public function categoriaDestroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect()->route('almacen.categoria')
            ->with('success', 'Se eliminó la categoría satisfactoriamente.');
    }

    // Métodos para Productos
    public function productoIndex()
    {
        $productos = Producto::with('categoria')->orderBy('id', 'DESC')->get();
        return view('almacen.producto.index', compact('productos'));
    }

    public function productoCreate()
    {
        $categorias = Categoria::all();
        return view('almacen.producto.create', compact('categorias'));
    }

    public function productoStore(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'producto' => 'required|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto = new Producto();
        $producto->codigo = $request->codigo;
        $producto->producto = $request->producto;
        $producto->categoria_id = $request->categoria_id;
        $producto->stock = $request->stock;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;

        if ($request->hasFile('imagen')) {
            $image = $request->file('imagen');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('uploads/productos'), $imageName);
            $producto->imagen = $imageName;
        }

        $producto->save();

        return redirect()->route('almacen.producto')
            ->with('success', 'Se registró el producto satisfactoriamente.');
    }

    public function productoShow($id)
    {
        $producto = Producto::with('categoria')->findOrFail($id);
        return view('almacen.producto.show', compact('producto'));
    }

    public function productoEdit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::all();
        return view('almacen.producto.edit', compact('producto', 'categorias'));
    }

    public function productoUpdate(Request $request, $id)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $id,
            'producto' => 'required|string|max:100',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stock' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($id);
        $producto->codigo = $request->codigo;
        $producto->producto = $request->producto;
        $producto->categoria_id = $request->categoria_id;
        $producto->stock = $request->stock;
        $producto->descripcion = $request->descripcion;
        $producto->precio = $request->precio;

        if ($request->hasFile('imagen')) {
            // Eliminar la imagen anterior si no es la imagen por defecto
            if ($producto->imagen && $producto->imagen !== 'sin_imagen.png') {
                $imagePath = public_path('uploads/productos/' . $producto->imagen);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
            $image = $request->file('imagen');
            $imageName = time() . '-' . $image->getClientOriginalName();
            $image->move(public_path('uploads/productos'), $imageName);
            $producto->imagen = $imageName;
        }

        $producto->save();

        return redirect()->route('almacen.producto')
            ->with('success', 'Se actualizó el producto satisfactoriamente.');
    }

    public function productoDestroy($id)
    {
        $producto = Producto::findOrFail($id);

        // Eliminar la imagen si no es la imagen por defecto
        if ($producto->imagen && $producto->imagen !== 'sin_imagen.png') {
            $imagePath = public_path('uploads/productos/' . $producto->imagen);
            if (File::exists($imagePath)) {
                File::delete($imagePath);
            }
        }

        $producto->delete();

        return redirect()->route('almacen.producto')
            ->with('success', 'Se eliminó el producto satisfactoriamente.');
    }

    // Nuevos métodos para Servicios
    public function servicioIndex()
    {
        $servicios = Servicio::orderBy('id', 'DESC')->get();
        return view('almacen.servicio.index', compact('servicios'));
    }

    public function servicioCreate()
    {
        return view('almacen.servicio.create');
    }

    public function servicioStore(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:servicios,nombre',
            'precio' => 'required|numeric|min:0',
        ]);

        $servicio = new Servicio();
        $servicio->nombre = $request->nombre;
        $servicio->precio = $request->precio;
        $servicio->save();

        return redirect()->route('almacen.servicio')
            ->with('success', 'Se registró el servicio satisfactoriamente.');
    }

    public function servicioShow($id)
    {
        $servicio = Servicio::findOrFail($id);
        return view('almacen.servicio.show', compact('servicio'));
    }

    public function servicioEdit($id)
    {
        $servicio = Servicio::findOrFail($id);
        return view('almacen.servicio.edit', compact('servicio'));
    }

    public function servicioUpdate(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:servicios,nombre,' . $id,
            'precio' => 'required|numeric|min:0',
        ]);

        $servicio = Servicio::findOrFail($id);
        $servicio->nombre = $request->nombre;
        $servicio->precio = $request->precio;
        $servicio->save();

        return redirect()->route('almacen.servicio')
            ->with('success', 'Se actualizó el servicio satisfactoriamente.');
    }

    public function servicioDestroy($id)
    {
        $servicio = Servicio::findOrFail($id);
        $servicio->delete();

        return redirect()->route('almacen.servicio')
            ->with('success', 'Se eliminó el servicio satisfactoriamente.');
    }
}
