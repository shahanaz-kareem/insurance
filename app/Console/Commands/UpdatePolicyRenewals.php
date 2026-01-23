<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Policy;
use Carbon\Carbon;

class UpdatePolicyRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'policies:update-renewals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update policy renewal dates if they have passed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting policy renewal updates...');

        $today = date('Y-m-d');
        
        // Fetch policies that need updating (renewal_date < today) and have a valid renewal date
        // Processing in chunks to avoid memory issues
        Policy::where('renewal_date', '<', $today)
            ->where('renewal_date', '!=', '0000-00-00')
            ->whereNotNull('renewal_date')
            ->chunkById(100, function ($policies) {
                foreach ($policies as $policy) {
                    $new_renewal_date = null;
                    
                    // Logic moved from PolicyController
                    if ($policy->type == 'annually') {
                        $new_renewal_date = date('Y-m-d', strtotime('+1 years', strtotime($policy->renewal_date)));
                    } else if ($policy->type == 'monthly') {
                        $new_renewal_date = date('Y-m-d', strtotime('+1 months', strtotime($policy->renewal_date)));
                    } else if ($policy->type == 'half yearly') {
                        $new_renewal_date = date('Y-m-d', strtotime('+6 months', strtotime($policy->renewal_date)));
                    } else {
                         // Default case (assuming quarterly as per original controller logic else branch)
                        $new_renewal_date = date('Y-m-d', strtotime('+3 months', strtotime($policy->renewal_date)));
                    }

                    if ($new_renewal_date) {
                         DB::table('policies')
                            ->where('id', $policy->id)
                            ->update(['renewal_date' => $new_renewal_date]);
                    }
                }
            });

        $this->info('Policy renewal dates updated successfully.');
        return 0;
    }
}
