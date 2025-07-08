<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'price', 'image'];
    public static function store($request, $id = null)
{
    $data = $request->only('name', 'description', 'price');

    // Handle image upload if present
    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->storeAs('image', $filename, 'public');
        $data['image'] = 'storage/image/' . $filename;
    }

    if ($id) {
        $product = self::find($id);
        $product->update($data);
    } else {
        $product = self::create($data);
    }
    return $product;
}
}
