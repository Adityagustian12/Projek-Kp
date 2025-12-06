<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;

class SyncRoomStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rooms:sync-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync room status based on booking status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Syncing room statuses...');
        
        $rooms = Room::with('bookings')->get();
        $updated = 0;
        
        foreach ($rooms as $room) {
            if ($room->syncStatus()) {
                $updated++;
                $this->line("Updated: {$room->room_number} -> {$room->status}");
            }
        }
        
        $this->info("Sync completed! Updated {$updated} room(s).");
        
        return Command::SUCCESS;
    }
}
