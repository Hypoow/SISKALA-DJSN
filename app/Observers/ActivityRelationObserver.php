<?php

namespace App\Observers;

use App\Models\ActivityDocumentation;
use App\Models\ActivityMaterial;
use App\Models\ActivityMom;
use App\Support\RealtimeBroadcaster;
use Illuminate\Database\Eloquent\Model;

class ActivityRelationObserver
{
    public function created(Model $model): void
    {
        $this->broadcast($model, 'updated');
    }

    public function updated(Model $model): void
    {
        $this->broadcast($model, 'updated');
    }

    public function deleted(Model $model): void
    {
        $this->broadcast($model, 'updated');
    }

    private function broadcast(Model $model, string $action): void
    {
        if (!method_exists($model, 'activity')) {
            return;
        }

        $model->loadMissing('activity');

        if (!$model->activity) {
            return;
        }

        RealtimeBroadcaster::broadcastActivitySegment(
            $model->activity,
            match ($model::class) {
                ActivityMaterial::class => 'materials',
                ActivityMom::class => 'minutes',
                ActivityDocumentation::class => 'documentation',
                default => 'activity',
            },
            $action
        );
    }
}
