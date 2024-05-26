<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Address;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Manager\ImageManager;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    public function index()
    {
        //
    }
    
    public function store(StoreSupplierRequest $request)
    {
        $supplierData = (new Supplier())->prepareData($request->all(), auth());
        $addressData = (new Address())->prepareData($request->all());

        if($request->has('logo')) {
            $name = Str::slug($supplierData['name']);
            $supplierData['logo'] = ImageManager::processImageUpload(
                $request->input('logo'),
                $name,
                Supplier::IMAGE_UPLOAD_PATH,
                Supplier::THUMB_IMAGE_UPLOAD_PATH,
                Supplier::LOGO_WIDTH,
                Supplier::LOGO_HEIGHT,
                Supplier::LOGO_THUMB_WIDTH,
                Supplier::LOGO_THUMB_HEIGHT
            );
        }

        $supplier = Supplier::create($supplierData);
        $supplier->address()->create($addressData);

        return response()->json(['msg' => 'Supplier Created Successfully', 'cls' => 'success']);
    }


    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }


}