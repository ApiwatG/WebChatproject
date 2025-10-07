<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cosmetic;
use App\Models\CosmeticType;
use App\Models\Rarity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class adminCosmeticController extends Controller
{
    // แสดงหน้า index (รายการทั้งหมด)
    public function index()
    {
        $cosmetics = Cosmetic::with(['cosmeticType', 'rarity'])->latest()->get();
        $types = CosmeticType::all();
        $rarities = Rarity::all();

        return view('admin.cosmetic.index', compact('cosmetics', 'types', 'rarities'));
    }

    // เพิ่มข้อมูลใหม่
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'cosmetic_type_id' => 'required|exists:cosmetic_types,id',
            'rarity_id' => 'required|exists:rarities,id',
            'image' => 'required|image|max:2048', // <= ตรวจสอบว่าเป็นรูปภาพ
        ]);

        $path = $request->file('image')->store('cosmetics', 'public');

        Cosmetic::create([
            'cosmetic_name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'cosmetic_type_id' => $request->cosmetic_type_id,
            'rarity_id' => $request->rarity_id,
            'cosmetic_img' => $path, // เก็บแค่พาธ ไม่ต้องใส่ storage/
        ]);

        return redirect()->route('admin.cosmetics.index')->with('success', 'Cosmetic added successfully!');
    }


    // แก้ไขข้อมูล
    public function edit($id)
    {
        $cosmetic = Cosmetic::findOrFail($id);
        $types = CosmeticType::all();
        $rarities = Rarity::all();

        return view('admin.cosmetic.edit_cosmetic', compact('cosmetic', 'types', 'rarities'));
    }

    // อัปเดตข้อมูลที่แก้ไขแล้ว
    public function update(Request $request, $id)
    {
        $cosmetic = Cosmetic::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'cosmetic_type_id' => 'required|exists:cosmetic_types,id',
            'rarity_id' => 'required|exists:rarities,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'cosmetic_name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'cosmetic_type_id' => $request->cosmetic_type_id,
            'rarity_id' => $request->rarity_id,
        ];

        if ($request->hasFile('image')) {
            // ลบรูปเก่าออกจาก storage
            if ($cosmetic->cosmetic_img && Storage::disk('public')->exists($cosmetic->cosmetic_img)) {
                Storage::disk('public')->delete($cosmetic->cosmetic_img);
            }
            // บันทึกรูปใหม่
            $data['cosmetic_img'] = $request->file('image')->store('cosmetics', 'public');
        }

        $cosmetic->update($data);

        return redirect()->route('admin.cosmetics.index')->with('success', 'Cosmetic updated successfully!');
    }

    // ลบข้อมูล
    public function destroy($id)
    {
        $cosmetic = Cosmetic::findOrFail($id);

        if ($cosmetic->cosmetic_img && Storage::disk('public')->exists($cosmetic->cosmetic_img)) {
            Storage::disk('public')->delete($cosmetic->cosmetic_img);
        }

        $cosmetic->delete();

        return redirect()->route('admin.cosmetics.index')->with('success', 'Cosmetic deleted successfully!');
    }

    // เพิ่มประเภทใหม่
    public function storeType(Request $request)
    {
        $request->validate([
            'type_name' => 'required|string|max:255|unique:cosmetic_types,name'
        ]);

        CosmeticType::create(['name' => $request->type_name]);

        return redirect()->route('admin.cosmetics.index')->with('success', 'Category added successfully!');
    }
}
