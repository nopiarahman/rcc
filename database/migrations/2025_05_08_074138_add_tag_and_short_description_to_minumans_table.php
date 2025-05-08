<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTagAndShortDescriptionToMinumansTable extends Migration
{
    public function up()
    {
        Schema::table('minumans', function (Blueprint $table) {
            $table->string('tag')->nullable()->after('kategori'); // misal setelah kategori
            $table->string('short_description', 255)->nullable()->after('deskripsi');
        });
    }

    public function down()
    {
        Schema::table('minumans', function (Blueprint $table) {
            $table->dropColumn(['tag', 'short_description']);
        });
    }
}
