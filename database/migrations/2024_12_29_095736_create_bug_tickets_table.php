<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBugTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('bug_tickets', function (Blueprint $table) {
            $table->id(); // This automatically creates 'bug_id' as an auto-incrementing primary key.
            $table->string('title'); // Add title field
            $table->string('description');
            $table->string('status');
            $table->enum('priority', ['low', 'medium', 'high']); // Add priority field as enum
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->string('screenshot')->nullable();
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
        Schema::dropIfExists('bug_tickets');
    }
}
