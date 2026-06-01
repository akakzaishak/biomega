<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('FirstName', 25);
            $table->string('LastName', 25);
            $table->string('PhoneNumber', 10)->unique();
            $table->string('Role', 25);
            $table->string('Password', 255);
        });

        Schema::create('pharmacy', function (Blueprint $table) {
            $table->string('NIF', 10)->primary();
            $table->string('FirstName', 25);
            $table->string('LastName', 25);
            $table->string('PhoneNumber', 10)->unique();
            $table->time('WorkTime');
            $table->string('Password', 255);
            $table->string('Location', 200);
            $table->string('Role', 25)->default('pharmacy');
        });

        foreach (['commercialservice', 'deliverymanager', 'deliveryperson', 'stockemployee'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                $table->increments('ID');
                $table->string('FirstName', 25);
                $table->string('LastName', 25);
                $table->string('PhoneNumber', 10)->unique();
                $table->string('Role', 25);
                $table->string('Password', 255);
            });
        }

        Schema::create('order', function (Blueprint $table) {
            $table->string('Tracking')->primary();
            $table->string('QRCode', 2000);
            $table->date('Date');
            $table->integer('otalAmount');
            $table->string('ProofImage', 200)->default('');
            $table->integer('PackageNumber');
            $table->unsignedTinyInteger('Status');
            $table->string('QRimage', 200)->default('');
            $table->unsignedTinyInteger('IsUrgen');
        });

        Schema::create('asined_order', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('order_id', 200);
            $table->string('pharmacy_id', 10);
            $table->string('deliveryperson_id', 10)->nullable();
        });

        Schema::create('orderitem', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('Name', 200);
            $table->integer('contiti');
        });

        Schema::create('order_item_link', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('orderitem_id');
            $table->string('pharmacy_id', 10);
            $table->integer('contiti');
        });

        Schema::create('delivery_location', function (Blueprint $table) {
            $table->string('PhoneNumber', 10)->primary();
            $table->decimal('Latitude', 10, 7);
            $table->decimal('Longitude', 10, 7);
            $table->unsignedTinyInteger('Status')->default(1);
            $table->timestamp('UpdatedAt')->useCurrent();
            $table->unsignedTinyInteger('GpsForced')->default(0);
            $table->timestamp('ForcedAt')->nullable();
            $table->string('ForcedByAdmin', 25)->nullable();
        });

        Schema::create('delivery_location_history', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('PhoneNumber', 10);
            $table->decimal('Latitude', 10, 7);
            $table->decimal('Longitude', 10, 7);
            $table->timestamp('UpdatedAt')->useCurrent();
        });

        Schema::create('payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('order_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('method')->nullable();
            $table->string('status')->nullable();
        });

        Schema::table('asined_order', function (Blueprint $table) {
            $table->foreign('deliveryperson_id')->references('PhoneNumber')->on('deliveryperson');
        });

        Schema::table('delivery_location', function (Blueprint $table) {
            $table->foreign('PhoneNumber')->references('PhoneNumber')->on('deliveryperson')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('delivery_location_history', function (Blueprint $table) {
            $table->foreign('PhoneNumber')->references('PhoneNumber')->on('deliveryperson')->cascadeOnDelete()->cascadeOnUpdate();
        });

        Schema::table('order_item_link', function (Blueprint $table) {
            $table->foreign('orderitem_id')->references('ID')->on('orderitem');
            $table->foreign('pharmacy_id')->references('NIF')->on('pharmacy');
        });
    }

    public function down(): void
    {
        Schema::table('order_item_link', function (Blueprint $table) {
            $table->dropForeign(['orderitem_id']);
            $table->dropForeign(['pharmacy_id']);
        });

        Schema::table('delivery_location_history', function (Blueprint $table) {
            $table->dropForeign(['PhoneNumber']);
        });

        Schema::table('delivery_location', function (Blueprint $table) {
            $table->dropForeign(['PhoneNumber']);
        });

        Schema::table('asined_order', function (Blueprint $table) {
            $table->dropForeign(['deliveryperson_id']);
        });

        Schema::dropIfExists('delivery_location_history');
        Schema::dropIfExists('delivery_location');
        Schema::dropIfExists('order_item_link');
        Schema::dropIfExists('payment');
        Schema::dropIfExists('orderitem');
        Schema::dropIfExists('asined_order');
        Schema::dropIfExists('order');

        foreach (['stockemployee', 'deliveryperson', 'deliverymanager', 'commercialservice', 'pharmacy', 'admin'] as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
}; 