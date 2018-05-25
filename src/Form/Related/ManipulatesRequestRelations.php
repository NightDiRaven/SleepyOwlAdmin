<?php

namespace SleepingOwl\Admin\Form\Related;

use Illuminate\Http\Request;

trait ManipulatesRequestRelations
{
    /**
     * @var bool
     */
    protected $copyAfterSave = false;

    /**
     * Marks relations to be able to be copied after form saving.
     *
     * @return $this
     */
    public function copyAfterSave()
    {
        $this->copyAfterSave = true;

        return $this;
    }

    /**
     * Removes relation from request when it's in save_and_create mode.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Request
     */
    protected function prepareRequestToBeCopied(Request $request)
    {
        $remove = [];
        if ($request->method() === 'POST' && $request->input('next_action') === 'save_and_create') {
            if (! $this->copyAfterSave) {
                $remove[] = $this->relationName;
            }
            $this->makeCopyOfRelations($request);
        }

        return $request->replace($request->except($remove));
    }

    /**
     * Creates a copy of form relations.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function makeCopyOfRelations(\Illuminate\Http\Request $request)
    {
        $newData = [];
        $data = $request->input($this->relationName, []);

        $counter = 1;

        foreach ($data as $key => $values) {
            $newData["new_{$counter}"] = $values;
            $counter++;
        }

        $request->merge([$this->relationName => $newData]);
    }
}