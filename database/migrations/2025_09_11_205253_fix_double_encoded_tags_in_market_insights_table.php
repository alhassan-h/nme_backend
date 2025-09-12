<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix double-encoded JSON tags by decoding them
        $insights = DB::table('market_insights')->whereNotNull('tags')->get();

        foreach ($insights as $insight) {
            $tags = $insight->tags;

            // If tags is a JSON string containing another JSON string, decode it
            if (is_string($tags) && strlen($tags) > 2) {
                $decoded = json_decode($tags, true);
                if (is_string($decoded)) {
                    // It's double-encoded, decode again
                    $finalDecoded = json_decode($decoded, true);
                    if (is_array($finalDecoded)) {
                        DB::table('market_insights')
                            ->where('id', $insight->id)
                            ->update(['tags' => json_encode($finalDecoded)]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('market_insights', function (Blueprint $table) {
            //
        });
    }
};
