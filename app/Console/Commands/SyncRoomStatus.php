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
        
        // Get all rooms with their occupied booking count
        $rooms = Room::all();
        $updated = 0;
        
        foreach ($rooms as $room) {
            $oldStatus = $room->status;
            
            // Get occupied bookings count directly
            $occupiedCount = \DB::table('bookings')
                ->where('room_id', $room->id)
                ->where('status', 'occupied')
                ->count();
            
            $this->line("Room {$room->id} ({$room->room_number}): Current = {$oldStatus}, Occupied bookings = {$occupiedCount}");
            
            // Update based on occupied bookings
            if ($occupiedCount > 0) {
                // Room should be occupied
                if ($oldStatus !== 'occupied') {
                    // Get current capacity before update
                    $currentCapacity = \DB::table('rooms')->where('id', $room->id)->value('capacity');
                    $currentOriginalCapacity = \DB::table('rooms')->where('id', $room->id)->value('original_capacity');
                    
                    $updateData = [
                        'status' => 'occupied',
                        'capacity' => 1
                    ];
                    
                    // Store original capacity if not set
                    if (!$currentOriginalCapacity) {
                        $updateData['original_capacity'] = $currentCapacity ?? 1;
                    }
                    
                    \DB::table('rooms')->where('id', $room->id)->update($updateData);
                    $updated++;
                    $this->info("  ✓ Updated to OCCUPIED");
                }
            } else {
                // No occupied bookings - should be available
                if ($oldStatus === 'occupied') {
                    $currentOriginalCapacity = \DB::table('rooms')->where('id', $room->id)->value('original_capacity');
                    \DB::table('rooms')->where('id', $room->id)->update([
                        'status' => 'available',
                        'capacity' => $currentOriginalCapacity ?? 1,
                        'original_capacity' => null
                    ]);
                    $updated++;
                    $this->info("  ✓ Updated to AVAILABLE");
                }
            }
        }
        
        $this->info("\nSync completed! Updated {$updated} room(s).");
        
        // Show final status
        $this->line("\nFinal Room Status:");
        Room::all()->each(function($room) {
            $occupiedCount = \DB::table('bookings')
                ->where('room_id', $room->id)
                ->where('status', 'occupied')
                ->count();
            $statusIcon = $room->status === 'occupied' ? '🔴' : ($room->status === 'available' ? '🟢' : '🟡');
            $this->line("  {$statusIcon} {$room->room_number}: {$room->status} (Occupied bookings: {$occupiedCount})");
        });
        
        return Command::SUCCESS;
    }
}
