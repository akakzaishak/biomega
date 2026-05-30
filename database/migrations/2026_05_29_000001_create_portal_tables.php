<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->id('ID');
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('PhoneNumber')->unique();
            $table->string('Password');
            $table->string('Role')->default('admin');
        });

        Schema::create('pharmacy', function (Blueprint $table) {
            $table->string('NIF')->primary();
            $table->string('FirstName');
            $table->string('LastName');
            $table->string('PhoneNumber')->unique();
            $table->string('WorkTime');
            $table->string('Password');
            $table->string('Location');
            $table->string('Role')->default('pharmacy');
        });

        foreach (['commercialservice', 'deliverymanager', 'deliveryperson', 'stockemployee'] as $tableName) {
            Schema::create($tableName, function (Blueprint $table) use ($tableName) {
                $table->id('ID');
                $table->string('FirstName');
                $table->string('LastName');
                $table->string('PhoneNumber')->unique();
                $table->string('Password');
                $table->string('Role')->default($tableName);
            });
        }

        Schema::create('order', function (Blueprint $table) {
            $table->string('Tracking')->primary();
            $table->string('QRCode');
            $table->date('Date');
            $table->decimal('otalAmount', 10, 2)->default(0);
            $table->string('ProofImage')->nullable();
            $table->unsignedInteger('PackageNumber')->default(0);
            $table->unsignedTinyInteger('Status')->default(0);
            $table->string('QRimage')->nullable();
            $table->unsignedTinyInteger('IsUrgen')->default(0);
        });

        Schema::create('asined_order', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('pharmacy_id');
            $table->unsignedBigInteger('deliveryperson_id')->nullable();
        });

        Schema::create('orderitem', function (Blueprint $table) {
            $table->id();
            $table->string('Name');
            $table->unsignedInteger('contiti');
        });

        Schema::create('payment', function (Blueprint $table) {
            $table->id('payment_id');
            $table->string('order_id')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('method')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment');
        Schema::dropIfExists('orderitem');
        Schema::dropIfExists('asined_order');
        Schema::dropIfExists('order');

        foreach (['stockemployee', 'deliveryperson', 'deliverymanager', 'commercialservice', 'pharmacy', 'admin'] as $tableName) {
            Schema::dropIfExists($tableName);
        }
    }
};