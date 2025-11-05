<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Tour\Models\Tour;
use Carbon\Carbon;

class UpdateTourStatus extends Command
{
    protected $signature = 'tour:update-status';
    protected $description = 'Update the status of tours based on publish and draft dates';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $now = Carbon::now();

        // Publish scheduled tours if the publish date has arrived
        Tour::where('status', 'scheduled')
            ->where('publish_date', '<=', $now)
            ->update(['status' => 'publish']);

        // Draft published tours if the draft date has arrived
        Tour::where('status', 'publish')
            ->where('draft_date', '<=', $now)
            ->update(['status' => 'draft']);

        $this->info('Tour statuses have been updated.');
    }
}
