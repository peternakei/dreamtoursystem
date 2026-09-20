<?php

namespace App\Project\Modules\Core\Logs;

class Activity extends \Spatie\Activitylog\Models\Activity
{
    // Audit entries live in the logs database; their related records live in the
    // application database unless a related model explicitly chooses another one.
    protected function newRelatedInstance($class)
    {
        $instance = new $class;
        if (! $instance->getConnectionName()) {
            $instance->setConnection(config('database.default'));
        }

        return $instance;
    }

    protected function morphEagerTo($name, $type, $id, $ownerKey)
    {
        $context = clone $this;
        $context->setConnection(config('database.default'));

        return $this->newMorphTo(
            $context->newQuery()->setEagerLoads([]), $this, $id, $ownerKey, $type, $name
        );
    }
}
