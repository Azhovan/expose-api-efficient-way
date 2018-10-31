<?php

namespace App\ExposeApi\Recipe;

use App\ExposeApi\Recipe\Core\Event\AbstractRecipeEvent;
use Closure;

class Builder
{
    /**
     * Create a new Recipe based on Template.
     *
     * @param  $data
     * @param Closure $callback
     *
     * @return string
     */
    public function create($data, Closure $callback = null): string
    {
        return $this->build(
            plug(
                $this->createTemplate($data), function ($template) use ($callback) {
                    return $callback($template);
                }
            ), 'RecipeCreated'
        );
    }

    /**
     * Delete a Recipe template.
     *
     * @param  $id
     * @param Closure $callback
     *
     * @return string
     */
    public function delete(array $id, Closure $callback = null): string
    {
        return $this->build($this->createTemplate($id), 'RecipeDeleted');
    }

    /**
     * Update the Recipe with given data.
     *
     * @param  $data
     * @param Closure $callback
     *
     * @return string
     */
    public function update(array $data, Closure $callback = null): string
    {
        return $this->build(
            plug(
                $this->createTemplate($data), function ($template) use ($callback) {
                    return $callback($template);
                }
            ), 'RecipeUpdated'
        );
    }

    /**
     * Get one or more Recipe[s] details.
     *
     * @param  $id
     * @param Closure|null $callback
     *
     * @return mixed
     */
    public function get(array $id, Closure $callback = null)
    {
        return $this->build($this->createTemplate($id), 'RecipeQueried');
    }

    /**
     * Rate the Recipe.
     *
     * @param array   $data
     * @param Closure $callback
     *
     * @return mixed
     */
    public function rate(array $data, Closure $callback)
    {
        return $this->build(
            plug(
                $this->createTemplate($data), function ($template) use ($callback) {
                    return $callback($template);
                }
            ), 'RecipeRated'
        );
    }

    /**
     * Search the Recipe.
     *
     * @param array   $data
     * @param Closure $callback
     *
     * @return mixed
     */
    public function search(array $data, Closure $callback)
    {
        return $this->build(
            plug(
                $this->createTemplate($data), function ($template) use ($callback) {
                    return $callback($template);
                }
            ), 'RecipeSearched'
        );
    }

    /**
     * dispatch the event.
     *
     * @param RecipeTemplate $templateObject
     *
     * @return mixed
     */
    private function build(RecipeTemplate $templateObject, $event)
    {
        return dispatch(AbstractRecipeEvent::getContextFromType($event), $templateObject);
    }

    /**
     * Create new Template.
     *
     * @param array $data
     *
     * @return RecipeTemplate
     */
    public function createTemplate(array $data): RecipeTemplate
    {
        return new RecipeTemplate($data);
    }
}
