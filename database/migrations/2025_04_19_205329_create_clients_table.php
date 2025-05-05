<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// class CreateClientsTable extends Migration
// {
//     public function up()
//     {
//         Schema::create('clients', function (Blueprint $table) {
//             $table->id();
//             $table->string('name');
//             $table->string('email')->unique();
//             $table->string('phone')->nullable();
//             $table->string('address')->nullable();
//             $table->timestamps();
//         });
//     }

//     public function down()
//     {
//         Schema::dropIfExists('clients');
//     }
// }








// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// class CreateClientsTable extends Migration
// {
//     public function up()
//     {
//         Schema::create('clients', function (Blueprint $table) {
//             $table->id();
//             $table->string('name');
//             $table->string('lastname');
//             $table->unsignedBigInteger('tipo_id')->nullable()->after('lastname');
//             $table->foreign('tipo_id')->references('id')->on('tipo_documentos')->onDelete('set null');
//             $table->string('nro_documento')->nullable()->after('tipo_id');
//             $table->string('nro_matricula')->nullable()->after('nro_documento');
//             $table->string('email')->unique();
//             $table->string('phone')->nullable();
//             $table->string('address')->nullable();
//             $table->timestamps();
//         });
//     }

//     public function down()
//     {
//         Schema::dropIfExists('clients');
//     }
// }




use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lastname');
            $table->unsignedBigInteger('tipo_id')->nullable();
            $table->string('nro_documento')->nullable();
            $table->string('nro_matricula')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();

            // Agregar la llave foránea después de definir todas las columnas
            $table->foreign('tipo_id')->references('id')->on('tipo_documentos')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
