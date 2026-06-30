<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Assumption: the "events" table is owned by another slice of the platform
 * and isn't part of this assessment's scope. A minimal version is included
 * here purely so ticket_tiers.event_id has something to reference and the
 * test suite can run end-to-end.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
