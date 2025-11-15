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
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nome amigável da credencial: "Painel ERP", "Servidor X"
            $table->string('system')->nullable(); // Sistema/plataforma: "ERP TOTVS", "Azure", "GitHub"
            $table->string('username')->nullable();
            $table->text('password_encrypted'); // senha criptografada
            $table->text('notes')->nullable(); // observações
            $table->string('url')->nullable(); // link de acesso
            $table->foreignId('created_by')->constrained('users'); // quem cadastrou
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
