<?php

namespace SmartBots\Console\Commands;

use Illuminate\Console\Command;

use SmartBots\{
    User,
    Hub,
    Bot,
    Member,
    Schedule,
    Automation,
    HubPermission,
    BotPermission,
    SchedulePermission,
    AutomationPermission
};

class RunSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runschedule {schedule_id : The Id of schedule}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a schedule';

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
     * @return mixed
     */
    public function handle()
    {
        $schedule_id = $this->argument('schedule_id');

        $schedule = Schedule::findOrFail($schedule_id);

        if ($schedule->isActivated()) {

            $this->info('Schedule #'.$schedule_id.' is activated');

            if ($schedule->condition_type != 0) {

                $schedule_conditions = explode('|',$schedule->condition_data);

                for ($i=0; $i<count($schedule_conditions); $i++) {

                    $schedule_conditions[$i] = explode(',',$schedule_conditions[$i]);
                    $return_conditions[] = Bot::findOrFail($schedule_conditions[$i][0])->realStatus() == $schedule_conditions[$i][1];

                }

                if ($schedule->condition_method == 1) {

                    $return_condition = true;

                    foreach ($return_conditions as $single_return_condition) {

                        if ($single_return_condition == false) {
                            $return_condition2 = false;
                            break;
                        }

                    }

                } else if ($schedule->condition_method == 2) {

                    $return_condition = false;

                    foreach ($return_conditions as $single_return_condition) {

                        if ($single_return_condition == true) {
                            $return_condition = true;
                            break;
                        }
                    }
                }

                if (($return_condition == true && $schedule->condition_type == 1)
                || ($return_condition == false && $schedule->condition_type == 2)) {
                    $run_schedule = true;
                    $this->info('Schedule #'.$schedule_id.' has true condition');
                } else {
                    $run_schedule = false;
                    $this->info('Schedule #'.$schedule_id.' has false condition');
                }

            } else {
                $run_schedule = true;
                $this->info('Schedule #'.$schedule_id.' has no condition');
            }

            if ($run_schedule == true) {

                if ($schedule->ran_times+1 == 1) {
                    $times = '1st';
                } else if ($schedule->ran_times+1 == 2) {
                    $times = '2nd';
                } else if ($schedule->ran_times+1 == 3) {
                    $times = '3rd';
                } else {
                    $times = ($schedule->ran_times+1).'th';
                }

                $this->info('Running schedule #'.$schedule_id.' the '.$times.' time...');

                $schedule_actions = explode('|',$schedule->action);

                for($i=0; $i<count($schedule_actions); $i++) {

                    $schedule_actions[$i] = explode(',',$schedule_actions[$i]);

                    switch ($schedule_actions[$i][0]) {
                        case 1:
                            Bot::findOrFail($schedule_actions[$i][1])->toggle();
                            $this->line('Toggled bot #'.$schedule_actions[$i][1]);
                            break;
                        case 2:
                            Bot::findOrFail($schedule_actions[$i][1])->control(1);
                            $this->line('Turned on bot #'.$schedule_actions[$i][1]);
                            break;
                        case 3:
                            Bot::findOrFail($schedule_actions[$i][1])->control(0);
                            $this->line('Turned off bot #'.$schedule_actions[$i][1]);
                            break;
                    }

                }

                if ($schedule->type == 1) {
                    $schedule->delete();
                    $this->info('Deleted schedule #'.$schedule_id.' because it is "one-time" schedule');
                }

                $schedule->ran_times++;
                $this->info('Increase ran times of schedule #'.$schedule_id.'...');

                if ($schedule->ran_times >= $schedule->deactivate_after_times) {
                    $schedule->status = 0;
                    $schedule->ran_times = 0;
                    $schedule->deactivate_after_datetime = null;
                    $schedule->deactivate_after_times = null;
                    $this->info('Deactivate schedule #'.$schedule_id.' because it reach the limit');
                }

                $schedule->save();
            }
        } else {
            $this->info('Schedule #'.$schedule_id.' is deactivated');
        }
    }
}
