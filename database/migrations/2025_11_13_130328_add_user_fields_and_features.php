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
        // Add user profile fields
        Schema::table('users', function (Blueprint $table) {
            $table->string('alias')->nullable()->after('name');
            $table->boolean('is_anonymous')->default(false)->after('alias');
            $table->string('phone')->nullable()->after('email');
            $table->text('bio')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('bio');
            $table->integer('reputation_points')->default(0)->after('avatar');
            $table->json('notification_preferences')->nullable()->after('reputation_points');
        });

        // Add user_id to issues table
        Schema::table('issues', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->integer('votes_count')->default(0)->after('is_verified');
            $table->integer('comments_count')->default(0)->after('votes_count');
            $table->foreignId('assigned_to')->nullable()->after('admin_notes')->constrained('users')->onDelete('set null');
            $table->foreignId('department_id')->nullable()->after('assigned_to')->constrained()->onDelete('set null');
            $table->date('due_date')->nullable()->after('department_id');
            $table->integer('priority')->default(0)->after('due_date');
        });

        // Create issue_votes table
        Schema::create('issue_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('issue_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->unique(['issue_id', 'user_id']);
            $table->index(['issue_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_votes');
        
        Schema::table('issues', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['user_id', 'votes_count', 'comments_count', 'assigned_to', 'department_id', 'due_date', 'priority']);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['alias', 'is_anonymous', 'phone', 'bio', 'avatar', 'reputation_points', 'notification_preferences']);
        });
    }
};
