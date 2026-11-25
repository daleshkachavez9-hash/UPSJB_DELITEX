<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Un cliente puede ser una persona (DNI, 8 dígitos) o un club, colegio o empresa (RUC, 11 dígitos)
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['dni']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('dni', 'document_number');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('document_type', 3)->default('DNI')->after('name');
            $table->string('document_number', 11)->change();
            $table->unique('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['document_number']);
            $table->dropColumn('document_type');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('document_number', 'dni');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->char('dni', 8)->change();
            $table->unique('dni');
        });
    }
};
