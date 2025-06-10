<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



//Arrival
Route::prefix('arrivals')->group(function () {
    Route::get("/", "ArrivalController@getAll");
    Route::get("/inTransitorpartial","ArrivalController@getInTransitorPartiallyReceivedArrivals");
    Route::get("{id}", "ArrivalController@getbyId");
    Route::get("{id}/tier","ArrivalController@getTierbyArrivalId");
    Route::patch("{id}/status/{status}","ArrivalController@setArrivalStatus");
    Route::post("/","ArrivalController@create");
    Route::patch("{id}","ArrivalController@update");
    Route::delete("{id}","ArrivalController@logicalDelete") ;
});

//Tier
Route::prefix('tiers')->group(function () {

    Route::get("/", "TierController@getAll");
    Route::get("/suppliers","TierController@getAllSuppliers");
    Route::get("/clients","TierController@getAllClients");
    Route::get("/series","ProductSeriesController@getAll");
    Route::get("/series/{id}","ProductSeriesController@getbyId");
    Route::get("{id}", "TierController@getbyId");
    Route::post("/","TierController@create");
    Route::post("/series","ProductSeriesController@create");
    Route::patch("{id}","TierController@update");
    Route::patch("/series/{id}","ProductSeriesController@update");
    Route::delete("{id}","TierController@logicalDelete") ;
    Route::delete("/series/{id}","ProductSeriesController@logicalDelete") ;
});


//Department
Route::prefix('departments')->group(function () {

    Route::get("/", "DepartmentController@getAll");
    Route::get("{id}", "DepartmentController@getbyId");
    Route::post("/","DepartmentController@create");
    Route::patch("{id}","DepartmentController@update");
    Route::delete("{id}","DepartmentController@logicalDelete") ;

});


//Material
Route::prefix('materials')->group(function () {

    Route::get("/", "MaterialController@getAll");
    Route::get("/available", "MaterialController@getAvailable");
    Route::get("{id}", "MaterialController@getbyId");
    Route::get("{id}/tier","MaterialController@getTierbyArrivalId");
    Route::post("/","MaterialController@create");
    Route::patch("{id}","MaterialController@update");
    Route::delete("{id}","MaterialController@logicalDelete") ;

});

Route::prefix('batches')->group(function () {

    Route::get("/", "MaterialBatchController@getAll");
    Route::post("/","MaterialBatchController@create");

});


//MaterialStatus
Route::prefix('materialstatus')->group(function () {

    Route::get("/", function () {
        return "MaterialStatus test";
    });

});


//MaterialType
Route::prefix('materialtypes')->group(function () {

    Route::get("/", "MaterialTypeController@getAll");
    Route::get("/operationdefs", "MaterialTypeController@getOperationsPerType");
    Route::post("/", "MaterialTypeController@create");

});



//Operation
Route::prefix('operations')->group(function () {

    Route::get("/", function () {
        return "Operation test";
    });

});


//OperationDefinitions
Route::prefix('operationdef')->group(function () {

    Route::get("/", "OperationDefinitionController@getAll");
    Route::get("/materialtype/{id}", "OperationDefinitionController@getDefsbyProduct");
    // Route::get("{id}", "DepartmentController@getbyId");
    Route::post("/","OperationDefinitionController@create");
    Route::patch("{id}","OperationDefinitionController@update");
    Route::delete("{id}","OperationDefinitionController@logicalDelete") ;


});


//Product
Route::prefix('products')->group(function () {

    Route::get("/", "ProductController@getAll");
    Route::get("/created", "ProductController@getCreated");
    Route::get("{id}", "ProductController@getbyId");
    Route::post("/", "ProductController@create");
    Route::patch("{id}","ProductController@update");

});


//ProductMaterial
Route::prefix('productmaterial')->group(function () {

    Route::get("/", function () {
        return "ProductMaterial test";
    });

});


//ProductOperations
Route::prefix('productoperations')->group(function () {

    Route::post("/", "ProductOperationsController@create");

});


//ProductSeries
Route::prefix('productseries')->group(function () {

    Route::get("/", function () {
        return "ProductSeries test";
    });

});


//ProductStatus
Route::prefix('productstatus')->group(function () {

    Route::get("/", function () {
        return "ProductStatus test";
    });

});


//Shipping
Route::prefix('shipping')->group(function () {

    Route::get("/", function () {
        return "Shipping test";
    });

});


//Stock
Route::prefix('locations')->group(function () {

    Route::get("/", "LocationController@getAll");
    Route::get("{id}", "LocationController@getbyId");
    Route::post("/","LocationController@create");
    Route::patch("{id}","LocationController@update");
    Route::delete("{id}","LocationController@logicalDelete") ;
});



//Unit
Route::prefix('units')->group(function () {

    Route::get("/", "UnitController@getAll");
    Route::get("{id}", "UnitController@getbyId");
    Route::post("/","UnitController@create");
    Route::patch("{id}","UnitController@update");
    Route::delete("{id}","UnitController@logicalDelete") ;

});



//User
Route::prefix('users')->group(function () {

    Route::get("/", "UserController@getAll");
    Route::get("{id}", "UserController@getbyId");
    Route::post("/","UserController@create");
    Route::patch("{id}","UserController@update");
    Route::delete("{id}","UserController@logicalDelete") ;

});
