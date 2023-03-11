<?php

namespace App\Modules\Maintenance\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DesktopApplicationListResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        $installer = $this->resource->installer;

        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'is_self_install' => $this->resource->is_self_install,
            'is_ps' => $this->resource->is_ps,
            'ps_or_dl' => $this->resource->ps_or_dl,
            'arguments' => $this->resource->arguments,
            'execution_date' => $this->resource->execution_date,
            'time' => $this->resource->time,
            'filename' => $installer['filename'],
        ];
    }
}
