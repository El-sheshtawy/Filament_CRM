<?php

namespace App\Filament\Resources\TaskResource\Pages;

use App\Filament\Resources\TaskResource;
use App\Models\Task;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];

        if (auth()->user() && ! auth()->user()->isAdmin()) {
            $tabs['my'] = Tab::make('My tasks')
                ->badge(Task::query()->where('user_id', auth()->id())->count())
            ->modifyQueryUsing(function ($query) {
                return $query->where('user_id', auth()->id());
            });
        }

        $tabs['all'] = Tab::make('All tasks')->badge(Task::query()->count());

        $tabs['completed'] = Tab::make('Completed tasks')
            ->badge(Task::query()->where('is_completed', true)->count())
        ->modifyQueryUsing(function ($query) {
            return $query->where('is_completed', true);
        });

        $tabs['uncompleted'] = Tab::make('Uncompleted tasks')
            ->badge(Task::query()->where('is_completed', false)->count())
            ->modifyQueryUsing(function ($query) {
                return $query->where('is_completed', false);
            });

        return $tabs;
    }
}
