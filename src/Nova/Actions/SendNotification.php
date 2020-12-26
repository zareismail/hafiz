<?php

namespace Zareismail\Hafiz\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\{ActionFields, Hidden, Text, Select};
use Zareismail\Hafiz\Notifications\Announcement;
use Zareismail\Hafiz\Contracts\Subscribable;

class SendNotification extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {  
        $users = $models->whereInstanceOf(Subscribable::class)->flatMap->subscribers();

        Notification::send($users, new Announcement($fields->toArray()));
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make(__('Notification Level'), 'level')
                ->displayUsingLabels()
                ->sortable()
                ->default('info')
                ->options([
                    'info'      => __('Notice'),
                    'success'   => __('Attention'),
                    'error'     => __('Alert'), 
                ]),

            Text::make('Title', 'title')
                ->sortable()
                ->required()
                ->rules('required'), 

            Text::make('Subtitle', 'subtitle')
                ->sortable()
                ->required()
                ->rules('required'),  

        ];
    }
}
