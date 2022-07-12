<?php

use App\Models\Role;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->mediumInteger('age')->nullable();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
