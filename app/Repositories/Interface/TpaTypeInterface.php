<?php

namespace App\Repositories\Interface;

interface TpaTypeInterface
{
    public function getAll();

    public function getById(string $id);

    public function create(array $attributes);

    public function update(string $id, array $attributes);

    public function delete(string $id);
}
